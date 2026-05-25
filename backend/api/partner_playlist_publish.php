<?php
// utbn-backend/api/partner_playlist_publish.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");
ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];
$playlist_id = (int)($in["playlist_id"] ?? 0);

if ($playlist_id <= 0) {
  json_out(["ok"=>false,"error"=>"MISSING_PLAYLIST_ID"], 400);
}

// ✅ FIX: warning لازم يكون معرّف من البداية (بدون ما نغيّر المنطق)
$warning = [];

// ensure columns exist (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN description MEDIUMTEXT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN expected_lectures INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN difficulty INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN published_at DATETIME NULL");

// NEW: store selected major as text on playlist
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");

// NEW: catalog table for "how many materials/courses exist in this major"
// Fill it once (admin/import) per major.
$conn->query("CREATE TABLE IF NOT EXISTS major_courses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  major_text VARCHAR(220) NOT NULL,
  course_code VARCHAR(40) NULL,
  course_name VARCHAR(220) NULL,
  PRIMARY KEY (id),
  KEY idx_major_text (major_text)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// load playlist (must be owned)
$plst = $conn->prepare("
  SELECT id, name, description, expected_lectures, difficulty, coin_pool, is_published, major_text, source_template_playlist_id
  FROM partner_playlists
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
$plst->bind_param("ii", $playlist_id, $user_id);
$plst->execute();
$pl = $plst->get_result()->fetch_assoc();
$plst->close();

if (!$pl) {
  json_out(["ok"=>false,"error"=>"PLAYLIST_NOT_FOUND"], 404);
}

// ✅ FIX: عرّف المتغيرات من $pl (هذا كان ناقص)
$expected_lectures = (int)($pl["expected_lectures"] ?? 0);
$difficulty        = (int)($pl["difficulty"] ?? 0);
$major_text        = trim((string)($pl["major_text"] ?? ""));
$current_pool      = (int)($pl["coin_pool"] ?? 0);
$template_id       = (int)($pl["source_template_playlist_id"] ?? 0);

// strong checks
$description = trim((string)($pl["description"] ?? ""));
if ($description === "") {
  $description = "Playlist published by company"; // أو استخدم اسم البلاي ليست
  $uu = $conn->prepare("UPDATE partner_playlists SET description=? WHERE id=? AND partner_user_id=?");
  $uu->bind_param("sii", $description, $playlist_id, $user_id);
  $uu->execute();
  $uu->close();
}
// if ($expected_lectures <= 0) $expected_lectures = 0; // ما تمنع النشر
if ($difficulty < 0 || $difficulty > 100) $difficulty = 0; // ما تمنع النشر

// count videos
$cnt = $conn->prepare("SELECT COUNT(*) AS c FROM partner_videos WHERE playlist_id=? AND partner_user_id=?");
$cnt->bind_param("ii", $playlist_id, $user_id);
$cnt->execute();
$crow = $cnt->get_result()->fetch_assoc();
$cnt->close();

$videos_count = (int)($crow["c"] ?? 0);
if ($expected_lectures > 0 && $videos_count < $expected_lectures) {
  // ✅ FIX: لا تستخدم $warning قبل تعريفه، ولا تمسحه
  $warning["not_enough_videos"] = [
    "code" => "NOT_ENOUGH_VIDEOS",
    "videos_count" => $videos_count,
    "expected_lectures" => $expected_lectures
  ];
  // ما نعمل json_out هون، خليه يكمل publish
}

// each video needs at least one: quiz or code
$vv = $conn->prepare("
  SELECT v.id,
    EXISTS(SELECT 1 FROM partner_video_quizzes q WHERE q.partner_video_id=v.id AND q.partner_user_id=? LIMIT 1) AS has_quiz,
    EXISTS(SELECT 1 FROM partner_video_code_problems c WHERE c.partner_video_id=v.id AND c.partner_user_id=? LIMIT 1) AS has_code
  FROM partner_videos v
  WHERE v.playlist_id=? AND v.partner_user_id=?
");
$vv->bind_param("iiii", $user_id, $user_id, $playlist_id, $user_id);
$vv->execute();
$rs = $vv->get_result();
$missing = [];
while ($row = $rs->fetch_assoc()) {
  $has_q = (int)$row["has_quiz"] === 1;
  $has_c = (int)$row["has_code"] === 1;
  if (!$has_q && !$has_c) $missing[] = (int)$row["id"];
}
$vv->close();

// ✅ FIX: لا تمسح $warning (كان عندك $warning = null;)
if (count($missing)) {
  // بدل ما نمنع النشر، بنخليها تحذير فقط
  $warning["video_needs_quiz_or_code"] = [
    "code" => "VIDEO_NEEDS_QUIZ_OR_CODE",
    "missing_video_ids" => $missing
  ];
}

// ✅ FIX: عرفهم دائماً عشان ما يصير Undefined بالـ json_out
$TOTAL_MAJOR_BUDGET = 15000;
$course_count = 0;

// =====================================================
// COIN POOL LOGIC (fix):
// 1) إذا البلاي ليست فيها coin_pool أصلاً -> خليه زي ما هو
// 2) إذا coin_pool = 0 وكانت منسوخة من Template -> انسخ coin_pool من الـ Template
// 3) إذا لسه 0 -> احسبه بالطريقة القديمة (اختياري)
// =====================================================

$coin_pool = $current_pool;

// (2) copy from template if needed
if ($coin_pool <= 0 && $template_id > 0) {
  $tp = $conn->prepare("SELECT coin_pool FROM partner_playlists WHERE id=? LIMIT 1");
  if ($tp) {
    $tp->bind_param("i", $template_id);
    $tp->execute();
    $tr = $tp->get_result()->fetch_assoc();
    $tp->close();
    $coin_pool = (int)($tr["coin_pool"] ?? 0);
  }
}

// (3) fallback compute only if still 0
if ($coin_pool <= 0) {
  if ($major_text !== "") {
    $st = $conn->prepare("SELECT COUNT(*) AS c FROM major_courses WHERE major_text=?");
    if ($st) {
      $st->bind_param("s", $major_text);
      $st->execute();
      $r = $st->get_result()->fetch_assoc();
      $st->close();
      $course_count = (int)($r["c"] ?? 0);
    }
  }

  if ($course_count > 0) {
    $coin_pool = (int)floor($TOTAL_MAJOR_BUDGET / $course_count);
  } else {
    // old formula
    $importance = 1.0;
    $name = mb_strtolower((string)($pl["name"] ?? ""), 'UTF-8');
    $keys = ["advanced"=>1.2, "متقدم"=>1.2, "capstone"=>1.3, "مشروع"=>1.3, "intro"=>0.6, "مقدمة"=>0.6, "basics"=>0.6, "أساسيات"=>0.6];
    foreach ($keys as $k=>$v) { if (mb_strpos($name, $k) !== false) { $importance = $v; break; } }

    // difficulty صار مضمون لأنه معرفينه فوق
    $coin_pool = (int)round(2000 * ($difficulty / 100.0) * $importance);
  }
}

if ($coin_pool < 0) $coin_pool = 0;

// publish
$up = $conn->prepare("UPDATE partner_playlists
  SET is_published=1, published_at=NOW(), coin_pool=?
  WHERE id=? AND partner_user_id=?");
$up->bind_param("iii", $coin_pool, $playlist_id, $user_id);
if (!$up->execute()) {
  $err = $conn->error;
  $up->close();
  json_out(["ok"=>false,"error"=>"PUBLISH_FAILED","details"=>$err], 500);
}
$up->close();

json_out([
  "ok"=>true,
  "playlist_id"=>$playlist_id,
  "coin_pool"=>$coin_pool,
  // ✅ FIX: رجّع warning بشكل صحيح
  "warning" => !empty($warning) ? $warning : null,
  "major_text"=>$major_text,
  "major_budget_total"=>$TOTAL_MAJOR_BUDGET,
  "major_courses_count"=>$course_count,
  "videos_count"=>$videos_count
]);

