<?php
require __DIR__ . "/../db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"]);
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$type = $_POST["type"] ?? ($_GET["type"] ?? "");
$id = (int)($_POST["id"] ?? ($_GET["id"] ?? 0));

if ($type !== "plan" && $type !== "experience") {
  http_response_code(400);
  echo json_encode(["error" => "INVALID_TYPE"]);
  exit;
}
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["error" => "INVALID_ID"]);
  exit;
}

// احضر المرفق وتأكد انه تابع للمستخدم
$stmt = $conn->prepare("SELECT id, file_path FROM student_attachments WHERE id=? AND user_id=? AND type=? LIMIT 1");
$stmt->bind_param("iis", $id, $user_id, $type);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
  http_response_code(404);
  echo json_encode(["error" => "NOT_FOUND"]);
  exit;
}

$file_path = $row["file_path"] ?? "";
$root = realpath(__DIR__ . "/../../");            // utbn-backend
$uploads = realpath(__DIR__ . "/../../uploads"); // utbn-backend/uploads

// احذف الملف من السيرفر (اذا موجود وبداخل uploads فقط)
if ($root && $uploads && $file_path) {
  $abs = realpath($root . DIRECTORY_SEPARATOR . $file_path);
  if ($abs && strpos($abs, $uploads) === 0 && file_exists($abs)) {
    @unlink($abs);
  }
}

// احذف من DB
$del = $conn->prepare("DELETE FROM student_attachments WHERE id=? AND user_id=? AND type=?");
$del->bind_param("iis", $id, $user_id, $type);
$del->execute();

echo json_encode(["ok" => true], JSON_UNESCAPED_UNICODE);
