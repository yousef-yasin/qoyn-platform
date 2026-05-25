<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php", __DIR__."/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["error"=>"DB_FILE_NOT_FOUND","tried"=>$try], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;
require_once __DIR__ . "/_ensure_tracking_tables.php";
ensure_tracking_tables($conn);

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["error"=>"METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE); exit; }

$user_id = (int)$_SESSION["user_id"];
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) { http_response_code(400); echo json_encode(["error"=>"INVALID_JSON"], JSON_UNESCAPED_UNICODE); exit; }

$video_id = (int)($data["video_id"] ?? 0);
$watched_seconds = (int)($data["watched_seconds"] ?? 0);
$completed = (int)($data["completed"] ?? 0);
$duration_seconds_in = (int)($data["duration_seconds"] ?? 0);
if ($duration_seconds_in < 0) $duration_seconds_in = 0;

if (!$video_id) { http_response_code(400); echo json_encode(["error"=>"MISSING_VIDEO_ID"], JSON_UNESCAPED_UNICODE); exit; }
if ($watched_seconds < 0) $watched_seconds = 0;
if ($completed !== 1) $completed = 0;

$stmt = $conn->prepare("
  INSERT INTO student_video_progress (user_id, video_id, watched_seconds, completed)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    watched_seconds = GREATEST(watched_seconds, VALUES(watched_seconds)),
    completed = GREATEST(completed, VALUES(completed))
");
$stmt->bind_param("iiii", $user_id, $video_id, $watched_seconds, $completed);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["error"=>"DB_SAVE_FAILED","details"=>$stmt->error], JSON_UNESCAPED_UNICODE);
  exit;
}
// ===== ADD-ON TRACKING (no change to existing behavior) =====

// جلب مدة الفيديو من partner_videos عشان نحسب النسبة
$duration_seconds = 0;
$vd = $conn->prepare("SELECT duration_seconds FROM partner_videos WHERE id=? LIMIT 1");
// جلب مدة الفيديو: خذها من الـ client أولاً، وإذا مش موجودة ارجع لـ DB
$duration_seconds = $duration_seconds_in;

if ($duration_seconds <= 0) {
  $vd = $conn->prepare("SELECT duration_seconds FROM partner_videos WHERE id=? LIMIT 1");
  if ($vd) {
    $vd->bind_param("i", $video_id);
    if ($vd->execute()) {
      $rr = $vd->get_result()->fetch_assoc();
      $duration_seconds = (int)($rr["duration_seconds"] ?? 0);
    }
    $vd->close();
  }
}

  $vd->close();


$watched_percent = 0.0;
if ($duration_seconds > 0) {
  $watched_percent = $watched_seconds / $duration_seconds;
  if ($watched_percent < 0) $watched_percent = 0.0;
  if ($watched_percent > 1) $watched_percent = 1.0;
}

// سجل event في user_behavior
$meta = json_encode([
  "watched_seconds" => $watched_seconds,
  "duration_seconds" => $duration_seconds,
  "completed" => $completed
], JSON_UNESCAPED_UNICODE);

$ub = $conn->prepare("
  INSERT INTO user_behavior (user_id, event_type, video_id, value_int, value_float, meta_json)
  VALUES (?, 'watch_progress', ?, ?, ?, ?)
");
if ($ub) {
  $vid_str = (string)$video_id;
  $valFloat = (double)$watched_percent;
  $ub->bind_param("isids", $user_id, $vid_str, $watched_seconds, $valFloat, $meta);
  @$ub->execute();
  $ub->close();
}

/* ==========================================================
   ✅ ADDITION (extra only): keep a performance snapshot too
   - This does NOT affect coins / quizzes / existing tables
   - It just ensures student_performance has watched_percent
   ========================================================== */
$vid_str = (string)$video_id;

// إذا ما في سجل أداء لهذا الفيديو للمستخدم، بننشئ واحد "مشاهدة فقط" (score=0)
$exists = 0;
$chk = $conn->prepare("SELECT id FROM student_performance WHERE user_id=? AND video_id=? ORDER BY id DESC LIMIT 1");
if ($chk) {
  $chk->bind_param("is", $user_id, $vid_str);
  if ($chk->execute()) {
    $rowx = $chk->get_result()->fetch_assoc();
    $exists = $rowx ? 1 : 0;
  }
  $chk->close();
}

if (!$exists) {
  // create a baseline row (watch only)
  $meta_watch = json_encode([
    "source" => "student_video_progress_save",
    "kind" => "watch_only"
  ], JSON_UNESCAPED_UNICODE);

  $sp0 = $conn->prepare("
    INSERT INTO student_performance
    (user_id, video_id, quiz_type, attempt_no, score, total, score_percent, time_spent_seconds, watched_percent, difficulty, meta_json)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
  ");
  if ($sp0) {
    $qt = "quick";
    $attempt_no = 0;
    $score = 0;
    $total = 0;
    $score_percent = 0;
    $time_spent_seconds = 0;
    $difficulty = 1;
    $sp0->bind_param(
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
      $meta_watch
    );
    @$sp0->execute();
    $sp0->close();
  }
} else {
  // update last row watched_percent snapshot
  $upd = $conn->prepare("
    UPDATE student_performance
    SET watched_percent = GREATEST(watched_percent, ?)
    WHERE user_id=? AND video_id=?
  ");
  if ($upd) {
    $upd->bind_param("dis", $watched_percent, $user_id, $vid_str);
    @$upd->execute();
    $upd->close();
  }
}

/* ==========================================================
   ✅ ADDITION (extra only): optional 'watch_completed' event
   ========================================================== */
if ($completed === 1) {
  $ub2 = $conn->prepare("
    INSERT INTO user_behavior (user_id, event_type, video_id, value_int, value_float, meta_json)
    VALUES (?, 'watch_completed', ?, ?, ?, ?)
  ");
  if ($ub2) {
    $vf = (double)$watched_percent;
    $ub2->bind_param("isids", $user_id, $vid_str, $watched_seconds, $vf, $meta);
    @$ub2->execute();
    $ub2->close();
  }
}

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);
