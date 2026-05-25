<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$user_id = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : 0;

if ($user_id <= 0) {
  http_response_code(401);
  echo json_encode([
    "ok" => false,
    "error" => "NOT_LOGGED_IN"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$simulation_id = (int)($input["simulation_id"] ?? 0);
$project_url = trim($input["project_url"] ?? "");

if ($simulation_id <= 0) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_SIMULATION_ID"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = $conn->prepare("
  INSERT INTO job_simulation_project_analysis (simulation_id, project_url)
  VALUES (?, ?)
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

$stmt->bind_param("is", $simulation_id, $project_url);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "INSERT_FAILED",
    "details" => $stmt->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt2 = $conn->prepare("
  UPDATE job_simulations
  SET github_url = ?, updated_at = NOW()
  WHERE id = ? AND user_id = ?
");

if (!$stmt2) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "PREPARE_FAILED_2",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt2->bind_param("sii", $project_url, $simulation_id, $user_id);

if (!$stmt2->execute()) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "UPDATE_FAILED",
    "details" => $stmt2->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  "ok" => true
], JSON_UNESCAPED_UNICODE);
exit;