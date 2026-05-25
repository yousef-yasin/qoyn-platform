<?php
// utbn-backend/api/phase3/partner_projects.php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$q = $conn->prepare("
  SELECT
    p.id AS project_id,
    p.capstone_title,
    p.status,
    p.created_at,
    CASE
      WHEN p.final_report_json IS NULL OR p.final_report_json = '' THEN 0
      ELSE 1
    END AS has_report,
    (SELECT COUNT(*) FROM phase3_tasks t WHERE t.project_id=p.id) AS tasks_count,
    (SELECT COUNT(*) FROM phase3_task_submissions s WHERE s.project_id=p.id) AS submissions_count
  FROM partner_phase3_projects p
  WHERE p.partner_user_id=?
  ORDER BY p.created_at DESC
");
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
$q->bind_param("i", $partner_id);
$q->execute();

json_out(["ok"=>true, "projects"=>$q->get_result()->fetch_all(MYSQLI_ASSOC)]);
