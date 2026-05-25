<?php
require __DIR__ . "/db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"]);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

// جيب عنوان الخطة/التخصص من نفس الجدول (group_title)
$stmt = $conn->prepare("
  SELECT group_title
  FROM user_plan_courses
  WHERE user_id = ?
  LIMIT 1
");
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["error"=>"SQL_PREPARE_FAILED","details"=>$conn->error]);
  exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$majorName = $row["group_title"] ?? "";

// جيب المواد (الإجبارية) للمستخدم
$stmt = $conn->prepare("
  SELECT course_code AS code, course_name AS name, credits
  FROM user_plan_courses
  WHERE user_id = ? AND is_required = 1
  ORDER BY course_code
");
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["error"=>"SQL_PREPARE_FAILED","details"=>$conn->error]);
  exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$courses = [];
while ($c = $res->fetch_assoc()) {
  $c["credits"] = (int)($c["credits"] ?? 0);
  $courses[] = $c;
}
$stmt->close();

echo json_encode([
  "major" => ["id" => null, "name" => $majorName],
  "courses" => $courses
], JSON_UNESCAPED_UNICODE);
