<?php
// Admin: viewers + submissions summary for a given partner video
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) json_out(["ok"=>false, "error"=>"MISSING_VIDEO_ID"], 400);

// Ensure submissions table exists (created in partner analytics pages too)
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

// Video info
$vst = $conn->prepare("SELECT id, title, playlist_id, partner_user_id FROM partner_videos WHERE id=? LIMIT 1");
$vst->bind_param("i", $video_id);
$vst->execute();
$video = $vst->get_result()->fetch_assoc();
$vst->close();
if (!$video) json_out(["ok"=>false, "error"=>"VIDEO_NOT_FOUND"], 404);

$sql = "
  SELECT
    u.id AS student_id,
    u.full_name,
    u.email,
    svp.watched_seconds,
    svp.completed,
    s.score,
    s.total,
    s.submitted_at
  FROM student_video_progress svp
  JOIN users u ON u.id = svp.user_id
  LEFT JOIN partner_video_submissions s
    ON s.partner_video_id = svp.video_id AND s.student_user_id = svp.user_id
  WHERE svp.video_id = ?
  ORDER BY svp.completed DESC, svp.watched_seconds DESC
";

$st = $conn->prepare($sql);
$st->bind_param("i", $video_id);
$st->execute();
$r = $st->get_result();

$items = [];
while ($row = $r->fetch_assoc()) {
  $items[] = [
    "student_id" => (int)$row["student_id"],
    "full_name" => (string)($row["full_name"] ?? ""),
    "email" => (string)($row["email"] ?? ""),
    "watched_seconds" => (int)($row["watched_seconds"] ?? 0),
    "completed" => (int)($row["completed"] ?? 0),
    "score" => isset($row["score"]) ? (int)$row["score"] : null,
    "total" => isset($row["total"]) ? (int)$row["total"] : null,
    "submitted_at" => $row["submitted_at"] ?? null,
  ];
}
$st->close();

json_out([
  "ok" => true,
  "video" => [
    "id" => (int)$video["id"],
    "title" => (string)$video["title"],
    "playlist_id" => (int)$video["playlist_id"],
    "partner_user_id" => (int)$video["partner_user_id"],
  ],
  "items" => $items
]);
