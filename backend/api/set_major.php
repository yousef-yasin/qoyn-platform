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
$major = trim($_POST["major"] ?? "");

if ($user_id <= 0) json_out(["ok"=>false,"error"=>"NO_SESSION"], 401);
if ($major === "") json_out(["ok"=>false,"error"=>"MAJOR_REQUIRED"], 400);

$q = $conn->prepare("UPDATE users SET major_text=? WHERE id=?");
$q->bind_param("si", $major, $user_id);
$q->execute();
$q->close();

json_out(["ok"=>true, "major"=>$major]);
