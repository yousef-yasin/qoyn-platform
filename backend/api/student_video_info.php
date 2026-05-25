<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) {
  if (file_exists($p)) {
    $found = $p;
    break;
  }
}

if (!$found) {
  http_response_code(500);
  echo json_encode(["error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

$video_id = (int)($_GET["video_id"] ?? 0);
if ($video_id <= 0) {
  http_response_code(400);
  echo json_encode(["error"=>"MISSING_VIDEO_ID"], JSON_UNESCAPED_UNICODE);
  exit;
}

@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");

function majors_list($s){
  $s = trim((string)$s);
  if ($s === "") return [];
  $parts = preg_split('/[,\|;]+/u', $s);
  $out = [];
  foreach($parts as $p){
    $p = trim($p);
    if ($p !== "") $out[] = mb_strtolower($p, "UTF-8");
  }
  return array_values(array_unique($out));
}

function majors_match($playlist_major, $student_major){
  $A = majors_list($playlist_major);
  $B = majors_list($student_major);
  if (!count($A) || !count($B)) return false;
  foreach($A as $a){
    if (in_array($a, $B, true)) return true;
  }
  return false;
}

function get_playlist_videos($conn, $playlist_id){
  $videos = [];

  $st = $conn->prepare("
    SELECT id, title
    FROM partner_videos
    WHERE playlist_id = ?
    ORDER BY id DESC
  ");
  $st->bind_param("i", $playlist_id);
  $st->execute();
  $rs = $st->get_result();

  while ($v = $rs->fetch_assoc()) {
    $videos[] = [
      "id" => (int)$v["id"],
      "title" => (string)$v["title"]
    ];
  }

  $st->close();
  return $videos;
}

function send_video_response($row, $playlist_videos){
  echo json_encode([
    "ok" => true,
    "video" => [
      "id" => (int)$row["id"],
      "title" => (string)$row["title"],
      "stored_path" => (string)$row["stored_path"],
      "playlist_id" => (int)$row["playlist_id"],
    ],
    "playlist" => [
      "id" => (int)$row["playlist_id"],
      "name" => (string)$row["playlist_name"],
      "difficulty" => (int)($row["difficulty"] ?? 0),
      "coin_pool" => (int)($row["coin_pool"] ?? 0),
      "videos" => $playlist_videos
    ]
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$q = $conn->prepare("
  SELECT
    v.id, v.title, v.stored_path, v.playlist_id,
    p.name AS playlist_name, p.is_published, p.major_text, p.difficulty, p.coin_pool,
    p.path_id, p.source_path_id,
    COALESCE(p.source_path_id, p.path_id) AS path_ref
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  WHERE v.id = ?
  LIMIT 1
");
$q->bind_param("i", $video_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

if (!$row) {
  http_response_code(404);
  echo json_encode(["error"=>"VIDEO_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

if ((int)$row["is_published"] !== 1) {
  http_response_code(403);
  echo json_encode(["error"=>"FORBIDDEN_VIDEO_PLAYLIST_NOT_PUBLISHED"], JSON_UNESCAPED_UNICODE);
  exit;
}

$playlist_videos = get_playlist_videos($conn, (int)$row["playlist_id"]);

$stp = $conn->prepare("SELECT path_id FROM user_selected_path WHERE user_id=? LIMIT 1");
$stp->bind_param("i", $user_id);
$stp->execute();
$sr = $stp->get_result()->fetch_assoc();
$stp->close();

$selected_path_id = (int)($sr["path_id"] ?? 0);
$video_path_ref   = (int)($row["path_ref"] ?? 0);

if ($selected_path_id > 0 && $video_path_ref > 0 && $selected_path_id === $video_path_ref) {
  send_video_response($row, $playlist_videos);
}

$student_major = "";
$m = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
$m->bind_param("i", $user_id);
$m->execute();
$mr = $m->get_result()->fetch_assoc();
$m->close();

$student_major = trim((string)($mr["major_text"] ?? ""));
$playlist_major = trim((string)($row["major_text"] ?? ""));

if ($playlist_major !== "" && $student_major !== "") {
  if (!majors_match($playlist_major, $student_major)) {
    http_response_code(403);
    echo json_encode([
      "error" => "FORBIDDEN_VIDEO_MAJOR_MISMATCH",
      "playlist_major" => $playlist_major,
      "student_major" => $student_major
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }

  send_video_response($row, $playlist_videos);
}

$sqlOld = "
  SELECT v.id
  FROM partner_videos v
  JOIN partner_playlists p ON p.id = v.playlist_id
  JOIN user_plan_courses upc
    ON upc.user_id = ?
   AND upc.is_required = 1
   AND (
        LOWER(p.name) = LOWER(upc.course_name)
        OR LOWER(p.name) LIKE CONCAT('%', LOWER(upc.course_name), '%')
        OR LOWER(upc.course_name) LIKE CONCAT('%', LOWER(p.name), '%')
   )
  WHERE v.id = ?
  LIMIT 1
";
$stmt = $conn->prepare($sqlOld);
$stmt->bind_param("ii", $user_id, $video_id);
$stmt->execute();
$ok = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ok) {
  http_response_code(403);
  echo json_encode(["error"=>"FORBIDDEN_VIDEO"], JSON_UNESCAPED_UNICODE);
  exit;
}

send_video_response($row, $playlist_videos);