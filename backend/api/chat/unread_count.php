<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_chat_login();

$user_id = (int)$_SESSION["user_id"];
$role = current_role();

if ($role === "company") {
  $sql = "
    SELECT COUNT(*) c
    FROM chat_messages m
    JOIN chat_threads t ON t.id=m.thread_id
    WHERE t.company_id=?
      AND m.sender_role='student'
      AND m.is_read=0
  ";
} else {
  $sql = "
    SELECT COUNT(*) c
    FROM chat_messages m
    JOIN chat_threads t ON t.id=m.thread_id
    WHERE t.student_id=?
      AND m.sender_role='company'
      AND m.is_read=0
  ";
}

$st = $conn->prepare($sql);
$st->bind_param("i", $user_id);
$st->execute();
$row = $st->get_result()->fetch_assoc();

chat_json(["ok"=>true, "count"=>(int)($row["c"] ?? 0)]);