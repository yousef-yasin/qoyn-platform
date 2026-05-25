<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [
  __DIR__ . "/db.php",
  __DIR__ . "/../db.php",
  __DIR__ . "/../config/db.php",
  __DIR__ . "/../includes/db.php"
];

$found = null;
foreach ($try as $p) {
  if (file_exists($p)) {
    $found = $p;
    break;
  }
}

if (!$found) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok" => false, "error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int) $_SESSION["user_id"];

$coins_video = 0;
$coins_code = 0;
$coins_training = 0;
$coins_phase2 = 0;
$coins_user = 0;

// video_rewards
$hasVR = $conn->query("SHOW TABLES LIKE 'video_rewards'");
if ($hasVR && $hasVR->num_rows > 0) {
  $st = $conn->prepare("SELECT COALESCE(SUM(total_coin),0) AS s FROM video_rewards WHERE user_id=?");
  if ($st) {
    $st->bind_param("i", $user_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $coins_video = (int) ($row["s"] ?? 0);
    $st->close();
  }
}

// code_rewards
$hasCR = $conn->query("SHOW TABLES LIKE 'code_rewards'");
if ($hasCR && $hasCR->num_rows > 0) {
  $st = $conn->prepare("SELECT COALESCE(SUM(coin_awarded),0) AS s FROM code_rewards WHERE user_id=?");
  if ($st) {
    $st->bind_param("i", $user_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $coins_code = (int) ($row["s"] ?? 0);
    $st->close();
  }
}

// training_rewards
$hasTR = $conn->query("SHOW TABLES LIKE 'training_rewards'");
if ($hasTR && $hasTR->num_rows > 0) {
  $st = $conn->prepare("SELECT COALESCE(SUM(coins_awarded),0) AS s FROM training_rewards WHERE user_id=?");
  if ($st) {
    $st->bind_param("i", $user_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $coins_training = (int) ($row["s"] ?? 0);
    $st->close();
  }
}

// phase2_submissions
$hasP2 = $conn->query("SHOW TABLES LIKE 'phase2_submissions'");
if ($hasP2 && $hasP2->num_rows > 0) {
  $st = $conn->prepare("SELECT COALESCE(SUM(coins_awarded),0) AS s FROM phase2_submissions WHERE user_id=?");
  if ($st) {
    $st->bind_param("i", $user_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $coins_phase2 = (int) ($row["s"] ?? 0);
    $st->close();
  }
}

// users.coins
$st = $conn->prepare("SELECT COALESCE(coins,0) AS c FROM users WHERE id=? LIMIT 1");
if ($st) {
  $st->bind_param("i", $user_id);
  $st->execute();
  $row = $st->get_result()->fetch_assoc();
  $coins_user = (int) ($row["c"] ?? 0);
  $st->close();
}

// اعتمد فقط رصيد المستخدم من جدول users
$coins_total = $coins_user;

echo json_encode([
  "ok" => true,
  "user_id" => $user_id,
  "coins_total" => $coins_total,
  "breakdown" => [
    "users_coins" => $coins_user,
    "video_rewards" => $coins_video,
    "code_rewards" => $coins_code,
    "training_rewards" => $coins_training,
    "phase2_rewards" => $coins_phase2
  ]
], JSON_UNESCAPED_UNICODE);