<?php
require __DIR__ . "/db.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
  json_out(["error" => "FORBIDDEN"], 403);
}

$user_id = (int)$_SESSION["user_id"];
$course_id = (int)($_GET["course_id"] ?? 0);
if ($course_id <= 0) json_out(["error"=>"MISSING_COURSE_ID"], 400);

// course
$stmt = $conn->prepare("SELECT id, code, name, description FROM courses WHERE id=? LIMIT 1");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) json_out(["error"=>"COURSE_NOT_FOUND"],404);

// subscription status
$subStmt = $conn->prepare("SELECT status, end_at FROM subscriptions WHERE user_id=? ORDER BY id DESC LIMIT 1");
$subStmt->bind_param("i", $user_id);
$subStmt->execute();
$sub = $subStmt->get_result()->fetch_assoc();
$sub_active = false;
if ($sub && $sub["status"] === "active") {
  if (!$sub["end_at"] || strtotime($sub["end_at"]) >= time()) $sub_active = true;
}

$stmtT = $conn->prepare("SELECT id, title, description, coin_reward, sort_order FROM trainings WHERE course_id=? ORDER BY sort_order ASC, id ASC");
$stmtT->bind_param("i", $course_id);
$stmtT->execute();
$tres = $stmtT->get_result();

$trainings = [];
while ($t = $tres->fetch_assoc()) {
  $tid = (int)$t["id"];

  $vstmt = $conn->prepare("SELECT id, title, is_paid, video_url, sort_order FROM videos WHERE training_id=? ORDER BY sort_order ASC, id ASC");
  $vstmt->bind_param("i", $tid);
  $vstmt->execute();
  $vres = $vstmt->get_result();

  $videos = [];
  while ($v = $vres->fetch_assoc()) {
    $vid = (int)$v["id"];
    $pstmt = $conn->prepare("SELECT watched FROM video_progress WHERE user_id=? AND video_id=? LIMIT 1");
    $pstmt->bind_param("ii", $user_id, $vid);
    $pstmt->execute();
    $p = $pstmt->get_result()->fetch_assoc();
    $watched = $p ? ((int)$p["watched"] === 1) : false;

    $locked = ((int)$v["is_paid"] === 1) && !$sub_active;

    $videos[] = [
      "id" => $vid,
      "title" => $v["title"],
      "is_paid" => ((int)$v["is_paid"] === 1),
      "locked" => $locked,
      "watched" => $watched
    ];
  }

  $qstmt = $conn->prepare("SELECT id, title, pass_score FROM quizzes WHERE training_id=? LIMIT 1");
  $qstmt->bind_param("i", $tid);
  $qstmt->execute();
  $quiz = $qstmt->get_result()->fetch_assoc();
  $quiz_summary = null;
  if ($quiz) {
    $qid = (int)$quiz["id"];
    $sstmt = $conn->prepare("SELECT score, passed, submitted_at FROM quiz_submissions WHERE user_id=? AND quiz_id=? ORDER BY id DESC LIMIT 1");
    $sstmt->bind_param("ii", $user_id, $qid);
    $sstmt->execute();
    $subm = $sstmt->get_result()->fetch_assoc();

    $quiz_summary = [
      "id" => $qid,
      "title" => $quiz["title"],
      "pass_score" => (int)$quiz["pass_score"],
      "last_score" => $subm ? (int)$subm["score"] : null,
      "passed" => $subm ? ((int)$subm["passed"] === 1) : false
    ];
  }

  $trainings[] = [
    "id" => $tid,
    "title" => $t["title"],
    "description" => $t["description"],
    "coin_reward" => (int)$t["coin_reward"],
    "videos" => $videos,
    "quiz" => $quiz_summary
  ];
}

json_out([
  "course" => $course,
  "subscription_active" => $sub_active,
  "trainings" => $trainings
]);