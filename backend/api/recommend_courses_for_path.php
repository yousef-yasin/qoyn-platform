<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$selected_role_id = isset($_GET["role_id"]) ? (int)$_GET["role_id"] : 0;
/* --------------------------
   1) Best Role for user
--------------------------- */

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
if ($selected_role_id > 0) {
    // المستخدم مختار دور معيّن
    $stmt = $conn->prepare("
        SELECT r.id, r.role_key, r.role_name,
               SUM(CASE WHEN us.skill_id IS NOT NULL THEN rs.weight ELSE 0 END) as score,
               SUM(rs.weight) as max_score
        FROM career_roles r
        JOIN role_skills rs ON r.id = rs.role_id
        LEFT JOIN user_skills us 
               ON us.skill_id = rs.skill_id 
               AND us.user_id = ?
        WHERE r.id = ?
        GROUP BY r.id
        LIMIT 1
    ");
    $stmt->bind_param("ii", $user_id, $selected_role_id);
} else {
    // احسب أفضل دور تلقائياً
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$best = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$best) {
  echo json_encode(["ok"=>false,"error"=>"NO_ROLES_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

$role_id = (int)$best["id"];
$role_name = $best["role_name"];
$coverage = ($best["max_score"] > 0) ? round(($best["score"] / $best["max_score"]) * 100) : 0;

/* --------------------------
   2) Missing skills for role
--------------------------- */

$sql2 = "
SELECT s.skill_name, rs.weight,
       CASE WHEN us.skill_id IS NULL THEN 1 ELSE 0 END as is_missing
FROM role_skills rs
JOIN skills s ON s.id = rs.skill_id
LEFT JOIN user_skills us 
       ON us.skill_id = rs.skill_id 
       AND us.user_id = ?
WHERE rs.role_id = ?
ORDER BY rs.weight DESC
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("ii", $user_id, $role_id);
$stmt2->execute();
$res = $stmt2->get_result();

$core = [];
$important = [];
$support = [];
$mastered = [];

while ($row = $res->fetch_assoc()) {
  $is_missing = (int)$row["is_missing"];
  $w = (int)$row["weight"];
  $name = $row["skill_name"];

  if ($is_missing === 1) {
    if ($w >= 5) $core[] = $name;
    else if ($w >= 3) $important[] = $name;
    else $support[] = $name;
  } else {
    $mastered[] = $name;
  }
}
$stmt2->close();

$path = [
  "phase_1_foundations" => array_slice($core, 0, 10),
  "phase_2_core"        => array_slice($important, 0, 12),
  "phase_3_advanced"    => array_slice($support, 0, 10),
];

/* --------------------------
   3) Helper: check column exists
--------------------------- */
function column_exists($conn, $table, $col) {
  // ⚠️ ما نستخدم bind_param هون لأن MariaDB ما بتحب ? مع SHOW
  $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
  $col   = preg_replace('/[^a-zA-Z0-9_]/', '', $col);

  $sql = "SHOW COLUMNS FROM `$table` LIKE '$col'";
  $res = $conn->query($sql);
  if (!$res) return false;

  return ($res->num_rows > 0);
}

/* --------------------------
   4) Fetch courses for skills (FULLTEXT -> fallback LIKE)
--------------------------- */
function fetch_courses_for_skills($conn, $skills, $limit_per_skill = 3, &$used_ids = []) {
  $out = [];

$has_desc = column_exists($conn, "courses", "description");
$use_fulltext = false;

  foreach ($skills as $skill) {
    $q = trim($skill);
    if ($q === "") continue;

    $rows = [];

    if ($use_fulltext) {
      $stmt = $conn->prepare("
        SELECT id, title,
               MATCH(title, description) AGAINST (? IN NATURAL LANGUAGE MODE) AS score
        FROM courses
        WHERE MATCH(title, description) AGAINST (? IN NATURAL LANGUAGE MODE)
        ORDER BY score DESC
        LIMIT 30
      ");
      if ($stmt) {
        $stmt->bind_param("ss", $q, $q);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
      } else {
        $use_fulltext = false;
      }
    }

    if (!$use_fulltext) {
      if ($has_desc) {
        $stmt2 = $conn->prepare("
          SELECT id, title
          FROM courses
          WHERE title LIKE CONCAT('%', ?, '%')
             OR description LIKE CONCAT('%', ?, '%')
          LIMIT 30
        ");
        if ($stmt2) {
          $stmt2->bind_param("ss", $q, $q);
          $stmt2->execute();
          $rows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
          $stmt2->close();
        }
      } else {
        $stmt2 = $conn->prepare("
          SELECT id, title
          FROM courses
          WHERE title LIKE CONCAT('%', ?, '%')
          LIMIT 30
        ");
        if ($stmt2) {
          $stmt2->bind_param("s", $q);
          $stmt2->execute();
          $rows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
          $stmt2->close();
        }
      }
    }

    // ✅ إزالة التكرارات: نفس الكورس ما يطلع إلا مرة بكل الصفحة
    $uniq = [];
    foreach ($rows as $r) {
      $cid = (int)($r["id"] ?? 0);
      if ($cid <= 0) continue;
      if (isset($used_ids[$cid])) continue;   // كورس طلع قبل هيك
      $used_ids[$cid] = true;
      $uniq[] = $r;
      if (count($uniq) >= $limit_per_skill) break;
    }

    $out[] = [
      "skill" => $skill,
      "courses" => $uniq
    ];
  }

  return $out;
}

$used_ids = [];

$rec = [
  "phase_1_foundations" => fetch_courses_for_skills($conn, $path["phase_1_foundations"], 3, $used_ids),
  "phase_2_core"        => fetch_courses_for_skills($conn, $path["phase_2_core"], 3, $used_ids),
  "phase_3_advanced"    => fetch_courses_for_skills($conn, $path["phase_3_advanced"], 3, $used_ids),
];

echo json_encode([
  "ok" => true,
  "recommended_role" => $role_name,
  "coverage_percent" => $coverage,
  "learning_path_skills" => $path,
  "recommended_courses" => $rec
], JSON_UNESCAPED_UNICODE);