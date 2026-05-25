<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_student();
$student_id = (int)($_SESSION["user_id"] ?? 0);



$sql = "
SELECT
  s.id AS submission_id,
  s.project_id,
  s.task_id,
  s.repo_url,
  s.zip_path,
  s.notes,
  s.score,
  s.decision,
  s.grade_json,
  s.evidence_json,
  s.submitted_at,
  s.partner_rating,
  s.partner_comment,
  s.partner_reviewed_at,
  s.selection_status,

  t.task_code,
  t.role_name,

  p.capstone_title,
  u.full_name AS company_name,

  r.rating AS review_rating,
  r.final_decision AS partner_final_decision,
  r.comment AS review_comment,
  r.created_at AS review_created_at

FROM phase3_task_submissions s
JOIN phase3_tasks t ON t.id = s.task_id
JOIN partner_phase3_projects p ON p.id = s.project_id
JOIN users u ON u.id = p.partner_user_id
LEFT JOIN phase3_partner_reviews r
  ON r.project_id = s.project_id
 AND r.task_id    = s.task_id
 AND r.student_id = s.student_id
WHERE s.student_id=?
ORDER BY s.submitted_at DESC, s.id DESC
";

$q = $conn->prepare($sql);
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);

$q->bind_param("i", $student_id);
$q->execute();

$rows = [];
$res = $q->get_result();

while($r = $res->fetch_assoc()){
  $rows[] = $r;
}

json_out(["ok"=>true, "results"=>$rows]);