<?php
require __DIR__ . "/../db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$type = $_GET["type"] ?? "";

if ($type !== "plan" && $type !== "experience") {
  http_response_code(400);
  echo json_encode(["error" => "INVALID_TYPE"], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = $conn->prepare("
  SELECT id, title, file_path, original_name, mime_type, file_size, created_at
  FROM student_attachments
  WHERE user_id=? AND type=? AND (? <> 'plan' OR mime_type LIKE 'image/%')
  ORDER BY id DESC
");
$stmt->bind_param("iss", $user_id, $type, $type);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
