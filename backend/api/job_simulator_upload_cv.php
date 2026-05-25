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

$simulation_id = (int)($_POST["simulation_id"] ?? 0);

if ($simulation_id <= 0) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_SIMULATION_ID"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

if (!isset($_FILES["cv_file"])) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "CV_FILE_REQUIRED"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$allowedExt = ["pdf", "doc", "docx"];
$originalName = $_FILES["cv_file"]["name"] ?? "";
$tmpName = $_FILES["cv_file"]["tmp_name"] ?? "";
$error = $_FILES["cv_file"]["error"] ?? 1;

if ($error !== 0) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "UPLOAD_ERROR"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExt, true)) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_FILE_TYPE"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$uploadDir = dirname(__DIR__) . "/uploads/job_simulator_cv";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

$newName = "cv_" . $simulation_id . "_" . time() . "." . $ext;
$targetPath = $uploadDir . "/" . $newName;

if (!move_uploaded_file($tmpName, $targetPath)) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "MOVE_UPLOAD_FAILED"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$dbPath = "uploads/job_simulator_cv/" . $newName;

$stmt = $conn->prepare("
  UPDATE job_simulations
  SET cv_file_path = ?, updated_at = NOW()
  WHERE id = ? AND user_id = ?
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

$stmt->bind_param("sii", $dbPath, $simulation_id, $user_id);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "UPDATE_FAILED",
    "details" => $stmt->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  "ok" => true,
  "cv_file_path" => $dbPath
], JSON_UNESCAPED_UNICODE);
exit;