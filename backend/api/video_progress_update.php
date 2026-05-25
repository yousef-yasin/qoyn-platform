<?php
require __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

$user_id = (int)$_SESSION["user_id"];

$in = json_decode(file_get_contents("php://input"), true) ?: [];
$video_id = (int)($in["video_id"] ?? 0);
$watched = (int)($in["watched_seconds"] ?? 0);
$duration = (int)($in["duration_seconds"] ?? 0);

if ($video_id <= 0 || $duration <= 0) {
  json_out(["error"=>"INVALID_INPUT"], 400);
}

if ($watched < 0) $watched = 0;
if ($watched > $duration) $watched = $duration;

$percent = ($duration > 0) ? ($watched / $duration) : 0;
$is_completed = ($percent >= 0.90) ? 1 : 0; // ✅ 90% يعتبر كامل

// upsert progress
$stmt = $conn->prepare("
  INSERT INTO video_progress (user_id, video_id, watched_seconds, duration_seconds, is_completed, completed_at)
  VALUES (?, ?, ?, ?, ?, IF(?, NOW(), NULL))
  ON DUPLICATE KEY UPDATE
    watched_seconds = GREATEST(watched_seconds, VALUES(watched_seconds)),
    duration_seconds = GREATEST(duration_seconds, VALUES(duration_seconds)),
    is_completed = GREATEST(is_completed, VALUES(is_completed)),
    completed_at = IF(GREATEST(is_completed, VALUES(is_completed))=1 AND completed_at IS NULL, NOW(), completed_at)
");
$stmt->bind_param("iiiiii", $user_id, $video_id, $watched, $duration, $is_completed, $is_completed);
$stmt->execute();
$stmt->close();

json_out([
  "ok" => true,
  "video_id" => $video_id,
  "watched_seconds" => $watched,
  "duration_seconds" => $duration,
  "is_completed" => (bool)$is_completed
]);
