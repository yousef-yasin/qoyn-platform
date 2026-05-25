<?php
// utbn-backend/api/partner_video_quiz_get.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["error"=>"DB_FILE_NOT_FOUND","tried"=>$try], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) { http_response_code(400); echo json_encode(["error"=>"MISSING_VIDEO_ID"], JSON_UNESCAPED_UNICODE); exit; }

$q = $conn->prepare("
  SELECT id, quiz_json
  FROM partner_video_quizzes
  WHERE partner_video_id=?
  ORDER BY id DESC
  LIMIT 1
");
$q->bind_param("i", $video_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();

if (!$row) {
  echo json_encode(["ok"=>true, "quiz"=>null], JSON_UNESCAPED_UNICODE);
  exit;
}

$quiz = json_decode($row["quiz_json"], true);
echo json_encode(["ok"=>true, "quiz"=>$quiz], JSON_UNESCAPED_UNICODE);
