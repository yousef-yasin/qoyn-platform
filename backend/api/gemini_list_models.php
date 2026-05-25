<?php
header("Content-Type: application/json; charset=utf-8");

function load_env_file($path) {
  if (!file_exists($path)) return;
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) continue;
    $key = trim($parts[0]);
    $val = trim($parts[1]);
    $val = trim($val, "\"'");
    putenv("$key=$val");
    $_ENV[$key] = $val;
  }
}
load_env_file(__DIR__ . "/../.env");

$key = getenv("GEMINI_API_KEY");
if (!$key) { echo json_encode(["ok"=>false,"error"=>"MISSING_GEMINI_API_KEY"]); exit; }

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . rawurlencode($key);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($out === false) {
  echo json_encode(["ok"=>false,"error"=>"CURL_ERROR","details"=>$err]); exit;
}

echo json_encode(["ok"=>($code>=200 && $code<300), "httpcode"=>$code, "raw"=>json_decode($out,true)], JSON_UNESCAPED_UNICODE);