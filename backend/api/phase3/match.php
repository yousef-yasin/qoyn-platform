<?php

require_once __DIR__ . "/../_phase3_bootstrap.php";
require_once __DIR__ . "/team_lib.php";

if (!defined("AI_BASE")) define("AI_BASE", "http://127.0.0.1:5006");

require_partner();

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
function phase3_ensure_team_chat(mysqli $conn, int $company_id, int $project_id, int $team_id): array {
    $chk = $conn->prepare("
        SELECT id
        FROM chat_threads
        WHERE company_id=?
          AND COALESCE(student_id,0)=0
          AND COALESCE(team_id,0)=?
          AND COALESCE(phase2_submission_id,0)=0
          AND COALESCE(phase3_project_id,0)=?
          AND COALESCE(phase3_task_id,0)=0
          AND is_team_chat=1
        LIMIT 1
    ");
    if (!$chk) {
        return ["thread_id" => 0, "created" => false];
    }

    $chk->bind_param("iii", $company_id, $team_id, $project_id);
    $chk->execute();
    $row = $chk->get_result()->fetch_assoc();
    $chk->close();

    if ($row) {
        return [
            "thread_id" => (int)$row["id"],
            "created"   => false
        ];
    }

    $ins = $conn->prepare("
        INSERT INTO chat_threads
        (company_id, student_id, team_id, is_team_chat, phase_source, phase2_submission_id, phase3_project_id, phase3_task_id, created_by, last_message_at)
        VALUES (?, 0, ?, 1, 'phase3', 0, ?, 0, ?, NOW())
    ");
    if (!$ins) {
        return ["thread_id" => 0, "created" => false];
    }

    $ins->bind_param("iiii", $company_id, $team_id, $project_id, $company_id);
    $ins->execute();
    $thread_id = (int)$conn->insert_id;
    $ins->close();

    return [
        "thread_id" => $thread_id,
        "created"   => true
    ];
}

function phase3_normalize_role_key(string $v): string {
    return strtolower(trim($v));
}

function phase3_split_skills($value): array {
    if (is_array($value)) {
        $out = [];
        foreach ($value as $x) {
            $x = strtolower(trim((string)$x));
            if ($x !== "") $out[] = $x;
        }
        return array_values(array_unique($out));
    }

    $text = trim((string)$value);
    if ($text === "") return [];

    $text = str_replace(["،", ";", "|", "\n", "\r"], ",", $text);
    $parts = array_map("trim", explode(",", $text));

    $out = [];
    foreach ($parts as $p) {
        $p = strtolower(trim((string)$p));
        if ($p !== "") $out[] = $p;
    }
    return array_values(array_unique($out));
}

function phase3_extract_task_skills(array $task): array {
    $candidates = [
        $task["required_skills"] ?? null,
        $task["skills_required"] ?? null,
        $task["skills_text"] ?? null,
        $task["task_skills"] ?? null,
    ];

    foreach ($candidates as $c) {
        $skills = phase3_split_skills($c);
        if ($skills) return $skills;
    }

    return [];
}

function phase3_find_student_map(array $students): array {
    $map = [];
    foreach ($students as $s) {
        $sid = (int)($s["id"] ?? 0);
        if ($sid > 0) {
            $map[$sid] = $s;
        }
    }
    return $map;
}

function phase3_find_task_map(array $tasks): array {
    $map = [];
    foreach ($tasks as $t) {
        $tid = (int)($t["id"] ?? 0);
        if ($tid > 0) {
            $map[$tid] = $t;
        }
    }
    return $map;
}

// Support JSON body
$raw = file_get_contents("php://input");
if ($raw) {
    $j = json_decode($raw, true);
    if (is_array($j)) {
        foreach ($j as $k => $v) {
            if (!isset($_POST[$k])) $_POST[$k] = $v;
        }
    }
}

$partner_id = (int)($_SESSION["user_id"] ?? 0);

$project_id = (int)($_POST["project_id"] ?? 0);
if ($project_id <= 0) {
    json_out(["ok" => false, "error" => "project_id required"], 400);
}

/*
|--------------------------------------------------------------------------
| 0) تأكد المشروع للـ partner
|--------------------------------------------------------------------------
*/
$p = $conn->prepare("
    SELECT id, capstone_title, capstone_description
    FROM partner_phase3_projects
    WHERE id=? AND partner_user_id=?
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
| 1) جلب المهام
|--------------------------------------------------------------------------
*/
$tq = $conn->prepare("
    SELECT *
    FROM phase3_tasks
    WHERE project_id=?
    ORDER BY task_order ASC, id ASC
");
if (!$tq) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}

$tq->bind_param("i", $project_id);
$tq->execute();
$tasks = $tq->get_result()->fetch_all(MYSQLI_ASSOC);
$tq->close();

if (!$tasks) {
    json_out(["ok" => false, "error" => "NO_TASKS"], 400);
}

/*
|--------------------------------------------------------------------------
| 2) جلب الطلاب المؤهلين فقط (coins > 20000)
|--------------------------------------------------------------------------
*/
$students = [];

$rs = $conn->query("
    SELECT
        u.id AS student_id,
        u.full_name,
        u.coins,
        usr.role_key AS selected_role_key,
        usr.score AS role_score,
        lp.role_key AS selected_path_role_key,
        COALESCE(GROUP_CONCAT(DISTINCT s.skill_name SEPARATOR ', '), '') AS skills_text
    FROM users u
    LEFT JOIN user_selected_role usr
        ON usr.user_id = u.id
    LEFT JOIN user_selected_path usp
        ON usp.user_id = u.id
    LEFT JOIN learning_paths lp
        ON lp.id = usp.path_id
    LEFT JOIN user_skills us
        ON us.user_id = u.id
    LEFT JOIN skills s
        ON s.id = us.skill_id
    WHERE u.role='student'
      AND u.coins > 20000
    GROUP BY u.id
    ORDER BY u.id ASC
    LIMIT 2000
");

if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $students[] = [
            "id"                     => (int)$row["student_id"],
            "full_name"              => (string)($row["full_name"] ?? ""),
            "coins"                  => (int)($row["coins"] ?? 0),
            "selected_role_key"      => phase3_normalize_role_key((string)($row["selected_role_key"] ?? "")),
            "selected_path_role_key" => phase3_normalize_role_key((string)($row["selected_path_role_key"] ?? "")),
            "role_score"             => (float)($row["role_score"] ?? 0),
            "skills_text"            => (string)($row["skills_text"] ?? "")
        ];
    }
}

if (!$students) {
    json_out(["ok" => false, "error" => "NO_STUDENTS_FOUND"], 400);
}

/*
|--------------------------------------------------------------------------
| 3) ابنِ payload للـ AI service
|--------------------------------------------------------------------------
*/
$aiTasks = [];
foreach ($tasks as $t) {
    $aiTasks[] = [
        "id"               => (int)($t["id"] ?? 0),
        "task_code"        => (string)($t["task_code"] ?? ""),
        "role_key"         => phase3_normalize_role_key((string)($t["role_key"] ?? "")),
        "role_name"        => (string)($t["role_name"] ?? ""),
        "task_description" => (string)($t["task_description"] ?? ""),
        "skills"           => phase3_extract_task_skills($t)
    ];
}

$aiPayload = [
    "students" => $students,
    "tasks"    => $aiTasks
];

$ai = ai_post_json(AI_BASE . "/phase3/match", $aiPayload, 300);

if (!$ai["ok"]) {
    json_out([
        "ok"       => false,
        "error"    => "AI_MATCH_FAILED",
        "status"   => $ai["status"] ?? 0,
        "ai_reply" => $ai
    ], 500);
}

$aiJson = $ai["json"] ?? [];
if (empty($aiJson["ok"])) {
    json_out([
        "ok"       => false,
        "error"    => "AI_MATCH_NOT_OK",
        "ai_reply" => $aiJson
    ], 500);
}

$aiAssignments = $aiJson["assignments"] ?? [];
if (!is_array($aiAssignments) || !$aiAssignments) {
    json_out([
        "ok"       => false,
        "error"    => "EMPTY_AI_ASSIGNMENTS",
        "ai_reply" => $aiJson
    ], 500);
}

$studentsMap = phase3_find_student_map($students);
$tasksMap = phase3_find_task_map($tasks);

/*
|--------------------------------------------------------------------------
| 4) حذف البيانات القديمة لهذا المشروع
|--------------------------------------------------------------------------
*/
$delMsgs = $conn->prepare("
    DELETE cm
    FROM chat_messages cm
    INNER JOIN chat_threads ct ON ct.id = cm.thread_id
    WHERE ct.phase_source='phase3'
      AND ct.phase3_project_id=?
");
if (!$delMsgs) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$delMsgs->bind_param("i", $project_id);
$delMsgs->execute();
$delMsgs->close();

$delThreads = $conn->prepare("
    DELETE FROM chat_threads
    WHERE phase_source='phase3'
      AND phase3_project_id=?
");
if (!$delThreads) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$delThreads->bind_param("i", $project_id);
$delThreads->execute();
$delThreads->close();

$delAssign = $conn->prepare("DELETE FROM phase3_task_assignments WHERE project_id=?");
if (!$delAssign) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$delAssign->bind_param("i", $project_id);
$delAssign->execute();
$delAssign->close();

$delMembers = $conn->prepare("DELETE FROM phase3_team_members WHERE project_id=?");
if (!$delMembers) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$delMembers->bind_param("i", $project_id);
$delMembers->execute();
$delMembers->close();

$delTeams = $conn->prepare("DELETE FROM phase3_teams WHERE project_id=?");
if (!$delTeams) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$delTeams->bind_param("i", $project_id);
$delTeams->execute();
$delTeams->close();

/*
|--------------------------------------------------------------------------
| 5) أنشئ Team واحد للمشروع
|--------------------------------------------------------------------------
*/
$team_no = 1;
$required_members = count($tasks);
if ($required_members <= 0) $required_members = 1;

$team_id = phase3_create_team(
    $conn,
    $project_id,
    $team_no,
    "Project Team #".$team_no,
    $required_members
);

/*
|--------------------------------------------------------------------------
| 6) جهز insert assignment
|--------------------------------------------------------------------------
*/
$insAssign = $conn->prepare("
    INSERT INTO phase3_task_assignments
    (project_id, task_id, task_code, student_id, match_score, reason, status)
    VALUES (?, ?, ?, ?, ?, ?, 'ASSIGNED')
");
if (!$insAssign) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}


$bulkAssignments = [];
$teamMembersAdded = [];
$attempted = 0;
$done = 0;

/*
|--------------------------------------------------------------------------
| 7) خزّن التوزيع القادم من الـ AI
|--------------------------------------------------------------------------
*/
foreach ($aiAssignments as $row) {
    $task_id = (int)($row["task_id"] ?? 0);
    $task_code = (string)($row["task_code"] ?? "");
    $role_key = phase3_normalize_role_key((string)($row["role_key"] ?? ""));
    $assigned_student_id = (int)($row["assigned_student_id"] ?? 0);
    $assigned_score = (float)($row["assigned_score"] ?? 0);
    $top_candidates = $row["top_candidates"] ?? [];

    $task = $tasksMap[$task_id] ?? null;
    $role_name = (string)($task["role_name"] ?? $role_key);

    $student_name = "";
    $reason = "Assigned by AI matching engine";

    if ($assigned_student_id > 0 && isset($studentsMap[$assigned_student_id])) {
        $attempted++;

        $student = $studentsMap[$assigned_student_id];
        $student_name = (string)($student["full_name"] ?? "");

        if (!isset($teamMembersAdded[$assigned_student_id])) {
            phase3_add_team_member(
                $conn,
                $team_id,
                $project_id,
                $assigned_student_id,
                $role_key,
                $role_name
            );
            $teamMembersAdded[$assigned_student_id] = true;
        }

        $reasonParts = [];
        $reasonParts[] = "Assigned by AI matching engine";
        $reasonParts[] = "score=" . round($assigned_score, 4);

        if (is_array($top_candidates) && !empty($top_candidates)) {
            $candTexts = [];
            foreach (array_slice($top_candidates, 0, 3) as $cand) {
                $candSid = (int)($cand["student_id"] ?? 0);
                $candScore = (float)($cand["score"] ?? 0);
                $candName = "";

                if ($candSid > 0 && isset($studentsMap[$candSid])) {
                    $candName = (string)($studentsMap[$candSid]["full_name"] ?? "");
                }

                if ($candSid > 0) {
                    $candTexts[] = $candSid . ($candName !== "" ? ":" . $candName : "") . "@" . round($candScore, 4);
                }
            }

            if ($candTexts) {
                $reasonParts[] = "top_candidates=" . implode(" | ", $candTexts);
            }
        }

        $reason = implode(" ; ", $reasonParts);

        $insAssign->bind_param(
            "iisids",
            $project_id,
            $task_id,
            $task_code,
            $assigned_student_id,
            $assigned_score,
            $reason
        );
        $ok = $insAssign->execute();

        if ($ok) {
            $done++;
        }
    } else {
        $reason = "No student assigned by AI";
    }

    $bulkAssignments[] = [
        "team_id"           => $team_id,
        "team_no"           => $team_no,
        "task_id"           => $task_id,
        "task_code"         => $task_code,
        "role_key"          => $role_key,
        "role_name"         => $role_name,
        "student_id"        => $assigned_student_id,
        "student_name"      => $student_name,
        "score"             => round($assigned_score, 4),
        "reason"            => $reason,
        "top_candidates"    => is_array($top_candidates) ? array_slice($top_candidates, 0, 5) : []
    ];
}

$insAssign->close();

/*
|--------------------------------------------------------------------------
| 8) حدّث عدد أعضاء التيم الفعلي
|--------------------------------------------------------------------------
*/
phase3_refresh_team_actual_members($conn, $team_id);

/*
|--------------------------------------------------------------------------
| 9) أنشئ شات الفريق
|--------------------------------------------------------------------------
*/
$chat = phase3_ensure_team_chat($conn, $partner_id, $project_id, $team_id);
$thread_id = (int)($chat["thread_id"] ?? 0);

if ($thread_id > 0 && !empty($chat["created"])) {
    $welcome = $conn->prepare("
        INSERT INTO chat_messages (thread_id, sender_id, sender_role, message, is_read, created_at)
        VALUES (?, ?, 'company', ?, 0, NOW())
    ");
    if ($welcome) {
        $welcomeMsg = "تم إنشاء شات الفريق لهذا المشروع. أي استفسار أو تنسيق بينكم وبين الشركة يكون هون.";
        $welcome->bind_param("iis", $thread_id, $partner_id, $welcomeMsg);
        $welcome->execute();
        $welcome->close();
    }
}

/*
|--------------------------------------------------------------------------
| 10) اربط كل التاسكات بهذا التيم
|--------------------------------------------------------------------------
*/
$upTaskTeam = $conn->prepare("
    UPDATE phase3_tasks
    SET team_id=?, assigned_user_id=NULL, status='ASSIGNED'
    WHERE project_id=?
");
if (!$upTaskTeam) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$upTaskTeam->bind_param("ii", $team_id, $project_id);
$upTaskTeam->execute();
$upTaskTeam->close();

/*
|--------------------------------------------------------------------------
| 11) جهز match_json
|--------------------------------------------------------------------------
*/
$match = [
    "ok"               => true,
    "mode"             => "ai_team_based",
    "project_id"       => $project_id,
    "project_title"    => (string)($project["capstone_title"] ?? ""),
    "team_id"          => $team_id,
    "team_no"          => $team_no,
    "thread_id"        => $thread_id,
    "ai_endpoint"      => AI_BASE . "/phase3/match",
    "ai_payload_meta"  => [
        "students_count" => count($students),
        "tasks_count"    => count($aiTasks)
    ],
    "assignments"      => $bulkAssignments
];

$match_json = json_encode($match, JSON_UNESCAPED_UNICODE);

/*
|--------------------------------------------------------------------------
| 12) خزّن match_json + status
|--------------------------------------------------------------------------
*/
$up = $conn->prepare("
    UPDATE partner_phase3_projects
    SET match_json=?, status='MATCHED'
    WHERE id=?
");
if (!$up) {
    json_out(["ok" => false, "error" => "PREPARE_FAILED", "mysql_error" => $conn->error], 500);
}
$up->bind_param("si", $match_json, $project_id);
$up->execute();
$up->close();

/*
|--------------------------------------------------------------------------
| 13) Response
|--------------------------------------------------------------------------
*/
json_out([
    "ok"               => true,
    "project_id"       => $project_id,
    "mode"             => "ai_team_based",
    "team_id"          => $team_id,
    "team_no"          => $team_no,
    "thread_id"        => $thread_id,
    "attempted"        => $attempted,
    "assigned"         => $done,
    "students_count"   => count($students),
    "tasks_count"      => count($tasks),
    "assignments"      => $bulkAssignments,
    "ai_response"      => $aiJson,
    "match"            => $match
]);