<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$project_id    = (int)($_POST["project_id"] ?? 0);
$task_id       = (int)($_POST["task_id"] ?? 0);
$student_id    = (int)($_POST["student_id"] ?? 0);
$submission_id = (int)($_POST["submission_id"] ?? 0);

if ($project_id <= 0 || $task_id <= 0 || $student_id <= 0 || $submission_id <= 0) {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}

$chk = $conn->prepare("
  SELECT id
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
$chk->bind_param("ii", $project_id, $partner_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
  json_out(["ok"=>false,"error"=>"NOT_YOUR_PROJECT"], 403);
}

$conn->begin_transaction();

try {
  $u1 = $conn->prepare("
    UPDATE phase3_task_submissions
    SET selection_status='NOT_SELECTED'
    WHERE task_id=?
  ");
  $u1->bind_param("i", $task_id);
  $u1->execute();

  $u2 = $conn->prepare("
    UPDATE phase3_task_assignments
    SET status='NOT_SELECTED'
    WHERE task_id=?
  ");
  $u2->bind_param("i", $task_id);
  $u2->execute();

  $u3 = $conn->prepare("
    UPDATE phase3_tasks
    SET status='NOT_SELECTED'
    WHERE id=?
  ");
  $u3->bind_param("i", $task_id);
  $u3->execute();

  $u4 = $conn->prepare("
    UPDATE phase3_task_submissions
    SET selection_status='SELECTED'
    WHERE id=?
  ");
  $u4->bind_param("i", $submission_id);
  $u4->execute();

  $u5 = $conn->prepare("
    UPDATE phase3_task_assignments
    SET status='SELECTED',
        selected_at=NOW()
    WHERE task_id=? AND student_id=?
  ");
  $u5->bind_param("ii", $task_id, $student_id);
  $u5->execute();

  $u6 = $conn->prepare("
    UPDATE phase3_tasks
    SET status='SELECTED',
        assigned_user_id=?
    WHERE id=?
  ");
  $u6->bind_param("ii", $student_id, $task_id);
  $u6->execute();

  $ins = $conn->prepare("
    INSERT INTO phase3_task_selection
    (project_id, task_id, student_id, submission_id, selected_by_partner_id)
    VALUES (?,?,?,?,?)
    ON DUPLICATE KEY UPDATE
      student_id=VALUES(student_id),
      submission_id=VALUES(submission_id),
      selected_by_partner_id=VALUES(selected_by_partner_id)
  ");
  $ins->bind_param("iiiii", $project_id, $task_id, $student_id, $submission_id, $partner_id);
  $ins->execute();

  $conn->commit();
  json_out(["ok"=>true, "selected_student_id"=>$student_id, "task_id"=>$task_id]);
} catch (Throwable $e) {
  $conn->rollback();
  json_out(["ok"=>false, "error"=>"SELECT_FAILED", "message"=>$e->getMessage()], 500);
}