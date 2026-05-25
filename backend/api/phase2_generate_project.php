<?php
session_start();
require_once __DIR__ . "/../config/db.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

// هنا جيب role و path من جدولك الحالي
$role_key = "fullstack"; // مؤقتاً
$path_title = "Full Stack Development"; // مؤقتاً

// جيب البلاي ليستات من DB حسب path_id
$playlists = [
    ["name"=>"PHP Basics","description"=>"variables, forms, sessions"],
    ["name"=>"MySQL CRUD","description"=>"select, insert, update, delete"]
];

$payload = [
    "role_key" => $role_key,
    "path_title" => $path_title,
    "playlists" => $playlists,
    "base_coins" => 2000
];

$ch = curl_init("http://127.0.0.1:5006/phase2/generate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!$data || !$data["ok"]) {
    echo json_encode(["ok"=>false,"error"=>"AI_FAILED","raw"=>$response]);
    exit;
}

$project_json = json_encode($data["project"], JSON_UNESCAPED_UNICODE);

$stmt = $conn->prepare("INSERT INTO phase2_projects (user_id, role_key, path_title, project_json, base_coins) VALUES (?,?,?,?,?)");
$stmt->bind_param("isssi", $user_id, $role_key, $path_title, $project_json, $payload["base_coins"]);
$stmt->execute();

echo json_encode([
    "ok"=>true,
    "project"=>$data["project"]
]);