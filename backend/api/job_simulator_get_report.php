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

$simulation_id = (int)($_GET["simulation_id"] ?? 0);

if ($simulation_id <= 0) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "INVALID_SIMULATION_ID"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  البيانات الرئيسية
*/
$stmt = $conn->prepare("
  SELECT id, role_key, status, final_score, verdict, cv_file_path, github_url, created_at
  FROM job_simulations
  WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $simulation_id, $user_id);
$stmt->execute();
$sim = $stmt->get_result()->fetch_assoc();

if (!$sim) {
  http_response_code(404);
  echo json_encode([
    "ok" => false,
    "error" => "SIMULATION_NOT_FOUND"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  CV analysis
*/
$stmt2 = $conn->prepare("
  SELECT cv_score, extracted_skills_json, strengths_json, weaknesses_json
  FROM job_simulation_cv_analysis
  WHERE simulation_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$stmt2->bind_param("i", $simulation_id);
$stmt2->execute();
$cv = $stmt2->get_result()->fetch_assoc();

/*
  Project analysis
*/
$stmt3 = $conn->prepare("
  SELECT project_url, project_score, strengths_json, weaknesses_json
  FROM job_simulation_project_analysis
  WHERE simulation_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$stmt3->bind_param("i", $simulation_id);
$stmt3->execute();
$project = $stmt3->get_result()->fetch_assoc();

/*
  Scores
*/
$stmt4 = $conn->prepare("
  SELECT cv_score, project_score, skill_match_score, progress_score, final_score
  FROM job_simulation_scores
  WHERE simulation_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$stmt4->bind_param("i", $simulation_id);
$stmt4->execute();
$scores = $stmt4->get_result()->fetch_assoc();

/*
  Roadmap
*/
$stmt5 = $conn->prepare("
  SELECT roadmap_json
  FROM job_simulation_roadmaps
  WHERE simulation_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$stmt5->bind_param("i", $simulation_id);
$stmt5->execute();
$roadmapRow = $stmt5->get_result()->fetch_assoc();

echo json_encode([
  "ok" => true,
  "simulation" => $sim,
  "cv_analysis" => [
    "cv_score" => isset($cv["cv_score"]) ? (float)$cv["cv_score"] : 0,
    "skills" => json_decode($cv["extracted_skills_json"] ?? "[]", true),
    "strengths" => json_decode($cv["strengths_json"] ?? "[]", true),
    "weaknesses" => json_decode($cv["weaknesses_json"] ?? "[]", true)
  ],
  "project_analysis" => [
    "project_url" => $project["project_url"] ?? "",
    "project_score" => isset($project["project_score"]) ? (float)$project["project_score"] : 0,
    "strengths" => json_decode($project["strengths_json"] ?? "[]", true),
    "weaknesses" => json_decode($project["weaknesses_json"] ?? "[]", true)
  ],
  "scores" => [
    "cv_score" => isset($scores["cv_score"]) ? (float)$scores["cv_score"] : 0,
    "project_score" => isset($scores["project_score"]) ? (float)$scores["project_score"] : 0,
    "skill_match_score" => isset($scores["skill_match_score"]) ? (float)$scores["skill_match_score"] : 0,
    "progress_score" => isset($scores["progress_score"]) ? (float)$scores["progress_score"] : 0,
    "final_score" => isset($scores["final_score"]) ? (float)$scores["final_score"] : 0
  ],
  "roadmap" => json_decode($roadmapRow["roadmap_json"] ?? "{}", true)
], JSON_UNESCAPED_UNICODE);
exit;