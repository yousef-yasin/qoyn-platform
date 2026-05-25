<?php
// utbn-backend/api/phase2_get_or_create_project.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

// 1) get path_id
$path_id = 0;
$st = $conn->prepare("SELECT path_id FROM user_selected_path WHERE user_id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i", $user_id);
$st->execute();
$rs = $st->get_result();
if ($row = $rs->fetch_assoc()) $path_id = (int)$row["path_id"];
$st->close();

if ($path_id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"NO_SELECTED_PATH"], JSON_UNESCAPED_UNICODE);
  exit;
}

// 2) get role_key
$role_key = "";
$role_name = "";
$st = $conn->prepare("SELECT role_key, role_name FROM user_selected_role WHERE user_id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i", $user_id);
$st->execute();
$rs = $st->get_result();
if ($row = $rs->fetch_assoc()) {
  $role_key = (string)$row["role_key"];
  $role_name = (string)$row["role_name"];
}
$st->close();
if ($role_key === "") $role_key = "fullstack";
if ($role_name === "") $role_name = $role_key;

// 3) path title (من learning_paths)
$path_title = "Path #".$path_id;
$st = $conn->prepare("SELECT COALESCE(title, name, '') AS t FROM learning_paths WHERE id=? LIMIT 1");
if($st){
  $st->bind_param("i", $path_id);
  $st->execute();
  $rs = $st->get_result();
  if ($row = $rs->fetch_assoc()) {
    $t = trim((string)$row["t"]);
    if ($t !== "") $path_title = $t;
  }
  $st->close();
}

/**
 * ✅ helper: detect old cached schema (tasks/mcq) vs new schema (milestones)
 */
function is_old_phase2_project($tasks_arr) {
  if (!is_array($tasks_arr)) return true;
  if (count($tasks_arr) === 0) return true;

  // old style uses: type/question/expected OR has mcq
  foreach ($tasks_arr as $t) {
    if (!is_array($t)) continue;
    if (isset($t["type"]) || isset($t["question"]) || isset($t["expected"])) return true;
    if (isset($t["type"]) && $t["type"] === "mcq") return true;
  }

  // new style milestones should have: title + deliverable + acceptance
  $has_new = false;
  foreach ($tasks_arr as $m) {
    if (!is_array($m)) continue;
    if (isset($m["title"]) && isset($m["deliverable"])) $has_new = true;
    if (isset($m["acceptance"]) && is_array($m["acceptance"])) $has_new = true;
  }
  return !$has_new;
}
function load_playlists_for_path($conn, int $path_id): array {
  $out = [];
  $st = $conn->prepare("
    SELECT p.name, COALESCE(p.description,'') AS description
    FROM learning_path_playlists lp
    JOIN partner_playlists p ON p.id = lp.template_playlist_id
    WHERE lp.path_id=?
    ORDER BY lp.sort_order ASC, lp.id ASC
    LIMIT 12
  ");
  if ($st) {
    $st->bind_param("i", $path_id);
    $st->execute();
    $rs = $st->get_result();
    while ($row = $rs->fetch_assoc()) {
      $out[] = ["name" => (string)$row["name"], "description" => (string)$row["description"]];
    }
    $st->close();
  }
  return $out;
}

function apply_phase2_fallback(array $project, string $role_key, string $path_title, array $playlists_for_scope): array {
  // ---------- stack ----------
  $stack = $project["stack"] ?? null;
  if (!is_array($stack) || count($stack) === 0) {
    $map = [
      "fullstack" => ["HTML/CSS", "JavaScript", "PHP", "MySQL", "Git"],
      "backend"   => ["PHP", "MySQL", "REST API", "Auth/Sessions", "Git"],
      "frontend"  => ["HTML/CSS", "JavaScript", "UI Components", "Fetch/REST", "Git"],
      "data"      => ["Python", "Pandas", "NumPy", "SQL", "Jupyter"],
      "ml"        => ["Python", "scikit-learn", "FastAPI", "Model Evaluation", "Git"],
    ];
    $project["stack"] = $map[$role_key] ?? ["Python", "Git", "Docker"];
  }

  // ---------- deliverables ----------
  $deliverables = $project["deliverables"] ?? null;
  if (!is_array($deliverables) || count($deliverables) === 0) {
    $project["deliverables"] = [
      "Git repo link (GitHub/GitLab) OR ZIP upload",
      "README with setup/run instructions",
      "Architecture diagram (image or markdown)",
      "Demo video link (3-7 minutes)",
      "Short report (decisions, tradeoffs, what you learned)"
    ];
  }

  // ---------- scope ----------
  $scope = $project["scope"] ?? null;
  $must = is_array($scope) && isset($scope["must_have"]) && is_array($scope["must_have"]) ? $scope["must_have"] : [];
  $nice = is_array($scope) && isset($scope["nice_to_have"]) && is_array($scope["nice_to_have"]) ? $scope["nice_to_have"] : [];

  if (count($must) === 0) {
    $must = [];
    $i = 0;
    foreach ($playlists_for_scope as $pl) {
      $name = trim((string)($pl["name"] ?? ""));
      if ($name === "") continue;
      $must[] = "Implement core work aligned to playlist: ".$name;
      $i++;
      if ($i >= 5) break;
    }
    $must[] = "README with clear setup/run steps";
    $must[] = "Demo video that shows the system working";
  }

  if (count($nice) === 0) {
    $nice = [
      "Add basic tests or sanity checks",
      "Add Dockerfile / containerized run",
      "Add simple CI (optional)",
      "Improve UI/UX & error messages",
    ];
  }

  $project["scope"] = ["must_have" => $must, "nice_to_have" => $nice];

  // ---------- title/description minimal fallback ----------
  if (empty($project["title"])) $project["title"] = "Phase 2 Capstone - ".$path_title;
  if (empty($project["description"])) $project["description"] = "Build a real-world capstone project aligned to your learning path playlists.";

  return $project;
}
// 4) cached project for this USER + path + role (skip old schema)
$st = $conn->prepare("
  SELECT id, title, description, tasks_json, rubric_json, base_coins, pass_score, ai_model, playlist_ids_json
  FROM phase2_projects
  WHERE user_id=? AND path_id=? AND role_key=?
  ORDER BY id DESC
  LIMIT 1
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("iis", $user_id, $path_id, $role_key); // ✅ FIX
$st->execute();
$rs = $st->get_result();
if ($row = $rs->fetch_assoc()) {
  $tasks_arr = json_decode($row["tasks_json"], true);
  $rubric_arr = json_decode($row["rubric_json"], true);

  if (!is_old_phase2_project($tasks_arr)) {
    $st->close();
    $pls_scope = load_playlists_for_path($conn, $path_id);

$proj = [
  "title" => (string)$row["title"],
  "description" => (string)$row["description"],
  "milestones" => is_array($tasks_arr) ? $tasks_arr : [],
  "rubric" => is_array($rubric_arr) ? $rubric_arr : [],
  "pass_score" => (int)$row["pass_score"]
];

$proj = apply_phase2_fallback($proj, $role_key, $path_title, $pls_scope);

echo json_encode([
  "ok"=>true,
  "cached"=>true,
  "project_id" => (int)$row["id"],
  "base_coins" => (int)$row["base_coins"],
  "role_key"   => $role_key,
  "role_name"  => $role_name,
  "path_id"    => $path_id,
  "path_title" => $path_title,
  "project"    => $proj,
  "ai_model"   => $row["ai_model"]
], JSON_UNESCAPED_UNICODE);
exit;
  }
}
$st->close();

// 5) load playlists
$playlists = [];
$playlist_ids = [];
$sum_coin_pool = 0;

$st = $conn->prepare("
  SELECT lp.template_playlist_id, lp.coin_pool,
         p.name, COALESCE(p.description,'') AS description
  FROM learning_path_playlists lp
  JOIN partner_playlists p ON p.id = lp.template_playlist_id
  WHERE lp.path_id = ?
  ORDER BY lp.sort_order ASC, lp.id ASC
  LIMIT 12
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i", $path_id);
$st->execute();
$rs = $st->get_result();
while ($row = $rs->fetch_assoc()) {
  $pid = (int)$row["template_playlist_id"];
  $coin_pool = (int)($row["coin_pool"] ?? 0);
  $sum_coin_pool += $coin_pool;

  $playlist_ids[] = $pid;
  $playlists[] = [
    "id" => $pid,
    "name" => (string)$row["name"],
    "description" => (string)$row["description"]
  ];
}
$st->close();

if (!$playlists) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"NO_PLAYLISTS_FOR_PATH"], JSON_UNESCAPED_UNICODE);
  exit;
}

$base_coins = ($sum_coin_pool > 0) ? $sum_coin_pool : 2000;

// 6) call AI
$payload = [
  "role_key" => $role_key,
  "path_title" => $path_title,
  "playlists" => $playlists,
  "base_coins" => $base_coins
];

$ch = curl_init("http://127.0.0.1:5006/phase2/generate");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 240,
]);
$resp = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($resp === false) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"AI_CURL_FAILED","detail"=>$err], JSON_UNESCAPED_UNICODE);
  exit;
}
$data = json_decode($resp, true);
if ($code !== 200 || !$data || empty($data["ok"])) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"AI_BAD_RESPONSE","http"=>$code,"raw"=>$resp], JSON_UNESCAPED_UNICODE);
  exit;
}

$project = $data["project"];
$ai_model = (string)($data["model"] ?? "");
$pls_scope = load_playlists_for_path($conn, $path_id);
$project = apply_phase2_fallback($project, $role_key, $path_title, $pls_scope);
$title = (string)($project["title"] ?? "Phase2 Project");
$desc  = (string)($project["description"] ?? "");
$pass_score  = (int)($project["pass_score"] ?? 70);
$playlist_ids_json = json_encode($playlist_ids, JSON_UNESCAPED_UNICODE);

$milestones = $project["milestones"] ?? null;
if (!is_array($milestones)) $milestones = ($project["tasks"] ?? []);
if (!is_array($milestones)) $milestones = [];

$tasks_json  = json_encode($milestones, JSON_UNESCAPED_UNICODE);
$rubric_json = json_encode($project["rubric"] ?? [], JSON_UNESCAPED_UNICODE);

// 7) save (✅ include user_id)
$st = $conn->prepare("
  INSERT INTO phase2_projects (user_id, path_id, role_key, playlist_ids_json, title, description, tasks_json, rubric_json, base_coins, pass_score, ai_model)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param(
  "iissssssiis",
  $user_id, $path_id, $role_key,
  $playlist_ids_json, $title, $desc,
  $tasks_json, $rubric_json,
  $base_coins, $pass_score, $ai_model
);
$st->execute();
$new_id = (int)$st->insert_id;
$st->close();

echo json_encode([
  "ok"=>true,
  "cached"=>false,
  "project_id"=>$new_id,
  "base_coins"=>$base_coins,
  "role_key"=>$role_key,
  "role_name"=>$role_name,
  "path_id"=>$path_id,
  "path_title"=>$path_title,
  "ai_model"=>$ai_model,
  "project"=>$project
], JSON_UNESCAPED_UNICODE);