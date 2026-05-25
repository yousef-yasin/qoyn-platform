<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

if (!defined("AI_BASE")) define("AI_BASE", "http://127.0.0.1:5006");

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

// دعم JSON body
$raw = file_get_contents("php://input");
if ($raw) {
  $j = json_decode($raw, true);
  if (is_array($j)) {
    foreach ($j as $k => $v) {
      if (!isset($_POST[$k])) $_POST[$k] = $v;
    }
  }
}

$project_id = (int)($_POST["project_id"] ?? $_GET["project_id"] ?? 0);
if ($project_id <= 0) {
  json_out(["ok"=>false,"error"=>"project_id required"], 400);
}

// 1) المشروع
$pq = $conn->prepare("
  SELECT id, capstone_title, capstone_description, architect_json, tasks_json, match_json, status
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
if (!$pq) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);

$pq->bind_param("ii", $project_id, $partner_id);
$pq->execute();
$proj = $pq->get_result()->fetch_assoc();

if (!$proj) {
  json_out(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], 404);
}

// 2) المهام
$tq = $conn->prepare("
  SELECT
    id,
    project_id,
    task_code,
    role_key,
    role_name,
    description,
    assigned_user_id,
    status
  FROM phase3_tasks
  WHERE project_id=?
  ORDER BY id ASC
");
if (!$tq) json_out(["ok"=>false,"error"=>"PREPARE_TASKS_FAILED","mysql_error"=>$conn->error], 500);

$tq->bind_param("i", $project_id);
$tq->execute();
$tasks = $tq->get_result()->fetch_all(MYSQLI_ASSOC);

// 3) التسليمات + اسم الطالب
$sq = $conn->prepare("
  SELECT
    s.id,
    s.task_id,
    s.student_id,
    u.full_name AS student_name,
    s.score,
    s.decision,
    s.partner_rating,
    s.partner_comment,
    s.selection_status,
    s.grade_json,
    s.evidence_json,
    s.repo_url,
    s.zip_path,
    s.submitted_at
  FROM phase3_task_submissions s
  JOIN users u ON u.id = s.student_id
  WHERE s.project_id=?
  ORDER BY s.id DESC
");
if (!$sq) json_out(["ok"=>false,"error"=>"PREPARE_SUBS_FAILED","mysql_error"=>$conn->error], 500);

$sq->bind_param("i", $project_id);
$sq->execute();
$subs = $sq->get_result()->fetch_all(MYSQLI_ASSOC);

$payload = [
  "project" => [
    "id" => (int)$proj["id"],
    "title" => $proj["capstone_title"],
    "description" => $proj["capstone_description"],
    "status" => $proj["status"],
    "architect" => json_decode($proj["architect_json"] ?? "{}", true),
    "tasks_json" => json_decode($proj["tasks_json"] ?? "[]", true),
    "match_json" => json_decode($proj["match_json"] ?? "{}", true),
  ],
  "tasks" => array_map(function($t){
    return [
      "id" => (int)$t["id"],
      "project_id" => (int)$t["project_id"],
      "task_code" => $t["task_code"],
      "role_key" => $t["role_key"],
      "role_name" => $t["role_name"],
      "description" => $t["description"],
      "assigned_user_id" => (int)($t["assigned_user_id"] ?? 0),
      "status" => $t["status"],
    ];
  }, $tasks),
  "submissions" => array_map(function($s){
    return [
      "submission_id" => (int)$s["id"],
      "task_id" => (int)$s["task_id"],
      "student_id" => (int)$s["student_id"],
      "student_name" => $s["student_name"],
      "score" => is_null($s["score"]) ? null : (float)$s["score"],
      "decision" => (string)($s["decision"] ?? ""),
      "partner_rating" => is_null($s["partner_rating"]) ? null : (int)$s["partner_rating"],
      "partner_comment" => (string)($s["partner_comment"] ?? ""),
      "selection_status" => (string)($s["selection_status"] ?? ""),
      "grade" => json_decode($s["grade_json"] ?? "{}", true),
      "evidence" => json_decode($s["evidence_json"] ?? "{}", true),
      "repo_url" => $s["repo_url"],
      "zip_path" => $s["zip_path"],
      "submitted_at" => $s["submitted_at"],
    ];
  }, $subs),
];

$ai = ai_post_json(AI_BASE . "/phase3/final_report", $payload, 300);
if (empty($ai["ok"])) {
  json_out(["ok"=>false,"error"=>"AI_FINAL_REPORT_FAILED","ai"=>$ai], 502);
}

$aiJson = $ai["json"] ?? [];
$report = $aiJson["report"] ?? $aiJson;

if (!is_array($report)) {
  json_out(["ok"=>false,"error"=>"INVALID_AI_REPORT"], 500);
}

$report_json = json_encode($report, JSON_UNESCAPED_UNICODE);

$up = $conn->prepare("
  UPDATE partner_phase3_projects
  SET final_report_json=?
  WHERE id=? AND partner_user_id=?
");
if (!$up) json_out(["ok"=>false,"error"=>"PREPARE_UPDATE_FAILED","mysql_error"=>$conn->error], 500);

$up->bind_param("sii", $report_json, $project_id, $partner_id);
$up->execute();

json_out([
  "ok"=>true,
  "project_id"=>$project_id,
  "report"=>$report
]);