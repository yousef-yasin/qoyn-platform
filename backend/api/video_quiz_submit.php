<?php
// utbn-backend/api/video_quiz_submit.php
// Save quiz attempts from playlist.php (YouTube quizzes) and return score/total.
// Also writes a row into student_performance.

require __DIR__ . "/db.php";
require_login();
require __DIR__ . "/_ensure_tracking_tables.php";
ensure_tracking_tables($conn);

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$d = json_decode(file_get_contents("php://input"), true) ?: [];

// Accept YouTube id from videoId / video_id
$video_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($d["videoId"] ?? $d["video_id"] ?? ""));
$youtube_id = $video_id;

$type    = (($d["type"] ?? "quick") === "deep") ? "deep" : "quick";
$quiz    = $d["quiz"] ?? null;
$answers = $d["answers"] ?? null;

if ($youtube_id === "") {
  json_out(["ok" => false, "error" => "MISSING_VIDEO_ID"], 400);
}


// ===== Score (MCQ + True/False only) =====
$score = 0;
$total = 0;

if (is_array($quiz) && is_array($answers)) {
  // MCQ
  $mcqQ = $quiz["mcq"] ?? [];
  $mcqA = $answers["mcq"] ?? [];
  if (is_array($mcqQ) && is_array($mcqA)) {
    foreach ($mcqQ as $i => $q) {
      if (!is_array($q)) continue;
      $ansIdx = isset($q["answerIndex"]) ? (int)$q["answerIndex"] : null;
      $picked = array_key_exists($i, $mcqA) ? $mcqA[$i] : null;
      $total++;
      if ($picked !== null && $ansIdx !== null && (int)$picked === (int)$ansIdx) $score++;
    }
  }

  // True/False
  $tfQ = $quiz["trueFalse"] ?? [];
  $tfA = $answers["trueFalse"] ?? [];
  if (is_array($tfQ) && is_array($tfA)) {
    foreach ($tfQ as $i => $q) {
      if (!is_array($q)) continue;
      $ans = array_key_exists("answer", $q) ? (bool)$q["answer"] : null;
      $picked = array_key_exists($i, $tfA) ? $tfA[$i] : null;
      $total++;
      if ($picked !== null && $ans !== null && (bool)$picked === (bool)$ans) $score++;
    }
  }
}

// Backward-compat
if ($total <= 0 && isset($d["total"])) {
  $total = (int)($d["total"] ?? 0);
  $score = (int)($d["score"] ?? 0);
}

$quiz_json    = is_array($quiz) ? json_encode($quiz, JSON_UNESCAPED_UNICODE) : null;
$answers_json = is_array($answers) ? json_encode($answers, JSON_UNESCAPED_UNICODE) : null;

// ===== Ensure attempts table (YouTube friendly) =====
$conn->query("
  CREATE TABLE IF NOT EXISTS video_quiz_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    youtube_id VARCHAR(32) NOT NULL,
    quiz_type ENUM('quick','deep') NOT NULL DEFAULT 'quick',
    score INT NOT NULL DEFAULT 0,
    total INT NOT NULL DEFAULT 0,
    quiz_json MEDIUMTEXT NULL,
    answers_json MEDIUMTEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_video_type (user_id, youtube_id, quiz_type),
    INDEX idx_user (user_id),
    INDEX idx_video (youtube_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// detect columns (support old schemas)
$has_youtube = false;
$has_videoid = false;
$type_col = null;      // quiz_type OR type
$quiz_col = null;      // quiz_json OR quiz
$answers_col = null;   // answers_json OR answers

if ($res = $conn->query("SHOW COLUMNS FROM video_quiz_attempts")) {
  while ($col = $res->fetch_assoc()) {
    $name = strtolower($col["Field"] ?? "");
    if ($name === "youtube_id") $has_youtube = true;
    if ($name === "video_id")   $has_videoid = true;
    if ($name === "quiz_type" || $name === "type") $type_col = $name;
    if ($name === "quiz_json" || $name === "quiz") $quiz_col = $name;
    if ($name === "answers_json" || $name === "answers") $answers_col = $name;
  }
  $res->free();
}

$tc = $type_col ?: "quiz_type";
$qc = $quiz_col ?: "quiz_json";
$ac = $answers_col ?: "answers_json";

$saved = false;

// Prefer youtube schema
if ($has_youtube) {
  $sql = "INSERT INTO video_quiz_attempts (user_id, youtube_id, {$tc}, score, total, {$qc}, {$ac})
          VALUES (?,?,?,?,?,?,?)
          ON DUPLICATE KEY UPDATE
            score=VALUES(score),
            total=VALUES(total),
            {$qc}=VALUES({$qc}),
            {$ac}=VALUES({$ac}),
            updated_at=NOW()";
  $stmt = $conn->prepare($sql);
  if ($stmt) {
    // i s s i i s s  => "issiiss"
    $stmt->bind_param("issiiss", $user_id, $youtube_id, $type, $score, $total, $quiz_json, $answers_json);
    $saved = (bool)$stmt->execute();
    $stmt->close();
  }
} elseif ($has_videoid) {
  // legacy (int video_id) - try insert but usually not used with YouTube
  $vid_int = (int)$youtube_id;
  $sql = "INSERT INTO video_quiz_attempts (user_id, video_id, {$tc}, score, total, {$qc}, {$ac})
          VALUES (?,?,?,?,?,?,?)
          ON DUPLICATE KEY UPDATE
            score=VALUES(score),
            total=VALUES(total),
            {$qc}=VALUES({$qc}),
            {$ac}=VALUES({$ac}),
            updated_at=NOW()";

  $stmt = $conn->prepare($sql);
  if ($stmt) {
    $stmt->bind_param("iisiiss", $user_id, $vid_int, $type, $score, $total, $quiz_json, $answers_json);
    $saved = (bool)$stmt->execute();
    $stmt->close();
  }
}

// ===== Write into student_performance ALWAYS =====
$score_percent = 0;
if ($total > 0) $score_percent = (int)round(($score / $total) * 100);

$difficulty = ($type === "deep") ? 4 : 3;

// watched_percent: take max from user_behavior watch_progress
$watched_percent = 0.0;
$qw = $conn->prepare("
  SELECT COALESCE(MAX(value_float),0) AS w
  FROM user_behavior
  WHERE user_id=? AND video_id=? AND event_type='watch_progress'
");
if ($qw) {
  $qw->bind_param("is", $user_id, $youtube_id);
  if ($qw->execute()) {
    $rw = $qw->get_result()->fetch_assoc();
    $watched_percent = (float)($rw["w"] ?? 0);
  }
  $qw->close();
}
if ($watched_percent < 0) $watched_percent = 0;
if ($watched_percent > 1) $watched_percent = 1;

// time_spent_seconds (optional)
$time_spent_seconds = (int)($d["time_spent_seconds"] ?? 0);
if ($time_spent_seconds < 0) $time_spent_seconds = 0;

// attempt_no
$attempt_no = 1;
$cnt = $conn->prepare("SELECT COUNT(*) AS c FROM student_performance WHERE user_id=? AND video_id=? AND quiz_type=?");
if ($cnt) {
  $cnt->bind_param("iss", $user_id, $youtube_id, $type);
  if ($cnt->execute()) {
    $rowc = $cnt->get_result()->fetch_assoc();
    $attempt_no = ((int)($rowc["c"] ?? 0)) + 1;
  }
  $cnt->close();
}

$meta_json = json_encode([
  "source" => "video_quiz_submit",
  "quiz_type" => $type
], JSON_UNESCAPED_UNICODE);

$sp = $conn->prepare("
  INSERT INTO student_performance
  (user_id, video_id, quiz_type, attempt_no, score, total, score_percent, time_spent_seconds, watched_percent, difficulty, meta_json)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)
");
$sp_ok = false;
if ($sp) {
  $sp->bind_param(
    "issiiiiddis",
    $user_id,
    $youtube_id,
    $type,
    $attempt_no,
    $score,
    $total,
    $score_percent,
    $time_spent_seconds,
    $watched_percent,
    $difficulty,
    $meta_json
  );
  $sp_ok = (bool)@$sp->execute();
  $sp->close();
}

// Also log behavior event quiz_submit
$ub = $conn->prepare("
  INSERT INTO user_behavior (user_id, event_type, video_id, value_int, value_float, meta_json)
  VALUES (?, 'quiz_submit', ?, ?, ?, ?)
");
if ($ub) {
  $avgScoreFloat = (double)$score_percent;
  $ub->bind_param("isids", $user_id, $youtube_id, $score_percent, $avgScoreFloat, $meta_json);
  @$ub->execute();
  $ub->close();
}

json_out([
  "ok" => true,
  "video_id" => $youtube_id,
  "score" => $score,
  "total" => $total,
  "saved" => $saved,
  "performance_saved" => $sp_ok,
  "watched_percent" => $watched_percent
]);
