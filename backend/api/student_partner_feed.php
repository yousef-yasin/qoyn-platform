<?php
// utbn-backend/api/student_partner_feed.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ تأكد الأعمدة موجودة (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN course_name VARCHAR(220) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN cover_path VARCHAR(255) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN path_id INT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN source_path_id INT NULL");

// ✅ base url
$base = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http")
      . "://" . ($_SERVER["HTTP_HOST"] ?? "localhost") . "/utbn-backend/";

// آخر 30 يوم
$days = 30;

$sql = "
  SELECT
    p.id AS playlist_id,
    p.name AS playlist_name,
    p.course_name,
    
    p.published_at,
    p.difficulty,
    p.coin_pool,
    p.cover_path,
    p.path_id,
    p.source_path_id,
    COALESCE(u.full_name, 'Partner') AS partner_name

  FROM partner_playlists p
  JOIN users u ON u.id = p.partner_user_id
  WHERE p.is_published = 1
    AND p.published_at IS NOT NULL
    AND p.published_at >= (NOW() - INTERVAL ? DAY)

    -- ✅ استثني أي Playlist مرتبطة بـ Path
    AND COALESCE(p.source_path_id, p.path_id, 0) = 0

  ORDER BY p.published_at DESC, p.id DESC
  LIMIT 50
";

$q = $conn->prepare($sql);
if (!$q) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$q->bind_param("i", $days);
$q->execute();
$rs = $q->get_result();

$items = [];
while ($row = $rs->fetch_assoc()) {
  $coverPath = str_replace("\\", "/", (string)($row["cover_path"] ?? ""));
  $coverUrl = "";
  if ($coverPath !== "") {
    // إذا مخزن "uploads/..." نطلع URL كامل
    $coverUrl = $base . ltrim($coverPath, "/");
  }

  $items[] = [
    "notif_id"      => (int)$row["playlist_id"],
    "type"          => "playlist",
    "partner_name"  => (string)$row["partner_name"],
    "playlist_id"   => (int)$row["playlist_id"],
    "playlist_name" => (string)$row["playlist_name"],
    "course_name"   => (string)($row["course_name"] ?? ""),

    "published_at"  => (string)($row["published_at"] ?? ""),
    "difficulty"    => (int)($row["difficulty"] ?? 0),
    "coin_pool"     => (int)($row["coin_pool"] ?? 0),
    "cover_path"    => $coverPath,
    "cover_url"     => $coverUrl,
    "path_id"       => (int)($row["path_id"] ?? 0),
    "source_path_id"=> (int)($row["source_path_id"] ?? 0),
  ];

}
$q->close();

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);
