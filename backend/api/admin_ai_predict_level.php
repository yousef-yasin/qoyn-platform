<?php
// utbn-backend/api/admin_ai_predict_level.php
require __DIR__ . "/db.php";
require_once __DIR__ . "/require_admin.php";

header("Content-Type: application/json; charset=utf-8");

$student_id = (int)($_GET["student_id"] ?? 0);
if ($student_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_STUDENT_ID"], 400);

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

$stmt->bind_param("ii", $student_id, $N);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
$stmt->close();

if (count($rows) < 5) {
  json_out(["ok"=>true,"reason"=>"NOT_ENOUGH_DATA","need"=>5,"have"=>count($rows)]);
}

$cnt = count($rows);
$sumScore=0; $sumTime=0; $sumWatch=0.0; $sumDiff=0;
$hardScoreSum=0; $hardCnt=0;

foreach ($rows as $r) {
  $s = (int)($r["score_percent"] ?? 0);
  $t = (int)($r["time_spent_seconds"] ?? 0);

  $w = (float)($r["watched_percent"] ?? 0);
  if ($w > 1.0) $w = $w / 100.0; // normalize old data
  if ($w < 0) $w = 0;
  if ($w > 1) $w = 1;

  $d = (int)($r["difficulty"] ?? 3);

  $sumScore += $s;
  $sumTime  += $t;
  $sumWatch += $w;
  $sumDiff  += $d;

  if ($d >= 4) { $hardScoreSum += $s; $hardCnt++; }
}

$avg_score = $sumScore / $cnt;
$avg_time  = $sumTime / $cnt;
$avg_watch = $sumWatch / $cnt;
$avg_diff  = $sumDiff / $cnt;
$hard_avg  = ($hardCnt > 0) ? ($hardScoreSum / $hardCnt) : $avg_score;

// نفس rule-v1 عندك
$level = "beginner";
if ($avg_score >= 80 && $avg_watch >= 0.85) $level = "advanced";
else if ($avg_score >= 60 && $avg_watch >= 0.70) $level = "intermediate";

// جاهزية للمرحلة القادمة (غير coins)
$phase_ready = ($avg_score >= 70 && $avg_watch >= 0.80 && $hard_avg >= 60);

json_out([
  "ok"=>true,
  "features"=>[
    "n"=>$cnt,
    "avg_score"=>$avg_score,
    "avg_time"=>$avg_time,
    "avg_watch"=>$avg_watch,
    "avg_difficulty"=>$avg_diff,
    "hard_avg_score"=>$hard_avg
  ],
  "level"=>$level,
  "phase_ready"=>$phase_ready,
  "model_version"=>"rule-v1"
]);
