<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$path_id = (int)($_POST["path_id"] ?? 0);

if ($path_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"]);
  exit;
}

$st = $conn->prepare("
  INSERT INTO user_selected_path (user_id, path_id, selected_at)
  VALUES (?, ?, NOW())
  ON DUPLICATE KEY UPDATE path_id=VALUES(path_id), selected_at=VALUES(selected_at)
");
$st->bind_param("ii", $user_id, $path_id);
$st->execute();
$st->close();

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);