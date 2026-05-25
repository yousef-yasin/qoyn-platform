<?php
// utbn-backend/api/student_partner_course_videos.php
require __DIR__ . "/db.php";
require_login();
// ✅ ensure columns (safe)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");
@$conn->query("ALTER TABLE partner_videos ADD COLUMN cover_path VARCHAR(255) NULL");

// ✅ student major
$user_id = (int)($_SESSION["user_id"] ?? 0);
$student_major = "";
$u = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
if ($u) {
  $u->bind_param("i", $user_id);
  $u->execute();
  $ur = $u->get_result()->fetch_assoc();
  $u->close();
  $student_major = trim((string)($ur["major_text"] ?? ""));
}

header("Content-Type: application/json; charset=utf-8");

$course = trim($_GET["course"] ?? "");
if ($course === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"EMPTY_COURSE"], JSON_UNESCAPED_UNICODE);
  exit;
}

function norm_txt($s){
  $s = mb_strtolower($s, 'UTF-8');
  $s = preg_replace('/[\x{064B}-\x{0652}]/u', '', $s);
  $s = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s);
  $s = preg_replace('/\s+/u', ' ', $s);
  return trim($s);
}

function extract_keywords($course){
  $c = norm_txt($course);

  $parts = preg_split('/\s+/u', $c);
  $kw = [];
  foreach ($parts as $p) {
    $p = trim($p);
    if ($p === "") continue;
    if (mb_strlen($p, 'UTF-8') < 3) continue;
    if (preg_match('/^\d+$/u', $p)) continue;
    $kw[] = $p;
  }

  // ✅ أولوية كلمات machine / تعلم حسب طلبك
  $must = [];
  if (mb_strpos($c, "machine", 0, "UTF-8") !== false) $must[] = "machine";
  if (mb_strpos($c, "learning", 0, "UTF-8") !== false) $must[] = "learning";
  if (mb_strpos($c, "تعلم", 0, "UTF-8") !== false) $must[] = "تعلم";
  if (mb_strpos($c, "الالي", 0, "UTF-8") !== false || mb_strpos($c, "آلي", 0, "UTF-8") !== false) $must[] = "آلي";

  $out = [];
  foreach (array_merge($must, $kw) as $x) {
    if ($x === "") continue;
    if (!in_array($x, $out, true)) $out[] = $x;
  }
  return array_slice($out, 0, 6);
}

$keywords = extract_keywords($course);
if (!count($keywords)) {
  echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE);
  exit;
}

$where = [];
$types = "";
$params = [];

foreach ($keywords as $k) {
  $where[] = "LOWER(p.name) LIKE CONCAT('%', ?, '%')";
  $types .= "s";
  $params[] = $k;
}

$sql = "
  SELECT
    v.id AS video_id,
    v.title AS video_title,
    v.duration_seconds,
    v.created_at,
    v.cover_path AS cover_path,
    v.playlist_id AS playlist_id,
    p.name AS playlist_name,
    COALESCE(u.full_name,'Partner') AS partner_name
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  LEFT JOIN users u ON u.id = v.partner_user_id
  WHERE p.is_published = 1
    AND (" . implode(" OR ", $where) . ")
  ORDER BY v.created_at DESC
  LIMIT 200
";



$st = $conn->prepare($sql);
if (!$st) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$st->bind_param($types, ...$params);
$st->execute();
$rs = $st->get_result();

$items = [];
while ($row = $rs->fetch_assoc()) {
   $items[] = [
    "video_id" => (int)$row["video_id"],
    "video_title" => (string)$row["video_title"],
    "duration_seconds" => (int)$row["duration_seconds"],
    "created_at" => (string)$row["created_at"],
    "playlist_id" => (int)($row["playlist_id"] ?? 0),
    "playlist_name" => (string)$row["playlist_name"],
    "partner_name" => (string)$row["partner_name"],
    "cover_path" => (string)($row["cover_path"] ?? ""),
  ];

}
$st->close();

// ✅ Fallback: إذا ما في نتائج حسب course (بسبب عربي/انجليزي) رجّع آخر فيديوهات للتخصص
if (!count($items) && $student_major !== "") {

$sql2 = "
  SELECT
    v.id AS video_id,
    v.title AS video_title,
    v.duration_seconds,
    v.created_at,
    v.cover_path AS cover_path,
    v.playlist_id AS playlist_id,
    p.name AS playlist_name,
    COALESCE(u.full_name,'Partner') AS partner_name
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  LEFT JOIN users u ON u.id = v.partner_user_id
  WHERE p.is_published = 1
    AND (
      p.major_text IS NULL OR TRIM(p.major_text) = ''
      OR LOWER(TRIM(p.major_text)) = LOWER(TRIM(?))
    )
  ORDER BY v.created_at DESC, v.id DESC
  LIMIT 50
";



  $st2 = $conn->prepare($sql2);
  if ($st2) {
    $st2->bind_param("s", $student_major);
    $st2->execute();
    $rs2 = $st2->get_result();

    while ($row = $rs2->fetch_assoc()) {
          $items[] = [
        "video_id" => (int)$row["video_id"],
        "video_title" => (string)$row["video_title"],
        "duration_seconds" => (int)($row["duration_seconds"] ?? 0),
        "created_at" => (string)($row["created_at"] ?? ""),
        "playlist_id" => (int)($row["playlist_id"] ?? 0),
        "playlist_name" => (string)($row["playlist_name"] ?? ""),
        "partner_name" => (string)($row["partner_name"] ?? "Partner"),
        "cover_path" => (string)($row["cover_path"] ?? ""),
      ];

    }
    $st2->close();
  }
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);
