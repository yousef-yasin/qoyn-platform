<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_company();

$company_id = (int)$_SESSION["user_id"];

$sql = "
SELECT DISTINCT
  u.id AS student_id,
  u.full_name AS student_name,
  u.email AS student_email,
  'phase2' AS source_type,
  s.id AS ref_id,
  p.title AS ref_title,
  s.created_at AS last_activity
FROM phase2_submissions s
JOIN phase2_projects p ON p.id = s.project_id
JOIN users u ON u.id = s.user_id
WHERE p.user_id = ?

UNION

SELECT DISTINCT
  u.id AS student_id,
  u.full_name AS student_name,
  u.email AS student_email,
  'phase3' AS source_type,
  ts.id AS ref_id,
  pp.capstone_title AS ref_title,
  ts.submitted_at AS last_activity
FROM phase3_task_submissions ts
JOIN partner_phase3_projects pp ON pp.id = ts.project_id
JOIN users u ON u.id = ts.student_id
WHERE pp.partner_user_id = ?

ORDER BY last_activity DESC
";

$st = $conn->prepare($sql);
if (!$st) chat_json(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
$st->bind_param("ii", $company_id, $company_id);
$st->execute();
$res = $st->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
  $items[] = $row;
}

chat_json(["ok"=>true, "students"=>$items]);