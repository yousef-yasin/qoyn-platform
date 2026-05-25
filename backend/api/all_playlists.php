<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }

/*
  بيرجع:
  - Partner playlists (partner_playlists) المنشورة
  - Path playlists (partner_playlists او جدول الباثات عندك حسب تصميمك)
  
  هون رح نعرض Partner playlists + أي playlist موجودة بجدول partner_playlists
  لأنه عندك الباثات كمان بالنهاية عم تنزل كـ playlists (حسب كلامك).
*/

$sql = "
  SELECT
    p.id AS playlist_id,
    p.name AS playlist_name,
    COALESCE(p.course_name, p.name) AS course_name,
    COALESCE(p.cover_path,'') AS cover_path,
    COALESCE(p.difficulty,0) AS difficulty,
    COALESCE(p.coin_pool,0) AS coin_pool,
    COALESCE(p.published_at, p.created_at) AS published_at,
    COALESCE(u.username, u.name, 'Partner') AS partner_name
  FROM partner_playlists p
  LEFT JOIN users u ON u.id = p.partner_id
  WHERE p.is_published = 1
  ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC
";

$res = $conn->query($sql);
$items = [];
if($res){
  while($r = $res->fetch_assoc()){
    $items[] = [
      "type" => "playlist",
      "playlist_id" => (int)$r["playlist_id"],
      "playlist_name" => (string)$r["playlist_name"],
      "course_name" => (string)$r["course_name"],
      "partner_name" => (string)$r["partner_name"],
      "difficulty" => (int)$r["difficulty"],
      "coin_pool" => (int)$r["coin_pool"],
      "published_at" => (string)$r["published_at"],
      "playlist_cover_path" => (string)$r["cover_path"],
    ];
  }
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);