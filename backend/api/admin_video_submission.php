<?php
// Admin: detailed submission for a specific (video_id, student_id)
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";
header("Content-Type: application/json; charset=utf-8");

$video_id = (int)($_GET["video_id"] ?? 0);
$student_id = (int)($_GET["student_id"] ?? 0);
if ($video_id <= 0 || $student_id <= 0) {
  json_out(["ok"=>false, "error"=>"MISSING_FIELDS"], 400);
}

// Ensure submissions table exists
$conn->query("CREATE TABLE IF NOT EXISTS partner_video_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  partner_video_id INT UNSIGNED NOT NULL,
  student_user_id INT UNSIGNED NOT NULL,
  answers_json MEDIUMTEXT NOT NULL,
  score INT NOT NULL DEFAULT 0,
  total INT NOT NULL DEFAULT 0,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_video_student (partner_video_id, student_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Video info
$vst = $conn->prepare("SELECT id, title, playlist_id, partner_user_id FROM partner_videos WHERE id=? LIMIT 1");
$vst->bind_param("i", $video_id);
$vst->execute();
$video = $vst->get_result()->fetch_assoc();
$vst->close();
if (!$video) json_out(["ok"=>false, "error"=>"VIDEO_NOT_FOUND"], 404);

// Student info (optional)
$ust = $conn->prepare("SELECT id, full_name, email FROM users WHERE id=? LIMIT 1");
$ust->bind_param("i", $student_id);
$ust->execute();
$student = $ust->get_result()->fetch_assoc();
$ust->close();

$st = $conn->prepare("SELECT answers_json, score, total, submitted_at FROM partner_video_submissions WHERE partner_video_id=? AND student_user_id=? LIMIT 1");
$st->bind_param("ii", $video_id, $student_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();

if (!$row) {
  json_out([
    "ok" => true,
    "video" => [
      "id" => (int)$video["id"],
      "title" => (string)$video["title"],
      "playlist_id" => (int)$video["playlist_id"],
      "partner_user_id" => (int)$video["partner_user_id"],
    ],
    "student" => $student ? [
      "id" => (int)$student["id"],
      "full_name" => (string)($student["full_name"] ?? ""),
      "email" => (string)($student["email"] ?? ""),
    ] : null,
    "submission" => null,
  ]);
}

$answers = json_decode($row["answers_json"], true);
if (!is_array($answers)) $answers = null;

json_out([
  "ok" => true,
  "video" => [
    "id" => (int)$video["id"],
    "title" => (string)$video["title"],
    "playlist_id" => (int)$video["playlist_id"],
    "partner_user_id" => (int)$video["partner_user_id"],
  ],
  "student" => $student ? [
    "id" => (int)$student["id"],
    "full_name" => (string)($student["full_name"] ?? ""),
    "email" => (string)($student["email"] ?? ""),
  ] : null,
  "submission" => [
    "score" => (int)$row["score"],
    "total" => (int)$row["total"],
    "submitted_at" => $row["submitted_at"],
    "data" => $answers,
  ]
]);
