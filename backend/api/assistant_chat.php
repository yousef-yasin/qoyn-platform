<?php
// utbn-backend/api/assistant_chat.php
// الهدف: مساعد الموقع + ربط Gemini (مع اختيار موديل تلقائي عبر ListModels)

// ✅ مهم: امنع HTML errors وخلي أي خطأ يرجع JSON
ini_set("display_errors", "0");
ini_set("html_errors", "0");
error_reporting(E_ALL);

// أي warning/notice → JSON
set_error_handler(function($severity, $message, $file, $line) {
  if (!(error_reporting() & $severity)) return false;
  http_response_code(500);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode([
    "ok" => false,
    "error" => "PHP_WARNING",
    "message" => $message,
    "file" => basename($file),
    "line" => $line
  ], JSON_UNESCAPED_UNICODE);
  exit;
});

// أي Exception / Fatal غير ملحوظ → JSON
set_exception_handler(function($e) {
  http_response_code(500);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode([
    "ok" => false,
    "error" => "PHP_EXCEPTION",
    "message" => $e->getMessage(),
    "file" => basename($e->getFile()),
    "line" => $e->getLine()
  ], JSON_UNESCAPED_UNICODE);
  exit;
});

require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

// ---------- helpers ----------
function read_input_message(): string {
  // JSON body
  $raw = file_get_contents("php://input");
  $in = [];
  if ($raw) {
    $tmp = json_decode($raw, true);
    if (is_array($tmp)) $in = $tmp;
  }

  $msg =
    trim((string)($in["message"] ?? "")) ?:
    trim((string)($in["question"] ?? "")) ?:
    trim((string)($in["q"] ?? ""));

  // fallback: POST/GET (لو صار الطلب form أو querystring)
  if ($msg === "") {
    $msg =
      trim((string)($_POST["message"] ?? "")) ?:
      trim((string)($_POST["question"] ?? "")) ?:
      trim((string)($_POST["q"] ?? "")) ?:
      trim((string)($_GET["message"] ?? "")) ?:
      trim((string)($_GET["question"] ?? "")) ?:
      trim((string)($_GET["q"] ?? ""));
  }

  return $msg;
}

function http_get_json(string $url): array {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
  ]);
  $res = curl_exec($ch);
  $err = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($err) return ["__ok" => false, "__err" => $err, "__code" => $code, "__raw" => $res];
  $json = json_decode($res ?: "{}", true);
  if (!is_array($json)) $json = ["raw" => $res];
  $json["__ok"] = ($code >= 200 && $code < 300);
  $json["__code"] = $code;
  return $json;
}

function http_post_json(string $url, array $payload): array {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  ]);
  $res = curl_exec($ch);
  $err = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($err) return ["__ok" => false, "__err" => $err, "__code" => $code, "__raw" => $res];
  $json = json_decode($res ?: "{}", true);
  if (!is_array($json)) $json = ["raw" => $res];
  $json["__ok"] = ($code >= 200 && $code < 300);
  $json["__code"] = $code;
  return $json;
}

function pick_best_model_from_list(array $models): ?string {
  // كل عنصر عادة: ["name"=>"models/....", "supportedGenerationMethods"=>[...]]
  $candidates = [];
  foreach ($models as $m) {
    $name = (string)($m["name"] ?? "");
    $methods = $m["supportedGenerationMethods"] ?? [];
    if (!$name) continue;
    if (!is_array($methods)) $methods = [];
    if (!in_array("generateContent", $methods, true)) continue;
    // نفضّل Gemini text models
    if (strpos($name, "models/") !== 0) continue;
    $candidates[] = $name;
  }
  if (!$candidates) return null;

  // ترتيب تفضيل (لو متوفر بحسابك)
  $prefer = [
    "gemini-3", "gemini-2.5", "gemini-2", "gemini-1.5", "gemini-1",
    "flash", "pro"
  ];

  usort($candidates, function($a,$b) use ($prefer){
    $sa = 0; $sb = 0;
    foreach ($prefer as $i => $p) {
      if (stripos($a, $p) !== false) $sa += (100 - $i);
      if (stripos($b, $p) !== false) $sb += (100 - $i);
    }
    // أعلى نقاط أولاً
    if ($sa === $sb) return strcmp($a,$b);
    return ($sa > $sb) ? -1 : 1;
  });

  return $candidates[0];
}

function get_cached_model_path(): string {
  $dir = __DIR__ . "/../cache";
  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  return $dir . "/gemini_model.json";
}

function load_cached_model(): ?array {
  $p = get_cached_model_path();
  if (!file_exists($p)) return null;
  $data = json_decode((string)file_get_contents($p), true);
  if (!is_array($data)) return null;
  // صلاحية ساعة
  $ts = (int)($data["ts"] ?? 0);
  if ($ts && (time() - $ts) > 3600) return null;
  if (empty($data["model"])) return null;
  return $data;
}

function save_cached_model(string $apiVersion, string $model): void {
  $p = get_cached_model_path();
  file_put_contents($p, json_encode([
    "ts" => time(),
    "apiVersion" => $apiVersion,
    "model" => $model
  ], JSON_UNESCAPED_UNICODE));
}

function clear_cached_model(): void {
  $p = get_cached_model_path();
  if (file_exists($p)) @unlink($p);
}

function list_models(string $apiKey, string $apiVersion): array {
  $base = "https://generativelanguage.googleapis.com/{$apiVersion}";
  $url = $base . "/models?key=" . urlencode($apiKey);
  return http_get_json($url);
}

function resolve_model(string $apiKey, ?string $forcedModel = null): array {
  // إذا المستخدم محدد موديل بالـ env، نحاول فيه أولاً
  $forced = trim((string)$forcedModel);
  if ($forced !== "") {
    // اسم الموديل لازم يكون "models/xxx" أو "xxx"
    $name = (strpos($forced, "models/") === 0) ? $forced : ("models/" . $forced);
    return ["apiVersion" => "v1", "model" => $name, "forced" => true];
  }

  // cache
  $cached = load_cached_model();
  if ($cached) {
    return ["apiVersion" => (string)$cached["apiVersion"], "model" => (string)$cached["model"], "forced" => false];
  }

  // نجرب v1 أولاً
  foreach (["v1","v1beta"] as $ver) {
    $resp = list_models($apiKey, $ver);
    if (!($resp["__ok"] ?? false)) continue;
    $models = $resp["models"] ?? [];
    if (!is_array($models)) $models = [];
    $best = pick_best_model_from_list($models);
    if ($best) {
      save_cached_model($ver, $best);
      return ["apiVersion" => $ver, "model" => $best, "forced" => false];
    }
  }

  return ["apiVersion" => "v1", "model" => "models/gemini-1.5-flash", "forced" => false]; // fallback أخير
}

function gemini_generate(string $apiKey, string $apiVersion, string $modelName, string $prompt): array {
  $base = "https://generativelanguage.googleapis.com/{$apiVersion}";
  $url  = $base . "/" . $modelName . ":generateContent?key=" . urlencode($apiKey);

  $payload = [
    "contents" => [[
      "parts" => [[ "text" => $prompt ]]
    ]]
  ];

  return http_post_json($url, $payload);
}

function extract_text_from_gemini(array $data): ?string {
  return $data["candidates"][0]["content"]["parts"][0]["text"] ?? null;
}

// ---------- start ----------
$msg = read_input_message();
if ($msg === "") {
  echo json_encode(["ok" => false, "error" => "EMPTY_MESSAGE"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ---------- local pages knowledge (اختياري) ----------
$pagesCandidates = [
  // لو أضفت ملف وصف للصفحات بأي من هالمسارات، رح يقرأه
  __DIR__ . "/../data/pages.json",
  __DIR__ . "/../../utbn-web/assets/data/pages.json",
  __DIR__ . "/../../utbn-web/assets/pages.json",
];

$pages = null;
foreach ($pagesCandidates as $pf) {
  if (file_exists($pf)) {
    $tmp = json_decode((string)file_get_contents($pf), true);
    if (is_array($tmp)) { $pages = $tmp; break; }
  }
}

if (is_array($pages)) {
  foreach ($pages as $p) {
    $file = (string)($p["file"] ?? "");
    $desc = (string)($p["description"] ?? "");
    if ($file && $desc && stripos($msg, $file) !== false) {
      echo json_encode(["ok" => true, "answer" => $desc, "source" => "pages.json"], JSON_UNESCAPED_UNICODE);
      exit;
    }
  }
}

// ---------- Gemini ----------
$API_KEY = getenv("GEMINI_API_KEY");
if (!$API_KEY) {
  echo json_encode(["ok" => false, "error" => "GEMINI_API_KEY_MISSING"], JSON_UNESCAPED_UNICODE);
  exit;
}

$envModel = getenv("GEMINI_MODEL") ?: "";
$resolved = resolve_model($API_KEY, $envModel);
$apiVer   = $resolved["apiVersion"];
$model    = $resolved["model"];

// call 1
$out = gemini_generate($API_KEY, $apiVer, $model, $msg);

// لو NOT_FOUND أو موديل مش مدعوم، نمسح الكاش ونجرب listModels من جديد
$errStatus = $out["error"]["status"] ?? ($out["details"]["error"]["status"] ?? null);
$errMsg    = $out["error"]["message"] ?? ($out["details"]["error"]["message"] ?? null);
$httpCode  = (int)($out["__code"] ?? 0);

$needRetry = false;
if (!($out["__ok"] ?? false)) {
  if ($httpCode === 404 || (is_string($errStatus) && $errStatus === "NOT_FOUND")) {
    $needRetry = true;
  }
  if (is_string($errMsg) && stripos($errMsg, "not supported") !== false) {
    $needRetry = true;
  }
}

if ($needRetry) {
  clear_cached_model();
  // نعيد اختيار الموديل بدون forced
  $resolved = resolve_model($API_KEY, "");
  $apiVer   = $resolved["apiVersion"];
  $model    = $resolved["model"];
  $out = gemini_generate($API_KEY, $apiVer, $model, $msg);
}

if (!($out["__ok"] ?? false)) {
  echo json_encode(["ok" => false, "error" => "GEMINI_HTTP", "details" => $out], JSON_UNESCAPED_UNICODE);
  exit;
}

$txt = extract_text_from_gemini($out);
if (!$txt) {
  echo json_encode(["ok" => false, "error" => "GEMINI_EMPTY", "details" => $out], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  "ok" => true,
  "answer" => $txt,
  "model" => $model,
  "apiVersion" => $apiVer
], JSON_UNESCAPED_UNICODE);
