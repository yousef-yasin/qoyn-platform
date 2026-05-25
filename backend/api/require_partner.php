<?php
require_once __DIR__ . "/session_bootstrap.php";
require_once __DIR__ . "/db.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    http_response_code(401);
    echo json_encode([
        "ok" => false,
        "error" => "NOT_LOGGED_IN"
    ]);
    exit;
}

if ($_SESSION["role"] !== "partner") {
    http_response_code(403);
    echo json_encode([
        "ok" => false,
        "error" => "FORBIDDEN"
    ]);
    exit;
}

$email = $_SESSION["email"] ?? "";
if ($email === "") {
    http_response_code(401);
    echo json_encode([
        "ok" => false,
        "error" => "NOT_LOGGED_IN"
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM partners WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    http_response_code(403);
    echo json_encode([
        "ok" => false,
        "error" => "FORBIDDEN"
    ]);
    exit;
}