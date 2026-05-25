<?php
// utbn-backend/api/partner_video_code_problem_get.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_VIDEO_ID"], 400);

$has = $conn->query("SHOW TABLES LIKE 'partner_video_code_problems'");
if (!$has || $has->num_rows === 0) {
  json_out(["ok"=>true,"problem"=>null]);
}

$q = $conn->prepare("SELECT id, title, prompt, language, starter_code, max_coin
  FROM partner_video_code_problems
  WHERE partner_video_id=?
  ORDER BY id DESC
  LIMIT 1");
$q->bind_param("i", $video_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

if (!$row) json_out(["ok"=>true,"problem"=>null]);

json_out(["ok"=>true,"problem"=>$row]);
