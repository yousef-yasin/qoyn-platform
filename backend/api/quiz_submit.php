<?php
require __DIR__ . "/db.php";
require_login();

$user_id = (int)$_SESSION["user_id"];
$data = json_decode(file_get_contents("php://input"), true);
$quiz_id = (int)($data["quiz_id"] ?? 0);
$answers = $data["answers"] ?? [];

if ($quiz_id <= 0 || !is_array($answers)) json_out(["error"=>"BAD_REQUEST"], 400);

$stmt = $conn->prepare("SELECT q.id, q.pass_score, q.training_id, t.coin_reward
                        FROM quizzes q
                        JOIN trainings t ON t.id = q.training_id
                        WHERE q.id=? LIMIT 1");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
if (!$quiz) json_out(["error"=>"QUIZ_NOT_FOUND"], 404);

$pass_score = (int)$quiz["pass_score"];
$training_id = (int)$quiz["training_id"];
$coin_reward = (int)$quiz["coin_reward"];

// compute score
$total = 0;
$correct = 0;

$qstmt = $conn->prepare("SELECT id, correct_option_id FROM quiz_questions WHERE quiz_id=?");
$qstmt->bind_param("i", $quiz_id);
$qstmt->execute();
$qres = $qstmt->get_result();
$correct_map = [];
while ($q = $qres->fetch_assoc()) {
  $correct_map[(int)$q["id"]] = (int)$q["correct_option_id"];
}
$total = count($correct_map);

foreach ($answers as $qidStr => $optId) {
  $qid = (int)$qidStr;
  $opt = (int)$optId;
  if (isset($correct_map[$qid]) && $correct_map[$qid] === $opt) $correct++;
}

$score = ($total > 0) ? (int)round(($correct / $total) * 100) : 0;
$passed = ($score >= $pass_score) ? 1 : 0;

// store
$stmt2 = $conn->prepare("INSERT INTO quiz_submissions (user_id, quiz_id, score, passed, submitted_at)
                         VALUES (?,?,?,?,NOW())");
$stmt2->bind_param("iiii", $user_id, $quiz_id, $score, $passed);
$stmt2->execute();

// award coins if passed AND videos done AND not already awarded
$awarded_now = false;
if ($passed === 1) {
  // check videos completion
  $totalStmt = $conn->prepare("SELECT COUNT(*) AS n FROM videos WHERE training_id=?");
  $totalStmt->bind_param("i", $training_id);
  $totalStmt->execute();
  $vt = (int)($totalStmt->get_result()->fetch_assoc()["n"] ?? 0);

  $watchedStmt = $conn->prepare("SELECT COUNT(*) AS n
                                 FROM video_progress vp
                                 JOIN videos v ON v.id = vp.video_id
                                 WHERE vp.user_id=? AND v.training_id=? AND vp.watched=1");
  $watchedStmt->bind_param("ii", $user_id, $training_id);
  $watchedStmt->execute();
  $vw = (int)($watchedStmt->get_result()->fetch_assoc()["n"] ?? 0);

  $videos_done = ($vt === 0) ? true : ($vw >= $vt);

  if ($videos_done && $coin_reward > 0) {
    $award = $conn->prepare("INSERT IGNORE INTO training_rewards (user_id, training_id, coins_awarded, awarded_at)
                             VALUES (?,?,?,NOW())");
    $award->bind_param("iii", $user_id, $training_id, $coin_reward);
    if ($award->execute() && $award->affected_rows === 1) {
      $ledger = $conn->prepare("INSERT INTO coins_ledger (user_id, amount, reason, ref_type, ref_id, created_at)
                                VALUES (?,?, 'TRAINING_COMPLETE', 'training', ?, NOW())");
      $ledger->bind_param("iii", $user_id, $coin_reward, $training_id);
      $ledger->execute();
      $conn->query("UPDATE student_profiles SET coins_total = coins_total + $coin_reward WHERE user_id = $user_id");
      $stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
if ($stu) {
  $stu->bind_param("ii", $coin_reward, $user_id);
  $stu->execute();
  $stu->close();
}
      $awarded_now = true;
    }
  }
}

json_out([
  "ok" => true,
  "score" => $score,
  "passed" => ($passed === 1),
  "awarded_coins" => $awarded_now ? $coin_reward : 0
]);
