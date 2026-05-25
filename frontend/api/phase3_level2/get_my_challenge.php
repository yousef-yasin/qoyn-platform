<?php
require_once __DIR__ . "/_boot.php";

$project_id = (int)($_GET["project_id"] ?? 0);
$student_id = (int)($_SESSION["user_id"] ?? 0);

if ($project_id <= 0) {
  json_out(["ok" => false, "error" => "INVALID_PROJECT_ID"], 400);
}

$sql = "SELECT *
        FROM phase3_level2_challenges
        WHERE project_id = ? AND student_id = ?
        ORDER BY id DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $project_id, $student_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

json_out([
  "ok" => true,
  "challenge" => $row ?: null
]);