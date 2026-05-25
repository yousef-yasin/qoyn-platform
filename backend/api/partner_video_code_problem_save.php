<?php
// utbn-backend/api/partner_video_code_problem_save.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];

$video_id = (int)($in["video_id"] ?? 0);
$delete = (int)($in["delete"] ?? 0);

// verify ownership (حتى بالحذف)
if ($video_id <= 0) {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}
$chk = $conn->prepare("SELECT id FROM partner_videos WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $video_id, $user_id);
$chk->execute();
$ok = $chk->get_result()->num_rows > 0;
$chk->close();
if (!$ok) json_out(["ok"=>false,"error"=>"VIDEO_NOT_OWNED"], 403);

// ensure table
$conn->query("CREATE TABLE IF NOT EXISTS partner_video_code_problems (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  partner_user_id INT UNSIGNED NOT NULL,
  partner_video_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  prompt MEDIUMTEXT NOT NULL,
  language VARCHAR(40) NOT NULL DEFAULT 'python',
  starter_code MEDIUMTEXT NULL,
  solution_code MEDIUMTEXT NOT NULL,
  max_coin INT NOT NULL DEFAULT 50,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pvcp_video (partner_video_id),
  KEY idx_pvcp_partner (partner_user_id, partner_video_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// ✅ حذف سؤال الكود
if ($delete === 1) {
  $del = $conn->prepare("DELETE FROM partner_video_code_problems WHERE partner_video_id=? AND partner_user_id=?");
  $del->bind_param("ii", $video_id, $user_id);
  $del->execute();
  $del->close();
  json_out(["ok"=>true, "deleted"=>true]);
}

// حفظ/إضافة نسخة جديدة
$title = trim((string)($in["title"] ?? ""));
$prompt = trim((string)($in["prompt"] ?? ""));
$language = trim((string)($in["language"] ?? "python"));
$starter_code = (string)($in["starter_code"] ?? "");
$solution_code = (string)($in["solution_code"] ?? "");
$max_coin = (int)($in["max_coin"] ?? 50);

if ($title === "" || $prompt === "" || $solution_code === "") {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}

$max_coin = max(1, min(1000, $max_coin));
$language = preg_replace('/[^a-zA-Z0-9_\-\+\.]/', '', $language);
if ($language === "") $language = "python";

$st = $conn->prepare("INSERT INTO partner_video_code_problems
  (partner_user_id, partner_video_id, title, prompt, language, starter_code, solution_code, max_coin)
  VALUES (?,?,?,?,?,?,?,?)");
if (!$st) json_out(["ok"=>false,"error"=>"PREPARE_FAILED","details"=>$conn->error], 500);

$st->bind_param("iisssssi", $user_id, $video_id, $title, $prompt, $language, $starter_code, $solution_code, $max_coin);
if (!$st->execute()) {
  $err = $st->error;
  $st->close();
  json_out(["ok"=>false,"error"=>"DB_INSERT_FAILED","details"=>$err], 500);
}
$id = (int)$st->insert_id;
$st->close();

json_out(["ok"=>true,"problem_id"=>$id]);
