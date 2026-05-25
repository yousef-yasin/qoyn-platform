<?php
// utbn-backend/api/phase3/architect.php
require_once __DIR__ . "/../_phase3_bootstrap.php";
if (!defined("AI_BASE")) define("AI_BASE", "http://127.0.0.1:5006");

require_partner();

/**
 * توحيد role_key ليتوافق مع المسارات الحقيقية في النظام
 */
function normalize_role_key(string $rk): string {
  $rk = strtolower(trim($rk));

  $map = [
    "backend_developer" => "fullstack",
    "frontend_developer" => "fullstack",
    "full_stack_developer" => "fullstack",
    "fullstack_developer" => "fullstack",
    "web_developer" => "fullstack",
"data_scientist" => "ml_engineer",
"data_analyst" => "ml_engineer",
"machine_learning" => "ml_engineer",
    "machine_learning_engineer" => "ml_engineer",
    "ml_engineer" => "ml_engineer",
    "machine_learning_specialist" => "ml_engineer",

    "algorithmic_analyst" => "algorithm_engineer",
    "algorithm_engineer" => "algorithm_engineer",
    "algorithm_specialist" => "algorithm_engineer",

    "security_specialist" => "pentester",
    "security_engineer" => "pentester",
    "penetration_tester" => "pentester",
    "pentester" => "pentester",
    "security_tester" => "pentester",
  ];

  return $map[$rk] ?? $rk;
}

/**
 * اسم role أوضح بعد التطبيع
 */
function normalize_role_name(string $role_key, string $fallbackName = ""): string {
  $role_key = strtolower(trim($role_key));

  $map = [
    "fullstack" => "Fullstack Developer",
    "ml_engineer" => "Machine Learning Engineer",
    "algorithm_engineer" => "Algorithm Engineer",
    "pentester" => "Penetration Tester",
  ];

  return $map[$role_key] ?? ($fallbackName !== "" ? $fallbackName : "Unknown Role");
}

// دعم JSON body
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
  json_out(["ok"=>false,"error"=>"project_id required"], 400);
}

// 1) جلب المشروع
$stmt = $conn->prepare("
  SELECT id, capstone_title, capstone_description
  FROM partner_phase3_projects
  WHERE id=? AND partner_user_id=?
  LIMIT 1
");
if (!$stmt) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}

$stmt->bind_param("ii", $project_id, $partner_id);
$stmt->execute();
$proj = $stmt->get_result()->fetch_assoc();

if (!$proj) {
  json_out(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], 404);
}

// 2) Call AI architect
$payload = [
  "title" => $proj["capstone_title"],
  "description" => $proj["capstone_description"],
];

$ai = ai_post_json(AI_BASE . "/phase3/architect", $payload, 300);
if (empty($ai["ok"])) {
  json_out(["ok"=>false,"error"=>"AI_ARCHITECT_FAILED","ai"=>$ai], 502);
}

$architect = $ai["json"] ?? [];
$tasks = $architect["tasks"] ?? [];
if (!is_array($tasks)) $tasks = [];

/**
 * 3) Normalize architect roles_needed
 * حتى يصير التقرير نفسه متوافق مع الباثات الفعلية
 */
if (!empty($architect["architect"]["roles_needed"]) && is_array($architect["architect"]["roles_needed"])) {
  $normalizedRoles = [];

  foreach ($architect["architect"]["roles_needed"] as $r) {
    $rawRoleKey = (string)($r["role_key"] ?? "");
    $normRoleKey = normalize_role_key($rawRoleKey);

    // تجاهل project_manager لأنه ليس باث طالب عندك
    if ($normRoleKey === "project_manager" || $rawRoleKey === "project_manager") {
      continue;
    }

    $normalizedRoles[] = [
      "role_key" => $normRoleKey,
      "role_name" => normalize_role_name($normRoleKey, (string)($r["role_name"] ?? "")),
      "why" => (string)($r["why"] ?? "")
    ];
  }

  // إزالة التكرار لو backend/frontend صاروا fullstack
  $dedup = [];
  foreach ($normalizedRoles as $r) {
    $dedup[$r["role_key"]] = $r;
  }

  $architect["architect"]["roles_needed"] = array_values($dedup);
}

/**
 * 4) Normalize tasks
 */
$normalizedTasks = [];
$idx = 1;

foreach ($tasks as $t) {
  $task_code = (string)($t["task_code"] ?? ("T" . $idx));
  $task_order = (int)($t["task_order"] ?? $idx);

  $rawRoleKey = (string)($t["role_key"] ?? ($t["role"] ?? "unknown_role"));
  $normRoleKey = normalize_role_key($rawRoleKey);

  // تجاهل أي task غير مطلوب عندك لو طلع Project Manager مثلًا
  if ($normRoleKey === "project_manager") {
    $idx++;
    continue;
  }

  $role_name = normalize_role_name(
    $normRoleKey,
    (string)($t["role_name"] ?? ($t["role"] ?? "Unknown Role"))
  );

  $normalizedTasks[] = [
    "task_code" => $task_code,
    "task_order" => $task_order,
    "role_key" => $normRoleKey,
    "role_name" => $role_name,
    "description" => (string)($t["description"] ?? ""),
    "skills" => is_array($t["skills"] ?? null) ? $t["skills"] : [],
    "acceptance" => is_array($t["acceptance"] ?? null) ? $t["acceptance"] : [],
    "dependencies" => is_array($t["dependencies"] ?? null) ? $t["dependencies"] : [],
  ];

  $idx++;
}

$tasks = $normalizedTasks;
$architect["tasks"] = $tasks;

$architect_json = json_encode($architect, JSON_UNESCAPED_UNICODE);
$tasks_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

// 5) حفظ architect_json + tasks_json + status
$up = $conn->prepare("
  UPDATE partner_phase3_projects
  SET architect_json=?, tasks_json=?, status='ANALYZED'
  WHERE id=?
");
if (!$up) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}

$up->bind_param("ssi", $architect_json, $tasks_json, $project_id);
$up->execute();

// 6) إعادة إنشاء phase3_tasks
$del = $conn->prepare("DELETE FROM phase3_tasks WHERE project_id=?");
if (!$del) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}
$del->bind_param("i", $project_id);
$del->execute();

$ins = $conn->prepare("
  INSERT INTO phase3_tasks
  (project_id, task_code, task_order, role_key, role_name, description,
   skills_json, acceptance_json, dependencies_json,
   assigned_user_id, status, created_at)
  VALUES (?,?,?,?,?,?,?,?,?, NULL,'OPEN', NOW())
");
if (!$ins) {
  json_out(["ok"=>false,"error"=>"PREPARE_FAILED","mysql_error"=>$conn->error], 500);
}

$inserted = 0;


foreach ($tasks as $t) {
  $task_code = (string)($t["task_code"] ?? "");
  $task_order = (int)($t["task_order"] ?? 1);
  $role_key = (string)($t["role_key"] ?? "unknown_role");
  $role_name = (string)($t["role_name"] ?? "Unknown Role");
  $description = (string)($t["description"] ?? "");

  $skills_json = json_encode($t["skills"] ?? [], JSON_UNESCAPED_UNICODE);
  $acceptance_json = json_encode($t["acceptance"] ?? [], JSON_UNESCAPED_UNICODE);
  $dependencies_json = json_encode($t["dependencies"] ?? [], JSON_UNESCAPED_UNICODE);

  $ins->bind_param(
    "isissssss",
    $project_id,
    $task_code,
    $task_order,
    $role_key,
    $role_name,
    $description,
    $skills_json,
    $acceptance_json,
    $dependencies_json
  );
  $ins->execute();

  if ($ins->affected_rows > 0) {
    $inserted++;
  }
}

json_out([
  "ok"=>true,
  "project_id"=>$project_id,
  "tasks_inserted"=>$inserted,
  "architect"=>$architect
]);