<?php
header("Content-Type: application/json; charset=utf-8");
session_start();
require_once __DIR__ . "/../config/db.php";

$user_id = (int)($_SESSION["user_id"] ?? 0);
if (!$user_id) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"UNAUTHORIZED"], JSON_UNESCAPED_UNICODE); exit; }

$q = trim($_GET["q"] ?? "");
$max = (int)($_GET["max"] ?? 6);
$max = max(1, min(12, $max));

if ($q === "") { echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE); exit; }

// ضع YouTube API Key هنا
$YOUTUBE_API_KEY = "AIzaSyDHHEPS2lIhzNk0p1-sRDDNKy2qfOgDFs8";
if (!$YOUTUBE_API_KEY || strlen($YOUTUBE_API_KEY) < 20) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"YOUTUBE_API_KEY_MISSING"], JSON_UNESCAPED_UNICODE);
  exit;
}

// Cache table
$conn->query("CREATE TABLE IF NOT EXISTS youtube_cache (
  cache_key VARCHAR(255) PRIMARY KEY,
  json TEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$cache_key = "playlists:q:" . mb_strtolower($q, "UTF-8") . "|max:" . $max;

// cached (7 days)
$stmt = $conn->prepare("SELECT json, updated_at FROM youtube_cache WHERE cache_key=?");
$stmt->bind_param("s", $cache_key);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row) {
  $updated = strtotime($row["updated_at"]);
  if ($updated && (time() - $updated) < (7 * 24 * 3600)) {
    echo $row["json"];
    exit;
  }
}

// Search query (prefer full courses)
$query = $q . " دورة كاملة playlist كورس محاضرات";

$params = http_build_query([
  "part" => "snippet",
  "q" => $query,
  "type" => "playlist",
  "maxResults" => $max,
  "relevanceLanguage" => "ar",
  "safeSearch" => "strict",
  "key" => $YOUTUBE_API_KEY
]);

$url = "https://www.googleapis.com/youtube/v3/search?$params";

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 20
]);
$out = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err || $code < 200 || $code >= 300) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"YOUTUBE_FETCH_FAILED","http"=>$code,"detail"=>$err ?: $out], JSON_UNESCAPED_UNICODE);
  exit;
}

$data = json_decode($out, true);
$items = $data["items"] ?? [];

$result = [];
foreach ($items as $it) {
  $id = $it["id"] ?? [];
  $snippet = $it["snippet"] ?? [];
  if (($id["kind"] ?? "") !== "youtube#playlist") continue;

  $pid = $id["playlistId"] ?? "";
  if ($pid === "") continue;

  $result[] = [
    "playlistId" => $pid,
    "title" => $snippet["title"] ?? "",
    "channel" => $snippet["channelTitle"] ?? "",
    "thumb" => $snippet["thumbnails"]["medium"]["url"] ?? ($snippet["thumbnails"]["default"]["url"] ?? ""),
    "url" => "https://www.youtube.com/playlist?list=" . $pid
  ];
}

$json = json_encode(["ok"=>true,"items"=>$result], JSON_UNESCAPED_UNICODE);

// save cache
$ins = $conn->prepare("INSERT INTO youtube_cache (cache_key, json) VALUES (?, ?) ON DUPLICATE KEY UPDATE json=VALUES(json)");
$ins->bind_param("ss", $cache_key, $json);
$ins->execute();
$ins->close();

echo $json;
