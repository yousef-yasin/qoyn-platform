<?php
// utbn-backend/api/partner_video_submit.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

require_once __DIR__ . "/_ensure_tracking_tables.php";
ensure_tracking_tables($conn);

// ensure publish columns exist (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");

// ✅ NEW: ensure major_text exists (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"],JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"],JSON_UNESCAPED_UNICODE); exit; }

$user_id = (int)$_SESSION["user_id"];
$in = json_decode(file_get_contents("php://input"), true);
if(!is_array($in)){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"INVALID_JSON"],JSON_UNESCAPED_UNICODE); exit; }

$video_id = (int)($in["video_id"] ?? 0);
$answers  = $in["answers"] ?? null; // array

// optional tracking
$time_spent_seconds = (int)($in["time_spent_seconds"] ?? 0);
if ($time_spent_seconds < 0) $time_spent_seconds = 0;

if($video_id<=0 || !is_array($answers)){
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_FIELDS"],JSON_UNESCAPED_UNICODE);
  exit;
}

// table to store last submission per video/student
$conn->query("CREATE TABLE IF NOT EXISTS partner_video_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  partner_video_id INT UNSIGNED NOT NULL,
  student_user_id INT UNSIGNED NOT NULL,
  answers_json MEDIUMTEXT NOT NULL,
  score INT NOT NULL DEFAULT 0,
  total INT NOT NULL DEFAULT 0,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_video_student (partner_video_id, student_user_id),
  KEY idx_video (partner_video_id),
  KEY idx_student (student_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

/* =========================================================
   ✅ NEW: Permission by Major (مع fallback للقديم)
   - إذا playlist.major_text موجود -> لازم يطابق users.major_text
   - إذا playlist.major_text فاضي -> نفذ استعلامك القديم (مواد الخطة)
   ========================================================= */

// ✅ get student major
$student_major = "";
$m = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
$m->bind_param("i", $user_id);
$m->execute();
$mr = $m->get_result()->fetch_assoc();
$m->close();
$student_major = trim((string)($mr["major_text"] ?? ""));

// ✅ get playlist major + allow basic info
$pv = $conn->prepare("
  SELECT v.id, v.playlist_id, p.coin_pool, p.major_text AS playlist_major
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  WHERE v.id = ?
    AND p.is_published = 1
  LIMIT 1
");
$pv->bind_param("i", $video_id);
$pv->execute();
$base = $pv->get_result()->fetch_assoc();
$pv->close();

if(!$base){
  http_response_code(403);
  echo json_encode(["ok"=>false,"error"=>"FORBIDDEN_VIDEO"],JSON_UNESCAPED_UNICODE);
  exit;
}

$playlist_id = (int)$base["playlist_id"];
$coin_pool   = (int)$base["coin_pool"];
$playlist_major = trim((string)($base["playlist_major"] ?? ""));

// ✅ If playlist has major -> enforce major match
if ($playlist_major !== "" && $student_major !== "") {
  if (mb_strtolower($playlist_major, "UTF-8") !== mb_strtolower($student_major, "UTF-8")) {
    http_response_code(403);
    echo json_encode([
      "ok"=>false,
      "error"=>"FORBIDDEN_MAJOR_MISMATCH",
      "playlist_major"=>$playlist_major,
      "student_major"=>$student_major
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }
  // major matches -> allowed
} else if ($playlist_major === "") {
  // ✅ Fallback: استعلامك القديم (مواد الخطة required)
  $chk = $conn->prepare("
    SELECT v.id, v.playlist_id, p.coin_pool
    FROM partner_videos v
    JOIN partner_playlists p ON p.id = v.playlist_id
    JOIN user_plan_courses upc
      ON upc.user_id = ?
     AND upc.is_required = 1
     AND (
          LOWER(p.name) = LOWER(upc.course_name)
          OR LOWER(p.name) LIKE CONCAT('%', LOWER(upc.course_name), '%')
          OR LOWER(upc.course_name) LIKE CONCAT('%', LOWER(p.name), '%')
     )
    WHERE v.id = ?
      AND p.is_published = 1
    LIMIT 1
  ");
  $chk->bind_param("ii", $user_id, $video_id);
  $chk->execute();
  $allow = $chk->get_result()->fetch_assoc();
  $chk->close();

  if(!$allow){
    http_response_code(403);
    echo json_encode(["ok"=>false,"error"=>"FORBIDDEN_VIDEO"],JSON_UNESCAPED_UNICODE);
    exit;
  }

  // (safe re-assign)
  $playlist_id = (int)$allow["playlist_id"];
  $coin_pool   = (int)$allow["coin_pool"];
}

// videos_count inside playlist (for coins split)
$vc = 0;
$cnt = $conn->prepare("SELECT COUNT(*) AS c FROM partner_videos WHERE playlist_id=?");
$cnt->bind_param("i", $playlist_id);
$cnt->execute();
$vcRow = $cnt->get_result()->fetch_assoc();
$cnt->close();
$vc = (int)($vcRow["c"] ?? 0);

// ✅ جلب آخر Quiz للفيديو وحساب score على السيرفر (MCQ + Circle)
$q = $conn->prepare("
  SELECT quiz_json
  FROM partner_video_quizzes
  WHERE partner_video_id=?
  ORDER BY id DESC
  LIMIT 1
");
$q->bind_param("i",$video_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

$quiz = $row ? json_decode($row["quiz_json"], true) : null;

function answer_to_index($a) {
  if (is_bool($a)) return $a ? 0 : 1; // circle bool
  if (is_string($a)) {
    $s = strtoupper(trim($a));
    $map = ["A"=>0,"B"=>1,"C"=>2,"D"=>3];
    if (isset($map[$s])) return $map[$s];
    if (is_numeric($s)) return (int)$s;
  }
  if (is_int($a) || is_float($a)) return (int)$a;
  return -1;
}

$score = 0; $total = 0;
$detail = [];

if (is_array($quiz)) {
  $total = count($quiz);
  for($i=0;$i<$total;$i++){
    $qq = $quiz[$i] ?? [];
    $options = $qq["options"] ?? [];
    if (!is_array($options)) $options = [];

    $optCount = count($options);

    // correct index
    $correctIndex = 0;
    if ($optCount === 2) {
      if (isset($qq["correct_bool"]) && is_bool($qq["correct_bool"])) {
        $correctIndex = $qq["correct_bool"] ? 0 : 1;
      } else {
        $correctLetter = strtoupper(trim((string)($qq["correct"] ?? "A"))); // A/B
        $correctIndex = ($correctLetter === "B") ? 1 : 0;
      }
    } else { // default MCQ 4
      $correctLetter = strtoupper(trim((string)($qq["correct"] ?? "A"))); // A/B/C/D
      $map = ["A"=>0,"B"=>1,"C"=>2,"D"=>3];
      $correctIndex = $map[$correctLetter] ?? 0;
    }

    $chosen = answer_to_index($answers[$i] ?? -1);
    $isCorrect = ($chosen === $correctIndex);
    if($isCorrect) $score++;

    $detail[] = [
      "q" => (string)($qq["question"] ?? ""),
      "chosen" => $chosen,
      "correct" => $correctIndex
    ];
  }
}

// حفظ (آخر محاولة فقط لكل طالب/فيديو)
$answers_json = json_encode([
  "answers" => array_values($answers),
  "detail" => $detail
], JSON_UNESCAPED_UNICODE);

$ins = $conn->prepare("
  INSERT INTO partner_video_submissions (partner_video_id, student_user_id, answers_json, score, total, submitted_at)
  VALUES (?, ?, ?, ?, ?, NOW())
  ON DUPLICATE KEY UPDATE
    answers_json = VALUES(answers_json),
    score = VALUES(score),
    total = VALUES(total),
    submitted_at = NOW()
");
$ins->bind_param("iisii", $video_id, $user_id, $answers_json, $score, $total);
if(!$ins->execute()){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_SAVE_FAILED","details"=>$ins->error],JSON_UNESCAPED_UNICODE);
  exit;
}
$ins->close();

// ===== Coins (حسب نسبة الصح) =====
$score_percent = ($total > 0) ? (int)round(($score / $total) * 100) : 0;
$coins_per_video = 0;
$coins_awarded = 0;

if ($coin_pool > 0 && $vc > 0) {
  $coins_per_video = (int)floor($coin_pool / $vc);
  if ($coins_per_video < 0) $coins_per_video = 0;

  if ($total > 0) {
    $coins_awarded = (int)round($coins_per_video * ($score / $total));
    if ($coins_awarded < 0) $coins_awarded = 0;
    if ($coins_awarded > $coins_per_video) $coins_awarded = $coins_per_video;
  }
}

// save reward
// ملاحظة مهمة:
// جدول video_rewards عندك (حسب utbn_db.sql) أعمدته: base_coin, quiz_coin, total_coin ...
// عشان هيك نخزّن مكافأة الـ Partner هناك (بدون أعمدة coins_awarded القديمة).

$vid_str = "p_".(string)$video_id; // تمييز فيديوهات الـ Partner

// إذا كان الطالب أخذ مكافأة قبل، لا نعيد إعطاءها
$ex = $conn->prepare("SELECT total_coin FROM video_rewards WHERE user_id=? AND video_id=? LIMIT 1");
$already = false;
if ($ex) {
  $ex->bind_param("is", $user_id, $vid_str);
  $ex->execute();
  $er = $ex->get_result()->fetch_assoc();
  $ex->close();
  if ($er) $already = true;
}

if (!$already) {
  $base_coin = 0;
  $quiz_coin = $coins_awarded;
  $total_coin = $base_coin + $quiz_coin;

  $vrw = $conn->prepare("\
    INSERT INTO video_rewards (user_id, video_id, base_coin, quiz_coin, total_coin, rewarded_at, youtube_id)
    VALUES (?, ?, ?, ?, ?, NOW(), ?)
  ");
  if ($vrw) {
    $vrw->bind_param("isiiis", $user_id, $vid_str, $base_coin, $quiz_coin, $total_coin, $vid_str);
    @$vrw->execute();
    $vrw->close();
  }

  // update student_profiles coins_total (نفس منطق video_reward_claim)
  $hasSP = $conn->query("SHOW TABLES LIKE 'student_profiles'");
  if ($hasSP && $hasSP->num_rows > 0) {
    $stSp = $conn->prepare("\
      INSERT INTO student_profiles (user_id, major_id, level, coins_total)
      VALUES (?, 1, 1, ?)
      ON DUPLICATE KEY UPDATE coins_total = coins_total + VALUES(coins_total)
    ");
    if ($stSp) {
      $stSp->bind_param("ii", $user_id, $total_coin);
      @$stSp->execute();
      $stSp->close();
    }
  }
}
$stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
if ($stu) {
  $stu->bind_param("ii", $total_coin, $user_id);
  $stu->execute();
  $stu->close();
}
// ===== ADD-ON TRACKING =====

// attempt_no from student_performance
$attempt_no = 1;
$cnt2 = $conn->prepare("SELECT COUNT(*) AS c FROM student_performance WHERE user_id=? AND video_id=?");
if ($cnt2) {
  $cnt2->bind_param("is", $user_id, $vid_str);
  if ($cnt2->execute()) {
    $rowc = $cnt2->get_result()->fetch_assoc();
    $attempt_no = ((int)($rowc["c"] ?? 0)) + 1;
  }
  $cnt2->close();
}

// watched_percent from student_video_progress
$watched_percent = 0.0;
$dur = 0;

$vs = $conn->prepare("SELECT watched_seconds FROM student_video_progress WHERE user_id=? AND video_id=? LIMIT 1");
if ($vs) {
  $vs->bind_param("ii", $user_id, $video_id);
  if ($vs->execute()) {
    $rr = $vs->get_result()->fetch_assoc();
    $ws = (int)($rr["watched_seconds"] ?? 0);

    $vd = $conn->prepare("SELECT duration_seconds FROM partner_videos WHERE id=? LIMIT 1");
    if ($vd) {
      $vd->bind_param("i", $video_id);
      if ($vd->execute()) {
        $r2 = $vd->get_result()->fetch_assoc();
        $dur = (int)($r2["duration_seconds"] ?? 0);
      }
      $vd->close();
    }

    if ($dur > 0) {
      $watched_percent = $ws / $dur;
      if ($watched_percent < 0) $watched_percent = 0.0;
      if ($watched_percent > 1) $watched_percent = 1.0;
    }
  }
  $vs->close();
}

$difficulty = 3;

$meta_json = json_encode([
  "source" => "partner_video_submit",
  "answers_count" => count($answers),
  "detail" => $detail,
  "coin_pool" => $coin_pool,
  "coins_per_video" => $coins_per_video,
  "coins_awarded" => $coins_awarded
], JSON_UNESCAPED_UNICODE);

// insert attempt in student_performance
$sp = $conn->prepare("
  INSERT INTO student_performance
  (user_id, video_id, quiz_type, attempt_no, score, total, score_percent, time_spent_seconds, watched_percent, difficulty, meta_json)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)
");
if ($sp) {
  $qt = "quick";
  $sp->bind_param(
    "issiiiiddis",
    $user_id,
    $vid_str,
    $qt,
    $attempt_no,
    $score,
    $total,
    $score_percent,
    $time_spent_seconds,
    $watched_percent,
    $difficulty,
    $meta_json
  );
  @$sp->execute();
  $sp->close();
}

// behavior
$ub = $conn->prepare("
  INSERT INTO user_behavior (user_id, event_type, video_id, value_int, value_float, meta_json)
  VALUES (?, 'quiz_submit', ?, ?, ?, ?)
");
if ($ub) {
  $vf = (double)$score_percent;
  $ub->bind_param("isids", $user_id, $vid_str, $score_percent, $vf, $meta_json);
  @$ub->execute();
  $ub->close();
}

echo json_encode([
  "ok"=>true,
  "score"=>$score,
  "total"=>$total,
  "score_percent"=>$score_percent,
  "coin_pool"=>$coin_pool,
  "coins_per_video"=>$coins_per_video,
  "coins_awarded"=>$coins_awarded
],JSON_UNESCAPED_UNICODE);
