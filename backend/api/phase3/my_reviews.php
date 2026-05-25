<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_student();
$student_id = (int)($_SESSION["user_id"] ?? 0);

$q = $conn->prepare("
  SELECT
    r.project_id, r.task_id, r.rating, r.final_decision, r.comment, r.created_at,
    u.full_name AS company_name,
    p.capstone_title,
    t.task_code, t.role_name
  FROM phase3_partner_reviews r
  JOIN users u ON u.id = r.partner_id
  JOIN partner_phase3_projects p ON p.id = r.project_id
  JOIN phase3_tasks t ON t.id = r.task_id
  WHERE r.student_id=?
  ORDER BY r.created_at DESC
");
$q->bind_param("i", $student_id);
$q->execute();

json_out(["ok"=>true,"reviews"=>$q->get_result()->fetch_all(MYSQLI_ASSOC)]);