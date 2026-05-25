<?php
require_once __DIR__ . "/../db.php";

/**
 * AI Plan Analyzer (Gemini) - IMAGES ONLY (FAST)
 * Supports:
 *  - GET ?hours=&images=
 *  - POST JSON {hours, images}
 */

require_login();
@set_time_limit(300);
@ini_set('max_execution_time', '300');
@ini_set('memory_limit', '512M');

header("Content-Type: application/json; charset=utf-8");

// PHP7 compatibility
if (!function_exists("str_contains")) {
  function str_contains($haystack, $needle) { return $needle === "" || strpos($haystack, $needle) !== false; }
}
if (!function_exists("str_starts_with")) {
  function str_starts_with($haystack, $needle) { return $needle === "" || substr($haystack, 0, strlen($needle)) === $needle; }
}

function ensure_ai_tables(mysqli $conn) {
  $conn->query("CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT UNSIGNED PRIMARY KEY,
    term_credits INT NOT NULL DEFAULT 15,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $conn->query("CREATE TABLE IF NOT EXISTS plan_analysis (
    user_id INT UNSIGNED PRIMARY KEY,
    source_attachment_id INT UNSIGNED NULL,
    analysis_json MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $conn->query("CREATE TABLE IF NOT EXISTS user_plan_profile (
    user_id INT UNSIGNED PRIMARY KEY,
    major_name VARCHAR(200) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_upp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $conn->query("CREATE TABLE IF NOT EXISTS user_plan_courses (
    user_id INT UNSIGNED NOT NULL,
    course_code VARCHAR(40) NOT NULL,
    course_name VARCHAR(220) NOT NULL,
    group_title VARCHAR(220) NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    credits INT NOT NULL DEFAULT 0,
    PRIMARY KEY (user_id, course_code),
    KEY idx_upc_required (user_id, is_required),
    CONSTRAINT fk_upc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

function is_required_group(string $title, string $note, string $rawRow): bool {
  $s = mb_strtolower(trim($title." ".$note." ".$rawRow), 'UTF-8');
  if ($s === "") return false;
  $keys = ["اجباري","إجباري","اجبارية","إجبارية","متطلبات التخصص","متطلبات القسم","متطلبات كلية","متطلبات الجامعة","required","mandatory"];
  foreach ($keys as $k) {
    if (mb_strpos($s, mb_strtolower($k, 'UTF-8'), 0, 'UTF-8') !== false) return true;
  }
  return false;
}

function clean_major_name($v): string {
  $v = trim((string)$v);
  $v = preg_replace('/\s+/u', ' ', $v);
  return $v;
}

function load_env_from_dotenv_if_needed() {
  $apiKey = getenv("GEMINI_API_KEY");
  if ($apiKey) return $apiKey;

  $envPath = realpath(__DIR__ . "/../../.env");
  if ($envPath && file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === "" || strpos($line, "#") === 0) continue;
      if (!str_contains($line, "=")) continue;

      [$k,$v] = explode("=", $line, 2);
      $k = trim($k);
      $v = trim($v);
      $v = trim($v, "\"'");

      if ($k === "GEMINI_API_KEY" && $v) $apiKey = $v;
      if ($k === "GEMINI_MODEL" && $v && !getenv("GEMINI_MODEL")) putenv("GEMINI_MODEL=".$v);
    }
  }
  return $apiKey ?: null;
}

function is_completed_status(string $st): bool {
  return (strpos($st, "ناجح") !== false) || (strpos($st, "معفاة") !== false) || (strpos($st, "معفى") !== false);
}
function is_registered_status(string $st): bool {
  return (strpos($st, "مسجلة") !== false) || (strpos($st, "مسجل") !== false);
}
function extract_prereq_code(string $pr): string {
  $pr = trim($pr);
  if ($pr === "") return "";
  if (preg_match("/\(([0-9]+)\)/", $pr, $m)) return $m[1];
  $digits = preg_replace("/[^0-9]/", "", $pr);
  return $digits ?: "";
}

function parse_term(string $term): array {
  $term = trim($term);
  if (!preg_match("/^\d{5}$/", $term)) return ["ok"=>false, "term_code"=>$term, "year"=>null, "sem"=>null];
  $year = (int)substr($term, 0, 4);
  $sem  = (int)substr($term, 4, 1);
  if ($sem < 1 || $sem > 3) return ["ok"=>false, "term_code"=>$term, "year"=>$year, "sem"=>$sem];
  return ["ok"=>true, "term_code"=>$term, "year"=>$year, "sem"=>$sem];
}
function term_label_ar(string $term): string {
  $p = parse_term($term);
  if (!$p["ok"]) return ($term !== "" ? $term : "غير معروف");
  $year = $p["year"];
  $sem = $p["sem"];
  if ($sem === 1) return $year . " / الفصل الأول";
  if ($sem === 2) return $year . " / الفصل الثاني";
  return $year . " / الفصل الصيفي";
}
function term_sort_key(string $term): int {
  $p = parse_term($term);
  if (!$p["ok"]) return 999999999;
  return ((int)$p["year"] * 10) + (int)$p["sem"];
}

function flatten_extracted_rows(array $extracted): array {
  $all = [];
  $tables = $extracted["tables"] ?? [];
  if (!is_array($tables)) return $all;

  foreach ($tables as $t) {
    if (!is_array($t)) continue;
    $title = trim((string)($t["table_title"] ?? ""));
    $note  = trim((string)($t["table_note"] ?? ""));
    $rows  = $t["rows"] ?? [];
    if (!is_array($rows)) continue;

    foreach ($rows as $r) {
      if (!is_array($r)) continue;

      $code = trim((string)($r["code"] ?? ""));
      $name = trim((string)($r["name"] ?? ""));
      $term = trim((string)($r["term"] ?? ""));
      $status = trim((string)($r["status"] ?? ""));
      $grade  = trim((string)($r["grade"] ?? ""));
      $pr     = trim((string)($r["prerequisite"] ?? ""));
      $raw    = trim((string)($r["raw_row"] ?? ""));

      $credits = $r["credits"] ?? 0;
      $credits = is_numeric($credits) ? (int)$credits : 0;

      if ($code === "" && $name === "" && $raw === "") continue;

      $all[] = [
        "table_title" => $title,
        "table_note"  => $note,
        "row_index"   => (int)($r["row_index"] ?? 0),
        "term"        => $term,
        "status"      => $status,
        "grade"       => $grade,
        "credits"     => $credits,
        "code"        => $code,
        "name"        => $name,
        "prerequisite"=> $pr,
        "raw_row"     => $raw
      ];
    }
  }
  return $all;
}

/**
 * ضغط الصورة قبل الإرسال للذكاء (للسرعة)
 */
/**
 * تجهيز الصورة للإرسال للذكاء الاصطناعي (بدون GD)
 * أسرع + بدون تحويل
 */
function prepare_image_for_ai(string $path): array {
  if (!file_exists($path)) json_out(["error" => "IMAGE_NOT_FOUND"], 404);

  $mime = @mime_content_type($path);
  if (!$mime || strpos($mime, "image/") !== 0) {
    json_out(["error" => "NOT_AN_IMAGE", "mime" => $mime], 400);
  }

  $bytes = @file_get_contents($path);
  if (!$bytes) json_out(["error" => "READ_IMAGE_FAILED"], 500);

  // ✅ لو GD موجودة: ضغط/تصغير لتفادي BAD_STATUS بسبب الحجم
  if (function_exists("imagecreatefromstring")) {
    $im = @imagecreatefromstring($bytes);
    if ($im) {
      $w = imagesx($im);
      $h = imagesy($im);

      $maxW = 1280; // ممتاز لـ OCR/جداول
      if ($w > $maxW) {
        $newW = $maxW;
        $newH = (int)round($h * ($newW / $w));
        $dst = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($dst, $im, 0,0,0,0, $newW,$newH, $w,$h);
        imagedestroy($im);
        $im = $dst;
      }

      ob_start();
      imagejpeg($im, null, 70); // جودة 70 ممتازة
      $jpg = ob_get_clean();
      imagedestroy($im);

      if ($jpg && strlen($jpg) > 0) {
        return [
          "inline_data" => [
            "mime_type" => "image/jpeg",
            "data" => base64_encode($jpg)
          ]
        ];
      }
    }
  }

  // fallback: ابعت الأصل
  return [
    "inline_data" => [
      "mime_type" => $mime,
      "data" => base64_encode($bytes)
    ]
  ];
}



ensure_ai_tables($conn);

$apiKey = load_env_from_dotenv_if_needed();
if (!$apiKey) json_out(["error"=>"GEMINI_API_KEY_MISSING","msg"=>"ضع GEMINI_API_KEY في Environment أو في utbn-backend/.env"], 400);

$model = "gemini-2.5-flash";
$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) json_out(["error"=>"NO_SESSION"], 401);

/** اقرأ input */
$input = [];
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
  $raw = file_get_contents("php://input");
  $j = json_decode($raw ?: "{}", true);
  if (is_array($j)) $input = $j;
}

$term_credits = 15;
$qs = $conn->prepare("SELECT term_credits FROM user_settings WHERE user_id=? LIMIT 1");
$qs->bind_param("i", $user_id);
$qs->execute();
$rs = $qs->get_result();
if ($row = $rs->fetch_assoc()) $term_credits = (int)$row["term_credits"];
$qs->close();

$hours_in = null;
if (isset($input["hours"])) $hours_in = (int)$input["hours"];
if ($hours_in === null && isset($_GET["hours"])) $hours_in = (int)$_GET["hours"];
if ($hours_in !== null && $hours_in > 0 && $hours_in <= 24) $term_credits = $hours_in;

/** ✅ عدد الصور اللي بدنا نحللها */
$images = 3;
$images_in = null;
if (isset($input["images"])) $images_in = (int)$input["images"];
if ($images_in === null && isset($_GET["images"])) $images_in = (int)$_GET["images"];
if ($images_in !== null && $images_in >= 1 && $images_in <= 3) $images = $images_in;

/**
 * ✅ اجلب آخر N صور type=plan
 * مهم: order by created_at desc
 */
$q = $conn->prepare("SELECT id, file_path, original_name, mime_type, created_at
  FROM student_attachments
  WHERE user_id=? AND type='plan'
  ORDER BY created_at DESC
  LIMIT ?");
$q->bind_param("ii", $user_id, $images);
$q->execute();
$r = $q->get_result();

$atts = [];
while ($row = $r->fetch_assoc()) $atts[] = $row;
$q->close();

if (!$atts || count($atts) === 0) {
  json_out(["error"=>"NO_PLAN_UPLOADED","msg"=>"ارفع صور الخطة أولاً (حتى 3 صور)"], 404);
}

/** تأكد كلها صور */
foreach ($atts as $a) {
  $mt = (string)($a["mime_type"] ?? "");
  if (!$mt || !str_starts_with($mt, "image/")) {
    json_out([
      "error"=>"IMAGES_ONLY",
      "msg"=>"حالياً التحليل السريع يدعم الصور فقط. ارفع الخطة كـ 3 صور."
    ], 400);
  }
}
// ===============================
// ✅ CACHE: إذا نفس آخر صور تم تحليلها قريباً، رجّع النتيجة مباشرة
// لتجنب 429 (quota) وطلبات متكررة
// يمكن تجاوز الكاش بإضافة ?force=1
// ===============================
$force = (int)($_GET["force"] ?? 0);
if ($force !== 1) {
  $pc = $conn->prepare("SELECT analysis_json, source_attachment_id, updated_at
    FROM plan_analysis
    WHERE user_id=? LIMIT 1");
  $pc->bind_param("i", $user_id);
  $pc->execute();
  $prs = $pc->get_result();
  if ($prow = $prs->fetch_assoc()) {
    $cachedAttachment = (int)($prow["source_attachment_id"] ?? 0);
    $updatedAt = (string)($prow["updated_at"] ?? "");
    $updatedTs = $updatedAt ? strtotime($updatedAt) : 0;

    // إذا نفس آخر صورة (الأحدث) + آخر تحليل خلال 10 دقائق
    if ($cachedAttachment === (int)$atts[0]["id"] && $updatedTs > 0 && (time() - $updatedTs) < 600) {
      $cached = json_decode((string)$prow["analysis_json"], true);
      if (is_array($cached)) {
        json_out(["ok"=>true, "result"=>$cached, "cached"=>true]);
      }
    }
  }
  $pc->close();
}

// Prompt
$prompt =
"أنت محلل جداول عربية من كشف/خطة دراسية. أرجع JSON فقط بدون شرح.\n\n".
"المطلوب: استخراج كل ما يظهر في الصور كاملة، وليس صفوف محددة.\n\n".
"أرجع JSON بالشكل التالي:\n".
"{\n".
"  \"meta\": {\n".
"    \"major_name\": \"\",\n".
"    \"student_name\": \"\",\n".
"    \"student_id\": \"\",\n".
"    \"gpa\": \"\",\n".
"    \"total_hours\": \"\",\n".
"    \"remaining_hours\": \"\",\n".
"    \"plan_hours\": \"\"\n".
"  },\n".
"  \"tables\": [\n".
"    {\n".
"      \"table_title\": \"...\",\n".
"      \"table_note\": \"...\",\n".
"      \"rows\": [\n".
"        {\n".
"          \"row_index\": 1,\n".
"          \"term\": \"20231 أو فارغ\",\n".
"          \"status\": \"ناجح/مسجلة/معفاة/راسب/...\",\n".
"          \"grade\": \"أ/ب+/...\",\n".
"          \"credits\": 3,\n".
"          \"code\": \"50521204\",\n".
"          \"name\": \"حقوق الانسان\",\n".
"          \"prerequisite\": \"(40321101) أو فارغ\",\n".
"          \"raw_row\": \"...\"\n".
"        }\n".
"      ]\n".
"    }\n".
"  ]\n".
"}\n\n".
"قواعد مهمة جداً:\n".
"- لا تحذف أي صف حتى لو مكرر.\n".
"- لا تدمج صفوف لنفس المقرر.\n".
"- إذا في أعمدة فاضية اتركها فاضية.\n".
"- استخرج كل الصفوف الظاهرة في الصور حتى لو من جداول متعددة.\n".
"- أرجع JSON صالح فقط.\n";

$parts = [
  ["text" => $prompt]
];

/** ✅ أضف الصور (الأحدث أولاً) */
$sent = 0;
$first_attachment_id = (int)$atts[0]["id"];

foreach ($atts as $a) {
  $relative_path = (string)($a["file_path"] ?? "");
  $fullPath = realpath(__DIR__ . "/../../" . $relative_path);
  if (!$fullPath || !file_exists($fullPath)) continue;
$parts[] = prepare_image_for_ai($fullPath);
$sent++;
}

if ($sent === 0) {
  json_out(["error"=>"READ_IMAGE_FAILED","msg"=>"لم أستطع قراءة صور الخطة من السيرفر."], 500);
}

// Call Gemini API
$payload = [
  "contents" => [
    [
      "role" => "user",
      "parts" => $parts
    ]
  ],
  "generationConfig" => [
    "temperature" => 0.0,
    "responseMimeType" => "application/json"
  ]
];

$url = "https://generativelanguage.googleapis.com/v1beta/models/".$model.":generateContent?key=".urlencode($apiKey);

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 300
]);

$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);

// ===============================
// ✅ Retry تلقائي إذا طلع 429
// ===============================
if (!$err && $http == 429) {
  $retryAfter = 15; // افتراضي
  $msg = $resp ?: "";

  $j = json_decode($resp, true);
  if (is_array($j)) {
    $msg = $j["error"]["message"] ?? $msg;
    // مثال: "Please retry in 13.3s"
    if (preg_match('/retry in\s+([0-9.]+)s/i', $msg, $m)) {
      $retryAfter = (int)ceil((float)$m[1]);
      if ($retryAfter < 1) $retryAfter = 1;
      if ($retryAfter > 60) $retryAfter = 60;
    }
  }

  json_out([
    "ok" => false,
    "error" => "GEMINI_RATE_LIMIT",
    "status" => 429,
    "retry_after" => $retryAfter,
    "message" => $msg
  ], 200);
}


curl_close($ch);

if ($err) json_out(["error"=>"GEMINI_REQUEST_FAILED","msg"=>$err], 500);

if ($http < 200 || $http >= 300) {
  $msg = $resp;
  $jerr = json_decode($resp, true);
  if (is_array($jerr)) {
    $msg = $jerr["error"]["message"] ?? ($jerr["message"] ?? $resp);
  }

  // ✅ إذا 429 (quota/rate limit) رجّع retry_after بدل ما تعمل 500
  if ($http == 429) {
    $retryAfter = 60; // default
    if (preg_match('/retry in\s+([0-9.]+)s/i', $msg, $m)) {
      $retryAfter = (int)ceil((float)$m[1]);
      if ($retryAfter < 1) $retryAfter = 1;
      if ($retryAfter > 300) $retryAfter = 300;
    }

    json_out([
      "ok" => false,
      "error" => "GEMINI_RATE_LIMIT",
      "status" => 429,
      "retry_after" => $retryAfter,
      "message" => $msg
    ], 200);
  }

  json_out([
    "error"  => "GEMINI_BAD_STATUS",
    "status" => $http,
    "message"=> $msg
  ], 500);
}



$data = json_decode($resp, true);
$outText = $data["candidates"][0]["content"]["parts"][0]["text"] ?? null;
if (!$outText) json_out(["error"=>"GEMINI_EMPTY_OUTPUT","raw"=>$resp], 500);

// Strip code fences if any
$outText = trim($outText);
$outText = preg_replace("/^```(json)?/i", "", $outText);
$outText = preg_replace("/```$/", "", $outText);
$outText = trim($outText);

$extracted = json_decode($outText, true);
if (!$extracted || !is_array($extracted)) json_out(["error"=>"BAD_JSON_FROM_MODEL","model_output"=>$outText], 500);

// flatten rows
$rows = flatten_extracted_rows($extracted);
if (!$rows) json_out(["error"=>"NO_ROWS_EXTRACTED","msg"=>"النموذج رجّع JSON لكن بدون صفوف داخل tables."], 500);

// sets
$completedSet = [];
$registeredSet = [];

foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if ($code === "") continue;
  if (is_completed_status($rrow["status"])) $completedSet[$code] = true;
  if (is_registered_status($rrow["status"])) $registeredSet[$code] = true;
}

// major + plan courses
$major_name = clean_major_name(($extracted["meta"]["major_name"] ?? ""));

// planCourses
$planCourses = [];
foreach ($rows as $rrow) {
  $code = trim((string)$rrow["code"]);
  if ($code === "") continue;

  $name = trim((string)$rrow["name"]);
  $credits = (int)($rrow["credits"] ?? 0);
  $group_title = trim((string)($rrow["table_title"] ?? ""));
  $group_note  = trim((string)($rrow["table_note"] ?? ""));
  $raw_row     = trim((string)($rrow["raw_row"] ?? ""));

  $required = is_required_group($group_title, $group_note, $raw_row) ? 1 : 0;

  if (!isset($planCourses[$code])) {
    $planCourses[$code] = [
      "code" => $code,
      "name" => $name,
      "credits" => $credits,
      "group_title" => $group_title,
      "is_required" => $required
    ];
  } else {
    if ($required && !$planCourses[$code]["is_required"]) $planCourses[$code]["is_required"] = 1;
    if (($planCourses[$code]["credits"] ?? 0) <= 0 && $credits > 0) $planCourses[$code]["credits"] = $credits;
    if (($planCourses[$code]["name"] ?? "") === "" && $name !== "") $planCourses[$code]["name"] = $name;
  }
}

// group by term
$completed_by_term = [];
$registered_by_term = [];

foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if ($code === "") continue;

  $term_code = $rrow["term"] !== "" ? $rrow["term"] : "unknown";
  $term_label = term_label_ar($term_code);

  if (is_completed_status($rrow["status"])) {
    if (!isset($completed_by_term[$term_code])) {
      $completed_by_term[$term_code] = ["term_code"=>$term_code, "term_label"=>$term_label, "hours"=>0, "courses"=>[]];
    }
    $completed_by_term[$term_code]["courses"][] = [
      "code"=>$code, "name"=>$rrow["name"], "credits"=>(int)$rrow["credits"], "grade"=>$rrow["grade"], "status"=>$rrow["status"], "table"=>$rrow["table_title"]
    ];
    $completed_by_term[$term_code]["hours"] += (int)$rrow["credits"];
  }

  if (is_registered_status($rrow["status"])) {
    if (!isset($registered_by_term[$term_code])) {
      $registered_by_term[$term_code] = ["term_code"=>$term_code, "term_label"=>$term_label, "hours"=>0, "courses"=>[]];
    }
    $registered_by_term[$term_code]["courses"][] = [
      "code"=>$code, "name"=>$rrow["name"], "credits"=>(int)$rrow["credits"], "status"=>$rrow["status"], "table"=>$rrow["table_title"]
    ];
    $registered_by_term[$term_code]["hours"] += (int)$rrow["credits"];
  }
}

uksort($completed_by_term, fn($a,$b) => term_sort_key((string)$a) <=> term_sort_key((string)$b));
uksort($registered_by_term, fn($a,$b) => term_sort_key((string)$a) <=> term_sort_key((string)$b));

// remaining
$remaining = [];
foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if ($code === "") continue;
  if (isset($completedSet[$code]) || isset($registeredSet[$code])) continue;

  $prCode = extract_prereq_code((string)($rrow["prerequisite"] ?? ""));
  $ok = true;
  if ($prCode !== "") $ok = isset($completedSet[$prCode]);

  if ($ok) {
    $cr = (int)$rrow["credits"];
    if ($cr <= 0) continue;
    $remaining[] = [
      "code"=>$code, "name"=>$rrow["name"], "credits"=>$cr, "prerequisite"=>$prCode, "table"=>$rrow["table_title"]
    ];
  }
}

// next term
$next_term = ["hours"=>0, "courses"=>[]];
$sum = 0;
foreach ($remaining as $c) {
  $cr = (int)$c["credits"];
  if ($cr <= 0) continue;
  if ($sum + $cr > $term_credits && $sum > 0) break;
  $next_term["courses"][] = $c;
  $sum += $cr;
}
$next_term["hours"] = $sum;

// required lists
$required_courses_all = [];
$required_courses_remaining = [];
foreach ($planCourses as $c) {
  if (!(int)$c["is_required"]) continue;
  $item = ["code"=>$c["code"], "name"=>$c["name"], "credits"=>(int)$c["credits"], "group_title"=>$c["group_title"]];
  $required_courses_all[] = $item;
  if (!isset($completedSet[$c["code"]]) && !isset($registeredSet[$c["code"]])) $required_courses_remaining[] = $item;
}
if (count($required_courses_all) === 0) {
  foreach ($remaining as $c) {
    $required_courses_remaining[] = [
      "code"=>$c["code"], "name"=>$c["name"], "credits"=>(int)$c["credits"], "group_title"=>(string)($c["table"] ?? "")
    ];
  }
}

$result = [
  "meta" => $extracted["meta"] ?? new stdClass(),
  "major_name" => $major_name,
  "term_credits" => $term_credits,
  "completed_by_term" => array_values($completed_by_term),
  "registered_by_term" => array_values($registered_by_term),
  "next_term_suggestion" => $next_term,
  "required_courses_all" => $required_courses_all,
  "required_courses_remaining" => $required_courses_remaining,
  "generated_at" => date("c")
];

// save analysis (نربطه بأحدث صورة)
$analysisJson = json_encode($result, JSON_UNESCAPED_UNICODE);
$up = $conn->prepare("INSERT INTO plan_analysis (user_id, source_attachment_id, analysis_json)
  VALUES (?,?,?) ON DUPLICATE KEY UPDATE source_attachment_id=VALUES(source_attachment_id), analysis_json=VALUES(analysis_json)");
$up->bind_param("iis", $user_id, $first_attachment_id, $analysisJson);
$up->execute();
$up->close();

// save major + courses
$conn->begin_transaction();
try {
  $p = $conn->prepare("INSERT INTO user_plan_profile (user_id, major_name) VALUES (?,?)
    ON DUPLICATE KEY UPDATE major_name=VALUES(major_name)");
  $p->bind_param("is", $user_id, $major_name);
  $p->execute();
  $p->close();

  $d = $conn->prepare("DELETE FROM user_plan_courses WHERE user_id=?");
  $d->bind_param("i", $user_id);
  $d->execute();
  $d->close();

  $ins = $conn->prepare("INSERT INTO user_plan_courses (user_id, course_code, course_name, group_title, is_required, credits)
    VALUES (?,?,?,?,?,?)");

  foreach ($planCourses as $c) {
    $code = (string)$c["code"];
    $name = (string)$c["name"];
    if ($code === "" || $name === "") continue;
    $group = (string)($c["group_title"] ?? "");
    $req = (int)($c["is_required"] ?? 0);
    $cr  = (int)($c["credits"] ?? 0);
    $ins->bind_param("isssii", $user_id, $code, $name, $group, $req, $cr);
    $ins->execute();
  }
  $ins->close();

  $conn->commit();
} catch (Throwable $e) {
  $conn->rollback();
}

json_out(["ok"=>true, "result"=>$result]);
