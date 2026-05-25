<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

$role_key  = trim($_POST["role_key"] ?? "");
$role_name = trim($_POST["role_name"] ?? "");
$score     = (float)($_POST["score"] ?? 0);

if ($role_key === "" || $role_name === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_ROLE"], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = $conn->prepare("
  INSERT INTO user_selected_role (user_id, role_key, role_name, score)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    role_key=VALUES(role_key),
    role_name=VALUES(role_name),
    score=VALUES(score)
");
if(!$stmt){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt->bind_param("issd", $user_id, $role_key, $role_name, $score);
$stmt->execute();

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);