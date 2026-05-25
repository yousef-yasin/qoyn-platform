<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);
$project_id = (int)($_GET["project_id"] ?? 0);

if ($project_id <= 0) {
  json_out(["ok"=>false,"error"=>"project_id required"], 400);
}

$q = $conn->prepare("
  SELECT id, capstone_title, final_report_json, status
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
if (!$q) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);

$q->bind_param("ii", $project_id, $partner_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();

if (!$row) {
  json_out(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], 404);
}

$report = json_decode($row["final_report_json"] ?? "null", true);

json_out([
  "ok"=>true,
  "project_id"=>(int)$row["id"],
  "title"=>$row["capstone_title"],
  "status"=>$row["status"],
  "has_report"=>is_array($report),
  "report"=>$report
]);