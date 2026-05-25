<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_chat_login();

$user_id = (int)($_SESSION["user_id"] ?? 0);
$role = current_role();
$thread_id = (int)($_GET["thread_id"] ?? 0);

if ($thread_id <= 0) {
  chat_json(["ok"=>false,"error"=>"INVALID_THREAD"], 400);
}

/*
|--------------------------------------------------------------------------
| التحقق من صلاحية الوصول للثريد
|--------------------------------------------------------------------------
| company: لازم يكون thread تابع للشركة
| student: إما thread فردي له أو team chat وهو عضو في التيم
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
    chat_json(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
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
    chat_json(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
  }

  $chk->bind_param("iii", $user_id, $thread_id, $user_id);
}

$chk->execute();
$allowed = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$allowed) {
  chat_json(["ok"=>false,"error"=>"THREAD_NOT_ALLOWED"], 403);
}

/*
|--------------------------------------------------------------------------
| تعليم الرسائل المقروءة
|--------------------------------------------------------------------------
| إذا الشركة فتحت الشات، اعتبر رسائل الطالب مقروءة
| إذا الطالب فتح الشات، اعتبر رسائل الشركة مقروءة
|--------------------------------------------------------------------------
*/
if ($role === "company") {
  $mark = $conn->prepare("
    UPDATE chat_messages
    SET is_read=1
    WHERE thread_id=? AND sender_role='student' AND is_read=0
  ");
} else {
  $mark = $conn->prepare("
    UPDATE chat_messages
    SET is_read=1
    WHERE thread_id=? AND sender_role='company' AND is_read=0
  ");
}

if ($mark) {
  $mark->bind_param("i", $thread_id);
  $mark->execute();
  $mark->close();
}

/*
|--------------------------------------------------------------------------
| جلب الرسائل
|--------------------------------------------------------------------------
*/
$st = $conn->prepare("
  SELECT id, thread_id, sender_id, sender_role, message, is_read, created_at
  FROM chat_messages
  WHERE thread_id=?
  ORDER BY id ASC
");
if (!$st) {
  chat_json(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}

$st->bind_param("i", $thread_id);
$st->execute();
$messages = $st->get_result()->fetch_all(MYSQLI_ASSOC);
$st->close();

chat_json([
  "ok"=>true,
  "thread_id"=>$thread_id,
  "messages"=>$messages
]);