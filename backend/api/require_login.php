<?php
require_once __DIR__ . "/session_bootstrap.php";
header("Content-Type: application/json; charset=utf-8");
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
    exit;
}
