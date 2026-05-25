<?php

require_once __DIR__ . "/../_phase3_bootstrap.php";
require_once __DIR__ . "/team_lib.php";

require_student();
$student_id = (int)($_SESSION["user_id"] ?? 0);

/*
|--------------------------------------------------------------------------
| Helper: أرشف شات الفريق إذا كل المهام الموزعة على الفريق انسلّمت
|--------------------------------------------------------------------------
*/
function phase3_archive_team_chat_if_completed(mysqli $conn, int $project_id, int $team_id): void {
  $sql = "
    SELECT
      COUNT(*) AS total_assignments,
      SUM(
        CASE
          WHEN EXISTS (
            SELECT 1
            FROM phase3_task_submissions s
            WHERE s.project_id = a.project_id
              AND s.task_id = a.task_id
              AND s.student_id = a.student_id
          ) THEN 1 ELSE 0
        END
      ) AS submitted_count
    FROM phase3_task_assignments a
    JOIN phase3_team_members m
      ON m.project_id = a.project_id
     AND m.student_id = a.student_id
    WHERE a.project_id=?
      AND m.team_id=?
  ";

  $st = $conn->prepare($sql);
  if (!$st) {
    return;
  }

  $st->bind_param("ii", $project_id, $team_id);
  $st->execute();
  $row = $st->get_result()->fetch_assoc();
  $st->close();

  $total = (int)($row["total_assignments"] ?? 0);
  $submitted = (int)($row["submitted_count"] ?? 0);

  if ($total > 0 && $submitted >= $total) {
    $up = $conn->prepare("
      UPDATE chat_threads
      SET is_archived=1
      WHERE phase_source='phase3'
        AND phase3_project_id=?
        AND team_id=?
        AND is_team_chat=1
    ");
    if (!$up) {
      return;
    }

    $up->bind_param("ii", $project_id, $team_id);
    $up->execute();
    $up->close();
  }
}

$task_id  = (int)($_POST["task_id"] ?? 0);
$repo_url = trim((string)($_POST["repo_url"] ?? ""));
$notes    = trim((string)($_POST["notes"] ?? ""));

if ($task_id <= 0) {
  json_out(["ok" => false, "error" => "task_id required"], 400);
}

$as = $conn->prepare("
  SELECT a.project_id, a.task_id, t.task_code, t.role_name
  FROM phase3_task_assignments a
  JOIN phase3_tasks t ON t.id = a.task_id
  WHERE a.task_id=? AND a.student_id=?
  LIMIT 1
");
if (!$as) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$as->bind_param("ii", $task_id, $student_id);
$as->execute();
$assigned = $as->get_result()->fetch_assoc();
$as->close();

if (!$assigned) {
  json_out(["ok" => false, "error" => "TASK_NOT_ASSIGNED"], 403);
}

$project_id = (int)$assigned["project_id"];

$team_id = phase3_get_team_id_for_student_task($conn, $project_id, $student_id, $task_id);
if ($team_id <= 0) {
  json_out(["ok" => false, "error" => "TEAM_NOT_FOUND_FOR_STUDENT"], 400);
}

$zip_path = null;
$dest = null;


if (!isset($_FILES["zip"]) && isset($_FILES["artifact"])) {
  $_FILES["zip"] = $_FILES["artifact"];
}

if (isset($_FILES["zip"]) && $_FILES["zip"]["error"] === UPLOAD_ERR_OK) {
  $uploads = realpath(__DIR__ . "/../../") . "/uploads/phase3";
  if (!is_dir($uploads)) {
    mkdir($uploads, 0777, true);
  }

  $name = "p{$project_id}_team{$team_id}_t{$task_id}_u{$student_id}_" . time() . ".zip";
  $dest = $uploads . "/" . $name;

  if (!move_uploaded_file($_FILES["zip"]["tmp_name"], $dest)) {
    json_out(["ok" => false, "error" => "ZIP_SAVE_FAILED"], 500);
  }

  $zip_path = "uploads/phase3/" . $name;
}


if (($zip_path === null || $zip_path === "") && $repo_url === "") {
  json_out(["ok" => false, "error" => "MISSING_REPO_OR_ZIP"], 400);
}

$evidence_json = json_encode([
  "review_mode" => "COMPANY",
  "team_id" => $team_id,
  "zip_path" => $zip_path,
  "notes" => $notes,
  "task_code" => $assigned["task_code"],
  "role_name" => $assigned["role_name"],
], JSON_UNESCAPED_UNICODE);

$grade_json = json_encode([
  "review_mode" => "COMPANY",
  "team_id" => $team_id,
  "status" => "PENDING_COMPANY_REVIEW"
], JSON_UNESCAPED_UNICODE);




$zip_path_db = (string)($zip_path ?? "");


$submitted_at = date("Y-m-d H:i:s");
$score = 0;
$decision = "PENDING";

/*
|--------------------------------------------------------------------------
| منع التكرار: إذا الطالب سلّم نفس المهمة قبل هيك
|--------------------------------------------------------------------------
*/
$chkSub = $conn->prepare("
  SELECT id
  FROM phase3_task_submissions
  WHERE project_id=? AND team_id=? AND task_id=? AND student_id=?
  LIMIT 1
");
if (!$chkSub) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$chkSub->bind_param("iiii", $project_id, $team_id, $task_id, $student_id);
$chkSub->execute();
$existingSubmission = $chkSub->get_result()->fetch_assoc();
$chkSub->close();

if ($existingSubmission) {
  json_out(["ok" => false, "error" => "TASK_ALREADY_SUBMITTED"], 409);
}

$ins = $conn->prepare("
  INSERT INTO phase3_task_submissions
  (project_id, team_id, task_id, student_id, repo_url, zip_path, notes, submitted_at,
   evidence_json, grade_json, score, decision)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
");
if (!$ins) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$ins->bind_param(
  "iiiissssssis",
  $project_id,
  $team_id,
  $task_id,
  $student_id,
  $repo_url,
  $zip_path_db,
  $notes,
  $submitted_at,
  $evidence_json,
  $grade_json,
  $score,
  $decision
);

if (!$ins->execute()) {
  $ins->close();
  json_out(["ok" => false, "error" => "INSERT_FAILED", "mysql_error" => $ins->error], 500);
}
$ins->close();

$upA = $conn->prepare("
  UPDATE phase3_task_assignments
  SET status='WAITING_COMPANY_REVIEW',
      submitted_at=NOW()
  WHERE task_id=? AND student_id=?
");
if ($upA) {
  $upA->bind_param("ii", $task_id, $student_id);
  $upA->execute();
  $upA->close();
}

$upT = $conn->prepare("
  UPDATE phase3_tasks
  SET status='SUBMITTED'
  WHERE id=? AND project_id=?
");
if ($upT) {
  $upT->bind_param("ii", $task_id, $project_id);
  $upT->execute();
  $upT->close();
}

$upP = $conn->prepare("
  UPDATE partner_phase3_projects
  SET status='REVIEWING'
  WHERE id=? AND status IN ('PUBLISHED', 'MATCHED')
");
if ($upP) {
  $upP->bind_param("i", $project_id);
  $upP->execute();
  $upP->close();
}

/*
|--------------------------------------------------------------------------
| إذا كل الفريق سلّم، أخفِ شات الفريق
|--------------------------------------------------------------------------
*/
phase3_archive_team_chat_if_completed($conn, $project_id, $team_id);

json_out([
  "ok" => true,
  "project_id" => $project_id,
  "team_id" => $team_id,
  "task_id" => $task_id,
  "decision" => "PENDING",
  "review_mode" => "COMPANY",
  "message" => "SUBMITTED_WAITING_COMPANY_REVIEW"
]);