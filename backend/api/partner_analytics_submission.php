<?php
// utbn-backend/api/partner_analytics_submission.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"],JSON_UNESCAPED_UNICODE); exit; }
if (($_SESSION["role"] ?? "") !== "partner") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"NOT_PARTNER"],JSON_UNESCAPED_UNICODE); exit; }

$partner_id = (int)$_SESSION["user_id"];
$video_id = (int)($_GET["video_id"] ?? 0);
$student_id = (int)($_GET["student_id"] ?? 0);
if($video_id<=0 || $student_id<=0){
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_FIELDS"],JSON_UNESCAPED_UNICODE);
  exit;
}

// ملكية الفيديو
$chk = $conn->prepare("SELECT id,title FROM partner_videos WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii",$video_id,$partner_id);
$chk->execute();
$own = $chk->get_result()->fetch_assoc();
if(!$own){ http_response_code(403); echo json_encode(["ok"=>false,"error"=>"VIDEO_NOT_OWNED"],JSON_UNESCAPED_UNICODE); exit; }

$conn->query("CREATE TABLE IF NOT EXISTS partner_video_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  partner_video_id INT UNSIGNED NOT NULL,
  student_user_id INT UNSIGNED NOT NULL,
  answers_json MEDIUMTEXT NOT NULL,
  score INT NOT NULL DEFAULT 0,
  total INT NOT NULL DEFAULT 0,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_video_student (partner_video_id, student_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$st = $conn->prepare("SELECT answers_json, score, total, submitted_at FROM partner_video_submissions WHERE partner_video_id=? AND student_user_id=? LIMIT 1");
$st->bind_param("ii",$video_id,$student_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
if(!$row){
  echo json_encode(["ok"=>true,"video"=>$own,"submission"=>null],JSON_UNESCAPED_UNICODE);
  exit;
}

$answers = json_decode($row["answers_json"], true);

echo json_encode([
  "ok"=>true,
  "video"=>$own,
  "submission"=>[
    "score" => (int)$row["score"],
    "total" => (int)$row["total"],
    "submitted_at" => $row["submitted_at"],
    "data" => $answers
  ]
], JSON_UNESCAPED_UNICODE);
