<?php

require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$team_id = (int)($_GET["team_id"] ?? $_POST["team_id"] ?? 0);
if ($team_id <= 0) {
  json_out(["ok" => false, "error" => "team_id required"], 400);
}

$teamQ = $conn->prepare("
  SELECT
    t.id AS team_id,
    t.project_id,
    t.team_no,
    t.team_name,
    t.required_members,
    t.actual_members,
    t.status,
    p.capstone_title,
    p.partner_user_id
  FROM phase3_teams t
  JOIN partner_phase3_projects p
    ON p.id = t.project_id
  WHERE t.id=? AND p.partner_user_id=?
  LIMIT 1
");
if (!$teamQ) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$teamQ->bind_param("ii", $team_id, $partner_id);
$teamQ->execute();
$team = $teamQ->get_result()->fetch_assoc();
$teamQ->close();

if (!$team) {
  json_out(["ok" => false, "error" => "TEAM_NOT_FOUND"], 404);
}

$sumQ = $conn->prepare("
  SELECT
    ROUND(AVG(CASE
      WHEN s.score IS NOT NULL AND s.score > 0 THEN s.score
      ELSE NULL
    END), 2) AS avg_score,

    ROUND(AVG(CASE
      WHEN s.partner_rating IS NOT NULL AND s.partner_rating > 0 THEN s.partner_rating
      ELSE NULL
    END), 2) AS avg_partner_rating,

    ROUND(AVG(CASE
      WHEN (
        (s.score IS NOT NULL AND s.score > 0)
        OR
        (s.partner_rating IS NOT NULL AND s.partner_rating > 0)
      )
      THEN (
        (COALESCE(NULLIF(s.score,0),0) + COALESCE(NULLIF(s.partner_rating,0),0))
        /
        (
          (CASE WHEN s.score IS NOT NULL AND s.score > 0 THEN 1 ELSE 0 END) +
          (CASE WHEN s.partner_rating IS NOT NULL AND s.partner_rating > 0 THEN 1 ELSE 0 END)
        )
      )
      ELSE NULL
    END), 2) AS final_score,

    CASE
      WHEN SUM(CASE WHEN s.decision='FAIL' THEN 1 ELSE 0 END) > 0 THEN 'FAIL'
      WHEN SUM(CASE WHEN s.decision='NEEDS_FIX' THEN 1 ELSE 0 END) > 0 THEN 'NEEDS_FIX'
      WHEN SUM(CASE WHEN s.decision='PENDING' THEN 1 ELSE 0 END) > 0 THEN 'PENDING'
      WHEN SUM(CASE WHEN s.decision='PASS' THEN 1 ELSE 0 END) > 0 THEN 'PASS'
      ELSE ?
    END AS final_decision
  FROM phase3_task_submissions s
  WHERE s.team_id=?
");
if (!$sumQ) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$teamStatus = (string)($team["status"] ?? "");
$sumQ->bind_param("si", $teamStatus, $team_id);
$sumQ->execute();
$summary = $sumQ->get_result()->fetch_assoc() ?: [];
$sumQ->close();

$sql = "
  SELECT
    tm.student_id,
    u.full_name,
    tm.role_key,
    tm.role_name,

    a.task_id,
    a.task_code,
    a.status AS assignment_status,

    s.id AS submission_id,
    s.repo_url,
    s.zip_path,
    s.notes,
    s.submitted_at,
    s.score,
    s.decision,
    s.partner_rating,
    s.partner_comment,
    s.partner_reviewed_at

  FROM phase3_team_members tm
  JOIN users u
    ON u.id = tm.student_id

  LEFT JOIN phase3_task_assignments a
    ON a.project_id = tm.project_id
   AND a.student_id = tm.student_id

  LEFT JOIN phase3_task_submissions s
    ON s.id = (
      SELECT s2.id
      FROM phase3_task_submissions s2
      WHERE s2.team_id = tm.team_id
        AND s2.project_id = tm.project_id
        AND s2.student_id = tm.student_id
        AND (
          (a.task_id IS NOT NULL AND s2.task_id = a.task_id)
          OR a.task_id IS NULL
        )
      ORDER BY s2.id DESC
      LIMIT 1
    )

  WHERE tm.team_id=?
  ORDER BY tm.id ASC, a.task_id ASC
";

$st = $conn->prepare($sql);
if (!$st) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$st->bind_param("i", $team_id);
$st->execute();
$rs = $st->get_result();

$items = [];
while ($row = $rs->fetch_assoc()) {
  $items[] = [
    "student_id"          => (int)($row["student_id"] ?? 0),
    "full_name"           => (string)($row["full_name"] ?? ""),
    "role_key"            => (string)($row["role_key"] ?? ""),
    "role_name"           => (string)($row["role_name"] ?? ""),
    "task_id"             => ($row["task_id"] !== null ? (int)$row["task_id"] : null),
    "task_code"           => (string)($row["task_code"] ?? ""),
    "task_status"         => (string)($row["assignment_status"] ?? ""),
    "submission_id"       => ($row["submission_id"] !== null ? (int)$row["submission_id"] : null),
    "repo_url"            => (string)($row["repo_url"] ?? ""),
    "zip_path"            => (string)($row["zip_path"] ?? ""),
    "notes"               => (string)($row["notes"] ?? ""),
    "submitted_at"        => (string)($row["submitted_at"] ?? ""),
    "score"               => ($row["score"] !== null ? (float)$row["score"] : null),
    "decision"            => (string)($row["decision"] ?? ""),
    "partner_rating"      => ($row["partner_rating"] !== null ? (float)$row["partner_rating"] : null),
    "partner_comment"     => (string)($row["partner_comment"] ?? ""),
    "partner_reviewed_at" => (string)($row["partner_reviewed_at"] ?? ""),
  ];
}
$st->close();

json_out([
  "ok" => true,
  "team" => [
    "team_id"            => (int)($team["team_id"] ?? 0),
    "project_id"         => (int)($team["project_id"] ?? 0),
    "team_no"            => (int)($team["team_no"] ?? 0),
    "team_name"          => (string)($team["team_name"] ?? ""),
    "required_members"   => (int)($team["required_members"] ?? 0),
    "actual_members"     => (int)($team["actual_members"] ?? 0),
    "status"             => (string)($team["status"] ?? ""),
    "capstone_title"     => (string)($team["capstone_title"] ?? ""),
    "avg_score"          => ($summary["avg_score"] !== null ? (float)$summary["avg_score"] : null),
    "avg_partner_rating" => ($summary["avg_partner_rating"] !== null ? (float)$summary["avg_partner_rating"] : null),
    "final_score"        => ($summary["final_score"] !== null ? (float)$summary["final_score"] : null),
    "final_decision"     => (string)($summary["final_decision"] ?? (string)($team["status"] ?? "")),
  ],
  "items" => $items
]);