<?php
// utbn-backend/api/ai_predict_level.php
// Predict student level based on last N performance rows.
// ✅ avg_watch normalized to 0..1 (handles old 0..100 data).
// ✅ rule fallback uses 0.85 threshold (not 85).

require __DIR__ . "/db.php";
require_once __DIR__ . "/_ensure_tracking_tables.php";
ensure_tracking_tables($conn);

require_login();


header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);

$N = (int)($_GET["n"] ?? 20);
if ($N <= 0 || $N > 200) $N = 20;

$stmt = $conn->prepare("
  SELECT score_percent, time_spent_seconds, watched_percent, difficulty
  FROM student_performance
  WHERE user_id=?
  ORDER BY created_at DESC
  LIMIT ?
");


if (!$stmt) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);

$stmt->bind_param("ii", $user_id, $N);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
$stmt->close();

if (count($rows) < 5) {
  json_out(["ok"=>true, "ready"=>false, "reason"=>"NOT_ENOUGH_DATA", "need"=>5, "have"=>count($rows)]);
}

// --- Feature engineering ---
$cnt = count($rows);
$sumScore=0; $sumTime=0; $sumWatch=0.0; $sumDiff=0;
$hardScoreSum=0; $hardCnt=0;

foreach ($rows as $r) {
  $s = (float)($r["score_percent"] ?? 0);
  $t = (int)($r["time_spent_seconds"] ?? 0);

  // watched_percent مخزن 0..1 (بس نخلي normalize للقديم لو في)
  $w = (float)($r["watched_percent"] ?? 0);
  $d = (int)($r["difficulty"] ?? 3);

  // ✅ normalize old data (0..100) => (0..1)
  if ($w > 1.0) $w = $w / 100.0;

  // clamp
  if ($w < 0) $w = 0.0;
  if ($w > 1) $w = 1.0;

  $d = (int)($r["difficulty"] ?? 3);

  $sumScore += $s;
  $sumTime  += $t;
  $sumWatch += $w;
  $sumDiff  += $d;

  if ($d >= 4) { $hardScoreSum += $s; $hardCnt++; }
}


$avg_score = $sumScore / max(1,$cnt);
$avg_time  = $sumTime  / max(1,$cnt);
$avg_watch = $sumWatch / max(1,$cnt);   // ✅ now 0..1
$avg_diff  = $sumDiff  / max(1,$cnt);
$hard_avg  = ($hardCnt>0) ? ($hardScoreSum/$hardCnt) : null;

// ✅ ADD-ON: speed feature (model expects it sometimes)
$speed = 0.0;
if ($avg_time > 0) {
  // score per second (simple & stable)
  $speed = $avg_score / $avg_time;
}

$features = [
  "n" => $cnt,
  "avg_score" => $avg_score,
  "avg_time"  => $avg_time,
  "avg_watch" => $avg_watch,
  "avg_difficulty" => $avg_diff,
  "hard_avg_score" => $hard_avg,
  "speed" => $speed, // ✅ added
];

// --- Call Python AI service (optional). If down, fall back to rule-based. ---
// ✅ FIX: your FastAPI runs on 127.0.0.1:5005
$ai_url = getenv("AI_SERVICE_URL") ?: "http://127.0.0.1:5005/predict_level";

$payload = json_encode([
  "user_id" => $user_id,
  "features" => $features
], JSON_UNESCAPED_UNICODE);

$level = null;
$phase_ready = 0;
$phase2_ready = 0;
$phase3_ready = 0;
$model_version = "rule-v1";

$ch = curl_init($ai_url);
if ($ch) {
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_TIMEOUT, 3);

  $resp = curl_exec($ch);
  $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($resp && $http >= 200 && $http < 300) {
    $j = json_decode($resp, true);
    if (is_array($j) && isset($j["level"])) {
      $level = (string)$j["level"];
      $phase_ready  = (int)($j["phase_ready"] ?? 0);
      $phase2_ready = (int)($j["phase2_ready"] ?? 0);
      $phase3_ready = (int)($j["phase3_ready"] ?? 0);
      $model_version = (string)($j["model_version"] ?? "ml-v1");
    }
  }
}

if ($level === null) {
  // ----- Rule-based fallback (works even without Python) -----
  $avg = (float)$features["avg_score"];

  if ($avg >= 85) $level = "advanced";
  else if ($avg >= 65) $level = "intermediate";
  else $level = "beginner";

  // ✅ FIX: avg_watch is 0..1, so threshold must be 0.85 (not 85)
  $phase2_ready = ($avg >= 75 && (float)$features["avg_watch"] >= 0.85) ? 1 : 0;
  $phase3_ready = ($avg >= 85 && (float)$features["avg_watch"] >= 0.90) ? 1 : 0;
  $phase_ready  = $phase2_ready;
}

// cache the latest prediction (best effort)
$up = $conn->prepare("
  INSERT INTO user_level_predictions (user_id, level_label, phase_ready, model_version, updated_at)
  VALUES (?,?,?,?,NOW())
  ON DUPLICATE KEY UPDATE
    level_label=VALUES(level_label),
    phase_ready=VALUES(phase_ready),
    model_version=VALUES(model_version),
    updated_at=NOW()
");
if ($up) {
  $up->bind_param("isis", $user_id, $level, $phase_ready, $model_version);
  @$up->execute();
  $up->close();
}

json_out([
  "ok" => true,
  "features" => $features,
  "level" => $level,
  "phase_ready"  => (bool)$phase_ready,
  "phase2_ready" => (bool)$phase2_ready,
  "phase3_ready" => (bool)$phase3_ready,
  "model_version" => $model_version
]);
