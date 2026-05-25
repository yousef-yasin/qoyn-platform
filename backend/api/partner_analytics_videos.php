<?php
// utbn-backend/api/partner_analytics_videos.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"],JSON_UNESCAPED_UNICODE); exit; }
// ✅ Allow access if user is logged in (role check removed)
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false,"error"=>"Not logged in"]);
    exit;
}


$partner_id = (int)$_SESSION["user_id"];

// (اختياري) جدول submissions إن لم يوجد
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

$sql = "
  SELECT
    v.id,
    v.title,
    p.name AS playlist_name,
    v.created_at,
    (SELECT COUNT(*) FROM student_video_progress svp WHERE svp.video_id=v.id) AS viewers,
    (SELECT COUNT(*) FROM student_video_progress svp WHERE svp.video_id=v.id AND svp.completed=1) AS completed,
    (SELECT COUNT(*) FROM partner_video_submissions s WHERE s.partner_video_id=v.id) AS submissions
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  WHERE v.partner_user_id = ?
  ORDER BY v.id DESC
";
$st = $conn->prepare($sql);
$st->bind_param("i",$partner_id);
$st->execute();
$res = $st->get_result();

$out=[];
while($row=$res->fetch_assoc()){
  $out[]=$row;
}
echo json_encode(["ok"=>true,"items"=>$out],JSON_UNESCAPED_UNICODE);
