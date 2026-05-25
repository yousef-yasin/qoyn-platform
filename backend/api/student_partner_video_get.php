<?php
// utbn-backend/api/student_partner_video_get.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "INVALID_VIDEO_ID"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

// ensure publish/coin/major columns exist (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");

/**
 * Normalize major text
 */
function norm_major($s) {
  $s = trim((string)$s);
  $s = mb_strtolower($s, "UTF-8");
  $s = preg_replace('/[\x{064B}-\x{0652}]/u', '', $s); // remove Arabic tashkeel
  $s = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s);
  $s = preg_replace('/\s+/u', ' ', $s);
  return trim($s);
}

/**
 * Convert detailed majors into broad groups
 */
function major_group($major) {
  $m = norm_major($major);

  if ($m === "") return "";

  $it_keywords = [
    "it",
    "information technology",
    "computer science",
    "cs",
    "software",
    "software engineering",
    "software engineer",
    "ai",
    "artificial intelligence",
    "machine learning",
    "cyber",
    "cyber security",
    "cybersecurity",
    "information security",
    "security",
    "informatics",
    "data science",
    "data scientist",
    "programming",
    "developer",
    "web development",
    "backend",
    "frontend",
    "fullstack",
    "networks",
    "network",
    "علوم حاسوب",
    "علم حاسوب",
    "هندسة برمجيات",
    "برمجيات",
    "برمجة",
    "ذكاء اصطناعي",
    "امن سيبراني",
    "أمن سيبراني",
    "امن معلومات",
    "أمن معلومات",
    "تكنولوجيا المعلومات",
    "تقنية معلومات",
    "شبكات",
    "علم البيانات",
    "علوم البيانات"
  ];

  foreach ($it_keywords as $kw) {
    $kw = norm_major($kw);
    if ($kw !== "" && (mb_strpos($m, $kw) !== false || mb_strpos($kw, $m) !== false)) {
      return "it";
    }
  }

  $business_keywords = [
    "business",
    "marketing",
    "finance",
    "accounting",
    "management",
    "ادارة",
    "إدارة",
    "تسويق",
    "محاسبة",
    "مالية",
    "اعمال",
    "أعمال"
  ];

  foreach ($business_keywords as $kw) {
    $kw = norm_major($kw);
    if ($kw !== "" && (mb_strpos($m, $kw) !== false || mb_strpos($kw, $m) !== false)) {
      return "business";
    }
  }

  $design_keywords = [
    "design",
    "ui",
    "ux",
    "graphic design",
    "interior design",
    "تصميم",
    "جرافيك",
    "ui ux"
  ];

  foreach ($design_keywords as $kw) {
    $kw = norm_major($kw);
    if ($kw !== "" && (mb_strpos($m, $kw) !== false || mb_strpos($kw, $m) !== false)) {
      return "design";
    }
  }

  return $m; // fallback
}

/**
 * Read student major from best available source
 */
function get_student_major(mysqli $conn, int $user_id): string {
  $student_major = "";

  $q1 = $conn->prepare("SELECT major_text FROM student_profiles WHERE user_id=? LIMIT 1");
  if ($q1) {
    $q1->bind_param("i", $user_id);
    $q1->execute();
    $r1 = $q1->get_result()->fetch_assoc();
    $q1->close();
    $student_major = trim((string)($r1["major_text"] ?? ""));
  }

  if ($student_major === "") {
    $q2 = $conn->prepare("SELECT major_text FROM user_plan_profile WHERE user_id=? LIMIT 1");
    if ($q2) {
      $q2->bind_param("i", $user_id);
      $q2->execute();
      $r2 = $q2->get_result()->fetch_assoc();
      $q2->close();
      $student_major = trim((string)($r2["major_text"] ?? ""));
    }
  }

  if ($student_major === "") {
    $q3 = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
    if ($q3) {
      $q3->bind_param("i", $user_id);
      $q3->execute();
      $r3 = $q3->get_result()->fetch_assoc();
      $q3->close();
      $student_major = trim((string)($r3["major_text"] ?? ""));
    }
  }

  return $student_major;
}

// ✅ get student major
$student_major = get_student_major($conn, $user_id);

$st = $conn->prepare("
  SELECT
    v.id,
    v.title,
    v.stored_path,
    v.duration_seconds,
    v.created_at,
    v.playlist_id,
    p.name AS playlist_name,
    p.coin_pool AS coin_pool,
    u.full_name AS partner_name,
    p.major_text AS playlist_major
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  JOIN users u ON u.id = v.partner_user_id
  WHERE v.id = ?
    AND p.is_published = 1
  LIMIT 1
");
if (!$st) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "SQL_PREPARE_FAILED",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$st->bind_param("i", $video_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();

if (!$row) {
  http_response_code(403);
  echo json_encode(["ok" => false, "error" => "FORBIDDEN_VIDEO"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ major-group-based permission
$plMajorRaw = trim((string)($row["playlist_major"] ?? ""));
$stMajorRaw = trim((string)$student_major);

$playlist_group = major_group($plMajorRaw);
$student_group  = major_group($stMajorRaw);

// اسمح إذا واحد منهم فاضي
if ($playlist_group !== "" && $student_group !== "") {
  if ($playlist_group !== $student_group) {
    http_response_code(403);
    echo json_encode([
      "ok" => false,
      "error" => "FORBIDDEN_MAJOR_MISMATCH",
      "student_major" => $stMajorRaw,
      "playlist_major" => $plMajorRaw,
      "student_group" => $student_group,
      "playlist_group" => $playlist_group
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }
}

// آخر نسخة أسئلة
$q = $conn->prepare("
  SELECT quiz_json
  FROM partner_video_quizzes
  WHERE partner_video_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$quiz = [];
if ($q) {
  $q->bind_param("i", $video_id);
  $q->execute();
  $qrow = $q->get_result()->fetch_assoc();
  $q->close();

  if ($qrow && isset($qrow["quiz_json"])) {
    $quiz = json_decode((string)$qrow["quiz_json"], true);
    if (!is_array($quiz)) $quiz = [];
  }
}

// آخر Code Problem (إن وجد)
$c = $conn->prepare("
  SELECT id, title, prompt, language, starter_code, max_coin
  FROM partner_video_code_problems
  WHERE partner_video_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$crow = null;
if ($c) {
  $c->bind_param("i", $video_id);
  $c->execute();
  $crow = $c->get_result()->fetch_assoc();
  $c->close();
}

// ==============================
// build correct video URL
// ==============================
$sp = ltrim((string)($row["stored_path"] ?? ""), "/");

// إذا المخزّن مجرد اسم ملف (ما فيه /) جرّب تحطه داخل مجلدات شائعة
if ($sp !== "" && strpos($sp, "/") === false) {
  $cands = [
    "uploads/",
    "partner_uploads/",
    "uploads/partner_videos/",
    "partner_videos/",
  ];

  foreach ($cands as $dir) {
    
    $fs = __DIR__ . "/../" . $dir . $sp;
    if (file_exists($fs)) {
      $sp = $dir . $sp;
      break;
    }
  }
}

// رابط الفيديو النهائي للويب
$video_url = "/utbn-backend/" . ltrim($sp, "/");

// ==============================
// Response
// ==============================
$out_video = [
  "id" => (int)$row["id"],
  "title" => (string)$row["title"],
  "video_url" => $video_url,
  "duration_seconds" => (int)$row["duration_seconds"],
  "created_at" => (string)$row["created_at"],
  "playlist_id" => (int)$row["playlist_id"],
  "playlist_name" => (string)$row["playlist_name"],
  "partner_name" => (string)$row["partner_name"],
  "coin_pool" => (int)$row["coin_pool"]
];

echo json_encode([
  "ok" => true,

  // حتى partner_video.php يظل يشتغل
  "video_title"   => $out_video["title"],
  "partner_name"  => $out_video["partner_name"],
  "playlist_name" => $out_video["playlist_name"],
  "video_url"     => $out_video["video_url"],

  // object جديد
  "video" => $out_video,

  "quiz" => $quiz,
  "code_problem" => $crow ? [
    "id" => (int)$crow["id"],
    "title" => (string)$crow["title"],
    "prompt" => (string)$crow["prompt"],
    "language" => (string)$crow["language"],
    "starter_code" => (string)$crow["starter_code"],
    "max_coin" => (int)$crow["max_coin"]
  ] : null
], JSON_UNESCAPED_UNICODE);
