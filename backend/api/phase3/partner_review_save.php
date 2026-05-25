<?php
require_once __DIR__ . "/../_phase3_bootstrap.php";
require_once __DIR__ . "/team_lib.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$project_id = (int)($_POST["project_id"] ?? 0);
$task_id    = (int)($_POST["task_id"] ?? 0);
$student_id = (int)($_POST["student_id"] ?? 0);

$score = (int)($_POST["score"] ?? 0);
$final_decision = strtoupper(trim((string)($_POST["final_decision"] ?? ($_POST["decision"] ?? ""))));
$comment = trim((string)($_POST["comment"] ?? ($_POST["feedback"] ?? "")));
$rating = (int)($_POST["rating"] ?? 0);

if ($project_id <= 0 || $task_id <= 0 || $student_id <= 0) {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}

if ($score < 0) $score = 0;
if ($score > 100) $score = 100;

if ($rating < 0) $rating = 0;
if ($rating > 5) $rating = 5;

$allowed = ["PASS", "FAIL", "NEEDS_FIX"];
if (!in_array($final_decision, $allowed, true)) {
  json_out(["ok"=>false,"error"=>"BAD_FINAL_DECISION"], 400);
}

/*
|--------------------------------------------------------------------------
| 1) تأكد أن المشروع لهذا الشريك
|--------------------------------------------------------------------------
*/
$chk = $conn->prepare("
  SELECT id
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
if (!$chk) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}
$chk->bind_param("ii", $project_id, $partner_id);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) {
  json_out(["ok"=>false,"error"=>"NOT_YOUR_PROJECT"], 403);
}
$chk->close();

/*
|--------------------------------------------------------------------------
| 2) تأكد أن هذا الطالب مربوط بهذه المهمة
|--------------------------------------------------------------------------
*/
$ct = $conn->prepare("
  SELECT id
  FROM phase3_task_assignments
  WHERE project_id=? AND task_id=? AND student_id=?
  LIMIT 1
");
if (!$ct) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}
$ct->bind_param("iii", $project_id, $task_id, $student_id);
$ct->execute();
if (!$ct->get_result()->fetch_assoc()) {
  json_out(["ok"=>false,"error"=>"TASK_STUDENT_MISMATCH"], 400);
}
$ct->close();

/*
|--------------------------------------------------------------------------
| 3) هات team_id من آخر submission
|--------------------------------------------------------------------------
*/
$teamFind = $conn->prepare("
  SELECT team_id
  FROM phase3_task_submissions
  WHERE project_id=? AND task_id=? AND student_id=?
  ORDER BY submitted_at DESC, id DESC
  LIMIT 1
");
if (!$teamFind) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}
$teamFind->bind_param("iii", $project_id, $task_id, $student_id);
$teamFind->execute();
$teamRow = $teamFind->get_result()->fetch_assoc();
$teamFind->close();

$team_id = $teamRow ? (int)$teamRow["team_id"] : 0;

/*
|--------------------------------------------------------------------------
| 4) خزّن partner review
|--------------------------------------------------------------------------
*/
$sql = "
INSERT INTO phase3_partner_reviews
(project_id, team_id, task_id, student_id, partner_id, rating, final_decision, comment)
VALUES (?,?,?,?,?,?,?,?)
ON DUPLICATE KEY UPDATE
  team_id=VALUES(team_id),
  rating=VALUES(rating),
  final_decision=VALUES(final_decision),
  comment=VALUES(comment)
";
$st = $conn->prepare($sql);
if (!$st) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}

$st->bind_param(
  "iiiiiiss",
  $project_id,
  $team_id,
  $task_id,
  $student_id,
  $partner_id,
  $rating,
  $final_decision,
  $comment
);
$st->execute();
$st->close();

/*
|--------------------------------------------------------------------------
| 5) حدّث submission الأخير
|--------------------------------------------------------------------------
*/
$grade_json = json_encode([
  "review_mode" => "COMPANY",
  "team_id" => $team_id,
  "company_score" => $score,
  "company_decision" => $final_decision,
  "company_feedback" => $comment,
  "rating" => $rating,
  "reviewed_at" => date("Y-m-d H:i:s"),
], JSON_UNESCAPED_UNICODE);

$upSub = $conn->prepare("
  UPDATE phase3_task_submissions
  SET score=?,
      decision=?,
      grade_json=?,
      partner_rating=?,
      partner_comment=?,
      partner_reviewed_at=NOW()
  WHERE project_id=? AND task_id=? AND student_id=?
  ORDER BY submitted_at DESC, id DESC
  LIMIT 1
");
if (!$upSub) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}
$upSub->bind_param(
  "isissiii",
  $score,
  $final_decision,
  $grade_json,
  $rating,
  $comment,
  $project_id,
  $task_id,
  $student_id
);
$upSub->execute();
$upSub->close();

/*
|--------------------------------------------------------------------------
| 6) حدّث assignment status
|--------------------------------------------------------------------------
*/
$upA = $conn->prepare("
  UPDATE phase3_task_assignments
  SET status='REVIEWED',
      reviewed_at=NOW()
  WHERE project_id=? AND task_id=? AND student_id=?
");
if ($upA) {
  $upA->bind_param("iii", $project_id, $task_id, $student_id);
  $upA->execute();
  $upA->close();
}

/*
|--------------------------------------------------------------------------
| 7) حدّث حالة التاسك
|--------------------------------------------------------------------------
*/
$upT = $conn->prepare("
  UPDATE phase3_tasks
  SET status='REVIEWED'
  WHERE id=? AND project_id=?
");
if ($upT) {
  $upT->bind_param("ii", $task_id, $project_id);
  $upT->execute();
  $upT->close();
}

/*
|--------------------------------------------------------------------------
| 8) حدّث متوسط التيم
|--------------------------------------------------------------------------
*/
if ($team_id > 0) {
  phase3_sync_team_review($conn, $project_id, $team_id, $partner_id);
}

json_out([
  "ok"=>true,
  "project_id"=>$project_id,
  "team_id"=>$team_id,
  "task_id"=>$task_id,
  "student_id"=>$student_id,
  "score"=>$score,
  "final_decision"=>$final_decision,
  "rating"=>$rating,
  "comment"=>$comment,
  "task_status"=>"REVIEWED"
]);