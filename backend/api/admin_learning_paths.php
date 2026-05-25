<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }
if (($_SESSION["role"] ?? "") !== "admin") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"]); exit; }

$sql = "
  SELECT lp.id, lp.role_key, lp.title, lp.description, lp.source, lp.is_published, lp.created_at,
         cr.role_name
  FROM learning_paths lp
  LEFT JOIN career_roles cr ON cr.role_key = lp.role_key
  ORDER BY lp.id DESC
";
$res = $conn->query($sql);
$items = [];
if($res){
  while($row = $res->fetch_assoc()){
    $items[] = $row;
  }
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);