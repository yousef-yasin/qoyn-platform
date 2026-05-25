<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

$sql = "
SELECT
  u.id AS partner_user_id,
  p.id AS partner_id,
  u.full_name AS partner_name,
  u.email,
  COALESCE(p.company_name, u.full_name) AS company_name,
  COALESCE(p.partner_type,'') AS partner_type,
  COALESCE(pl.cnt,0) AS playlists_count,
  COALESCE(vv.cnt,0) AS videos_count
FROM users u
LEFT JOIN partners p ON p.email = u.email
LEFT JOIN (
  SELECT partner_user_id, COUNT(*) cnt
  FROM partner_playlists
  GROUP BY partner_user_id
) pl ON pl.partner_user_id = u.id
LEFT JOIN (
  SELECT partner_user_id, COUNT(*) cnt
  FROM partner_videos
  GROUP BY partner_user_id
) vv ON vv.partner_user_id = u.id
WHERE u.role='partner'
ORDER BY u.id DESC
";

$res = $conn->query($sql);
$items = [];
while($row = $res->fetch_assoc()){
  $items[] = [
    "partner_user_id" => (int)$row["partner_user_id"], // users.id
    "partner_id"      => (int)($row["partner_id"] ?? 0), // partners.id ✅
    "company_name"    => (string)$row["company_name"],
    "partner_type"    => (string)$row["partner_type"],
    "email"           => (string)$row["email"],
    "playlists_count" => (int)$row["playlists_count"],
    "videos_count"    => (int)$row["videos_count"],
  ];
}

json_out(["ok"=>true, "items"=>$items]);
