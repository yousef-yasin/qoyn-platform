<?php
require __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id, full_name, email, phone, created_at FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
  json_out($row);
}

json_out(["error"=>"USER_NOT_FOUND"],404);
