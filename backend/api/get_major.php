<?php
require_once __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

function json_out($d,$c=200){
  http_response_code($c);
  echo json_encode($d, JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

$q = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result()->fetch_assoc();
$q->close();

json_out([
  "ok"=>true,
  "major_text" => $r["major_text"] ?? ""
]);
