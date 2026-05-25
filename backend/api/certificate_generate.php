<?php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) json_out(["ok"=>false,"error"=>"NO_SESSION"], 401);

// ---------- DB migration (safe/optional) ----------
function ensure_certificate_columns(mysqli $conn) {
  $wanted = [
    "student_name" => "ALTER TABLE certificates ADD COLUMN student_name VARCHAR(100) NULL",
    "major_name"   => "ALTER TABLE certificates ADD COLUMN major_name VARCHAR(120) NULL",
    "token"        => "ALTER TABLE certificates ADD COLUMN token VARCHAR(64) NULL",
    "pdf_path"     => "ALTER TABLE certificates ADD COLUMN pdf_path VARCHAR(255) NULL",
  ];

  $cols = [];
  $res = $conn->query("SHOW COLUMNS FROM certificates");
  if ($res) {
    while ($r = $res->fetch_assoc()) $cols[strtolower($r["Field"])] = true;
  }

  foreach ($wanted as $col => $sql) {
    if (!isset($cols[strtolower($col)])) {
      @ $conn->query($sql);
    }
  }

  $idx = $conn->query("SHOW INDEX FROM certificates WHERE Key_name='uniq_cert_token'");
  if (!$idx || $idx->num_rows === 0) {
    @ $conn->query("ALTER TABLE certificates ADD UNIQUE KEY uniq_cert_token (token)");
  }
}

ensure_certificate_columns($conn);

// ---------- fetch student name + major_text ----------
$stU = $conn->prepare("SELECT full_name, COALESCE(major_text,'') AS major_text FROM users WHERE id=? LIMIT 1");
if (!$stU) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);
$stU->bind_param("i", $user_id);
$stU->execute();
$u = $stU->get_result()->fetch_assoc();
$stU->close();

if (!$u) json_out(["ok"=>false,"error"=>"NO_USER"], 400);

$student_name = trim((string)($u["full_name"] ?? ""));
$major_name   = trim((string)($u["major_text"] ?? ""));

// ---------- compute coins (same logic as coins.php) ----------
$coins_video = 0;
$coins_code  = 0;

// video_rewards sum
$hasVR = $conn->query("SHOW TABLES LIKE 'video_rewards'");
if ($hasVR && $hasVR->num_rows > 0) {
  $st = $conn->prepare("SELECT COALESCE(SUM(total_coin),0) AS s FROM video_rewards WHERE user_id=?");
  if (!$st) json_out(["ok"=>false,"error"=>"COINS_SUM_PREPARE_FAILED","details"=>$conn->error], 500);
  $st->bind_param("i", $user_id);
  $st->execute();
  $row = $st->get_result()->fetch_assoc();
  $st->close();
  $coins_video = (int)($row["s"] ?? 0);
}

// code_rewards sum
$hasCR = $conn->query("SHOW TABLES LIKE 'code_rewards'");
if ($hasCR && $hasCR->num_rows > 0) {
  $st2 = $conn->prepare("SELECT COALESCE(SUM(coin_awarded),0) AS s FROM code_rewards WHERE user_id=?");
  if ($st2) {
    $st2->bind_param("i", $user_id);
    $st2->execute();
    $row2 = $st2->get_result()->fetch_assoc();
    $st2->close();
    $coins_code = (int)($row2["s"] ?? 0);
  }
}

$coins = $coins_video + $coins_code;

if ($coins < 10000) {
  json_out([
    "ok"=>false,
    "error"=>"NOT_ENOUGH_COINS",
    "need"=>10000,
    "coins"=>$coins,
    "coins_video"=>$coins_video,
    "coins_code"=>$coins_code
  ], 403);
}

// ---------- create (idempotent) ----------
$title  = "UTBN - شهادة إتمام المرحلة الأولى";
$issued = date("Y-m-d H:i:s");

$stE = $conn->prepare("SELECT id, token, pdf_path FROM certificates WHERE user_id=? AND title=? LIMIT 1");
if (!$stE) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);
$stE->bind_param("is", $user_id, $title);
$stE->execute();
$existing = $stE->get_result()->fetch_assoc();
$stE->close();

if ($existing) {
  $cid = (int)($existing["id"] ?? 0);
  $token = (string)($existing["token"] ?? "");
  json_out([
    "ok" => true,
    "certificate_id" => $cid,
    "view_url" => "/utbn-backend/api/certificate_view.php?id=" . $cid,
    "download_url" => "/utbn-backend/api/certificate_download.php?id=" . $cid,
    "token" => $token,
  ]);
}

// token for future share links (optional)
$token = bin2hex(random_bytes(16)); // 32 chars

$stI = $conn->prepare(
  "INSERT INTO certificates (user_id, title, issued_at, student_name, major_name, token)
   VALUES (?,?,?,?,?,?)"
);
if (!$stI) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);
$stI->bind_param("isssss", $user_id, $title, $issued, $student_name, $major_name, $token);
$stI->execute();
$cid = (int)$conn->insert_id;
$stI->close();

json_out([
  "ok" => true,
  "certificate_id" => $cid,
  "view_url" => "/utbn-backend/api/certificate_view.php?id=" . $cid,
  "download_url" => "/utbn-backend/api/certificate_download.php?id=" . $cid,
  "token" => $token,
]);
