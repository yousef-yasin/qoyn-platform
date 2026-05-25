<?php
header("Content-Type: application/json; charset=utf-8");

function load_env_file($path) {
  if (!file_exists($path)) return;
  foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) continue;
    $key = trim($parts[0]);
    $val = trim($parts[1], "\"' ");
    putenv("$key=$val");
    $_ENV[$key] = $val;
  }
}

load_env_file(__DIR__ . "/../.env");

echo json_encode([
  "has_key" => (bool)getenv("GEMINI_API_KEY"),
  "model"   => getenv("GEMINI_MODEL"),
], JSON_UNESCAPED_UNICODE);