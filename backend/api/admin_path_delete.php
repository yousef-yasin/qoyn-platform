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

$path_id = (int)($_POST["path_id"] ?? 0);
if($path_id<=0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"]); exit; }

// ensure column exists (safe)
@$conn->query("ALTER TABLE learning_paths ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0");

// soft delete
$st = $conn->prepare("UPDATE learning_paths SET is_deleted=1 WHERE id=?");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i",$path_id);
$ok = $st->execute();
$st->close();

if(!$ok){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_ERROR","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);