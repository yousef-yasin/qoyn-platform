<?php
// utbn-backend/api/phase2_submit.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE);
  exit;
}

$in = json_decode(file_get_contents("php://input"), true) ?: [];
$project_id = (int)($in["project_id"] ?? 0);
$answers = $in["answers"] ?? null;

if ($project_id <= 0 || !is_array($answers)) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"INVALID_INPUT"], JSON_UNESCAPED_UNICODE);
  exit;
}

// load project
$st = $conn->prepare("SELECT title, description, tasks_json, rubric_json, base_coins, pass_score, ai_model FROM phase2_projects WHERE id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i", $project_id);
$st->execute();
$rs = $st->get_result();
$row = $rs->fetch_assoc();
$st->close();

if (!$row) {
  http_response_code(404);
  echo json_encode(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

$project = [
  "title" => (string)$row["title"],
  "description" => (string)$row["description"],
  "tasks" => json_decode($row["tasks_json"], true),
  "rubric" => json_decode($row["rubric_json"], true),
  "pass_score" => (int)$row["pass_score"]
];
$base_coins = (int)$row["base_coins"];
$ai_model = (string)($row["ai_model"] ?? "");

// call AI grade
$payload = ["project"=>$project, "answers"=>$answers];

$ch = curl_init("http://127.0.0.1:5005/phase2/grade");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 240,
]);
$resp = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($resp === false) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"AI_CURL_FAILED","detail"=>$err], JSON_UNESCAPED_UNICODE);
  exit;
}
$data = json_decode($resp, true);
if ($code !== 200 || !$data || empty($data["ok"])) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"AI_BAD_RESPONSE","http"=>$code,"raw"=>$resp], JSON_UNESCAPED_UNICODE);
  exit;
}

$grade = $data["grade"] ?? [];
$score = (int)($grade["score"] ?? 0);
$decision = (string)($grade["decision"] ?? "NEEDS_FIX");
$feedback = (string)($grade["feedback"] ?? "");
$fixes_json = json_encode($grade["fixes"] ?? [], JSON_UNESCAPED_UNICODE);

// coins logic
$earned = (int)round($base_coins * max(0, min(100, $score)) / 100.0);
if ($decision === "FAIL") $earned = 0;
elseif ($decision === "NEEDS_FIX") $earned = (int)round($earned * 0.30);

// save submission (now with grade columns)
$answers_json = json_encode($answers, JSON_UNESCAPED_UNICODE);
$status = "graded";

$st = $conn->prepare("
  INSERT INTO phase2_submissions (user_id, project_id, answers_json, status, score, decision, coins_awarded, feedback, fixes_json)
  VALUES (?,?,?,?,?,?,?,?,?)
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }

$st->bind_param("iissisiss", $user_id, $project_id, $answers_json, $status, $score, $decision, $earned, $feedback, $fixes_json);
$st->execute();
$submission_id = (int)$st->insert_id;
$st->close();

// add coins to coins_ledger (مطابق لجدولك 100%)
$reason = "phase2_project";
$ref_type = "phase2_submission";
$st = $conn->prepare("INSERT INTO coins_ledger (user_id, amount, reason, ref_type, ref_id, created_at) VALUES (?,?,?,?,?,NOW())");
if($st){
  $st->bind_param("iisii", $user_id, $earned, $reason, $ref_type, $submission_id);
  @$st->execute();
  @$st->close();
}
if ($earned > 0) {
  $stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
  if ($stu) {
    $stu->bind_param("ii", $earned, $user_id);
    $stu->execute();
    $stu->close();
  }
}
echo json_encode([
  "ok"=>true,
  "submission_id"=>$submission_id,
  "score"=>$score,
  "decision"=>$decision,
  "coins_awarded"=>$earned,
  "feedback"=>$feedback,
  "fixes"=>($grade["fixes"] ?? []),
  "ai_model"=>$ai_model
], JSON_UNESCAPED_UNICODE);