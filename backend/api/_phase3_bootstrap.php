<?php
// utbn-backend/api/_phase3_bootstrap.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../config/db.php", __DIR__."/../db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"]); exit; }
require_once $found;

function json_out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}
function require_login(){
  if (!isset($_SESSION["user_id"])) json_out(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);
}
function require_partner(){
  require_login();
  if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") json_out(["ok"=>false,"error"=>"FORBIDDEN"], 403);
}
function require_student(){
  require_login();
  if (isset($_SESSION["role"]) && $_SESSION["role"] === "partner") json_out(["ok"=>false,"error"=>"FORBIDDEN"], 403);
}

function ai_post_json($url, $payload, $timeout=300){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  $raw = curl_exec($ch);
  $err = curl_error($ch);
  $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($raw === false) return ["ok"=>false,"status"=>$code,"error"=>"CURL_ERROR","detail"=>$err];
  $j = json_decode($raw, true);
  if (!is_array($j)) $j = ["_raw"=>$raw];
  return ["ok"=>($code>=200 && $code<300), "status"=>$code, "json"=>$j];
}