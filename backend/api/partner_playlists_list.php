<?php
// utbn-backend/api/partner_playlists_list.php
require_once __DIR__ . "/db.php";
header("Content-Type: application/json; charset=utf-8");

require_login();

$user_id = (int)$_SESSION["user_id"];

// ensure columns exist (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN description MEDIUMTEXT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN expected_lectures INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN difficulty INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN published_at DATETIME NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN course_name VARCHAR(220) NULL");

$sql = "
SELECT 
  p.id,
  p.name,
  p.slug,
  p.expected_lectures,
  p.difficulty,
  p.coin_pool,
  p.is_published,
  p.major_text,
  p.course_name,
  COALESCE(SUM(v.duration_seconds), 0) AS total_seconds
  FROM partner_playlists p
  LEFT JOIN partner_videos v
    ON v.playlist_id = p.id
   AND v.partner_user_id = p.partner_user_id
  WHERE p.partner_user_id = ?
  GROUP BY p.id
  ORDER BY p.id DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "SQL_PREPARE_FAILED",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
  $items[] = [
    "id" => (int)$row["id"],
    "name" => (string)$row["name"],
    "slug" => (string)$row["slug"],
    "expected_lectures" => (int)$row["expected_lectures"],
    "difficulty" => (int)$row["difficulty"],
    "coin_pool" => (int)$row["coin_pool"],
    "is_published" => (int)$row["is_published"],
    "total_seconds" => (int)$row["total_seconds"],
    "major_text" => (string)($row["major_text"] ?? ""),
"course_name" => (string)($row["course_name"] ?? ""),
  ];
}

echo json_encode(["ok"=>true, "items" => $items], JSON_UNESCAPED_UNICODE);
