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

if ($simulation_id <= 0) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_SIMULATION_ID"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = $conn->prepare("
  SELECT id, user_id, role_key, cv_file_path, github_url
  FROM job_simulations
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

$stmt->bind_param("ii", $simulation_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if (!$row) {
  http_response_code(404);
  echo json_encode([
    "ok" => false,
    "error" => "SIMULATION_NOT_FOUND"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

if (empty($row["cv_file_path"])) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "CV_NOT_UPLOADED"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$cvAbsPath = dirname(__DIR__) . "/" . $row["cv_file_path"];

$payload = [
  "cv_path" => $cvAbsPath,
  "role_key" => $row["role_key"],
  "project_url" => $row["github_url"] ?? "",
  "progress_score" => 50
];

$ch = curl_init("http://127.0.0.1:5006/job_simulator/analyze");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "AI_SERVICE_FAILED",
    "details" => $curlError
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_AI_RESPONSE",
    "raw" => $response
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  حذف النتائج القديمة إذا أعاد الطالب التحليل أكثر من مرة
*/
$stmtDel1 = $conn->prepare("DELETE FROM job_simulation_cv_analysis WHERE simulation_id = ?");
$stmtDel1->bind_param("i", $simulation_id);
$stmtDel1->execute();

$stmtDel2 = $conn->prepare("DELETE FROM job_simulation_scores WHERE simulation_id = ?");
$stmtDel2->bind_param("i", $simulation_id);
$stmtDel2->execute();

$stmtDel3 = $conn->prepare("DELETE FROM job_simulation_roadmaps WHERE simulation_id = ?");
$stmtDel3->bind_param("i", $simulation_id);
$stmtDel3->execute();

/*
  حفظ تحليل CV
*/
$stmt2 = $conn->prepare("
  INSERT INTO job_simulation_cv_analysis
  (simulation_id, extracted_text, extracted_skills_json, strengths_json, weaknesses_json, cv_score)
  VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt2) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "PREPARE_FAILED_CV",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$skillsJson = json_encode($data["skills"] ?? [], JSON_UNESCAPED_UNICODE);
$strengthsJson = json_encode($data["cv_strengths"] ?? [], JSON_UNESCAPED_UNICODE);
$weaknessesJson = json_encode($data["cv_weaknesses"] ?? [], JSON_UNESCAPED_UNICODE);
$emptyText = "";
$cvScore = (float)($data["cv_score"] ?? 0);

$stmt2->bind_param("issssd", $simulation_id, $emptyText, $skillsJson, $strengthsJson, $weaknessesJson, $cvScore);
$stmt2->execute();

/*
  تحديث تحليل المشروع
*/
$stmt3 = $conn->prepare("
  UPDATE job_simulation_project_analysis
  SET project_score = ?, strengths_json = ?, weaknesses_json = ?
  WHERE simulation_id = ?
");

if ($stmt3) {
  $projectScore = (float)($data["project_score"] ?? 0);
  $projectStrengths = json_encode($data["project_strengths"] ?? [], JSON_UNESCAPED_UNICODE);
  $projectWeaknesses = json_encode($data["project_weaknesses"] ?? [], JSON_UNESCAPED_UNICODE);
  $stmt3->bind_param("dssi", $projectScore, $projectStrengths, $projectWeaknesses, $simulation_id);
  $stmt3->execute();
}

/*
  حفظ السكورات
*/
$stmt4 = $conn->prepare("
  INSERT INTO job_simulation_scores
  (simulation_id, cv_score, project_score, skill_match_score, progress_score, final_score)
  VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt4) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "PREPARE_FAILED_SCORES",
    "details" => $conn->error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$projectScore = (float)($data["project_score"] ?? 0);
$skillMatch = (float)($data["skill_match_score"] ?? 0);
$progressScore = (float)($data["progress_score"] ?? 0);
$finalScore = (float)($data["final_score"] ?? 0);

$stmt4->bind_param("iddddd", $simulation_id, $cvScore, $projectScore, $skillMatch, $progressScore, $finalScore);
$stmt4->execute();

/*
  حفظ roadmap
*/
$stmt5 = $conn->prepare("
  INSERT INTO job_simulation_roadmaps (simulation_id, roadmap_json)
  VALUES (?, ?)
");

if ($stmt5) {
  $roadmapJson = json_encode($data["roadmap"] ?? [], JSON_UNESCAPED_UNICODE);
  $stmt5->bind_param("is", $simulation_id, $roadmapJson);
  $stmt5->execute();
}

/*
  تحديث المحاكاة الرئيسية
*/
$stmt6 = $conn->prepare("
  UPDATE job_simulations
  SET status = 'completed', final_score = ?, verdict = ?, updated_at = NOW()
  WHERE id = ? AND user_id = ?
");

if ($stmt6) {
  $verdict = $data["verdict"] ?? "Unknown";
  $stmt6->bind_param("dsii", $finalScore, $verdict, $simulation_id, $user_id);
  $stmt6->execute();
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;