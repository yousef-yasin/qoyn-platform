<?php
// utbn-backend/api/partner_playlist_details.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php", __DIR__."/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["error"=>"DB_FILE_NOT_FOUND","tried"=>$try], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
$partner_id = (int)$_SESSION["user_id"];

$playlist_id = (int)($_GET["playlist_id"] ?? 0);
if (!$playlist_id) { http_response_code(400); echo json_encode(["error"=>"MISSING_PLAYLIST_ID"], JSON_UNESCAPED_UNICODE); exit; }

// ensure columns exist (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN description MEDIUMTEXT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN expected_lectures INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN difficulty INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN published_at DATETIME NULL");

// تحقق ملكية الـ playlist + رجّع بياناتها
$chk = $conn->prepare("
  SELECT id, name, slug, description, expected_lectures, difficulty, coin_pool, is_published, published_at, created_at
  FROM partner_playlists
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
$chk->bind_param("ii", $playlist_id, $partner_id);
$chk->execute();
$pl = $chk->get_result()->fetch_assoc();
$chk->close();
if (!$pl) { http_response_code(404); echo json_encode(["error"=>"PLAYLIST_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }

// فيديوهات
$v = $conn->prepare("SELECT id, title, duration_seconds, stored_path, created_at FROM partner_videos WHERE playlist_id=? ORDER BY id DESC");
$v->bind_param("i", $playlist_id);
$v->execute();
$vr = $v->get_result();

$videos = [];
$total_seconds = 0;
while ($row = $vr->fetch_assoc()) {
  $total_seconds += (int)$row["duration_seconds"];
  $videos[] = [
    "id" => (int)$row["id"],
    "title" => (string)$row["title"],
    "duration_seconds" => (int)$row["duration_seconds"],
    "stored_path" => (string)$row["stored_path"],
    "created_at" => (string)$row["created_at"],
  ];
}
$v->close();

// الطلاب اللي حضروا
$w = $conn->prepare("
  SELECT
    p.user_id,
    COALESCE(u.full_name, u.name, u.email) AS student_name,
    COALESCE(u.email,'') AS email,
    COUNT(DISTINCT p.video_id) AS watched_videos,
    COUNT(DISTINCT CASE WHEN p.completed=1 THEN p.video_id END) AS completed_videos
  FROM student_video_progress p
  JOIN partner_videos v ON v.id = p.video_id
  LEFT JOIN users u ON u.id = p.user_id
  WHERE v.playlist_id = ?
  GROUP BY p.user_id
  ORDER BY completed_videos DESC, watched_videos DESC
");
$w->bind_param("i", $playlist_id);
$w->execute();
$wr = $w->get_result();

$students = [];
while ($row = $wr->fetch_assoc()) {
  $students[] = [
    "user_id" => (int)$row["user_id"],
    "student_name" => (string)$row["student_name"],
    "email" => (string)$row["email"],
    "watched_videos" => (int)$row["watched_videos"],
    "completed_videos" => (int)$row["completed_videos"],
  ];
}
$w->close();

echo json_encode([
  "ok"=>true,
  "playlist"=>[
    "id" => (int)$pl["id"],
    "name" => (string)$pl["name"],
    "slug" => (string)$pl["slug"],
    "description" => (string)($pl["description"] ?? ""),
    "expected_lectures" => (int)($pl["expected_lectures"] ?? 0),
    "difficulty" => (int)($pl["difficulty"] ?? 0),
    "coin_pool" => (int)($pl["coin_pool"] ?? 0),
    "is_published" => (int)($pl["is_published"] ?? 0),
    "published_at" => $pl["published_at"],
    "created_at" => (string)($pl["created_at"] ?? "")
  ],
  "total_seconds"=>$total_seconds,
  "videos"=>$videos,
  "students"=>$students
], JSON_UNESCAPED_UNICODE);
