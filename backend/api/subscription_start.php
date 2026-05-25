<?php
require __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];
$data = json_decode(file_get_contents("php://input"), true);
$plan = $data["plan"] ?? "monthly"; // monthly|yearly (MVP: manual activation)

if ($plan !== "monthly" && $plan !== "yearly") json_out(["error"=>"BAD_PLAN"], 400);

$start = date("Y-m-d H:i:s");
$end = ($plan === "monthly") ? date("Y-m-d H:i:s", strtotime("+30 days")) : date("Y-m-d H:i:s", strtotime("+365 days"));

$stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, status, start_at, end_at) VALUES (?,?, 'active', ?, ?)");
$stmt->bind_param("isss", $user_id, $plan, $start, $end);
$stmt->execute();

json_out(["ok"=>true, "plan"=>$plan, "start_at"=>$start, "end_at"=>$end]);
