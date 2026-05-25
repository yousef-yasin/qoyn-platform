<?php
// utbn-backend/api/phase2_status.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

// ✅ project_id optional but recommended
$project_id = (int)($_GET["project_id"] ?? 0);
if ($project_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_PROJECT_ID"], JSON_UNESCAPED_UNICODE);
  exit;
}
$sql = "
  SELECT s.*, u.coins AS user_coins
  FROM phase2_submissions s
  JOIN users u ON u.id = s.user_id
  WHERE s.user_id=?
";

$types = "i";
$params = [$user_id];

if ($project_id > 0) {
  $sql .= " AND s.project_id=?";
  $types .= "i";
  $params[] = $project_id;
}

// ✅ priority: reviewed first, then awaiting_company, then submitted; newest id wins
$sql .= "
  ORDER BY
    CASE s.status
      WHEN 'reviewed' THEN 1
      WHEN 'awaiting_company' THEN 2
      WHEN 'submitted' THEN 3
      ELSE 4
    END,
    s.id DESC
  LIMIT 1
";

$st = $conn->prepare($sql);
if (!$st) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

// bind dynamically
$st->bind_param($types, ...$params);

$st->execute();
$rs = $st->get_result();
$row = $rs->fetch_assoc();
$st->close();

if (!$row) { echo json_encode(["ok"=>true,"has"=>false], JSON_UNESCAPED_UNICODE); exit; }

echo json_encode([
  "ok"=>true,
  "has"=>true,
  "submission"=>[
    "id" => (int)$row["id"],
    "project_id" => (int)$row["project_id"],
    "score" => (int)$row["score"],
    "decision" => (string)$row["decision"],
    "status" => (string)($row["status"] ?? ""),
    "review_mode" => (string)($row["review_mode"] ?? "ai"),
    "user_coins" => (int)($row["user_coins"] ?? 0),
    "coins_awarded" => (int)($row["coins_awarded"] ?? 0),
    "coins_total" => (int)($row["coins_total"] ?? 0),
    "feedback" => (string)($row["feedback"] ?? ""),
    "fixes" => json_decode($row["fixes_json"] ?? "[]", true) ?: [],
    "created_at" => (string)($row["created_at"] ?? ""),
  ]
], JSON_UNESCAPED_UNICODE);