<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [
  __DIR__ . "/../db.php",
  __DIR__ . "/../config/db.php",
  __DIR__ . "/../includes/db.php",
  __DIR__ . "/db.php"
];
$found = null;
foreach ($try as $p) {
  if (file_exists($p)) {
    $found = $p;
    break;
  }
}
if (!$found) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

// جيب الـ path المختار للطالب
$st = $conn->prepare("SELECT path_id FROM user_selected_path WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();

$path_id = (int)($row["path_id"] ?? 0);

$base = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http")
      . "://" . ($_SERVER["HTTP_HOST"] ?? "localhost") . "/utbn-backend/";

if ($path_id <= 0) {
  echo json_encode(["ok"=>true, "items"=>[], "note"=>"NO_SELECTED_PATH"], JSON_UNESCAPED_UNICODE);
  exit;
}

// تأكد الأعمدة موجودة

// جيب الـ playlists المرتبطة بالـ path + احسب التقدم
$sql = "
SELECT
  p.id AS playlist_id,
  p.name AS playlist_name,
  p.course_name,
  p.difficulty,
  p.coin_pool,
  p.published_at,
  p.cover_path,
  COALESCE(u.full_name, 'qoyn') AS partner_name,

  COUNT(DISTINCT v.id) AS total_videos,
  COUNT(DISTINCT CASE WHEN svp.completed = 1 THEN svp.video_id END) AS completed_videos

FROM partner_playlists p
JOIN users u ON u.id = p.partner_user_id
LEFT JOIN partner_videos v
  ON v.playlist_id = p.id
LEFT JOIN student_video_progress svp
  ON svp.video_id = v.id
 AND svp.user_id = ?

WHERE p.is_published = 1
  AND COALESCE(p.source_path_id, p.path_id) = ?

GROUP BY
  p.id, p.name, p.course_name, p.difficulty, p.coin_pool, p.published_at, p.cover_path, u.full_name

ORDER BY p.published_at DESC, p.id DESC
LIMIT 50
";

$st2 = $conn->prepare($sql);
$st2->bind_param("ii", $user_id, $path_id);
$st2->execute();
$rs = $st2->get_result();

$items = [];

while ($r = $rs->fetch_assoc()) {
  $cover_path = str_replace("\\", "/", (string)($r["cover_path"] ?? ""));
  $cover_path = trim($cover_path);
  $cover_url  = $cover_path !== "" ? $base . ltrim($cover_path, "/") : "";

  $total_videos = (int)($r["total_videos"] ?? 0);
  $completed_videos = (int)($r["completed_videos"] ?? 0);

  $progress_percent = 0;
  if ($total_videos > 0) {
    $progress_percent = (int)round(($completed_videos / $total_videos) * 100);
  }

  if ($progress_percent < 0) $progress_percent = 0;
  if ($progress_percent > 100) $progress_percent = 100;

  $items[] = [
    "playlist_id"       => (int)$r["playlist_id"],
    "playlist_name"     => (string)$r["playlist_name"],
    "course_name"       => (string)($r["course_name"] ?? ""),
    "difficulty"        => (int)($r["difficulty"] ?? 0),
    "coin_pool"         => (int)($r["coin_pool"] ?? 0),
    "published_at"      => (string)($r["published_at"] ?? ""),
    "partner_name"      => (string)($r["partner_name"] ?? "qoyn"),
    "cover_path"        => $cover_path,
    "cover_url"         => $cover_url,
    "total_videos"      => $total_videos,
    "completed_videos"  => $completed_videos,
    "progress_percent"  => $progress_percent
  ];
}
$st2->close();

echo json_encode(["ok"=>true, "items"=>$items], JSON_UNESCAPED_UNICODE);
exit;