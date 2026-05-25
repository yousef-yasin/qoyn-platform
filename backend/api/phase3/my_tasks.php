<?php

require_once __DIR__ . "/../_phase3_bootstrap.php";

require_student();
$student_id = (int)($_SESSION["user_id"] ?? 0);

$sql = "
SELECT
  a.id AS assignment_id,
  a.project_id,
  a.task_id,
  a.student_id,
  a.status AS assignment_status,
  a.match_score,
  a.reason,
  a.submitted_at,
  a.reviewed_at,
  a.selected_at,

  t.task_code,
  t.task_order,
  t.role_key,
  t.role_name,
  t.description,
  t.skills_json,
  t.acceptance_json,
  t.dependencies_json,
  t.status AS task_status,

  p.capstone_title,
  p.capstone_description,
  p.status AS project_status,

  u.full_name AS company_name,

  s.id AS submission_id,
  s.repo_url,
  s.zip_path,
  s.notes,
  s.score,
  s.decision,
  s.partner_rating,
  s.partner_comment,
  s.partner_reviewed_at,
  s.selection_status,
  s.submitted_at AS latest_submitted_at

FROM phase3_task_assignments a
JOIN phase3_tasks t
  ON t.id = a.task_id
JOIN partner_phase3_projects p
  ON p.id = a.project_id
JOIN users u
  ON u.id = p.partner_user_id
LEFT JOIN phase3_task_submissions s
  ON s.id = (
    SELECT ss.id
    FROM phase3_task_submissions ss
    WHERE ss.task_id = a.task_id
      AND ss.student_id = a.student_id
    ORDER BY ss.submitted_at DESC, ss.id DESC
    LIMIT 1
  )
WHERE a.student_id = ?
  AND p.status IN ('PUBLISHED','REVIEWING','FINAL')
ORDER BY p.id DESC, t.task_order ASC, t.id ASC
";

$q = $conn->prepare($sql);
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);

$q->bind_param("i", $student_id);
$q->execute();

$rows = [];
$res = $q->get_result();
while ($r = $res->fetch_assoc()) {
  $rows[] = $r;
}

json_out(["ok"=>true, "tasks"=>$rows]);