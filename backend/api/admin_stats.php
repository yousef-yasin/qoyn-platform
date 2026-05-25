<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

function table_exists($conn, $name){
  $name = $conn->real_escape_string($name);
  $res = $conn->query("SHOW TABLES LIKE '$name'");
  if(!$res) return false;
  return $res->num_rows > 0;
}

$total_students = table_exists($conn, "users")
  ? (int)($conn->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch_assoc()["c"] ?? 0)
  : 0;

$total_partners = table_exists($conn, "users")
  ? (int)($conn->query("SELECT COUNT(*) c FROM users WHERE role='partner'")->fetch_assoc()["c"] ?? 0)
  : 0;

$total_playlists = table_exists($conn, "partner_playlists")
  ? (int)($conn->query("SELECT COUNT(*) c FROM partner_playlists")->fetch_assoc()["c"] ?? 0)
  : 0;

$total_videos = table_exists($conn, "partner_videos")
  ? (int)($conn->query("SELECT COUNT(*) c FROM partner_videos")->fetch_assoc()["c"] ?? 0)
  : 0;


$total_video_views = table_exists($conn, "student_video_progress")
  ? (int)($conn->query("SELECT COUNT(*) c FROM student_video_progress")->fetch_assoc()["c"] ?? 0)
  : 0;

$total_video_completions = table_exists($conn, "student_video_progress")
  ? (int)($conn->query("SELECT COALESCE(SUM(completed),0) c FROM student_video_progress")->fetch_assoc()["c"] ?? 0)
  : 0;

json_out([
  "ok"=>true,
  "total_students"=>$total_students,
  "total_partners"=>$total_partners,
  "total_playlists"=>$total_playlists,
  "total_videos"=>$total_videos,
  "total_video_views"=>$total_video_views,
  "total_video_completions"=>$total_video_completions
]);
