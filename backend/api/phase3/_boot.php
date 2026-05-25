<?php
// utbn-backend/api/phase3/_boot.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [
  __DIR__."/../db.php",
  __DIR__."/../../api/db.php",
  __DIR__."/../config/db.php",
  __DIR__."/../includes/db.php",
  __DIR__."/../db.php"
];

$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!function_exists("json_out")) {
  function json_out($arr, $code=200){
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
  }
}

if (!isset($_SESSION["user_id"])) json_out(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);

// غيّر هذا إذا AI service عندك على بورت مختلف
if (!defined("AI_BASE")) define("AI_BASE", "http://127.0.0.1:5006");