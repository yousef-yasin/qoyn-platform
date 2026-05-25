<?php
require_once __DIR__ . "/_boot.php";

$project_id = (int)($_POST["project_id"] ?? 0);
$student_id = (int)($_SESSION["user_id"] ?? 0);

if ($project_id <= 0) {
  json_out(["ok" => false, "error" => "INVALID_PROJECT_ID"], 400);
}

/* 1) هل موجود challenge أصلاً؟ */
$check = $conn->prepare("
  SELECT * 
  FROM phase3_level2_challenges
  WHERE project_id = ? AND student_id = ?
  ORDER BY id DESC
  LIMIT 1
");

if (!$check) {
  json_out([
    "ok" => false,
    "error" => "CHECK_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$check->bind_param("ii", $project_id, $student_id);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
$check->close();

if ($exists) {
  json_out(["ok" => true, "challenge" => $exists]);
}

/* 2) هات بيانات المشروع فقط */
$sql = "
SELECT 
  p.id AS project_id,
  p.capstone_title,
  p.capstone_description
FROM partner_phase3_projects p
WHERE p.id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
  json_out([
    "ok" => false,
    "error" => "SQL_PREPARE_FAILED",
    "sql_error" => $conn->error,
    "sql" => $sql
  ], 500);
}

$stmt->bind_param("i", $project_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
  json_out(["ok" => false, "error" => "PROJECT_NOT_FOUND"], 404);
}

/* 3) اطلب challenge من Python AI service */
$task_id = 0;
$role_key = "general";

$payload = [
  "project_id" => $project_id,
  "student_id" => $student_id,
  "task_id" => $task_id,
  "role_key" => $role_key,
  "project_title" => $row["capstone_title"] ?? "",
  "project_description" => $row["capstone_description"] ?? ""
];

$ch = curl_init("http://127.0.0.1:5006/phase3/level2/generate");
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 120
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($response === false || $curlErr) {
  json_out([
    "ok" => false,
    "error" => "AI_SERVICE_UNREACHABLE",
    "details" => $curlErr
  ], 500);
}

$ai = json_decode($response, true);

if (!$ai || empty($ai["ok"]) || empty($ai["challenge"])) {
  json_out([
    "ok" => false,
    "error" => "AI_GENERATION_FAILED",
    "raw_response" => $response,
    "http_code" => $httpCode
  ], 500);
}

$g = $ai["challenge"];

$challenge_type = trim((string)($g["challenge_type"] ?? "bug"));
$title = trim((string)($g["title"] ?? "Post-Delivery Bug Fix Challenge"));
$scenario_text = trim((string)($g["scenario_text"] ?? "A new issue appeared after delivery."));

$required_actions = json_encode(($g["required_actions"] ?? [
  "Analyze the issue",
  "Propose a fix",
  "Explain validation steps"
]), JSON_UNESCAPED_UNICODE);

$deliverables = json_encode(($g["deliverables"] ?? [
  "Technical explanation",
  "Fix plan",
  "Validation steps"
]), JSON_UNESCAPED_UNICODE);

$rubric = json_encode(($g["rubric"] ?? [
  "analysis" => 30,
  "technical_fix" => 40,
  "validation" => 20,
  "communication" => 10
]), JSON_UNESCAPED_UNICODE);

$difficulty = trim((string)($g["difficulty"] ?? "medium"));

$grounding = json_encode([
  "project_title" => $row["capstone_title"] ?? "",
  "project_description" => $row["capstone_description"] ?? "",
  "ai_raw" => $g
], JSON_UNESCAPED_UNICODE);
/* 4) خزّن challenge */
$ins = $conn->prepare("
  INSERT INTO phase3_level2_challenges
  (
    project_id,
    task_id,
    student_id,
    role_key,
    challenge_type,
    title,
    scenario_text,
    required_actions_json,
    deliverables_json,
    rubric_json,
    grounding_json,
    difficulty,
    status
  )
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'generated')");

if (!$ins) {
  json_out([
    "ok" => false,
    "error" => "INSERT_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$ins->bind_param(
  "iiisssssssss",
  $project_id,
  $task_id,
  $student_id,
  $role_key,
  $challenge_type,
  $title,
  $scenario_text,
  $required_actions,
  $deliverables,
  $rubric,
  $grounding,
  $difficulty
);

if (!$ins->execute()) {
  json_out([
    "ok" => false,
    "error" => "INSERT_EXECUTE_FAILED",
    "sql_error" => $ins->error
  ], 500);
}

$new_id = $ins->insert_id;
$ins->close();

/* 5) رجّع challenge الجديد */
$get = $conn->prepare("
  SELECT * 
  FROM phase3_level2_challenges
  WHERE id = ?
  LIMIT 1
");

if (!$get) {
  json_out([
    "ok" => false,
    "error" => "FETCH_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$get->bind_param("i", $new_id);
$get->execute();
$challenge = $get->get_result()->fetch_assoc();
$get->close();

json_out([
  "ok" => true,
  "challenge" => $challenge
]);