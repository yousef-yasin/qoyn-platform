<?php
require __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

function json_out($d,$c=200){ http_response_code($c); echo json_encode($d,JSON_UNESCAPED_UNICODE); exit; }

if ($_SERVER["REQUEST_METHOD"] !== "POST") json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"],405);

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];
$playlist_id = (int)($in["playlist_id"] ?? 0);
if ($playlist_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_PLAYLIST_ID"],400);

// تأكد ملكية
$chk = $conn->prepare("SELECT id FROM partner_playlists WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii",$playlist_id,$user_id);
$chk->execute();
$pl = $chk->get_result()->fetch_assoc();
$chk->close();
if(!$pl) json_out(["ok"=>false,"error"=>"PLAYLIST_NOT_OWNED"],403);

// هات فيديوهات البلاي ليست
$vids = [];
$vq = $conn->prepare("SELECT id, stored_path FROM partner_videos WHERE playlist_id=? AND partner_user_id=?");
$vq->bind_param("ii",$playlist_id,$user_id);
$vq->execute();
$rs = $vq->get_result();
while($r=$rs->fetch_assoc()) $vids[] = $r;
$vq->close();

// احذف كل التعلقات
foreach($vids as $v){
  $vid = (int)$v["id"];

  @$conn->query("DELETE FROM partner_video_quizzes WHERE partner_video_id=".(int)$vid);
  @$conn->query("DELETE FROM partner_video_code_problems WHERE partner_video_id=".(int)$vid);
  @$conn->query("DELETE FROM partner_video_submissions WHERE partner_video_id=".(int)$vid);
  @$conn->query("DELETE FROM student_video_progress WHERE video_id=".(int)$vid);

  // إذا عندك rewards مخزنة video_id كنص/سترينغ
  $vidStr = $conn->real_escape_string((string)$vid);
  @$conn->query("DELETE FROM video_rewards WHERE video_id='".$vidStr."'");

  // حذف الملف من السيرفر (اختياري)
  $path = trim((string)($v["stored_path"] ?? ""));
  if($path !== ""){
    $abs = realpath(__DIR__ . "/..") . "/" . ltrim($path,"/");
    @unlink($abs);
  }
}

// احذف الفيديوهات نفسها
$delV = $conn->prepare("DELETE FROM partner_videos WHERE playlist_id=? AND partner_user_id=?");
$delV->bind_param("ii",$playlist_id,$user_id);
$delV->execute();
$delV->close();

// احذف البلاي ليست
$delP = $conn->prepare("DELETE FROM partner_playlists WHERE id=? AND partner_user_id=?");
$delP->bind_param("ii",$playlist_id,$user_id);
$delP->execute();
$delP->close();

json_out(["ok"=>true,"playlist_id"=>$playlist_id]);
