<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

$partner_user_id = (int)($_GET["partner_user_id"] ?? 0);
if ($partner_user_id <= 0) json_out(["ok"=>false,"error"=>"INVALID_PARTNER_ID"], 400);

$sql = "
SELECT
  pl.id,
  pl.name,
  pl.created_at,
  COALESCE(v.cnt,0) AS videos_count,
  COALESCE(w.viewers,0) AS viewers_count
FROM partner_playlists pl
LEFT JOIN (
  SELECT playlist_id, COUNT(*) cnt
  FROM partner_videos
  GROUP BY playlist_id
) v ON v.playlist_id = pl.id
LEFT JOIN (
  SELECT pv.playlist_id, COUNT(DISTINCT svp.user_id) viewers
  FROM partner_videos pv
  JOIN student_video_progress svp ON svp.video_id = pv.id
  GROUP BY pv.playlist_id
) w ON w.playlist_id = pl.id
WHERE pl.partner_user_id = ?
ORDER BY pl.id DESC
";

$st = $conn->prepare($sql);
$st->bind_param("i", $partner_user_id);
$st->execute();
$r = $st->get_result();

$items = [];
while($row = $r->fetch_assoc()){
  $items[] = [
    "id" => (int)$row["id"],
    "name" => (string)$row["name"],
    "created_at" => (string)$row["created_at"],
    "videos_count" => (int)$row["videos_count"],
    "viewers_count" => (int)$row["viewers_count"],
  ];
}
$st->close();

json_out(["ok"=>true,"items"=>$items]);
