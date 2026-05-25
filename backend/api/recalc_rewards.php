<?php
require __DIR__ . "/db.php";
require_login();

/**
 * Recalculate trainings.coin_reward based on number of videos.
 * - Biggest training per course can reach 1000
 * - Others capped at 700
 * - Formula: 100 + videos_count*20 (then caps)
 *
 * Security: allowed only from localhost
 */
$ip = $_SERVER["REMOTE_ADDR"] ?? "";
if (!in_array($ip, ["127.0.0.1", "::1"])) {
  json_out(["error" => "FORBIDDEN"], 403);
}

function calc_coin(int $videosCount): int {
  $coin = 100 + ($videosCount * 20);
  if ($coin < 100) $coin = 100;
  if ($coin > 1000) $coin = 1000;
  return $coin;
}

// 1) get video counts per training
$sql = "
  SELECT t.id AS training_id, t.course_id, COUNT(v.id) AS videos_count
  FROM trainings t
  LEFT JOIN videos v ON v.training_id = t.id
  GROUP BY t.id, t.course_id
";
$res = $conn->query($sql);
if (!$res) json_out(["error"=>"DB_QUERY_FAILED","details"=>$conn->error], 500);

$rows = [];
while ($r = $res->fetch_assoc()) {
  $rows[] = [
    "training_id" => (int)$r["training_id"],
    "course_id" => (int)$r["course_id"],
    "videos_count" => (int)$r["videos_count"],
  ];
}

// 2) determine max videos per course (the "full course" training)
$maxByCourse = [];
foreach ($rows as $r) {
  $cid = $r["course_id"];
  $vc  = $r["videos_count"];
  if (!isset($maxByCourse[$cid]) || $vc > $maxByCourse[$cid]) $maxByCourse[$cid] = $vc;
}

// 3) update coin_reward
$updated = 0;
$details = [];

$upd = $conn->prepare("UPDATE trainings SET coin_reward=? WHERE id=?");
if (!$upd) json_out(["error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);

foreach ($rows as $r) {
  $tid = $r["training_id"];
  $cid = $r["course_id"];
  $vc  = $r["videos_count"];

  $coin = calc_coin($vc);

  $isBiggest = ($vc === ($maxByCourse[$cid] ?? $vc));

  // only the biggest training in the course can reach 1000
  if (!$isBiggest && $coin > 700) $coin = 700;

  $upd->bind_param("ii", $coin, $tid);
  $upd->execute();
  if ($upd->affected_rows > 0) $updated++;

  $details[] = [
    "training_id" => $tid,
    "course_id" => $cid,
    "videos_count" => $vc,
    "coin_reward" => $coin,
    "is_biggest_in_course" => $isBiggest
  ];
}
$upd->close();

json_out([
  "ok" => true,
  "updated_rows" => $updated,
  "trainings" => $details
]);
