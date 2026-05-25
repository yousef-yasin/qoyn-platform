<?php
require_once __DIR__ . "/session_bootstrap.php";
header("Content-Type: application/json; charset=utf-8");
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
    exit;
}
if ((string)($_SESSION["role"] ?? "") !== "student") {
    http_response_code(403);
    echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"]);
    exit;
}
