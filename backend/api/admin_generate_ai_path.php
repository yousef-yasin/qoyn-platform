<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";

$role_key = trim($_GET["role_key"] ?? "");
if ($role_key === "") {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"MISSING_ROLE_KEY"]);
    exit;
}

$sql = "
SELECT 
  v.skill_name,
  m.template_subject,
  p.id AS template_id,
  p.name AS template_playlist
FROM v_role_skills v
LEFT JOIN skill_subject_map m
  ON LOWER(v.skill_name) LIKE CONCAT('%', m.skill_keyword, '%')
LEFT JOIN partner_playlists p
  ON p.is_template = 1
 AND p.template_subject = m.template_subject
WHERE v.role_key = ?
ORDER BY v.weight DESC
LIMIT 100
";

$st = $conn->prepare($sql);
$st->bind_param("s",$role_key);
$st->execute();
$rs = $st->get_result();

$playlists = [];
$skills = [];

while($row = $rs->fetch_assoc()){

    $skills[] = $row["skill_name"];

    if(!empty($row["template_id"])){
        $playlists[$row["template_id"]] = [
            "id" => $row["template_id"],
            "name" => $row["template_playlist"],
            "subject" => $row["template_subject"]
        ];
    }
}

echo json_encode([
    "ok" => true,
    "role_key" => $role_key,
    "skills_count" => count(array_unique($skills)),
    "recommended_playlists" => array_values($playlists)
], JSON_UNESCAPED_UNICODE);