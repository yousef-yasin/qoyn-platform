<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_chat_login();

$user_id   = (int)($_SESSION["user_id"] ?? 0);
$role      = current_role();
$thread_id = (int)($_POST["thread_id"] ?? 0);
$message   = trim((string)($_POST["message"] ?? ""));

if ($thread_id <= 0 || $message === "") {
  chat_json(["ok" => false, "error" => "INVALID_INPUT"], 400);
}

/*
|--------------------------------------------------------------------------
| تأكد أن المستخدم مسموح له يرسل داخل هذا الشات
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
}

$chk->execute();
$allowed = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$allowed) {
  chat_json(["ok" => false, "error" => "THREAD_NOT_ALLOWED"], 403);
}

/*
|--------------------------------------------------------------------------
| أضف الرسالة
|--------------------------------------------------------------------------
*/
$ins = $conn->prepare("
  INSERT INTO chat_messages (thread_id, sender_id, sender_role, message, is_read, created_at)
  VALUES (?, ?, ?, ?, 0, NOW())
");
if (!$ins) {
  chat_json([
    "ok" => false,
    "error" => "PREPARE_INSERT_FAILED",
    "mysql_error" => $conn->error
  ], 500);
}

$ins->bind_param("iiss", $thread_id, $user_id, $role, $message);
$ok = $ins->execute();

if (!$ok) {
  $ins->close();
  chat_json([
    "ok" => false,
    "error" => "INSERT_FAILED",
    "mysql_error" => $conn->error
  ], 500);
}

$message_id = (int)$conn->insert_id;
$ins->close();

/*
|--------------------------------------------------------------------------
| حدّث وقت آخر رسالة
|--------------------------------------------------------------------------
*/
$up = $conn->prepare("
  UPDATE chat_threads
  SET last_message_at = NOW()
  WHERE id=?
");
if ($up) {
  $up->bind_param("i", $thread_id);
  $up->execute();
  $up->close();
}

chat_json([
  "ok" => true,
  "message_id" => $message_id
]);