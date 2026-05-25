<?php
// utbn-backend/api/company_phase2_download.php
session_start();

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo "DB_FILE_NOT_FOUND"; exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo "NOT_LOGGED_IN"; exit; }
if (($_SESSION["role"] ?? "") !== "partner") { http_response_code(403); echo "FORBIDDEN"; exit; }

$submission_id = (int)($_GET["submission_id"] ?? 0);
if ($submission_id <= 0) { http_response_code(400); echo "MISSING_SUBMISSION_ID"; exit; }

$st = $conn->prepare("SELECT artifact_zip FROM phase2_submissions WHERE id=? LIMIT 1");
$st->bind_param("i", $submission_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();
$st->close();

$rel = (string)($row["artifact_zip"] ?? "");
if ($rel === "") { http_response_code(404); echo "NO_ZIP"; exit; }

// ✅ حول النسبي لمسار فعلي داخل المشروع
$base = realpath(__DIR__ . "/.."); // utbn-backend
$full = realpath($base . $rel);
if (!$full || !file_exists($full)) { http_response_code(404); echo "FILE_NOT_FOUND"; exit; }

// ✅ حماية: لازم يكون داخل uploads
$uploads = realpath($base . "/uploads");
if (!$uploads || strpos($full, $uploads) !== 0) { http_response_code(403); echo "INVALID_PATH"; exit; }

header("Content-Type: application/zip");
header('Content-Disposition: attachment; filename="submission_'.$submission_id.'.zip"');
header("Content-Length: " . filesize($full));
readfile($full);
exit;