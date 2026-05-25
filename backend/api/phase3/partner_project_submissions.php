<?php
// utbn-backend/api/phase3/partner_project_submissions.php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);
$project_id = (int)($_GET["project_id"] ?? 0);
if ($project_id <= 0) json_out(["ok"=>false,"error"=>"project_id required"], 400);

// تأكد انه مشروعك
$chk = $conn->prepare("SELECT id, capstone_title FROM partner_phase3_projects WHERE id=? AND partner_user_id=? LIMIT 1");
if (!$chk) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
$chk->bind_param("ii", $project_id, $partner_id);
$chk->execute();
$proj = $chk->get_result()->fetch_assoc();
if (!$proj) json_out(["ok"=>false,"error"=>"NOT_YOUR_PROJECT"], 403);

$sql = "
SELECT
  s.id AS submission_id,
  s.project_id,
  s.team_id,
  tm.team_no,
  pt.team_name,
  s.task_id,
  s.student_id,
  u.full_name AS student_name,
  t.task_code,
  t.role_name,
  s.repo_url,
  s.zip_path,
  s.notes,
  s.submitted_at,
  s.score,
  s.decision,
  r.rating AS partner_rating,
  r.final_decision AS partner_final_decision,
  r.comment AS partner_comment,
  r.created_at AS partner_reviewed_at
FROM phase3_task_submissions s
JOIN phase3_tasks t ON t.id=s.task_id
JOIN users u ON u.id=s.student_id
LEFT JOIN phase3_teams pt ON pt.id=s.team_id
LEFT JOIN phase3_teams tm ON tm.id=s.team_id
LEFT JOIN phase3_partner_reviews r
  ON r.project_id=s.project_id
 AND r.task_id=s.task_id
 AND r.student_id=s.student_id
WHERE s.project_id=?
ORDER BY tm.team_no ASC, s.submitted_at DESC, s.id DESC
";

$q = $conn->prepare($sql);
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
$q->bind_param("i", $project_id);
$q->execute();

json_out([
  "ok"=>true,
  "project"=>["project_id"=>$project_id, "capstone_title"=>$proj["capstone_title"]],
  "submissions"=>$q->get_result()->fetch_all(MYSQLI_ASSOC)
]);
