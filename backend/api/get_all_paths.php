<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"]); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }

$q = $conn->query("SELECT id, role_key, title, description FROM learning_paths WHERE is_published=1 AND is_deleted=0 ORDER BY id DESC");
$rows = [];
while($r = $q->fetch_assoc()){ $rows[] = $r; }

echo json_encode(["ok"=>true,"paths"=>$rows], JSON_UNESCAPED_UNICODE);