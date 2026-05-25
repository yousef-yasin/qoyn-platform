<?php
require_once __DIR__ . "/_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);


$raw = file_get_contents("php://input");
if ($raw) {
  $j = json_decode($raw, true);
  if (is_array($j)) {
    foreach ($j as $k => $v) {
      if (!isset($_POST[$k])) $_POST[$k] = $v;
    }
  }
}



$title = trim((string)(
  $_POST["capstone_title"] ??
  $_POST["project_title"] ??
  $_POST["title"] ?? ""
));

$desc = trim((string)(
  $_POST["capstone_description"] ??
  $_POST["project_description"] ??
  $_POST["description"] ?? ""
));

if ($title === "" || $desc === "") {
  json_out(["ok"=>false,"error"=>"TITLE_AND_DESCRIPTION_REQUIRED"], 400);
}

$project_id = (int)($_POST["project_id"] ?? 0);


if ($project_id > 0) {
  $stmt = $conn->prepare("
    UPDATE partner_phase3_projects
    SET capstone_title=?, capstone_description=?, status='DRAFT'
    WHERE id=? AND partner_user_id=?
  ");
  $stmt->bind_param("ssii", $title, $desc, $project_id, $partner_id);
  $stmt->execute();

  json_out([
    "ok"=>true,
    "project_id"=>$project_id,
    "status"=>"DRAFT",
    "updated"=>true
  ]);
}

$stmt = $conn->prepare("
  INSERT INTO partner_phase3_projects
  (partner_user_id, capstone_title, capstone_description, status)
  VALUES (?, ?, ?, 'DRAFT')
");
$stmt->bind_param("iss", $partner_id, $title, $desc);
$stmt->execute();

json_out([
  "ok"=>true,
  "project_id"=>(int)$conn->insert_id,
  "status"=>"DRAFT",
  "created"=>true
]);