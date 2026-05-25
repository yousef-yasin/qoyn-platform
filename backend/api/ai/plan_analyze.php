<?php
require_once __DIR__ . "/../db.php";

/**
 * AI Plan Analyzer
 * - Uses latest uploaded "plan" attachment from student_attachments
 * - Calls OpenAI Responses API (Vision) to extract rows from PDF/image as JSON
 * - Saves extraction + computed recommendations to plan_analysis table (per user)
 *
 * ENV:
 *  - OPENAI_API_KEY (required)
 *  - OPENAI_MODEL (optional, default: gpt-4o-mini)
 */

require_login();

// PHP7 compatibility (for XAMPP/PHP 7.x)
if (!function_exists("str_contains")) {
  function str_contains($haystack, $needle) { return $needle === "" || strpos($haystack, $needle) !== false; }
}
if (!function_exists("str_starts_with")) {
  function str_starts_with($haystack, $needle) { return $needle === "" || substr($haystack, 0, strlen($needle)) === $needle; }
}

function ensure_ai_tables(mysqli $conn) {
  // user_settings (term credits per user)
  $conn->query("CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT UNSIGNED PRIMARY KEY,
    term_credits INT NOT NULL DEFAULT 15,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  // plan_analysis (latest analysis per user)
  $conn->query("CREATE TABLE IF NOT EXISTS plan_analysis (
    user_id INT UNSIGNED PRIMARY KEY,
    source_attachment_id INT UNSIGNED NULL,
    analysis_json MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

ensure_ai_tables($conn);

$apiKey = getenv("OPENAI_API_KEY");
if (!$apiKey) {
  // Also support loading from utbn-backend/.env (simple KEY=VALUE)
  $envPath = realpath(__DIR__ . "/../../.env");
  if ($envPath && file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), "#") === 0) continue;
      if (!str_contains($line, "=")) continue;
      [$k,$v] = explode("=", $line, 2);
      $k = trim($k); $v = trim($v);
      $v = trim($v, "\"'");
      if ($k === "OPENAI_API_KEY" && $v) $apiKey = $v;
      if ($k === "OPENAI_MODEL" && !getenv("OPENAI_MODEL")) putenv("OPENAI_MODEL=".$v);
      if ($k === "PYTHON_BIN" && !getenv("PYTHON_BIN")) putenv("PYTHON_BIN=".$v);
    }
  }
}

if (!$apiKey) {
  json_out(["error"=>"OPENAI_API_KEY_MISSING","msg"=>"ضع OPENAI_API_KEY في Environment أو في utbn-backend/.env"], 400);
}

$model = getenv("OPENAI_MODEL") ?: "gpt-4o-mini";

$user_id = (int)$_SESSION["user_id"];

// term credits setting (default 15)
$term_credits = 15;
$qs = $conn->prepare("SELECT term_credits FROM user_settings WHERE user_id=? LIMIT 1");
$qs->bind_param("i", $user_id);
$qs->execute();
$rs = $qs->get_result();
if ($row = $rs->fetch_assoc()) $term_credits = (int)$row["term_credits"];
$qs->close();

// latest plan attachment
$q = $conn->prepare("SELECT id, file_path, original_name, mime_type FROM student_attachments
  WHERE user_id=? AND type='plan' ORDER BY created_at DESC LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
$att = $r->fetch_assoc();
$q->close();

if (!$att) {
  json_out(["error"=>"NO_PLAN_UPLOADED","msg"=>"ارفع الخطة (PDF/صورة) أولاً"], 404);
}

$attachment_id = (int)$att["id"];
$relative_path = $att["file_path"];
$original_name = $att["original_name"] ?? "plan.pdf";
$mime_type = $att["mime_type"] ?? "";

$fullPath = realpath(__DIR__ . "/../../" . $relative_path);
if (!$fullPath || !file_exists($fullPath)) {
  json_out(["error"=>"FILE_NOT_FOUND","msg"=>"الملف غير موجود على السيرفر: ".$relative_path], 404);
}

$bytes = file_get_contents($fullPath);
$b64 = base64_encode($bytes);

// build vision input for image/pdf
$inputContent = [
  ["type"=>"input_text","text"=>
"أنت محلل كشف/خطة دراسية (جدول عربي). استخرج البيانات كـ JSON فقط بدون أي شرح.

المطلوب:
1) rows: قائمة بكل صف/مقرر موجود في الجدول مهما كان نوعه، بالشكل:
{\"term\":\"20231\",\"code\":\"40321101\",\"name\":\"مقدمة في البرمجة\",\"credits\":3,\"status\":\"ناجح/مسجلة/معفاة/راسب/...\",\"grade\":\"أ/ب+/... أو فارغ\",\"prerequisite\":\"(40321101) أو فارغ\"}
2) meta: {\"student_name\":\"\",\"student_id\":\"\",\"gpa\":\"\",\"remaining_hours\":\"\",\"total_hours\":\"\"} إذا كانت موجودة.

قواعد:
- إذا المتطلب مكتوب بين أقواس مثل (40632201) خليه نفس النص.
- term هو رقم الفصل الموجود بالعمود (مثل 20231).
- credits رقم.
- إذا في تكرار صفوف لنفس المقرر، خذ الأحدث حسب term.
- أرجع JSON صالح فقط.
"]
];

// If it's an image -> send directly.
// If it's a PDF -> try to convert first 2 pages to images using Python (vision is much better with images on many scanned PDFs).
$sentAsImages = false;
if ($mime_type && str_starts_with($mime_type, "image/")) {
  $inputContent[] = ["type"=>"input_image","image_url"=>"data:".$mime_type.";base64,".$b64];
  $sentAsImages = true;
} else {
  // Try python conversion (optional)
  $python = getenv("PYTHON_BIN") ?: "python";
  $tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR."utbn_ai_".$user_id;
  if (!is_dir($tmpDir)) @mkdir($tmpDir, 0777, true);

  $script = realpath(__DIR__ . "/../../scripts/pdf_to_images.py");
  if ($script && file_exists($script)) {
    $cmd = escapeshellcmd($python)." ".escapeshellarg($script)." ".escapeshellarg($fullPath)." ".escapeshellarg($tmpDir)." 2";
    @exec($cmd, $outLines, $code);
    $imgs = glob($tmpDir.DIRECTORY_SEPARATOR."page_*.png");
    if ($code === 0 && $imgs && count($imgs) > 0) {
      foreach ($imgs as $imgPath) {
        $imgBytes = @file_get_contents($imgPath);
        if ($imgBytes) {
          $inputContent[] = ["type"=>"input_image","image_url"=>"data:image/png;base64,".base64_encode($imgBytes)];
        }
      }
      $sentAsImages = true;
    }
  }

  // Fallback: send as PDF file (Base64 DATA URL) ✅✅✅
  if (!$sentAsImages) {
    $pdfDataUrl = "data:application/pdf;base64,".$b64;  // مهم جداً حسب التوثيق :contentReference[oaicite:1]{index=1}
    $inputContent[] = [
      "type" => "input_file",
      "filename" => ($original_name ?: "plan.pdf"),
      "file_data" => $pdfDataUrl
    ];
  }
}

$payload = [
  "model" => $model,
  "input" => [
    ["role"=>"user","content"=>$inputContent]
  ],
  // خلي الموديل يرجّع JSON مضبوط (JSON mode) :contentReference[oaicite:2]{index=2}
  "text" => [
    "format" => ["type" => "json_object"]
  ],
  "max_output_tokens" => 2500
];

$ch = curl_init("https://api.openai.com/v1/responses");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer ".$apiKey
  ],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 90
]);

$resp = curl_exec($ch);
$err  = curl_error($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
  json_out(["error"=>"OPENAI_REQUEST_FAILED","msg"=>$err], 500);
}
if ($http < 200 || $http >= 300) {
  // الآن رح تشوف 400/401 + السبب الحقيقي داخل body
  json_out(["error"=>"OPENAI_BAD_STATUS","status"=>$http,"body"=>$resp], 500);
}

$data = json_decode($resp, true);
if (!$data) {
  json_out(["error"=>"OPENAI_BAD_JSON","body"=>$resp], 500);
}

// extract text output (Responses API)
$outText = "";
if (isset($data["output"]) && is_array($data["output"])) {
  foreach ($data["output"] as $o) {
    if (($o["type"] ?? "") === "message") {
      foreach (($o["content"] ?? []) as $c) {
        if (($c["type"] ?? "") === "output_text") $outText .= ($c["text"] ?? "");
      }
    }
  }
}
$outText = trim($outText);

// لأننا فعّلنا json_object، لازم يكون JSON
$extracted = json_decode($outText, true);
if (!$extracted || !isset($extracted["rows"])) {
  json_out(["error"=>"AI_PARSE_FAILED","msg"=>"الـ AI لم يرجع JSON بالصيغة المطلوبة","raw"=>$outText], 500);
}

// normalize rows, keep latest by code
$latest = [];
foreach ($extracted["rows"] as $row) {
  $code = trim((string)($row["code"] ?? ""));
  if ($code === "") continue;
  $term = preg_replace("/[^0-9]/", "", (string)($row["term"] ?? ""));
  $row["term"] = $term;
  $row["credits"] = (int)($row["credits"] ?? 0);
  $row["status"] = trim((string)($row["status"] ?? ""));
  $row["name"] = trim((string)($row["name"] ?? ""));
  $row["grade"] = trim((string)($row["grade"] ?? ""));
  $row["prerequisite"] = trim((string)($row["prerequisite"] ?? ""));
  if (!isset($latest[$code]) || strcmp($term, $latest[$code]["term"]) > 0) {
    $latest[$code] = $row;
  }
}
$rows = array_values($latest);

// build completed & registered sets
$completed = [];
$registered = [];
$remaining = [];

foreach ($rows as $rrow) {
  $st = $rrow["status"];
  $code = $rrow["code"];
  if (mb_strpos($st, "ناجح") !== false || mb_strpos($st, "معف") !== false) {
    $completed[$code] = true;
  } elseif (mb_strpos($st, "مسجل") !== false || mb_strpos($st, "مسجلة") !== false) {
    $registered[$code] = true;
  }
}

// eligible remaining: not completed, not registered
foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if (isset($completed[$code]) || isset($registered[$code])) continue;

  $pr = $rrow["prerequisite"];
  $prCode = "";
  if ($pr) {
    if (preg_match("/\((\d+)\)/", $pr, $m)) $prCode = $m[1];
    else $prCode = preg_replace("/[^0-9]/","",$pr);
  }

  $ok = true;
  if ($prCode) $ok = isset($completed[$prCode]); // simple single prerequisite
  if ($ok) $remaining[] = [
    "code"=>$code,
    "name"=>$rrow["name"],
    "credits"=>$rrow["credits"],
    "prerequisite"=>$prCode
  ];
}

// simple planner: pack courses into next terms up to term_credits
$planned = [];
$termIndex = 1;
$current = [];
$sum = 0;
foreach ($remaining as $c) {
  $cr = (int)$c["credits"];
  if ($cr <= 0) continue;
  if ($sum + $cr > $term_credits && $sum > 0) {
    $planned["term_".$termIndex] = $current;
    $termIndex++;
    $current = [];
    $sum = 0;
  }
  $current[] = $c;
  $sum += $cr;
  if ($termIndex >= 4) break; // suggest up to 3 terms
}
if ($current && $termIndex <= 3) $planned["term_".$termIndex] = $current;

// completed by term
$completed_by_term = [];
foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if (!isset($completed[$code])) continue;
  $t = $rrow["term"] ?: "unknown";
  if (!isset($completed_by_term[$t])) $completed_by_term[$t] = [];
  $completed_by_term[$t][] = [
    "code"=>$code,
    "name"=>$rrow["name"],
    "credits"=>$rrow["credits"],
    "grade"=>$rrow["grade"]
  ];
}
ksort($completed_by_term);

// registered by term
$registered_by_term = [];
foreach ($rows as $rrow) {
  $code = $rrow["code"];
  if (!isset($registered[$code])) continue;
  $t = $rrow["term"] ?: "unknown";
  if (!isset($registered_by_term[$t])) $registered_by_term[$t] = [];
  $registered_by_term[$t][] = [
    "code"=>$code,
    "name"=>$rrow["name"],
    "credits"=>$rrow["credits"]
  ];
}
ksort($registered_by_term);

$result = [
  "meta" => $extracted["meta"] ?? new stdClass(),
  "term_credits" => $term_credits,
  "completed_by_term" => $completed_by_term,
  "registered_by_term" => $registered_by_term,
  "suggested_terms" => $planned,
  "generated_at" => date("c")
];

// save to plan_analysis
$json = json_encode($result, JSON_UNESCAPED_UNICODE);

$save = $conn->prepare("INSERT INTO plan_analysis (user_id, source_attachment_id, analysis_json)
VALUES (?,?,?)
ON DUPLICATE KEY UPDATE source_attachment_id=VALUES(source_attachment_id), analysis_json=VALUES(analysis_json)");
$save->bind_param("iis", $user_id, $attachment_id, $json);
$save->execute();
if ($save->errno) {
  $errMsg = $save->error ?: $conn->error;
  $save->close();
  json_out(["error"=>"SAVE_FAILED","msg"=>"فشل حفظ التحليل في قاعدة البيانات. تأكد من وجود جدول plan_analysis.","db_error"=>$errMsg], 500);
}

$save->close();

json_out(["ok"=>true, "data"=>$result]);
