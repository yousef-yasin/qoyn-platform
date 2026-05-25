<?php
// utbn-backend/api/student_profile_phase2.php
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
$user_id = (int)$_SESSION["user_id"];

// filters (optional)
$limit = (int)($_GET["limit"] ?? 80);
if ($limit < 1) $limit = 80;
if ($limit > 200) $limit = 200;

$sql = "
SELECT
  s.id AS submission_id,
  s.project_id,
  s.submission_type,
  s.repo_url,
  s.notes,
  s.status,
  s.review_mode,
  s.created_at,

  -- AI
  s.score AS ai_score,
  s.decision AS ai_decision,
  s.feedback AS ai_feedback,
  s.fixes_json AS ai_fixes_json,
  s.ai_model,
  s.checks_json,
  s.manifest_json,
  s.evidence_json,

  -- Company
  s.company_score,
  s.company_decision,
  s.company_feedback,
  s.company_fixes_json,
  s.company_reviewer_id,
  s.company_reviewed_at,

  -- Final
  s.final_score,
  s.final_decision,
  s.final_feedback,
  s.final_fixes_json,

  -- Coins
  s.coins_awarded,
  s.coins_total,

  -- Project title
  p.title AS project_title,
  p.role_key,
  p.path_id

FROM phase2_submissions s
JOIN phase2_projects p ON p.id = s.project_id
WHERE s.user_id = ?
ORDER BY s.id DESC
LIMIT ?
";

$st = $conn->prepare($sql);
if(!$st) json_out(["ok"=>false,"error"=>"SQL_FAILED","sql_error"=>$conn->error], 500);

$st->bind_param("ii", $user_id, $limit);
$st->execute();
$rs = $st->get_result();

$items = [];
while($row = $rs->fetch_assoc()){
  $items[] = [
    "submission_id" => (int)$row["submission_id"],
    "project_id" => (int)$row["project_id"],
    "project_title" => (string)($row["project_title"] ?? ""),
    "role_key" => (string)($row["role_key"] ?? ""),
    "path_id" => (int)($row["path_id"] ?? 0),

    "created_at" => (string)($row["created_at"] ?? ""),
    "status" => (string)($row["status"] ?? ""),
    "review_mode" => (string)($row["review_mode"] ?? ""),

    "repo_url" => (string)($row["repo_url"] ?? ""),
    "notes" => (string)($row["notes"] ?? ""),

    "ai" => [
      "score" => is_null($row["ai_score"]) ? null : (int)$row["ai_score"],
      "decision" => (string)($row["ai_decision"] ?? ""),
      "feedback" => (string)($row["ai_feedback"] ?? ""),
      "fixes" => json_decode($row["ai_fixes_json"] ?? "[]", true) ?: [],
      "ai_model" => (string)($row["ai_model"] ?? ""),
      "checks" => json_decode($row["checks_json"] ?? "{}", true) ?: [],
      "manifest" => json_decode($row["manifest_json"] ?? "{}", true) ?: [],
      "evidence" => json_decode($row["evidence_json"] ?? "{}", true) ?: [],
    ],

    "company" => [
      "score" => is_null($row["company_score"]) ? null : (int)$row["company_score"],
      "decision" => (string)($row["company_decision"] ?? ""),
      "feedback" => (string)($row["company_feedback"] ?? ""),
      "fixes" => json_decode($row["company_fixes_json"] ?? "[]", true) ?: [],
      "reviewer_id" => is_null($row["company_reviewer_id"]) ? null : (int)$row["company_reviewer_id"],
      "reviewed_at" => (string)($row["company_reviewed_at"] ?? ""),
    ],

    "final" => [
      "score" => is_null($row["final_score"]) ? null : (int)$row["final_score"],
      "decision" => (string)($row["final_decision"] ?? ""),
      "feedback" => (string)($row["final_feedback"] ?? ""),
      "fixes" => json_decode($row["final_fixes_json"] ?? "[]", true) ?: [],
    ],

    "coins" => [
      "awarded" => (int)($row["coins_awarded"] ?? 0),
      "total" => (int)($row["coins_total"] ?? 0),
    ],
  ];
}
$st->close();

json_out(["ok"=>true, "user_id"=>$user_id, "items"=>$items]);