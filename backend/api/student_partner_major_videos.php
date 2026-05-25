<?php
// utbn-backend/api/student_partner_major_videos.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

// 1) احضر user_id
$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

// 2) اقرأ تخصصات الطالب (multi-major) من student_profiles.major_text
$majors = "";
$st = $conn->prepare("SELECT major_text FROM student_profiles WHERE user_id = ? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$rs = $st->get_result();
if ($row = $rs->fetch_assoc()) $majors = (string)($row["major_text"] ?? "");
$st->close();

// fallback إذا ما لقى ب student_profiles (اختياري)
if (trim($majors) === "") {
  // جرّب user_plan_profile لو عندك
  $st = $conn->prepare("SELECT major_text FROM user_plan_profile WHERE user_id = ? LIMIT 1");
  if ($st) {
    $st->bind_param("i", $user_id);
    $st->execute();
    $rs = $st->get_result();
    if ($row = $rs->fetch_assoc()) $majors = (string)($row["major_text"] ?? "");
    $st->close();
  }
}

// إذا فاضي ما في تخصص
$majors = trim($majors);
if ($majors === "") {
  echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE);
  exit;
}

// 3) قسم التخصصات إلى كلمات/عناصر
function norm_list($s){
  $s = mb_strtolower($s, 'UTF-8');
  $s = preg_replace('/[\x{064B}-\x{0652}]/u', '', $s);
  $s = str_replace(["؛","|","/"], ",", $s);
  $parts = preg_split('/[,]+/u', $s);
  $out = [];
  foreach ($parts as $p) {
    $p = trim($p);
    if ($p === "") continue;
    $p = preg_replace('/\s+/u', ' ', $p);
    $out[] = $p;
  }
  // إزالة تكرار
  $out2 = [];
  foreach ($out as $x) if (!in_array($x,$out2,true)) $out2[] = $x;
  return array_slice($out2, 0, 10);
}

$majorList = norm_list($majors);
if (!count($majorList)) {
  echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE);
  exit;
}

// 4) ابنِ WHERE: يطابق partner_playlists.major_text (قائمة) أو اسم البلاي ليست أو عنوان الفيديو
$where = [];
$types = "";
$params = [];

foreach ($majorList as $m) {
  // نطابق على major_text و name و title (مرن)
  $where[] = "(LOWER(p.major_text) LIKE CONCAT('%', ?, '%') OR LOWER(p.name) LIKE CONCAT('%', ?, '%') OR LOWER(v.title) LIKE CONCAT('%', ?, '%'))";
  $types .= "sss";
  $params[] = $m; $params[] = $m; $params[] = $m;
}

$sql = "
  SELECT
    v.id AS video_id,
    v.title AS video_title,
    v.stored_path,
    v.duration_seconds,
    v.created_at,
    p.name AS playlist_name,
    p.major_text AS playlist_majors,
    u.full_name AS partner_name
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  JOIN users u ON u.id = v.partner_user_id
  WHERE p.is_published = 1
    AND (" . implode(" OR ", $where) . ")
  ORDER BY v.created_at DESC
  LIMIT 200
";

$st = $conn->prepare($sql);
$st->bind_param($types, ...$params);
$st->execute();
$rs = $st->get_result();

$items = [];
while ($row = $rs->fetch_assoc()) {
  $items[] = [
    "video_id" => (int)$row["video_id"],
    "video_title" => (string)$row["video_title"],
    "stored_path" => (string)$row["stored_path"],
    "duration_seconds" => (int)$row["duration_seconds"],
    "created_at" => (string)$row["created_at"],
    "playlist_name" => (string)$row["playlist_name"],
    "playlist_majors" => (string)$row["playlist_majors"],
    "partner_name" => (string)$row["partner_name"],
  ];
}
$st->close();

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);
