<?php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);

$stmt = $conn->prepare("
    SELECT video_id, base_coin, quiz_coin, total_coin, rewarded_at
    FROM video_rewards
    WHERE user_id = ?
    ORDER BY rewarded_at DESC
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        "ok" => true,
        "found" => false,
        "hint" => "DB_PREPARE_FAILED: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {

    echo json_encode([
        "ok" => true,
        "found" => true,
        "title" => "Video ID: " . $row["video_id"],
        "base_coin" => (int)$row["base_coin"],
        "quiz_coin" => (int)$row["quiz_coin"],
        "total_coin" => (int)$row["total_coin"],
        "rewarded_at" => $row["rewarded_at"]
    ]);

} else {

    echo json_encode([
        "ok" => true,
        "found" => false
    ]);
}

$stmt->close();
