<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
if (($_SESSION["role"] ?? "") !== "partner") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"], JSON_UNESCAPED_UNICODE); exit; }

$user_id = (int)$_SESSION["user_id"];
$path_id = (int)($_GET["path_id"] ?? 0);
if($path_id<=0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"], JSON_UNESCAPED_UNICODE); exit; }

$st = $conn->prepare("
  SELECT id, name
  FROM partner_playlists
  WHERE partner_user_id=? AND is_template=0 AND source_path_id=?
  ORDER BY id DESC
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("ii",$user_id,$path_id);
$st->execute();
$rs = $st->get_result();

$items=[];
while($r=$rs->fetch_assoc()){
  $items[]=["id"=>(int)$r["id"],"name"=>(string)$r["name"]];
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);