<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php";
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
    exit;
}

$user_id = $_SESSION["user_id"];

// كل roles
$sql = "
SELECT r.id, r.role_key, r.role_name
FROM career_roles r
";
$roles = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$result = [];

foreach ($roles as $role) {

    // مجموع وزن كل مهارات الدور
    $total_q = $conn->prepare("
        SELECT SUM(weight) as total_weight
        FROM role_skills
        WHERE role_id=?
    ");
    $total_q->bind_param("i", $role["id"]);
    $total_q->execute();
    $total_weight = $total_q->get_result()->fetch_assoc()["total_weight"] ?? 0;

    // مجموع وزن المهارات المشتركة مع الطالب
    $match_q = $conn->prepare("
        SELECT SUM(rs.weight) as match_weight
        FROM role_skills rs
        JOIN user_skills us ON us.skill_id = rs.skill_id
        WHERE rs.role_id=? AND us.user_id=?
    ");
    $match_q->bind_param("ii", $role["id"], $user_id);
    $match_q->execute();
    $match_weight = $match_q->get_result()->fetch_assoc()["match_weight"] ?? 0;

    $score = 0;
    if ($total_weight > 0) {
        $score = round(($match_weight / $total_weight) * 100, 2);
    }

    $result[] = [
        "role_key" => $role["role_key"],
        "role_name" => $role["role_name"],
        "score" => $score
    ];
}

usort($result, fn($a,$b) => $b["score"] <=> $a["score"]);

echo json_encode([
    "ok" => true,
    "roles" => $result
]);