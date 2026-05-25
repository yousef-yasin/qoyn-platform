<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");
function table_exists($conn, $name){
  $name = $conn->real_escape_string($name);
  $res = $conn->query("SHOW TABLES LIKE '$name'");
  if(!$res) return false;
  return $res->num_rows > 0;
}


$hasVR = table_exists($conn, "video_rewards");
$hasCR = table_exists($conn, "code_rewards");
$hasSVP = table_exists($conn, "student_video_progress");

$joinVR = $hasVR ? "
LEFT JOIN (
  SELECT user_id, COALESCE(SUM(total_coin),0) s
  FROM video_rewards
  GROUP BY user_id
) vr ON vr.user_id = u.id
" : " ";

$joinCR = $hasCR ? "
LEFT JOIN (
  SELECT user_id, COALESCE(SUM(coin_awarded),0) s
  FROM code_rewards
  GROUP BY user_id
) cr ON cr.user_id = u.id
" : " ";

$coinsExpr = "0";
if ($hasVR && $hasCR) $coinsExpr = "COALESCE(vr.s,0) + COALESCE(cr.s,0)";
else if ($hasVR) $coinsExpr = "COALESCE(vr.s,0)";
else if ($hasCR) $coinsExpr = "COALESCE(cr.s,0)";

$joinSVP = $hasSVP ? "
LEFT JOIN (
  SELECT
    user_id,
    COUNT(DISTINCT video_id) AS watched_videos,
    COALESCE(SUM(watched_seconds),0) AS watched_seconds_sum,
    COALESCE(SUM(completed),0) AS completed_videos
  FROM student_video_progress
  GROUP BY user_id
) svp ON svp.user_id = u.id
" : " ";

$watchedVideosExpr = $hasSVP ? "COALESCE(svp.watched_videos,0)" : "0";
$watchedSecondsExpr = $hasSVP ? "COALESCE(svp.watched_seconds_sum,0)" : "0";
$completedVideosExpr = $hasSVP ? "COALESCE(svp.completed_videos,0)" : "0";

$sql = "
SELECT
  u.id,
  u.full_name,
  u.email,
  COALESCE(u.major_text,'') AS major_text,
  $coinsExpr AS coins_total,
  $watchedVideosExpr AS watched_videos,
  $completedVideosExpr AS completed_videos,
  $watchedSecondsExpr AS watched_seconds_sum
FROM users u
$joinVR
$joinCR
$joinSVP
WHERE u.role='student'
ORDER BY coins_total DESC, watched_videos DESC, u.id DESC
";

$res = $conn->query($sql);

$items = [];
while($row = $res->fetch_assoc()){
  $items[] = [
    "id" => (int)$row["id"],
    "full_name" => (string)$row["full_name"],
    "email" => (string)$row["email"],
    "major_text" => (string)$row["major_text"],
    "coins_total" => (int)$row["coins_total"],
    "watched_videos" => (int)$row["watched_videos"],
    "completed_videos" => (int)$row["completed_videos"],
    "watched_seconds_sum" => (int)$row["watched_seconds_sum"],
  ];
}

json_out(["ok"=>true, "items"=>$items]);
