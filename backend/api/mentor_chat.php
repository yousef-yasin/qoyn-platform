<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

if (!defined("AI_BASE")) {
    define("AI_BASE", "http://127.0.0.1:5006");
}

function ai_post_json(string $url, array $payload, int $timeout = 120): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => $timeout,
    ]);

    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        return ["ok" => false, "error" => "CURL_FAILED", "details" => $err];
    }

    $json = json_decode($resp, true);
    return [
        "ok" => ($code >= 200 && $code < 300),
        "status" => $code,
        "json" => $json,
        "raw" => $resp
    ];
}

$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
if (!is_array($body)) {
    $body = [];
}

$project_id = isset($body["project_id"]) ? (int)$body["project_id"] : 0;
$team_id    = isset($body["team_id"]) ? (int)$body["team_id"] : 0;
$question   = trim((string)($body["question"] ?? ""));
$chat_context = $body["chat_context"] ?? [];

if ($question === "") {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "QUESTION_REQUIRED"], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = [
    "user_id" => $user_id,
    "project_id" => $project_id > 0 ? $project_id : null,
    "team_id" => $team_id > 0 ? $team_id : null,
    "question" => $question,
    "chat_context" => is_array($chat_context) ? $chat_context : [],
];

$ai = ai_post_json(AI_BASE . "/mentor/chat", $payload, 180);

if (!$ai["ok"]) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "AI_REQUEST_FAILED",
        "ai" => $ai
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($ai["json"], JSON_UNESCAPED_UNICODE);
