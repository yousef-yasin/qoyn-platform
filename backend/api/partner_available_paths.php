<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"]); exit; }

$company_id = (int)$_SESSION["user_id"];

$sql = "
SELECT lp.id, lp.role_key, lp.title, lp.description, lp.is_published, cr.role_name
FROM company_path_offers o
JOIN learning_paths lp ON lp.id = o.path_id
LEFT JOIN career_roles cr ON cr.role_key = lp.role_key
WHERE o.company_id = ? AND o.is_active=1 AND lp.is_published=1
ORDER BY lp.id DESC
";
$st = $conn->prepare($sql);
$st->bind_param("i", $company_id);
$st->execute();
$rs = $st->get_result();

$items = [];
while($row = $rs->fetch_assoc()){
  $items[] = $row;
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);