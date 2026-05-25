<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// utbn-backend/api/phase2_submit_project.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

function json_out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

if (!isset($_SESSION["user_id"])) json_out(["ok"=>false,"error"=>"NOT_LOGGED_IN"], 401);
$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);

$project_id = (int)($_POST["project_id"] ?? 0);
$repo_url   = trim((string)($_POST["repo_url"] ?? ""));
$notes      = trim((string)($_POST["notes"] ?? ""));
// ✅ ALWAYS BOTH (AI + Company)
$review_mode = "both";
if ($project_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_PROJECT_ID"], 400);

// ✅ تأكد المشروع يخص هذا الطالب
$st = $conn->prepare("
  SELECT id, path_id, role_key, title, description, tasks_json, rubric_json, base_coins, pass_score
FROM phase2_projects
  WHERE id=? AND user_id=?
  LIMIT 1
");if(!$st) json_out(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], 500);
$st->bind_param("ii", $project_id, $user_id);
$st->execute();
$rs = $st->get_result();
$row = $rs->fetch_assoc();
$st->close();

if(!$row) json_out(["ok"=>false,"error"=>"PROJECT_NOT_FOUND"], 404);

$base_coins = (int)($row["base_coins"] ?? 2000);
$pass_score = (int)($row["pass_score"] ?? 70);

// ✅ لازم repo أو zip
$has_zip = isset($_FILES["artifact"]) && is_uploaded_file($_FILES["artifact"]["tmp_name"]);
if (!$has_zip && $repo_url === "") json_out(["ok"=>false,"error"=>"MISSING_REPO_OR_ZIP"], 400);

// -----------------------
// 1) حفظ ZIP + unzip
// -----------------------
$artifact_zip_path = null;
$artifact_dir_path = null;
$checks = [
  "has_repo_url" => ($repo_url !== ""),
  "zip_uploaded" => $has_zip,
  "zip_unzipped" => false,
  "files_count"  => 0,
  "has_readme"   => false,
  "has_requirements" => false,
  "has_dockerfile" => false,
  "has_api_or_app" => false,
  "has_notebook" => false,
  "has_model_file" => false,
  "found_models" => [],
  "found_entrypoints" => [],
  "has_code_files" => false,
  "warnings" => []
];

function ensure_dir($p){
  if (!is_dir($p)) @mkdir($p, 0777, true);
}

$base_dir = __DIR__ . "/../uploads/phase2/user_" . $user_id;
ensure_dir($base_dir);

$sub_dir = $base_dir . "/sub_" . date("Ymd_His") . "_" . bin2hex(random_bytes(3));
ensure_dir($sub_dir);

if ($has_zip) {
  $orig = (string)($_FILES["artifact"]["name"] ?? "project.zip");
  $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
  if ($ext !== "zip") json_out(["ok"=>false,"error"=>"ONLY_ZIP_ALLOWED"], 400);

  $artifact_zip_path = $sub_dir . "/project.zip";
  if (!move_uploaded_file($_FILES["artifact"]["tmp_name"], $artifact_zip_path)) {
    json_out(["ok"=>false,"error"=>"ZIP_SAVE_FAILED"], 500);
  }

  $artifact_dir_path = $sub_dir . "/unzipped";
  ensure_dir($artifact_dir_path);
if (!class_exists('ZipArchive')) {
  json_out(["ok"=>false,"error"=>"ZIP_EXTENSION_MISSING","detail"=>"Enable php_zip (ZipArchive) in php.ini ثم اعمل restart لـ Apache"], 500);
}
  $zip = new ZipArchive();
  if ($zip->open($artifact_zip_path) === TRUE) {
    $zip->extractTo($artifact_dir_path);
    $zip->close();
    $checks["zip_unzipped"] = true;
  } else {
    $checks["warnings"][] = "ZIP_OPEN_FAILED";
  }
}

// -----------------------
// 2) فحص الملفات (إذا unzipped)
// -----------------------
function scan_project($root, &$checks){
  if (!is_dir($root)) return;

  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
  $count = 0;

  $model_exts = ["pkl","joblib","onnx","pt","pth","h5","ckpt","sav"];
$entry_names = ["index.php","app.py","main.py","server.py","api.py","run.py","wsgi.py","package.json","docker-compose.yml","compose.yml"];  $api_hints = ["fastapi","flask","uvicorn","django","express"];

  foreach ($rii as $file) {
    if (!$file->isFile()) continue;
    $count++;
    $name = strtolower($file->getFilename());
    $path = strtolower($file->getPathname());

// ✅ README detection (covers: README.md, Read Me.txt, read-me, etc)
$readme_norm = preg_replace('/[^a-z0-9]+/', '', $name); // removes spaces/dashes
if (strpos($readme_norm, "readme") === 0) {
  $checks["has_readme"] = true;
}    if ($name === "requirements.txt") $checks["has_requirements"] = true;
    if ($name === "dockerfile") $checks["has_dockerfile"] = true;

    if (substr($name, -6) === ".ipynb") $checks["has_notebook"] = true;

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    // ✅ detect any real code files
if (in_array($ext, ["php","py","js","ts","java","cpp","c","cs","go","rb","rs","sql","ipynb"], true)) {
  $checks["has_code_files"] = true;
}
    if (in_array($ext, $model_exts, true)) {
      $checks["has_model_file"] = true;
      $checks["found_models"][] = $name;
    }

    foreach ($entry_names as $en) {
      if ($name === $en) $checks["found_entrypoints"][] = $en;
    }

    // quick hints: look at few small text files for "fastapi/flask/uvicorn" etc
    if (in_array($ext, ["py","js","ts","json","yml","yaml","md","txt"], true)) {
      if ($file->getSize() <= 200000) {
        $txt = @file_get_contents($file->getPathname());
        if (is_string($txt)) {
          $lt = strtolower($txt);
          foreach ($api_hints as $h) {
            if (strpos($lt, $h) !== false) { $checks["has_api_or_app"] = true; break; }
          }
        }
      }
    }
  }

  $checks["files_count"] = $count;
}

if ($checks["zip_unzipped"]) {
  scan_project($artifact_dir_path, $checks);
}
function safe_excerpt($s, $max=2500){
  $s = (string)$s;
  if (function_exists("mb_substr")) return mb_substr($s, 0, $max);
  return substr($s, 0, $max);
}

function build_manifest($root, $checks){
  $manifest = [
    "files" => [],
    "readme_excerpt" => "",
    "entrypoints" => $checks["found_entrypoints"] ?? [],
    "snippets" => []
  ];
  if (!is_dir($root)) return $manifest;

  // file list (حد أقصى 400 ملف)
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
  foreach ($rii as $file) {
    if (!$file->isFile()) continue;
    $rel = str_replace("\\","/", str_replace($root, "", $file->getPathname()));
    $manifest["files"][] = $rel;
    if (count($manifest["files"]) >= 400) break;
  }

  // README excerpt
foreach (["README.md","readme.md","README.txt","readme.txt","Read Me.txt","read me.txt","read-me.txt","README.MD"] as $rname) {    $cand = $root . "/" . $rname;
    if (file_exists($cand)) {
      $manifest["readme_excerpt"] = safe_excerpt(@file_get_contents($cand), 3000);
      break;
    }
  }

  // snippets للـ entrypoints المهمة (أول 1500 حرف)
  $want = ["app.py","main.py","server.py","api.py","run.py","wsgi.py","package.json","docker-compose.yml","compose.yml","Dockerfile","requirements.txt"];
  $rii2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
  foreach ($rii2 as $file) {
    if (!$file->isFile()) continue;
    $name = $file->getFilename();
    if (!in_array($name, $want, true)) continue;
    if ($file->getSize() > 250000) continue;
    $txt = @file_get_contents($file->getPathname());
    if (is_string($txt)) {
      $manifest["snippets"][$name] = safe_excerpt($txt, 1500);
    }
  }

  return $manifest;
}

$manifest = $checks["zip_unzipped"] ? build_manifest($artifact_dir_path, $checks) : [];
$artifact_dir_abs = $artifact_dir_path ? realpath($artifact_dir_path) : "";
// -----------------------
// 3) نادِ AI Reviewer
// -----------------------
$project = [
  "id" => (int)$row["id"],
  "path_id" => (int)($row["path_id"] ?? 0),
  "role_key" => (string)($row["role_key"] ?? ""),
  "title" => (string)$row["title"],
  "description" => (string)$row["description"],
  "tasks" => json_decode((string)$row["tasks_json"], true),
  "rubric" => json_decode((string)$row["rubric_json"], true),
  "pass_score" => $pass_score
];

$submission = [
  "repo_url" => $repo_url,
  "notes" => $notes,
  "checks" => $checks,
  "manifest" => $manifest,
  "artifact_dir_abs" => $artifact_dir_abs
];

$ai_payload = [
  "project" => $project,
  "submission" => $submission
];
// =======================
// ✅ Company Review Mode (NO AI CALL)
// =======================

$ch = curl_init("http://127.0.0.1:5006/phase2/grade_project_v2");curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($ai_payload, JSON_UNESCAPED_UNICODE),
CURLOPT_TIMEOUT => 600,]);
$resp = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$ai_ok = true;

if ($resp === false) {
  $ai_ok = false;
} else {
  $data = json_decode($resp, true);
  if ($code !== 200 || !$data || empty($data["ok"])) {
    $ai_ok = false;
  }
}

if (!$ai_ok) {
  // ✅ AI تعطل: نخزن submission وبننتظر الشركة
  $score = 0;
  $decision = "PENDING";
  $feedback = "AI service unavailable. Waiting for company review.";
  $fixes = [];
  $ai_model = "";
  $evidence = [];
  $evidence_json = json_encode($evidence, JSON_UNESCAPED_UNICODE);

  // ✅ لا تكوينز هون
  $coins_total = 0;
  $prev_best_total = 0;
  $new_best_total = 0;
  $coins_awarded = 0;

} else {

  // ✅ هنا AI شغال طبيعي
  $grade = $data["grade"] ?? [];
  $ai_model = (string)($data["model"] ?? "");
  $evidence = $data["evidence"] ?? [];
  $evidence_json = json_encode($evidence, JSON_UNESCAPED_UNICODE);

  $score = (int)($grade["score"] ?? 0);
  if ($score < 0) $score = 0;
  if ($score > 100) $score = 100;

  $llm_decision = (string)($grade["decision"] ?? "");
  if ($llm_decision === "") {
    $llm_decision = ($score >= $pass_score ? "PASS" : "FAIL");
  }

  // ✅ خليك على نفس caps/gates تبعونك (انسخهم زي ما هم)
  $caps = [];
  if (empty($checks["has_readme"])) $caps[] = 60;
  if (!empty($checks["zip_uploaded"]) && empty($checks["zip_unzipped"])) $caps[] = 40;
  if (empty($checks["has_api_or_app"]) && empty($checks["found_entrypoints"])) $caps[] = 55;
  if ((int)($checks["files_count"] ?? 0) < 5) $caps[] = 35;

  $decision = $llm_decision;
  if (!empty($caps)) {
    $score = min($score, min($caps));
    if ($score < $pass_score) $decision = "NEEDS_FIX";
  }

  // ✅ خلي hard gates تبعونك زي ما هم (من ملفك)
  // ... (هنا الصق نفس hard gates الموجودة عندك بدون تغيير)

  if (!isset($feedback) || $feedback === "") {
    $feedback = (string)($grade["feedback"] ?? "");
  }
  if (!isset($fixes) || !is_array($fixes) || count($fixes) === 0) {
    $fixes = $grade["fixes"] ?? [];
  }
  if (!is_array($fixes)) $fixes = [];

  // ✅ لا تكوينز هون
  $coins_total = 0;
  $prev_best_total = 0;
  $new_best_total = 0;
  $coins_awarded = 0;
}



// -----------------------
// 4) خزّن submission بالـ DB
// -----------------------
$checks_json = json_encode($checks, JSON_UNESCAPED_UNICODE);
$fixes_json  = json_encode($fixes, JSON_UNESCAPED_UNICODE);

// paths نسبية لطيفة
$zip_rel = $artifact_zip_path ? str_replace(realpath(__DIR__ . "/.."), "", realpath($artifact_zip_path)) : "";
$dir_rel = $artifact_dir_path ? str_replace(realpath(__DIR__ . "/.."), "", realpath($artifact_dir_path)) : "";

$submission_type = "project";
$status = "awaiting_company";
$artifact_path = "";
$manifest_json = json_encode($manifest, JSON_UNESCAPED_UNICODE);
$answers_json = "{}";


$st = $conn->prepare("
  INSERT INTO phase2_submissions
    (user_id, project_id, submission_type, repo_url, notes,
     artifact_zip, artifact_dir, artifact_path,
     checks_json, manifest_json, answers_json, status,
     review_mode,
     score, decision, coins_awarded, coins_total, feedback, fixes_json, ai_model, evidence_json)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");
if(!$st) json_out(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","sql_error"=>$conn->error], 500);

$types = "iisssssssssssisiissss";

$st->bind_param(
  $types,
  $user_id, $project_id, $submission_type,
  $repo_url, $notes,
  $zip_rel, $dir_rel, $artifact_path,
  $checks_json, $manifest_json, $answers_json, $status,
  $review_mode,
  $score, $decision,
  $coins_awarded, $coins_total,
  $feedback, $fixes_json, $ai_model, $evidence_json
);

$st->execute();
$submission_id = (int)$st->insert_id;
$st->close();
// -----------------------
// 5) (اختياري) تحديث coins للطالب إذا عندك عمود coins

// response للواجهة
json_out([
  "ok"=>true,
  "submission_id"=>$submission_id,
  "project_id"=>$project_id,
  "status"=>$status, // awaiting_company
  "ai_score"=>$score,
  "ai_decision"=>$decision,
  "ai_feedback"=>$feedback,
  "ai_fixes"=>$fixes,
  "ai_model"=>$ai_model,
  "checks"=>$checks,
  "evidence"=>$evidence
]);