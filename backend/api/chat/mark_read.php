<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_chat_login();

$user_id   = (int)($_SESSION["user_id"] ?? 0);
$role      = current_role();
$thread_id = (int)($_POST["thread_id"] ?? 0);

if ($thread_id <= 0) {
  chat_json(["ok" => false, "error" => "INVALID_THREAD"], 400);
}

/*
|--------------------------------------------------------------------------
| تأكد أن المستخدم يملك صلاحية الوصول للشات
|--------------------------------------------------------------------------
*/
if ($role === "company") {
  $chk = $conn->prepare("
    SELECT id
    FROM chat_threads
    WHERE id=? AND company_id=?
    LIMIT 1
  ");
  if (!$chk) {
    chat_json([
      "ok" => false,
      "error" => "PREPARE_CHECK_FAILED",
      "mysql_error" => $conn->error
    ], 500);
  }

  $chk->bind_param("ii", $thread_id, $user_id);
  $senderRoleToMark = "student";

} else {
  $chk = $conn->prepare("
    SELECT t.id
    FROM chat_threads t
    LEFT JOIN phase3_team_members m
      ON m.team_id = t.team_id
     AND m.student_id = ?
    WHERE t.id=?
      AND (
        t.student_id = ?
        OR (t.is_team_chat=1 AND m.student_id IS NOT NULL)
      )
    LIMIT 1
  ");
  if (!$chk) {
    chat_json([
      "ok" => false,
      "error" => "PREPARE_CHECK_FAILED",
      "mysql_error" => $conn->error
    ], 500);
  }

  $chk->bind_param("iii", $user_id, $thread_id, $user_id);
  $senderRoleToMark = "company";
}


$chk->execute();
$allowed = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$allowed) {
  chat_json(["ok" => false, "error" => "THREAD_NOT_ALLOWED"], 403);
}

/*
|--------------------------------------------------------------------------
| علّم الرسائل المقروءة
|--------------------------------------------------------------------------
*/
$up = $conn->prepare("
  UPDATE chat_messages
  SET is_read=1
  WHERE thread_id=? AND sender_role=? AND is_read=0
");
if (!$up) {
  chat_json([
    "ok" => false,
    "error" => "PREPARE_UPDATE_FAILED",
    "mysql_error" => $conn->error
  ], 500);
}

$up->bind_param("is", $thread_id, $senderRoleToMark);
$up->execute();
$up->close();

chat_json(["ok" => true]);