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
// 1) role_key من GET (اختياري)
$role_key = trim((string)($_GET["role_key"] ?? ""));

// 2) لو مش موجود، اقرأ آخر اختيار من DB
if ($role_key === "") {
  $st = $conn->prepare("SELECT role_key FROM user_selected_role WHERE user_id=? LIMIT 1");
  $st->bind_param("i", $user_id);
  $st->execute();
  $row = $st->get_result()->fetch_assoc();
  if ($row && !empty($row["role_key"])) {
    $role_key = $row["role_key"];
  }
}

// 3) لو لسا فاضي -> لازم المستخدم يختار
if ($role_key === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"NO_ROLE_SELECTED"]);
  exit;
}
/* --------------------------
   1) Get Best Role
--------------------------- */

if ($role_key !== "") {
  $sql = "
    SELECT r.id, r.role_key, r.role_name,
           SUM(CASE WHEN us.skill_id IS NOT NULL THEN rs.weight ELSE 0 END) as score,
           SUM(rs.weight) as max_score
    FROM career_roles r
    JOIN role_skills rs ON r.id = rs.role_id
    LEFT JOIN user_skills us 
           ON us.skill_id = rs.skill_id 
           AND us.user_id = ?
    WHERE r.role_key = ?
    GROUP BY r.id
    LIMIT 1
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('is', $user_id, $role_key);

} else {

  $sql = "
    SELECT r.id, r.role_key, r.role_name,
           SUM(CASE WHEN us.skill_id IS NOT NULL THEN rs.weight ELSE 0 END) as score,
           SUM(rs.weight) as max_score
    FROM career_roles r
    JOIN role_skills rs ON r.id = rs.role_id
    LEFT JOIN user_skills us 
           ON us.skill_id = rs.skill_id 
           AND us.user_id = ?
    GROUP BY r.id
    ORDER BY score DESC
    LIMIT 1
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $user_id);
}

$stmt->execute();
$best = $stmt->get_result()->fetch_assoc();

if (!$best) {
  echo json_encode(["ok"=>false,"error"=>"NO_ROLES_FOUND"]);
  exit;
}

$role_id = (int)$best["id"];
$role_name = $best["role_name"];
$coverage = $best["max_score"] > 0 
  ? round(($best["score"] / $best["max_score"]) * 100)
  : 0;

/* --------------------------
   2) Get Missing Skills
--------------------------- */

$sql2 = "
SELECT s.skill_name, rs.weight,
       CASE WHEN us.skill_id IS NULL THEN 1 ELSE 0 END as is_missing
FROM (
  SELECT role_id, skill_id, MAX(weight) as weight
  FROM role_skills
  WHERE role_id = ?
  GROUP BY role_id, skill_id
) rs
JOIN skills s ON s.id = rs.skill_id
LEFT JOIN user_skills us 
       ON us.skill_id = rs.skill_id 
       AND us.user_id = ?

ORDER BY rs.weight DESC
";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("ii", $role_id, $user_id);
$stmt2->execute();
$res = $stmt2->get_result();

$core = [];
$important = [];
$support = [];
$mastered = [];

while ($row = $res->fetch_assoc()) {

  if ($row["is_missing"]) {

   if ($row["weight"] == 5) {
  $core[] = $row["skill_name"];
} elseif ($row["weight"] == 4 || $row["weight"] == 3) {
  $important[] = $row["skill_name"];
} else {
  $support[] = $row["skill_name"];
}

  } else {
    $mastered[] = $row["skill_name"];
  }
}

/* --------------------------
   3) Build Phases
--------------------------- */

$path = [
  "phase_1_foundations" => array_slice($core, 0, 10),
  "phase_2_core" => array_slice($important, 0, 15),
  "phase_3_advanced" => array_slice($support, 0, 15)
];

/* --------------------------
   Final Response
--------------------------- */

echo json_encode([
  "ok" => true,
  "recommended_role" => $role_name,
  "coverage_percent" => $coverage,
  "skills_mastered_count" => count($mastered),
  "core_missing_count" => count($core),
  "important_missing_count" => count($important),
  "support_missing_count" => count($support),
  "learning_path" => $path
], JSON_UNESCAPED_UNICODE);