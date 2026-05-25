<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }
if (($_SESSION["role"] ?? "") !== "admin") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"]); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"]); exit; }

$company_id = (int)($_POST["company_id"] ?? 0);
$path_id    = (int)($_POST["path_id"] ?? 0);
$is_active  = (int)($_POST["is_active"] ?? 0);

if($company_id<=0 || $path_id<=0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PARAMS"]); exit; }
$is_active = ($is_active===1) ? 1 : 0;

$sql = "
INSERT INTO company_path_offers (company_id, path_id, is_active)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE is_active=VALUES(is_active)
";
$st = $conn->prepare($sql);
$st->bind_param("iii", $company_id, $path_id, $is_active);
$ok = $st->execute();

if(!$ok){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_ERROR","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);