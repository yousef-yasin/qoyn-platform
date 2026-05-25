<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);
$task_id = (int)($_GET["task_id"] ?? 0);

if ($task_id <= 0) {
  json_out(["ok"=>false,"error"=>"task_id required"], 400);
}

$tq = $conn->prepare("
  SELECT
    t.id,
    t.project_id,
    t.task_code,
    t.role_name,
    p.partner_user_id
  FROM phase3_tasks t
  JOIN partner_phase3_projects p
    ON p.id = t.project_id
  WHERE t.id=?
  LIMIT 1
");
$tq->bind_param("i", $task_id);
$tq->execute();
$task = $tq->get_result()->fetch_assoc();

if (!$task) {
  json_out(["ok"=>false,"error"=>"TASK_NOT_FOUND"], 404);
}

if ((int)$task["partner_user_id"] !== $partner_id) {
  json_out(["ok"=>false,"error"=>"NOT_YOUR_TASK"], 403);
}

$sq = $conn->prepare("
  SELECT
    s.id AS submission_id,
    s.project_id,
    s.task_id,
    s.student_id,
    u.full_name AS student_name,
    s.repo_url,
    s.zip_path,
    s.notes,
    s.score,
    s.decision,
    s.partner_rating,
    s.partner_comment,
    s.selection_status,
    s.submitted_at,
    r.final_decision AS partner_final_decision
  FROM phase3_task_submissions s
  JOIN users u
    ON u.id = s.student_id
  LEFT JOIN phase3_partner_reviews r
    ON r.project_id = s.project_id
   AND r.task_id = s.task_id
   AND r.student_id = s.student_id
  WHERE s.task_id=?
  ORDER BY
    COALESCE(s.partner_rating, -1) DESC,
    COALESCE(s.score, -1) DESC,
    s.submitted_at ASC,
    s.id ASC
");
$sq->bind_param("i", $task_id);
$sq->execute();
$res = $sq->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
  $items[] = $row;
}

unset($task["partner_user_id"]);

json_out([
  "ok"=>true,
  "task"=>$task,
  "items"=>$items
]);