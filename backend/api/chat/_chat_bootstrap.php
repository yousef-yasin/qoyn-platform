<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/../db.php", __DIR__."/../../config/db.php", __DIR__."/../../db.php"];
$found = null;
foreach ($try as $p) {
  if (file_exists($p)) { $found = $p; break; }
}
if (!$found) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"]);
  exit;
}
require_once $found;

function chat_json($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_chat_login(){
  if (!isset($_SESSION["user_id"])) {
    chat_json(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);
  }
}

function current_role(){
  $r = strtolower(trim((string)($_SESSION["role"] ?? "")));
  if ($r === "partner") return "company";
  if ($r === "company") return "company";
  return "student";
}

function require_company(){
  require_chat_login();
  if (current_role() !== "company") {
    chat_json(["ok"=>false,"error"=>"FORBIDDEN"], 403);
  }
}

function require_student(){
  require_chat_login();
  if (current_role() !== "student") {
    chat_json(["ok"=>false,"error"=>"FORBIDDEN"], 403);
  }
}