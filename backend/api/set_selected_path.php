<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"]); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }

$user_id = (int)$_SESSION["user_id"];
$in = json_decode(file_get_contents("php://input"), true) ?: [];
$path_id = (int)($in["path_id"] ?? 0);
if ($path_id <= 0) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"]); exit; }

$st = $conn->prepare("
  INSERT INTO user_selected_path (user_id, path_id)
  VALUES (?, ?)
  ON DUPLICATE KEY UPDATE path_id=VALUES(path_id), selected_at=CURRENT_TIMESTAMP()
");
$st->bind_param("ii", $user_id, $path_id);
$ok = $st->execute();

echo json_encode(["ok"=>$ok, "path_id"=>$path_id], JSON_UNESCAPED_UNICODE);