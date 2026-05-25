<?php
// utbn-backend/api/partner_video_update.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];

$video_id = (int)($in["video_id"] ?? 0);
$title = trim((string)($in["title"] ?? ""));

if ($video_id <= 0 || $title === "") {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}

// verify ownership
$chk = $conn->prepare("SELECT id FROM partner_videos WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $video_id, $user_id);
$chk->execute();
$ok = $chk->get_result()->num_rows > 0;
$chk->close();
if (!$ok) json_out(["ok"=>false,"error"=>"VIDEO_NOT_OWNED"], 403);

// update
$up = $conn->prepare("UPDATE partner_videos SET title=? WHERE id=? AND partner_user_id=?");
$up->bind_param("sii", $title, $video_id, $user_id);
if (!$up->execute()) {
  $err = $up->error;
  $up->close();
  json_out(["ok"=>false,"error"=>"DB_UPDATE_FAILED","details"=>$err], 500);
}
$up->close();

json_out(["ok"=>true, "video_id"=>$video_id, "title"=>$title]);
