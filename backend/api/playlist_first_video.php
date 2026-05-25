<?php
header("Content-Type: application/json; charset=utf-8");
session_start();

$user_id = (int)($_SESSION["user_id"] ?? 0);
if (!$user_id) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"UNAUTHORIZED"]); exit; }

$list = preg_replace("/[^a-zA-Z0-9_\-]/", "", $_GET["list"] ?? "");
if ($list === "") { echo json_encode(["ok"=>false,"error"=>"MISSING_LIST"]); exit; }

$YOUTUBE_API_KEY = "PUT_YOUR_KEY_HERE";
if (!$YOUTUBE_API_KEY || strlen($YOUTUBE_API_KEY) < 20) { echo json_encode(["ok"=>false,"error"=>"YOUTUBE_API_KEY_MISSING"]); exit; }

$params = http_build_query([
  "part" => "contentDetails",
  "playlistId" => $list,
  "maxResults" => 1,
  "key" => $YOUTUBE_API_KEY
]);

$url = "https://www.googleapis.com/youtube/v3/playlistItems?$params";
$out = file_get_contents($url);

$data = json_decode($out, true);
$items = $data["items"] ?? [];
$vid = $items[0]["contentDetails"]["videoId"] ?? "";

echo json_encode(["ok"=>true,"videoId"=>$vid], JSON_UNESCAPED_UNICODE);
