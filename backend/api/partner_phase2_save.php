<?php
// utbn-backend/api/partner_phase2_save.php
session_start();
require_once __DIR__ . "/../config/db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(["error" => "METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(["error" => "INVALID_JSON"], JSON_UNESCAPED_UNICODE);
  exit;
}

$course_code = trim((string)($data["course_code"] ?? ""));
$course_name = trim((string)($data["course_name"] ?? ""));
$title = trim((string)($data["project_title"] ?? ""));
$desc  = trim((string)($data["project_description"] ?? ""));

if ($title === "" || $desc === "") {
  http_response_code(400);
  echo json_encode(["error" => "MISSING_TITLE_OR_DESCRIPTION"], JSON_UNESCAPED_UNICODE);
  exit;
}

// إذا المستخدم اختار من dropdown، رح يجي code+name
// إذا كتب يدوي، ممكن يجي name فقط
if ($course_name === "" && $course_code === "") {
  // مسموح بس الأفضل يكون فيه مادة
  $course_name = null;
  $course_code = null;
} else {
  if ($course_name === "") $course_name = null;
  if ($course_code === "") $course_code = null;
}

$stmt = $conn->prepare("
  INSERT INTO partner_phase2_projects
    (partner_user_id, course_code, course_name, project_title, project_description)
  VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("issss", $user_id, $course_code, $course_name, $title, $desc);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["error" => "DB_INSERT_FAILED", "details" => $stmt->error], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode(["ok" => true, "project_id" => (int)$stmt->insert_id], JSON_UNESCAPED_UNICODE);
