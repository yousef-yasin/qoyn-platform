<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_company();

$company_id = (int)$_SESSION["user_id"];
$student_id = (int)($_POST["student_id"] ?? 0);
$team_id = (int)($_POST["team_id"] ?? 0);
$is_team_chat = $team_id > 0 ? 1 : 0;

$phase_source = trim((string)($_POST["phase_source"] ?? ""));
$phase2_submission_id = (int)($_POST["phase2_submission_id"] ?? 0);
$phase3_project_id = (int)($_POST["phase3_project_id"] ?? 0);
$phase3_task_id = (int)($_POST["phase3_task_id"] ?? 0);

/*
|--------------------------------------------------------------------------
| validation
|--------------------------------------------------------------------------
| إذا شات فردي لازم يكون student_id موجود
| إذا شات تيم يكفي team_id
|--------------------------------------------------------------------------
*/
if ($is_team_chat !== 1 && $student_id <= 0) {
  chat_json(["ok"=>false,"error"=>"INVALID_STUDENT"], 400);
}

$checkSql = "
SELECT id FROM chat_threads
WHERE company_id=?
  AND COALESCE(student_id,0)=?
  AND COALESCE(team_id,0)=?
  AND COALESCE(phase2_submission_id,0)=?
  AND COALESCE(phase3_project_id,0)=?
  AND COALESCE(phase3_task_id,0)=?
  AND is_team_chat=?
LIMIT 1
";
$chk = $conn->prepare($checkSql);
if (!$chk) {
  chat_json(["ok"=>false,"error"=>"PREPARE_CHECK_FAILED","mysql_error"=>$conn->error], 500);
}

$chk->bind_param(
  "iiiiiii",
  $company_id,
  $student_id,
  $team_id,
  $phase2_submission_id,
  $phase3_project_id,
  $phase3_task_id,
  $is_team_chat
);
$chk->execute();
$r = $chk->get_result()->fetch_assoc();
$chk->close();

if ($r) {
  chat_json([
    "ok"=>true,
    "thread_id"=>(int)$r["id"],
    "existing"=>true,
    "team_id"=>$team_id,
    "is_team_chat"=>$is_team_chat
  ]);
}

$ins = $conn->prepare("
  INSERT INTO chat_threads
  (company_id, student_id, team_id, is_team_chat, phase_source, phase2_submission_id, phase3_project_id, phase3_task_id, created_by, last_message_at)
  VALUES (?,?,?,?,?,?,?,?,?, NOW())
");
if (!$ins) {
  chat_json(["ok"=>false,"error"=>"PREPARE_INSERT_FAILED","mysql_error"=>$conn->error], 500);
}

$ins->bind_param(
  "iiiisiiii",
  $company_id,
  $student_id,
  $team_id,
  $is_team_chat,
  $phase_source,
  $phase2_submission_id,
  $phase3_project_id,
  $phase3_task_id,
  $company_id
);
$ins->execute();

if ($ins->error) {
  chat_json(["ok"=>false,"error"=>"INSERT_FAILED","mysql_error"=>$ins->error], 500);
}

$thread_id = (int)$conn->insert_id;
$ins->close();

chat_json([
  "ok"=>true,
  "thread_id"=>$thread_id,
  "existing"=>false,
  "team_id"=>$team_id,
  "is_team_chat"=>$is_team_chat
]);