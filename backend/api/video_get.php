<?php
require __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];
$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) json_out(["error"=>"MISSING_VIDEO_ID"], 400);

$stmt = $conn->prepare("SELECT v.id, v.title, v.is_paid, v.video_url, v.training_id,
                               t.coin_reward
                        FROM videos v
                        JOIN trainings t ON t.id = v.training_id
                        WHERE v.id=? LIMIT 1");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$v = $stmt->get_result()->fetch_assoc();
if (!$v) json_out(["error"=>"VIDEO_NOT_FOUND"], 404);

// subscription check if paid
if ((int)$v["is_paid"] === 1) {
  $subStmt = $conn->prepare("SELECT status, end_at FROM subscriptions WHERE user_id=? ORDER BY id DESC LIMIT 1");
  $subStmt->bind_param("i", $user_id);
  $subStmt->execute();
  $sub = $subStmt->get_result()->fetch_assoc();
  $active = false;
  if ($sub && $sub["status"] === "active") {
    if (!$sub["end_at"] || strtotime($sub["end_at"]) >= time()) $active = true;
  }
  if (!$active) json_out(["error"=>"SUBSCRIPTION_REQUIRED"], 402);
}

json_out([
  "id" => (int)$v["id"],
  "title" => $v["title"],
  "video_url" => $v["video_url"],
  "training_id" => (int)$v["training_id"],
]);
