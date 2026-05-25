<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents("php://input"), true);

$role_key = trim($input["role_key"] ?? "");
$user_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;

if ($user_id <= 0) {
  http_response_code(401);
  echo json_encode([
    "ok" => false,
    "error" => "NOT_LOGGED_IN"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($role_key === "") {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "ROLE_REQUIRED"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = $conn->prepare("
  INSERT INTO job_simulations (user_id, role_key, status)
  VALUES (?, ?, 'draft')
");

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "PREPARE_FAILED",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt->bind_param("is", $user_id, $role_key);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "INSERT_FAILED",
    "details" => $stmt->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  "ok" => true,
  "simulation_id" => $stmt->insert_id
], JSON_UNESCAPED_UNICODE);
exit;