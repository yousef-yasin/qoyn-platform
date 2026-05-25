<?php
require_once __DIR__ . "/session_bootstrap.php";
require_once __DIR__ . "/csrf.php";

header("Content-Type: application/json; charset=utf-8");

csrf_verify_request();

session_unset();
session_destroy();

echo json_encode([
    "ok" => true
]);
exit;