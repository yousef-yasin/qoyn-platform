<?php

require_once __DIR__ . "/../_phase3_bootstrap.php";

require_partner();
$partner_id = (int)($_SESSION["user_id"] ?? 0);

$project_id = (int)($_GET["project_id"] ?? $_POST["project_id"] ?? 0);
if ($project_id <= 0) {
  json_out(["ok" => false, "error" => "project_id required"], 400);
}

/*
|--------------------------------------------------------------------------
| تأكد أن المشروع للشركة الحالية
|--------------------------------------------------------------------------
*/
$p = $conn->prepare("
  SELECT
    p.id AS project_id,
    p.capstone_title,
    p.capstone_description,
    p.status,
    p.created_at
  FROM partner_phase3_projects p
  WHERE p.id=? AND p.partner_user_id=?
  LIMIT 1
");
if (!$p) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$p->bind_param("ii", $project_id, $partner_id);
$p->execute();
$project = $p->get_result()->fetch_assoc();
$p->close();

if (!$project) {
  json_out(["ok" => false, "error" => "PROJECT_NOT_FOUND"], 404);
}

/*
|--------------------------------------------------------------------------
| هات التيمات الخاصة بالمشروع
|--------------------------------------------------------------------------
*/
$sql = "
  SELECT
    t.id AS team_id,
    t.team_no,
    t.team_name,
    t.required_members,
    t.actual_members,
    t.status,

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
      WHEN SUM(CASE WHEN s.decision='PASS' THEN 1 ELSE 0 END) > 0 THEN 'PASS'
      WHEN SUM(CASE WHEN s.decision='FAIL' THEN 1 ELSE 0 END) > 0 THEN 'FAIL'
      WHEN SUM(CASE WHEN s.decision='PENDING' THEN 1 ELSE 0 END) > 0 THEN 'PENDING'
      WHEN SUM(CASE WHEN s.decision='NEEDS_FIX' THEN 1 ELSE 0 END) > 0 THEN 'NEEDS_FIX'
      ELSE t.status
    END AS final_decision,

    COUNT(DISTINCT m.student_id) AS members_count,
    COUNT(DISTINCT s.id) AS submissions_count
  FROM phase3_teams t
  LEFT JOIN phase3_team_members m
    ON m.team_id = t.id
  LEFT JOIN phase3_task_submissions s
    ON s.team_id = t.id
   AND s.project_id = t.project_id
  WHERE t.project_id=?
  GROUP BY
    t.id, t.team_no, t.team_name, t.required_members, t.actual_members, t.status
  ORDER BY t.team_no ASC, t.id ASC
";

$st = $conn->prepare($sql);
if (!$st) {
  json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$st->bind_param("i", $project_id);
$st->execute();
$rs = $st->get_result();

$teams = [];
while ($row = $rs->fetch_assoc()) {
  $teams[] = [
    "team_id"           => (int)($row["team_id"] ?? 0),
    "team_no"           => (int)($row["team_no"] ?? 0),
    "team_name"         => (string)($row["team_name"] ?? ""),
    "required_members"  => (int)($row["required_members"] ?? 0),
    "actual_members"    => (int)($row["actual_members"] ?? 0),
    "status"            => (string)($row["status"] ?? ""),
    "avg_score"         => ($row["avg_score"] !== null ? (float)$row["avg_score"] : null),
    "avg_partner_rating"=> ($row["avg_partner_rating"] !== null ? (float)$row["avg_partner_rating"] : null),
    "final_score"       => ($row["final_score"] !== null ? (float)$row["final_score"] : null),
    "final_decision"    => (string)($row["final_decision"] ?? ""),
    "members_count"     => (int)($row["members_count"] ?? 0),
    "submissions_count" => (int)($row["submissions_count"] ?? 0),
  ];
}
$st->close();

json_out([
  "ok" => true,
  "project" => [
    "project_id" => (int)($project["project_id"] ?? 0),
    "capstone_title" => (string)($project["capstone_title"] ?? ""),
    "capstone_description" => (string)($project["capstone_description"] ?? ""),
    "status" => (string)($project["status"] ?? ""),
    "created_at" => (string)($project["created_at"] ?? "")
  ],
  "teams" => $teams
]);