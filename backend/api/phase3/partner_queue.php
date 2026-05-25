<?php
// utbn-backend/api/phase3/partner_queue.php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$status = strtoupper(trim((string)($_GET["status"] ?? "SUBMITTED"))); // SUBMITTED / REVIEWED / ALL
$allowed = ["SUBMITTED", "REVIEWED", "ALL"];
if (!in_array($status, $allowed, true)) $status = "SUBMITTED";

// نجيب آخر submission لكل (task_id, student_id)
$sql = "
SELECT
  s.id AS submission_id,
  s.project_id,
  p.capstone_title AS project_title,
  s.task_id,
  t.task_code,
  t.role_name,
  s.student_id,
  u.full_name AS student_name,
  t.status AS task_status,
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
JOIN (
  SELECT task_id, student_id, MAX(submitted_at) AS mx
  FROM phase3_task_submissions
  GROUP BY task_id, student_id
) last ON last.task_id=s.task_id AND last.student_id=s.student_id AND last.mx=s.submitted_at
JOIN phase3_tasks t ON t.id=s.task_id
JOIN partner_phase3_projects p ON p.id=s.project_id
JOIN users u ON u.id=s.student_id
LEFT JOIN phase3_partner_reviews r
  ON r.project_id=s.project_id
 AND r.task_id=s.task_id
 AND r.student_id=s.student_id
WHERE p.partner_user_id=?
";

if ($status !== "ALL") {
  $sql .= " AND t.status=? ";
}

$sql .= " ORDER BY s.submitted_at DESC ";

$q = $conn->prepare($sql);
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);

if ($status !== "ALL") {
  $q->bind_param("is", $partner_id, $status);
} else {
  $q->bind_param("i", $partner_id);
}

$q->execute();
json_out(["ok"=>true, "items"=>$q->get_result()->fetch_all(MYSQLI_ASSOC)]);
