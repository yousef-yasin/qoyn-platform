<?php
require __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

$user_id = (int)$_SESSION["user_id"];
$video_id = (int)($_GET["video_id"] ?? 0);

if ($video_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_VIDEO_ID"], 400);

$stmt = $conn->prepare("
  SELECT base_coin, quiz_coin, total_coin, updated_at
  FROM video_rewards
  WHERE user_id=? AND video_id=?
  LIMIT 1
");
$stmt->bind_param("ii", $user_id, $video_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) json_out(["ok"=>true, "found"=>false]);

json_out([
  "ok"=>true,
  "found"=>true,
  "video_id"=>$video_id,
  "base_coin"=>(int)$row["base_coin"],
  "quiz_coin"=>(int)$row["quiz_coin"],
  "total_coin"=>(int)$row["total_coin"],
  "updated_at"=>$row["updated_at"]
]);
