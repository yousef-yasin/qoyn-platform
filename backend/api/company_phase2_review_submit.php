<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

function json_out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

if (!isset($_SESSION["user_id"])) json_out(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") json_out(["ok"=>false,"error"=>"FORBIDDEN"], 403);

if ($_SERVER["REQUEST_METHOD"] !== "POST") json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);

$in = json_decode(file_get_contents("php://input"), true) ?: [];
$submission_id = (int)($in["submission_id"] ?? 0);

$company_score = (int)($in["score"] ?? 0);
$company_decision = strtoupper(trim((string)($in["decision"] ?? "NEEDS_FIX")));
$company_feedback = trim((string)($in["feedback"] ?? ""));
$company_fixes = $in["fixes"] ?? [];
if (!is_array($company_fixes)) $company_fixes = [];

if ($submission_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_SUBMISSION_ID"], 400);
if ($company_score < 0) $company_score = 0;
if ($company_score > 100) $company_score = 100;
if (!in_array($company_decision, ["PASS","NEEDS_FIX","FAIL"], true)) $company_decision = "NEEDS_FIX";

$company_fixes_json = json_encode($company_fixes, JSON_UNESCAPED_UNICODE);
$reviewer_company_id = (int)$_SESSION["user_id"];

// 1) get submission + project + AI score
$st = $conn->prepare("
  SELECT
    s.id, s.user_id, s.project_id, s.review_mode, s.status,
    s.score AS ai_score,
    s.feedback AS ai_feedback,
    s.fixes_json AS ai_fixes_json,
    p.base_coins, p.pass_score
  FROM phase2_submissions s
  JOIN phase2_projects p ON p.id = s.project_id
  WHERE s.id=?
  LIMIT 1
");
if(!$st) json_out(["ok"=>false,"error"=>"SQL_FAILED","sql_error"=>$conn->error], 500);
$st->bind_param("i", $submission_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();

if(!$row) json_out(["ok"=>false,"error"=>"SUBMISSION_NOT_FOUND"], 404);

$student_id = (int)$row["user_id"];
$project_id = (int)$row["project_id"];
$base_coins = (int)($row["base_coins"] ?? 2000);
$pass_score = (int)($row["pass_score"] ?? 70);

$status_now = (string)($row["status"] ?? "");
if ($status_now !== "awaiting_company") {
  json_out(["ok"=>false,"error"=>"NOT_AWAITING_COMPANY","status"=>$status_now], 400);
}

$ai_score = is_null($row["ai_score"]) ? null : (int)$row["ai_score"];

// 2) FINAL score (avg AI + company) if AI exists
$final_score = ($ai_score === null) ? $company_score : (int)round(($ai_score + $company_score) / 2.0);
$final_score = max(0, min(100, $final_score));

$final_decision = ($final_score >= $pass_score) ? "PASS" : "NEEDS_FIX";
if ($final_score <= 20) $final_decision = "FAIL";

// 3) FINAL feedback/fixes (اختياري: دمج بسيط)
$final_feedback = trim($company_feedback);
if ($final_feedback === "" && !empty($row["ai_feedback"])) {
  $final_feedback = (string)$row["ai_feedback"];
}
$final_fixes_json = $company_fixes_json;
if ($final_fixes_json === "[]" && !empty($row["ai_fixes_json"])) {
  $final_fixes_json = (string)$row["ai_fixes_json"];
}

// 4) coins calc based on FINAL only
$coins_total = 0;
if ($final_decision === "PASS") {
  $coins_total = (int)round($base_coins * ($final_score / 100.0));
}
$coins_total = max(0, $coins_total);

// best previous (exclude current)
$prev_best = 0;
$stb = $conn->prepare("
  SELECT COALESCE(MAX(coins_total), 0) AS best
  FROM phase2_submissions
  WHERE user_id=? AND project_id=? AND id<>?
");
if(!$stb) json_out(["ok"=>false,"error"=>"SQL_FAILED","sql_error"=>$conn->error], 500);
$stb->bind_param("iii", $student_id, $project_id, $submission_id);
$stb->execute();
$bestRow = $stb->get_result()->fetch_assoc();
$prev_best = (int)($bestRow["best"] ?? 0);
$stb->close();

$coins_awarded = max(0, $coins_total - $prev_best);

// 5) update submission
$new_status = "reviewed";
$st2 = $conn->prepare("
  UPDATE phase2_submissions
  SET
    company_score=?,
    company_decision=?,
    company_feedback=?,
    company_fixes_json=?,
    company_reviewer_id=?,

    final_score=?,
    final_decision=?,
    final_feedback=?,
    final_fixes_json=?,

    coins_total=?,
    coins_awarded=?,
    status=?
  WHERE id=?
");
if(!$st2) json_out(["ok"=>false,"error"=>"SQL_FAILED","sql_error"=>$conn->error], 500);

$st2->bind_param(
  "isssiisssiisi",
  $company_score,
  $company_decision,
  $company_feedback,
  $company_fixes_json,
  $reviewer_company_id,
  $final_score,
  $final_decision,
  $final_feedback,
  $final_fixes_json,
  $coins_total,
  $coins_awarded,
  $new_status,
  $submission_id
);
$st2->execute();
$st2->close();

// 6) add coins
if ($coins_awarded > 0) {
  $stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
  if($stu){
    $stu->bind_param("ii", $coins_awarded, $student_id);
    $stu->execute();
    $stu->close();
  }
}

json_out([
  "ok"=>true,
  "submission_id"=>$submission_id,
  "ai_score"=>$ai_score,
  "company_score"=>$company_score,
  "final_score"=>$final_score,
  "final_decision"=>$final_decision,
  "coins_awarded"=>$coins_awarded,
  "coins_total"=>$coins_total,
  "prev_best_total"=>$prev_best
]);