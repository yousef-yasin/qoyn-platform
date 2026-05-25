<?php
// utbn-backend/api/courses_all.php
session_start();
require_once __DIR__ . "/../config/db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$sql = "
  SELECT
    id,
    COALESCE(name, course_name) AS name,
    COALESCE(code, course_code) AS code
  FROM courses
  ORDER BY COALESCE(name, course_name) ASC
";

$res = $conn->query($sql);
if (!$res) {
  http_response_code(500);
  echo json_encode(["error" => "DB_ERROR", "details" => $conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$rows = [];
while ($row = $res->fetch_assoc()) $rows[] = $row;

echo json_encode(["items" => $rows], JSON_UNESCAPED_UNICODE);
