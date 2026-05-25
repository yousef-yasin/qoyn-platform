<?php
// utbn-backend/api/partner_video_preview.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) {
  json_out(["ok"=>false,"error"=>"INVALID_VIDEO_ID"], 400);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

// video (must be owned)
$st = $conn->prepare("
  SELECT id, playlist_id, partner_user_id, title, stored_path, duration_seconds, created_at
  FROM partner_videos
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
$st->bind_param("ii", $video_id, $user_id);
$st->execute();
$video = $st->get_result()->fetch_assoc();
$st->close();

if (!$video) {
  json_out(["ok"=>false,"error"=>"VIDEO_NOT_OWNED"], 403);
}

// last quiz
$q = $conn->prepare("
  SELECT id, quiz_json, created_at
  FROM partner_video_quizzes
  WHERE partner_video_id=? AND partner_user_id=?
  ORDER BY id DESC
  LIMIT 1
");
$q->bind_param("ii", $video_id, $user_id);
$q->execute();
$qrow = $q->get_result()->fetch_assoc();
$q->close();

$quiz = null;
if ($qrow && isset($qrow["quiz_json"])) {
  $tmp = json_decode((string)$qrow["quiz_json"], true);
  $quiz = is_array($tmp) ? $tmp : [];
}

// last code problem
$c = $conn->prepare("
  SELECT id, title, prompt, language, starter_code, solution_code, max_coin, created_at
  FROM partner_video_code_problems
  WHERE partner_video_id=? AND partner_user_id=?
  ORDER BY id DESC
  LIMIT 1
");
$c->bind_param("ii", $video_id, $user_id);
$c->execute();
$code = $c->get_result()->fetch_assoc();
$c->close();

json_out([
  "ok"=>true,
  "video"=>[
    "id" => (int)$video["id"],
    "playlist_id" => (int)$video["playlist_id"],
    "title" => (string)$video["title"],
    "stored_path" => (string)$video["stored_path"],
    "duration_seconds" => (int)$video["duration_seconds"],
    "created_at" => (string)$video["created_at"]
  ],
  "quiz" => $quiz,
  "code_problem" => $code ? [
    "id" => (int)$code["id"],
    "title" => (string)$code["title"],
    "prompt" => (string)$code["prompt"],
    "language" => (string)$code["language"],
    "starter_code" => (string)$code["starter_code"],
    "solution_code" => (string)$code["solution_code"],
    "max_coin" => (int)$code["max_coin"],
    "created_at" => (string)$code["created_at"]
  ] : null
]);
