<?php
// utbn-backend/api/partner_analytics_playlists.php
session_start();
header("Content-Type: application/json; charset=utf-8");

// حمّل db.php تبعك (حسب هيكل مشروعك)
$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}
require_once $found;

// تأكد إنه $conn موجود
if (!isset($conn)) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_CONN_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "partner") {
  http_response_code(403);
  echo json_encode(["ok"=>false,"error"=>"NOT_PARTNER"], JSON_UNESCAPED_UNICODE);
  exit;
}

$me = (int)$_SESSION["user_id"];

// Helper: fetch all assoc safely (mysqlnd or fallback)
function fetch_all_assoc(mysqli_stmt $stmt){
  $rows = [];
  $res = $stmt->get_result();
  if ($res !== false) {
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    return $rows;
  }

  // fallback without mysqlnd
  $meta = $stmt->result_metadata();
  if (!$meta) return $rows;

  $fields = [];
  $row = [];
  $bind = [];
  while ($field = $meta->fetch_field()) {
    $row[$field->name] = null;
    $bind[] = &$row[$field->name];
  }
  call_user_func_array([$stmt, "bind_result"], $bind);
  while ($stmt->fetch()) {
    $copy = [];
    foreach ($row as $k=>$v) $copy[$k] = $v;
    $rows[] = $copy;
  }
  return $rows;
}

try {

  // 1) playlists + videos_count
  $sqlPlaylists = "
    SELECT
      pl.id,
      pl.name,
      pl.course_name,
      pl.major_text,
      pl.created_at,
      pl.is_published,
      pl.coin_pool,
      COUNT(pv.id) AS videos_count
    FROM partner_playlists pl
    LEFT JOIN partner_videos pv ON pv.playlist_id = pl.id
    WHERE pl.partner_user_id = ?
    GROUP BY pl.id
    ORDER BY pl.created_at DESC
  ";

  $stmt = $conn->prepare($sqlPlaylists);
  if(!$stmt){
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"PREPARE_FAILED: ".$conn->error], JSON_UNESCAPED_UNICODE);
    exit;
  }
  $stmt->bind_param("i", $me);
  $stmt->execute();
  $playlists = fetch_all_assoc($stmt);
  $stmt->close();

  $items = [];

  // 2) لكل playlist: فيديوهاتها + submissions
  $sqlVideos = "
    SELECT
      pv.id,
      pv.title,
      pv.created_at,
      (
      SELECT COUNT(*) FROM partner_video_submissions s
WHERE s.partner_video_id = pv.id

      ) AS submissions
    FROM partner_videos pv
    WHERE pv.playlist_id = ?
    ORDER BY pv.created_at DESC
  ";

  $stmt2 = $conn->prepare($sqlVideos);
  if(!$stmt2){
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"PREPARE_FAILED_VIDEOS: ".$conn->error], JSON_UNESCAPED_UNICODE);
    exit;
  }

  foreach($playlists as $pl){
    $pid = (int)$pl["id"];

    $stmt2->bind_param("i", $pid);
    $stmt2->execute();
    $videos = fetch_all_assoc($stmt2);

    $totalSub = 0;
    foreach($videos as $v) $totalSub += (int)($v["submissions"] ?? 0);

    $items[] = [
      "playlist_id"   => $pid,
      "playlist_name" => $pl["name"] ?? "",
      "course_name"   => $pl["course_name"] ?? "",
      "major_text"    => $pl["major_text"] ?? "",
      "is_published"  => (int)($pl["is_published"] ?? 0),
      "coin_pool"     => (int)($pl["coin_pool"] ?? 0),
      "created_at"    => $pl["created_at"] ?? "",
      "videos_count"  => (int)($pl["videos_count"] ?? 0),

      // إذا بدك views/completed لاحقًا بنضيفهم حسب جداولك
      "views"         => 0,
      "completed"     => 0,

      "submissions"   => $totalSub,
      "videos"        => $videos
    ];
  }

  $stmt2->close();

  echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
