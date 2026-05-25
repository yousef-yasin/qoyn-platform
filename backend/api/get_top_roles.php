<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

/**
 * بنحسب score لكل role:
 * score = sum(role_skills.weight * user_skills.confidence) للمهارات المشتركة
 * max_score = sum(role_skills.weight) لكل مهارات الدور (للتطبيع)
 */
$sql = "
SELECT
  r.role_key,
  r.role_name,
  COALESCE(SUM(rs.weight * us.confidence), 0) AS score,
  (SELECT COALESCE(SUM(rs2.weight),0) FROM role_skills rs2 WHERE rs2.role_id = r.id) AS max_score
FROM career_roles r
LEFT JOIN role_skills rs ON rs.role_id = r.id
LEFT JOIN user_skills us ON us.skill_id = rs.skill_id AND us.user_id = ?
GROUP BY r.id
ORDER BY (COALESCE(SUM(rs.weight * us.confidence),0) / NULLIF((SELECT COALESCE(SUM(rs2.weight),0) FROM role_skills rs2 WHERE rs2.role_id = r.id),0)) DESC,
         COALESCE(SUM(rs.weight * us.confidence),0) DESC
LIMIT 3
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$out = [];
foreach ($rows as $r) {
  $max = (float)$r["max_score"];
  $sc  = (float)$r["score"];
  $pct = ($max > 0) ? round(($sc / $max) * 100) : 0;

  $out[] = [
    "role_key" => $r["role_key"],
    "role_name" => $r["role_name"],
    "score_percent" => $pct,
    "score" => round($sc, 3),
    "max_score" => round($max, 3),
  ];
}

echo json_encode(["ok"=>true, "top_roles"=>$out], JSON_UNESCAPED_UNICODE);