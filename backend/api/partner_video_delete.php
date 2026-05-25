<?php
require_once __DIR__ . "/session_bootstrap.php";
require_once __DIR__ . "/require_partner.php";
require_once __DIR__ . "/csrf.php";

header("Content-Type: application/json; charset=utf-8");

csrf_verify_request();

require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$video_id = intval($data["video_id"] ?? 0);
$partner_id = intval($_SESSION["user_id"] ?? 0);

if ($video_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "ok" => false,
        "error" => "INVALID_VIDEO_ID"
    ]);
    exit;
}

try {
    $sql = "
        DELETE pv
        FROM partner_videos pv
        INNER JOIN partner_playlists pp ON pp.id = pv.playlist_id
        WHERE pv.id = ? AND pp.partner_user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $video_id, $partner_id);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        http_response_code(403);
        echo json_encode([
            "ok" => false,
            "error" => "FORBIDDEN_OR_NOT_FOUND"
        ]);
        exit;
    }

    echo json_encode([
        "ok" => true
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "SERVER_ERROR"
    ]);
}