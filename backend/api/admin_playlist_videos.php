<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

$playlist_id = (int)($_GET["playlist_id"] ?? 0);
if ($playlist_id <= 0) json_out(["ok"=>false,"error"=>"INVALID_PLAYLIST_ID"], 400);

$sql = "
SELECT
  v.id,
  v.title,
  v.duration_seconds,
  v.created_at,
  COALESCE(w.viewers,0) AS viewers_count,
  COALESCE(w.completed_sum,0) AS completed_sum
FROM partner_videos v
LEFT JOIN (
  SELECT
    video_id,
    COUNT(DISTINCT user_id) AS viewers,
    SUM(completed) AS completed_sum
  FROM student_video_progress
  GROUP BY video_id
) w ON w.video_id = v.id
WHERE v.playlist_id = ?
ORDER BY v.id DESC
";

$st = $conn->prepare($sql);
$st->bind_param("i", $playlist_id);
$st->execute();
$r = $st->get_result();

$items = [];
while($row = $r->fetch_assoc()){
  $items[] = [
    "id" => (int)$row["id"],
    "title" => (string)$row["title"],
    "duration_seconds" => (int)$row["duration_seconds"],
    "created_at" => (string)$row["created_at"],
    "viewers_count" => (int)$row["viewers_count"],
    "completed_sum" => (int)$row["completed_sum"],
  ];
}
$st->close();

json_out(["ok"=>true,"items"=>$items]);
