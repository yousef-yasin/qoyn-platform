<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]); exit; }

$role_key = trim((string)($_GET["role_key"] ?? ""));
if ($role_key === "") { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_ROLE_KEY"]); exit; }

$st = $conn->prepare("SELECT id, title FROM learning_paths WHERE role_key=? AND is_published=1 LIMIT 1");
$st->bind_param("s", $role_key);
$st->execute();
$r = $st->get_result()->fetch_assoc();
$st->close();

if(!$r){ echo json_encode(["ok"=>false,"error"=>"NO_PATH_FOR_ROLE"]); exit; }

echo json_encode(["ok"=>true,"path_id"=>(int)$r["id"],"title"=>$r["title"]], JSON_UNESCAPED_UNICODE);