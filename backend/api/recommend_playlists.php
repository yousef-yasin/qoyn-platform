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

$role_key = "";
$path_id = null;
$skills = [];
$missing_skills = [];

/* selected role */
$q1 = $conn->prepare("
    SELECT role_key
    FROM user_selected_role
    WHERE user_id = ?
    LIMIT 1
");
if ($q1) {
    $q1->bind_param("i", $user_id);
    $q1->execute();
    $r1 = $q1->get_result()->fetch_assoc();
    $q1->close();
    if ($r1) {
        $role_key = trim((string)($r1["role_key"] ?? ""));
    }
}

/* selected path */
$q2 = $conn->prepare("
    SELECT path_id
    FROM user_selected_path
    WHERE user_id = ?
    LIMIT 1
");
if ($q2) {
    $q2->bind_param("i", $user_id);
    $q2->execute();
    $r2 = $q2->get_result()->fetch_assoc();
    $q2->close();
    if ($r2 && isset($r2["path_id"])) {
        $path_id = (int)$r2["path_id"];
    }
}

/* user skills */
$q3 = $conn->prepare("
    SELECT s.skill_name
    FROM user_skills us
    INNER JOIN skills s ON s.id = us.skill_id
    WHERE us.user_id = ?
    ORDER BY s.skill_name ASC
");
if ($q3) {
    $q3->bind_param("i", $user_id);
    $q3->execute();
    $res = $q3->get_result();
    while ($row = $res->fetch_assoc()) {
        $name = trim((string)($row["skill_name"] ?? ""));
        if ($name !== "") $skills[] = $name;
    }
    $q3->close();
}

/* get missing skills from skill-gap service */
$gapPayload = [
    "user_id" => $user_id,
    "target_role_key" => $role_key,
    "student_skills" => array_values(array_unique($skills)),
];

$gap = ai_post_json(AI_BASE . "/skill-gap/analyze", $gapPayload, 180);

if (!empty($gap["json"]["ok"]) && !empty($gap["json"]["missing_skills"]) && is_array($gap["json"]["missing_skills"])) {
    foreach ($gap["json"]["missing_skills"] as $ms) {
        $n = trim((string)($ms["skill_name"] ?? ""));
        if ($n !== "") $missing_skills[] = $n;
    }
}

/* real recommendations call */
$payload = [
    "user_id" => $user_id,
    "role_key" => $role_key,
    "path_id" => $path_id,
    "skills" => array_values(array_unique($skills)),
    "missing_skills" => array_values(array_unique($missing_skills)),
];

$ai = ai_post_json(AI_BASE . "/recommend/playlists", $payload, 180);

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