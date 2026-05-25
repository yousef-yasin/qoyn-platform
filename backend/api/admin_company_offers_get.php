<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }
if (($_SESSION["role"] ?? "") !== "admin") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"]); exit; }

$company_id = (int)($_GET["company_id"] ?? 0);
if($company_id <= 0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_COMPANY_ID"]); exit; }

$st = $conn->prepare("SELECT path_id FROM company_path_offers WHERE company_id=? AND is_active=1");
$st->bind_param("i", $company_id);
$st->execute();
$rs = $st->get_result();

$ids = [];
while($r = $rs->fetch_assoc()){
  $ids[] = (int)$r["path_id"];
}

echo json_encode(["ok"=>true,"active_path_ids"=>$ids], JSON_UNESCAPED_UNICODE);