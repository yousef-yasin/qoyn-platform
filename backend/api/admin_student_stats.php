<?php
// Admin: learning stats for a given student
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

function table_exists($conn, $name){
  $st = $conn->prepare("SHOW TABLES LIKE ?");
  $st->bind_param("s", $name);
  $st->execute();
  $st->store_result();
  $ok = $st->num_rows > 0;
  $st->close();
  return $ok;
}

$student_id = (int)($_GET["student_id"] ?? 0);
if ($student_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_STUDENT_ID"], 400);

// Basic student info
$ust = $conn->prepare("SELECT id, full_name, email, COALESCE(major_text,'') AS major_text FROM users WHERE id=? AND role='student' LIMIT 1");
$ust->bind_param("i", $student_id);
$ust->execute();
$student = $ust->get_result()->fetch_assoc();
$ust->close();
if (!$student) json_out(["ok"=>false,"error"=>"STUDENT_NOT_FOUND"], 404);

if (!table_exists($conn, "student_video_progress")) {
  json_out([
    "ok"=>true,
    "student"=>[
      "id"=>(int)$student["id"],
      "full_name"=>(string)$student["full_name"],
      "email"=>(string)$student["email"],
      "major_text"=>(string)$student["major_text"],
    ],
    "totals"=>["watched_videos"=>0,"completed_videos"=>0,"watched_seconds_sum"=>0],
    "by_playlist"=>[],
    "recent"=>[]
  ]);
}

// Totals
$tst = $conn->prepare("
  SELECT
    COUNT(DISTINCT video_id) AS watched_videos,
    COALESCE(SUM(completed),0) AS completed_videos,
    COALESCE(SUM(watched_seconds),0) AS watched_seconds_sum
  FROM student_video_progress
  WHERE user_id=?
");
$tst->bind_param("i", $student_id);
$tst->execute();
$tot = $tst->get_result()->fetch_assoc();
$tst->close();

// By playlist (if partner_videos exists)
$by_playlist = [];
if (table_exists($conn, "partner_videos") && table_exists($conn, "partner_playlists")) {
  $pst = $conn->prepare("
    SELECT
      pl.id AS playlist_id,
      pl.name AS playlist_name,
      COUNT(DISTINCT p.video_id) AS watched_videos,
      COALESCE(SUM(p.completed),0) AS completed_videos,
      COALESCE(SUM(p.watched_seconds),0) AS watched_seconds_sum
    FROM student_video_progress p
    JOIN partner_videos v ON v.id = p.video_id
    LEFT JOIN partner_playlists pl ON pl.id = v.playlist_id
    WHERE p.user_id=?
    GROUP BY pl.id, pl.name
    ORDER BY watched_seconds_sum DESC
    LIMIT 50
  ");
  $pst->bind_param("i", $student_id);
  $pst->execute();
  $r = $pst->get_result();
  while ($row = $r->fetch_assoc()) {
    $by_playlist[] = [
      "playlist_id" => (int)($row["playlist_id"] ?? 0),
      "playlist_name" => (string)($row["playlist_name"] ?? ""),
      "watched_videos" => (int)($row["watched_videos"] ?? 0),
      "completed_videos" => (int)($row["completed_videos"] ?? 0),
      "watched_seconds_sum" => (int)($row["watched_seconds_sum"] ?? 0),
    ];
  }
  $pst->close();
}

// Recent watched videos
$recent = [];
if (table_exists($conn, "partner_videos")) {
  // We don't store updated_at in progress table, so we approximate by highest watched_seconds.
  $rst = $conn->prepare("
    SELECT
      v.id AS video_id,
      v.title,
      v.playlist_id,
      p.watched_seconds,
      p.completed
    FROM student_video_progress p
    LEFT JOIN partner_videos v ON v.id = p.video_id
    WHERE p.user_id=?
    ORDER BY p.watched_seconds DESC
    LIMIT 20
  ");
  $rst->bind_param("i", $student_id);
  $rst->execute();
  $r = $rst->get_result();
  while ($row = $r->fetch_assoc()) {
    $recent[] = [
      "video_id" => (int)($row["video_id"] ?? 0),
      "title" => (string)($row["title"] ?? ""),
      "playlist_id" => (int)($row["playlist_id"] ?? 0),
      "watched_seconds" => (int)($row["watched_seconds"] ?? 0),
      "completed" => (int)($row["completed"] ?? 0),
    ];
  }
  $rst->close();
}

json_out([
  "ok"=>true,
  "student"=>[
    "id"=>(int)$student["id"],
    "full_name"=>(string)$student["full_name"],
    "email"=>(string)$student["email"],
    "major_text"=>(string)$student["major_text"],
  ],
  "totals"=>[
    "watched_videos" => (int)($tot["watched_videos"] ?? 0),
    "completed_videos" => (int)($tot["completed_videos"] ?? 0),
    "watched_seconds_sum" => (int)($tot["watched_seconds_sum"] ?? 0),
  ],
  "by_playlist"=>$by_playlist,
  "recent"=>$recent,
]);
