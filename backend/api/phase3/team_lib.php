<?php

function phase3_create_team(mysqli $conn, int $project_id, int $team_no, string $team_name, int $required_members): int {
    $st = $conn->prepare("
        INSERT INTO phase3_teams
        (project_id, team_no, team_name, required_members, actual_members, status)
        VALUES (?, ?, ?, ?, 0, 'ACTIVE')
    ");

    if (!$st) {
        die("phase3_create_team PREPARE FAILED: " . $conn->error);
    }

    $st->bind_param("iisi", $project_id, $team_no, $team_name, $required_members);
    $st->execute();

    if ($st->error) {
        die("phase3_create_team EXECUTE FAILED: " . $st->error);
    }

    $id = (int)$conn->insert_id;
    $st->close();
    return $id;
}

function phase3_add_team_member(
    mysqli $conn,
    int $team_id,
    int $project_id,
    int $student_id,
    string $role_key,
    string $role_name
): void {
    $st = $conn->prepare("
        INSERT IGNORE INTO phase3_team_members
        (team_id, project_id, student_id, role_key, role_name, member_status)
        VALUES (?, ?, ?, ?, ?, 'ACTIVE')
    ");
    $st->bind_param("iiiss", $team_id, $project_id, $student_id, $role_key, $role_name);
    $st->execute();
}

function phase3_refresh_team_actual_members(mysqli $conn, int $team_id): void {
    $st = $conn->prepare("
        UPDATE phase3_teams t
        SET actual_members = (
            SELECT COUNT(*)
            FROM phase3_team_members m
            WHERE m.team_id = t.id AND m.member_status='ACTIVE'
        )
        WHERE t.id = ?
    ");
    $st->bind_param("i", $team_id);
    $st->execute();
}

function phase3_sync_team_review(mysqli $conn, int $project_id, int $team_id, int $partner_id): void {
    $sql = "
        SELECT
          AVG(COALESCE(s.score,0)) AS avg_score,
          AVG(COALESCE(s.partner_rating,0)) AS avg_partner_rating,
          COUNT(*) AS reviewed_count
        FROM phase3_task_submissions s
        WHERE s.project_id=? AND s.team_id=? AND s.partner_reviewed_at IS NOT NULL
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("ii", $project_id, $team_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();

    $avg_score = $row ? (float)$row["avg_score"] : 0;
    $avg_partner_rating = $row ? (float)$row["avg_partner_rating"] : 0;

    $final_score = round(($avg_score * 0.8) + (($avg_partner_rating / 5) * 100 * 0.2), 2);

    $final_decision = "NEEDS_FIX";
    if ($final_score >= 80) $final_decision = "PASS";
    elseif ($final_score < 50) $final_decision = "FAIL";

    $summary = "Auto team summary generated from member submissions";

    $up = $conn->prepare("
        INSERT INTO phase3_team_reviews
        (project_id, team_id, partner_id, avg_score, avg_partner_rating, final_score, final_decision, summary, reviewed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
          avg_score=VALUES(avg_score),
          avg_partner_rating=VALUES(avg_partner_rating),
          final_score=VALUES(final_score),
          final_decision=VALUES(final_decision),
          summary=VALUES(summary),
          reviewed_at=NOW()
    ");
    $up->bind_param(
        "iiidddss",
        $project_id,
        $team_id,
        $partner_id,
        $avg_score,
        $avg_partner_rating,
        $final_score,
        $final_decision,
        $summary
    );
    $up->execute();
}

function phase3_get_team_id_for_student_task(mysqli $conn, int $project_id, int $student_id, int $task_id): int {
    $sql = "
        SELECT t.team_id
        FROM phase3_tasks t
        JOIN phase3_team_members m
          ON m.team_id = t.team_id
         AND m.project_id = t.project_id
        WHERE t.project_id=? AND t.id=? AND m.student_id=?
        LIMIT 1
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("iii", $project_id, $task_id, $student_id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    return $row ? (int)$row["team_id"] : 0;
}