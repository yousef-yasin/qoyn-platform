<?php
require_once __DIR__ . "/_boot.php";

$project_id      = (int)($_POST["project_id"] ?? 0);
$challenge_id    = (int)($_POST["challenge_id"] ?? 0);
$student_id      = (int)($_SESSION["user_id"] ?? 0);
$submission_text = trim((string)($_POST["submission_text"] ?? ""));
$repo_url        = trim((string)($_POST["repo_url"] ?? ""));
$file_path       = null;

if ($project_id <= 0 || $challenge_id <= 0) {
  json_out(["ok" => false, "error" => "INVALID_INPUT"], 400);
}

if ($submission_text === "" && $repo_url === "" && empty($_FILES["solution_file"]["name"])) {
  json_out(["ok" => false, "error" => "EMPTY_SUBMISSION"], 400);
}

/* تأكد أن التحدي لهذا الطالب */
$chk = $conn->prepare("
  SELECT id, project_id, student_id
  FROM phase3_level2_challenges
  WHERE id = ? AND project_id = ? AND student_id = ?
  LIMIT 1
");

if (!$chk) {
  json_out([
    "ok" => false,
    "error" => "CHALLENGE_CHECK_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$chk->bind_param("iii", $challenge_id, $project_id, $student_id);
$chk->execute();
$challenge = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$challenge) {
  json_out(["ok" => false, "error" => "CHALLENGE_NOT_FOUND"], 404);
}

/* رفع الملف اختياري */
if (!empty($_FILES["solution_file"]["name"])) {
  $uploadDir = __DIR__ . "/../../uploads/phase3_level2/";
  if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
  }

  $safeName = time() . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["solution_file"]["name"]));
  $target = $uploadDir . $safeName;

  if (move_uploaded_file($_FILES["solution_file"]["tmp_name"], $target)) {
    $file_path = "uploads/phase3_level2/" . $safeName;
  }
}

/* خزّن submission */
$ins = $conn->prepare("
  INSERT INTO phase3_level2_submissions
  (challenge_id, project_id, student_id, submission_text, repo_url, file_path, extracted_text, status)
  VALUES (?, ?, ?, ?, ?, ?, ?, 'submitted')
");

if (!$ins) {
  json_out([
    "ok" => false,
    "error" => "SUBMISSION_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$extracted_text = $submission_text;

$ins->bind_param(
  "iiissss",
  $challenge_id,
  $project_id,
  $student_id,
  $submission_text,
  $repo_url,
  $file_path,
  $extracted_text
);

if (!$ins->execute()) {
  json_out([
    "ok" => false,
    "error" => "SUBMISSION_EXECUTE_FAILED",
    "sql_error" => $ins->error
  ], 500);
}

$submission_id = $ins->insert_id;
$ins->close();
/* index project + submission into vector store */
$projectMetaStmt = $conn->prepare("
  SELECT id, capstone_title, capstone_description
  FROM partner_phase3_projects
  WHERE id = ?
  LIMIT 1
");

if ($projectMetaStmt) {
  $projectMetaStmt->bind_param("i", $project_id);
  $projectMetaStmt->execute();
  $projectMeta = $projectMetaStmt->get_result()->fetch_assoc();
  $projectMetaStmt->close();

  $indexPayload = [
    "project_id" => $project_id,
    "student_id" => $student_id,
    "task_id" => 0,
    "role_key" => "general",
    "project_title" => $projectMeta["capstone_title"] ?? "",
    "project_description" => $projectMeta["capstone_description"] ?? "",
    "submission_text" => $submission_text
  ];

  $chIndex = curl_init("http://127.0.0.1:5006/phase3/level2/index");
  curl_setopt_array($chIndex, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($indexPayload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 120
  ]);

  $indexResp = curl_exec($chIndex);
  $indexErr  = curl_error($chIndex);
  curl_close($chIndex);

  // optional debug
  // if ($indexErr) {
  //   json_out(["ok"=>false, "error"=>"INDEX_FAILED", "details"=>$indexErr], 500);
  // }
}
/* اطلب evaluation من Python AI service */
$projectMetaStmt = $conn->prepare("
  SELECT id, capstone_title, capstone_description
  FROM partner_phase3_projects
  WHERE id = ?
  LIMIT 1
");
$projectMetaStmt->bind_param("i", $project_id);
$projectMetaStmt->execute();
$projectMeta = $projectMetaStmt->get_result()->fetch_assoc();
$projectMetaStmt->close();

$challengeStmt = $conn->prepare("
  SELECT *
  FROM phase3_level2_challenges
  WHERE id = ?
  LIMIT 1
");
$challengeStmt->bind_param("i", $challenge_id);
$challengeStmt->execute();
$challengeRow = $challengeStmt->get_result()->fetch_assoc();
$challengeStmt->close();

$payload = [
  "project" => [
    "id" => (int)$project_id,
    "title" => $projectMeta["capstone_title"] ?? "",
    "description" => $projectMeta["capstone_description"] ?? ""
  ],
  "challenge" => [
    "id" => (int)$challenge_id,
    "challenge_type" => $challengeRow["challenge_type"] ?? "",
    "title" => $challengeRow["title"] ?? "",
    "scenario_text" => $challengeRow["scenario_text"] ?? "",
    "rubric_json" => json_decode($challengeRow["rubric_json"] ?? "{}", true)
  ],
  "submission" => [
    "submission_text" => $submission_text,
    "repo_url" => $repo_url,
    "file_path" => $file_path
  ]
];

$ch = curl_init("http://127.0.0.1:5006/phase3/level2/evaluate");
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
    "error" => "AI_EVALUATION_UNREACHABLE",
    "details" => $curlErr
  ], 500);
}

$ai = json_decode($response, true);

if (!$ai || empty($ai["ok"]) || empty($ai["evaluation"])) {
  json_out([
    "ok" => false,
    "error" => "AI_EVALUATION_FAILED",
    "raw_response" => $response,
    "http_code" => $httpCode
  ], 500);
}

$ev = $ai["evaluation"];

$score = (float)($ev["score"] ?? 55);
$decision = trim((string)($ev["decision"] ?? "NEEDS_FIX"));
$feedback = trim((string)($ev["feedback_text"] ?? "Evaluation completed."));
$rubric_scores_json = json_encode(($ev["rubric_scores"] ?? []), JSON_UNESCAPED_UNICODE);
$readiness_json = json_encode(($ev["readiness"] ?? []), JSON_UNESCAPED_UNICODE);
$raw_output_json = json_encode($ev, JSON_UNESCAPED_UNICODE);
/* خزّن evaluation */
$eval = $conn->prepare("
  INSERT INTO phase3_level2_evaluations
  (challenge_id, submission_id, student_id, score, decision, feedback_text, rubric_scores_json, readiness_json, raw_output_json)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$eval) {
  json_out([
    "ok" => false,
    "error" => "EVALUATION_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$eval->bind_param(
  "iiidsssss",
  $challenge_id,
  $submission_id,
  $student_id,
  $score,
  $decision,
  $feedback,
  $rubric_scores_json,
  $readiness_json,
  $raw_output_json
);

if (!$eval->execute()) {
  json_out([
    "ok" => false,
    "error" => "EVALUATION_EXECUTE_FAILED",
    "sql_error" => $eval->error
  ], 500);
}

$evaluation_id = $eval->insert_id;
$eval->close();

/* رجّع التقييم */
$get = $conn->prepare("
  SELECT *
  FROM phase3_level2_evaluations
  WHERE id = ?
  LIMIT 1
");

if (!$get) {
  json_out([
    "ok" => false,
    "error" => "FETCH_EVALUATION_PREPARE_FAILED",
    "sql_error" => $conn->error
  ], 500);
}

$get->bind_param("i", $evaluation_id);
$get->execute();
$evaluation = $get->get_result()->fetch_assoc();
$get->close();

json_out([
  "ok" => true,
  "submission_id" => $submission_id,
  "evaluation" => $evaluation
]);