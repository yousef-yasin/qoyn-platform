<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"]); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$course = trim((string)($_GET["course"] ?? ""));
if ($course === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_COURSE"]);
  exit;
}

// ensure columns
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN course_name VARCHAR(220) NULL");

// student major
$student_major = "";
$u = $conn->prepare("SELECT major_text FROM users WHERE id=? LIMIT 1");
$u->bind_param("i", $user_id);
$u->execute();
$ur = $u->get_result()->fetch_assoc();
$u->close();
$student_major = trim((string)($ur["major_text"] ?? ""));

$like = "%".mb_strtolower($course, "UTF-8")."%";

$sql = "
  SELECT
    p.id AS playlist_id,
    p.name AS playlist_name,
    p.course_name,
    p.difficulty,
    p.coin_pool,
    p.published_at,
    COALESCE(u.full_name, 'Partner') AS partner_name
  FROM partner_playlists p
  JOIN users u ON u.id = p.partner_user_id
  WHERE p.is_published = 1
    AND (p.major_text IS NULL OR TRIM(p.major_text)='' OR LOWER(TRIM(p.major_text)) = LOWER(TRIM(?)))
    AND (
        LOWER(COALESCE(p.course_name,'')) LIKE ?
        OR LOWER(p.name) LIKE ?
        OR LOWER(p.description) LIKE ?
    )
  ORDER BY p.published_at DESC, p.id DESC
  LIMIT 12
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","details"=>$conn->error]);
  exit;
}

$stmt->bind_param("ssss", $student_major, $like, $like, $like);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while($row = $res->fetch_assoc()){
  $items[] = [
    "playlist_id" => (int)$row["playlist_id"],
    "playlist_name" => (string)$row["playlist_name"],
    "course_name" => (string)($row["course_name"] ?? ""),
    "difficulty" => (int)$row["difficulty"],
    "coin_pool" => (int)$row["coin_pool"],
    "published_at" => (string)($row["published_at"] ?? ""),
    "partner_name" => (string)$row["partner_name"],
  ];
}

// ✅ Fallback: إذا ما في نتائج حسب course (بسبب اختلاف عربي/انجليزي) رجّع آخر Playlists للتخصص
if (!count($items)) {

  $sql2 = "
    SELECT
      p.id AS playlist_id,
      p.name AS playlist_name,
      p.course_name,
      p.difficulty,
      p.coin_pool,
      p.published_at,
      COALESCE(u.full_name, 'Partner') AS partner_name
    FROM partner_playlists p
    JOIN users u ON u.id = p.partner_user_id
    WHERE p.is_published = 1
      AND (
        p.major_text IS NULL OR TRIM(p.major_text) = ''
        OR LOWER(TRIM(p.major_text)) = LOWER(TRIM(?))
      )
    ORDER BY p.published_at DESC, p.id DESC
    LIMIT 12
  ";

  $st2 = $conn->prepare($sql2);
  if ($st2) {
    $st2->bind_param("s", $student_major);
    $st2->execute();
    $rs2 = $st2->get_result();

    while ($row = $rs2->fetch_assoc()) {
      $items[] = [
        "playlist_id" => (int)$row["playlist_id"],
        "playlist_name" => (string)$row["playlist_name"],
        "course_name" => (string)($row["course_name"] ?? ""),
        "difficulty" => (int)($row["difficulty"] ?? 0),
        "coin_pool" => (int)($row["coin_pool"] ?? 0),
        "published_at" => (string)($row["published_at"] ?? ""),
        "partner_name" => (string)($row["partner_name"] ?? "Partner"),
      ];
    }
    $st2->close();
  }
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);
