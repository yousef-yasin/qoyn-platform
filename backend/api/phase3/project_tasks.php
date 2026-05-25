<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);
$project_id = (int)($_GET["project_id"] ?? 0);

if ($project_id <= 0) {
  json_out(["ok"=>false,"error"=>"project_id required"], 400);
}

$pq = $conn->prepare("
  SELECT id, capstone_title, capstone_description, status
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
$pq->bind_param("ii", $project_id, $partner_id);
$pq->execute();
$project = $pq->get_result()->fetch_assoc();

if (!$project) {
  json_out(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], 404);
}

$tq = $conn->prepare("
  SELECT
    t.id,
    t.project_id,
    t.task_code,
    t.task_order,
    t.role_key,
    t.role_name,
    t.description,
    t.status,
    COUNT(s.id) AS submissions_count
  FROM phase3_tasks t
  LEFT JOIN phase3_task_submissions s
    ON s.task_id = t.id
  WHERE t.project_id=?
  GROUP BY t.id
  ORDER BY t.task_order ASC, t.id ASC
");
$tq->bind_param("i", $project_id);
$tq->execute();
$res = $tq->get_result();

$tasks = [];
while ($row = $res->fetch_assoc()) {
  $tasks[] = $row;
}

json_out([
  "ok"=>true,
  "project"=>$project,
  "tasks"=>$tasks
]);