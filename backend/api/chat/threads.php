<?php
require_once __DIR__ . "/_chat_bootstrap.php";
require_chat_login();

$user_id = (int)($_SESSION["user_id"] ?? 0);
$role    = current_role();

if ($role === "company") {
  $sql = "
    SELECT
      t.id,
      t.student_id AS other_user_id,
      t.team_id,
      t.is_team_chat,
      CASE
        WHEN t.is_team_chat=1 THEN CONCAT('Team #', COALESCE(pt.team_no, t.team_id))
        ELSE u.full_name
      END AS other_name,
      u.email AS other_email,
      t.phase_source,
      t.phase2_submission_id,
      t.phase3_project_id,
      t.phase3_task_id,
      t.last_message_at,
      (
        SELECT COUNT(*)
        FROM chat_messages m
        WHERE m.thread_id = t.id
          AND m.sender_role = 'student'
          AND m.is_read = 0
      ) AS unread_count
    FROM chat_threads t
    LEFT JOIN users u
      ON u.id = t.student_id
    LEFT JOIN phase3_teams pt
      ON pt.id = t.team_id
    WHERE t.company_id = ?
      AND COALESCE(t.is_archived, 0) = 0
    ORDER BY COALESCE(t.last_message_at, t.created_at) DESC
  ";

  $st = $conn->prepare($sql);
  if (!$st) {
    chat_json([
      "ok" => false,
      "error" => "PREPARE_FAILED",
      "mysql_error" => $conn->error
    ], 500);
  }

  $st->bind_param("i", $user_id);

} else {
  $sql = "
    SELECT
      t.id,
      t.student_id AS other_user_id,
      t.team_id,
      t.is_team_chat,
      CASE
        WHEN t.is_team_chat=1 THEN CONCAT('Team #', COALESCE(pt.team_no, t.team_id))
        ELSE u.full_name
      END AS other_name,
      u.email AS other_email,
      t.phase_source,
      t.phase2_submission_id,
      t.phase3_project_id,
      t.phase3_task_id,
      t.last_message_at,
      (
        SELECT COUNT(*)
        FROM chat_messages m
        WHERE m.thread_id = t.id
          AND m.sender_role = 'company'
          AND m.is_read = 0
      ) AS unread_count
    FROM chat_threads t
    LEFT JOIN users u
      ON u.id = t.student_id
    LEFT JOIN phase3_teams pt
      ON pt.id = t.team_id
    LEFT JOIN phase3_team_members tm
      ON tm.team_id = t.team_id
     AND tm.student_id = ?
    WHERE t.company_id IS NOT NULL
      AND COALESCE(t.is_archived, 0) = 0
      AND (
        t.student_id = ?
        OR (t.is_team_chat = 1 AND tm.student_id IS NOT NULL)
      )
    ORDER BY COALESCE(t.last_message_at, t.created_at) DESC
  ";

  $st = $conn->prepare($sql);
  if (!$st) {
    chat_json([
      "ok" => false,
      "error" => "PREPARE_FAILED",
      "mysql_error" => $conn->error
    ], 500);
  }

  $st->bind_param("ii", $user_id, $user_id);
}

$st->execute();
$res = $st->get_result();
$threads = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$st->close();

chat_json([
  "ok" => true,
  "threads" => $threads
]);