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

$status = trim((string)($_GET["status"] ?? "awaiting_company"));
$allowed = ["awaiting_company","reviewed","submitted"];
if (!in_array($status, $allowed, true)) $status = "awaiting_company";


$sql = "
SELECT
  s.id AS submission_id,
  s.project_id,
  s.user_id AS student_id,
  COALESCE(NULLIF(u.full_name,''), NULLIF(u.email,''), CONCAT('User#', u.id)) AS student_name,
  p.title AS project_title,
  s.created_at,
  s.status,
  s.review_mode,
  s.repo_url,
  s.notes,

  -- AI fields
  s.score,
  s.decision,
  s.feedback,
  s.fixes_json,
  s.ai_model,
  s.evidence_json,

  -- files
  s.artifact_zip,
  s.artifact_dir,
  s.checks_json,
  s.manifest_json,

  -- company/final (optional to show later)
  s.company_score,
  s.company_decision,
  s.final_score,
  s.final_decision
FROM phase2_submissions s
JOIN users u ON u.id = s.user_id
JOIN phase2_projects p ON p.id = s.project_id
WHERE (s.review_mode IN ('company','both')) AND s.status=?
ORDER BY s.id DESC
LIMIT 200
";

$st = $conn->prepare($sql);
if(!$st) json_out(["ok"=>false,"error"=>"SQL_FAILED","sql_error"=>$conn->error], 500);

$st->bind_param("s", $status);
$st->execute();
$rs = $st->get_result();

$items = [];
while($row = $rs->fetch_assoc()){
  $items[] = [
    "submission_id" => (int)$row["submission_id"],
    "project_id" => (int)$row["project_id"],
    "student_id" => (int)$row["student_id"],
    "student_name" => (string)$row["student_name"],
    "project_title" => (string)$row["project_title"],
    "created_at" => (string)$row["created_at"],
    "status" => (string)$row["status"],
    "review_mode" => (string)$row["review_mode"],
    "repo_url" => (string)($row["repo_url"] ?? ""),
    "notes" => (string)($row["notes"] ?? ""),

    "ai_score" => is_null($row["score"]) ? null : (int)$row["score"],
    "ai_decision" => (string)($row["decision"] ?? ""),
    "ai_feedback" => (string)($row["feedback"] ?? ""),
    "ai_fixes" => json_decode($row["fixes_json"] ?? "[]", true),
    "ai_model" => (string)($row["ai_model"] ?? ""),
    "evidence" => json_decode($row["evidence_json"] ?? "{}", true),

    "artifact_zip" => (string)($row["artifact_zip"] ?? ""),
    "artifact_dir" => (string)($row["artifact_dir"] ?? ""),
    "checks" => json_decode($row["checks_json"] ?? "{}", true),
    "manifest" => json_decode($row["manifest_json"] ?? "{}", true),

    "company_score" => is_null($row["company_score"]) ? null : (int)$row["company_score"],
    "company_decision" => (string)($row["company_decision"] ?? ""),
    "final_score" => is_null($row["final_score"]) ? null : (int)$row["final_score"],
    "final_decision" => (string)($row["final_decision"] ?? ""),
  ];
}
$st->close();

json_out(["ok"=>true,"items"=>$items]);