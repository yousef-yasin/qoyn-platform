<?php
// utbn-backend/api/user_behavior_log.php

require __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);

$in = json_decode(file_get_contents("php://input"), true) ?: [];

$event_type = trim((string)($in["event_type"] ?? ""));
$page       = trim((string)($in["page"] ?? ""));
$video_id   = trim((string)($in["video_id"] ?? ""));
$quiz_id    = isset($in["quiz_id"]) ? (int)$in["quiz_id"] : null;
$meta       = $in["meta"] ?? null;

if ($event_type === "") {
  json_out(["ok"=>false, "error"=>"MISSING_EVENT_TYPE"], 400);
}

if (strlen($event_type) > 64) $event_type = substr($event_type, 0, 64);
if (strlen($page) > 128) $page = substr($page, 0, 128);
if (strlen($video_id) > 64) $video_id = substr($video_id, 0, 64);

$session_id = session_id();
$user_agent = substr((string)($_SERVER["HTTP_USER_AGENT"] ?? ""), 0, 255);
$ip = substr((string)($_SERVER["REMOTE_ADDR"] ?? ""), 0, 64);

$meta_json = null;
if ($meta !== null) {
  $meta_json = json_encode($meta, JSON_UNESCAPED_UNICODE);
  if ($meta_json === false) $meta_json = null;
}

$stmt = $conn->prepare("
  INSERT INTO user_behavior
    (user_id, session_id, event_type, page, video_id, quiz_id, meta_json, user_agent, ip)
  VALUES
    (?,?,?,?,?,?,?,?,?)
");

if (!$stmt) json_out(["ok"=>false, "error"=>"DB_PREPARE_FAILED", "details"=>$conn->error], 500);

$quiz_id_param = $quiz_id; // can be null
$stmt->bind_param(
  "issssisss",
  $user_id,
  $session_id,
  $event_type,
  $page,
  $video_id,
  $quiz_id_param,
  $meta_json,
  $user_agent,
  $ip
);

if (!$stmt->execute()) {
  $err = $stmt->error;
  $stmt->close();
  json_out(["ok"=>false, "error"=>"DB_EXEC_FAILED", "details"=>$err], 500);
}
$stmt->close();

json_out(["ok"=>true]);
