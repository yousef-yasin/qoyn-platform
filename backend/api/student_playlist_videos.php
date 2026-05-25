<?php
// utbn-backend/api/student_playlist_videos.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
$user_id = (int)$_SESSION["user_id"];

$playlist_id = (int)($_GET["playlist_id"] ?? 0);
if ($playlist_id <= 0) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PLAYLIST_ID"], JSON_UNESCAPED_UNICODE); exit; }

// ✅ base url
$base = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http")
      . "://" . ($_SERVER["HTTP_HOST"] ?? "localhost") . "/utbn-backend/";

// ensure columns
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN cover_path VARCHAR(255) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN path_id INT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN source_path_id INT NULL");
@$conn->query("ALTER TABLE partner_videos ADD COLUMN cover_path VARCHAR(255) NULL");

// ✅ 1) playlist لازم تكون منشورة + نجيب path_id/source_path_id
$plq = $conn->prepare("
  SELECT id, name, description, is_published, difficulty, coin_pool, cover_path,
         path_id, source_path_id
  FROM partner_playlists
  WHERE id=? LIMIT 1
");
$plq->bind_param("i", $playlist_id);
$plq->execute();
$pl = $plq->get_result()->fetch_assoc();
$plq->close();

if (!$pl) { http_response_code(404); echo json_encode(["ok"=>false,"error"=>"PLAYLIST_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
if ((int)$pl["is_published"] !== 1) { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN_PLAYLIST_NOT_PUBLISHED"], JSON_UNESCAPED_UNICODE); exit; }

// ✅ هل هاي playlist تبع Path؟
$pl_path = (int)($pl["path_id"] ?? 0);
$pl_source_path = (int)($pl["source_path_id"] ?? 0);
$belongsToPath = (max($pl_path, $pl_source_path) > 0);

// ✅ 2) إذا تبع Path -> لازم الطالب مختار نفس path
if ($belongsToPath) {
  $stp = $conn->prepare("SELECT path_id FROM user_selected_path WHERE user_id=? LIMIT 1");
  $stp->bind_param("i", $user_id);
  $stp->execute();
  $sr = $stp->get_result()->fetch_assoc();
  $stp->close();

  $selected_path_id = (int)($sr["path_id"] ?? 0);
  if ($selected_path_id <= 0) {
    http_response_code(403);
    echo json_encode(["ok"=>false,"error"=>"NO_SELECTED_PATH"], JSON_UNESCAPED_UNICODE);
    exit;
  }

  $chk = $conn->prepare("
    SELECT id
    FROM partner_playlists
    WHERE id=?
      AND is_published=1
      AND COALESCE(source_path_id, path_id)=?
    LIMIT 1
  ");
  $chk->bind_param("ii", $playlist_id, $selected_path_id);
  $chk->execute();
  $ok = $chk->get_result()->fetch_assoc();
  $chk->close();

  if (!$ok) {
    http_response_code(403);
    echo json_encode(["ok"=>false,"error"=>"FORBIDDEN_PATH_MISMATCH"], JSON_UNESCAPED_UNICODE);
    exit;
  }
  
}

// ✅ 3) غير هيك (package عادي) -> سماح لأي طالب مسجل ✅

// ✅ 4) هات الفيديوهات
$v = $conn->prepare("
  SELECT id, title, stored_path, cover_path, duration_seconds, created_at
  FROM partner_videos
  WHERE playlist_id=?
  ORDER BY id DESC
");
$v->bind_param("i", $playlist_id);
$v->execute();
$r = $v->get_result();

$videos = [];
while ($row = $r->fetch_assoc()) {
  $stored = str_replace("\\", "/", (string)($row["stored_path"] ?? ""));
  $vcover = str_replace("\\", "/", (string)($row["cover_path"] ?? ""));

  $videos[] = [
    "id" => (int)$row["id"],
    "title" => (string)$row["title"],
    "stored_path" => $stored,
    "stored_url"  => $stored !== "" ? $base . ltrim($stored, "/") : "",
    "cover_path"  => $vcover,
    "cover_url"   => $vcover !== "" ? $base . ltrim($vcover, "/") : "",
    "duration_seconds" => (int)($row["duration_seconds"] ?? 0),
    "created_at" => (string)($row["created_at"] ?? ""),
  ];
}
$v->close();

// playlist cover url
$plCoverPath = str_replace("\\", "/", (string)($pl["cover_path"] ?? ""));
$plCoverUrl  = $plCoverPath !== "" ? $base . ltrim($plCoverPath, "/") : "";

echo json_encode([
  "ok"=>true,
  "playlist"=>[
    "id"=>(int)$pl["id"],
    "name"=>(string)$pl["name"],
    "difficulty"=>(int)($pl["difficulty"] ?? 0),
    "coin_pool"=>(int)($pl["coin_pool"] ?? 0),
    "cover_path"=>$plCoverPath,
    "cover_url"=>$plCoverUrl,
    "belongs_to_path"=>$belongsToPath ? 1 : 0
  ],
  "items"=>$videos,
  "videos"=>$videos
], JSON_UNESCAPED_UNICODE);
