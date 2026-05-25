<?php
// utbn-backend/api/student_code_submit.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");
@set_time_limit(120);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

function load_gemini_key(): ?string {
  $k = getenv("GEMINI_API_KEY");
  if ($k) return $k;

  $envPath = realpath(__DIR__ . "/../.env");
  if ($envPath && file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === "" || strpos($line, "#") === 0) continue;
      if (strpos($line, "=") === false) continue;
      [$kk,$vv] = explode("=", $line, 2);
      $kk = trim($kk);
      $vv = trim(trim($vv), "\"'");
      if ($kk === "GEMINI_API_KEY" && $vv) $k = $vv;
      if ($kk === "GEMINI_MODEL" && $vv && !getenv("GEMINI_MODEL")) putenv("GEMINI_MODEL=".$vv);
    }
  }
  return $k ?: null;
}

file_put_contents(__DIR__."/__debug.txt", date("c")." student_code_submit hit\n", FILE_APPEND);

function http_get_json(string $url): array {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
  ]);
  $resp = curl_exec($ch);
  $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if (!$resp || $http >= 400) return ["ok"=>false, "http"=>$http, "error"=>$err ?: $resp];
  $j = json_decode($resp, true);
  if (!is_array($j)) return ["ok"=>false, "http"=>$http, "error"=>"BAD_JSON", "raw"=>$resp];
  return ["ok"=>true, "http"=>$http, "json"=>$j];
}

function pick_model_for_generate(string $apiKey, string $prefer = ""): string {
  $prefer = trim($prefer);
  if ($prefer !== "") {
    if (strpos($prefer, "models/") !== 0) $prefer = "models/".$prefer;
  }

  $listUrl = "https://generativelanguage.googleapis.com/v1/models?key=" . urlencode($apiKey);
  $res = http_get_json($listUrl);

  $models = $res["json"]["models"] ?? [];
  if (!is_array($models)) $models = [];

  $supports = function($m): bool {
    $methods = $m["supportedGenerationMethods"] ?? [];
    return is_array($methods) && in_array("generateContent", $methods, true);
  };

  if ($prefer !== "") {
    foreach ($models as $m) {
      if (($m["name"] ?? "") === $prefer && $supports($m)) return $prefer;
    }
  }

  $priority = ["flash", "pro"];
  foreach ($priority as $kw) {
    foreach ($models as $m) {
      $name = (string)($m["name"] ?? "");
      if ($name && stripos($name, "gemini") !== false && stripos($name, $kw) !== false && $supports($m)) {
        return $name;
      }
    }
  }

  foreach ($models as $m) {
    $name = (string)($m["name"] ?? "");
    if ($name && stripos($name, "gemini") !== false && $supports($m)) return $name;
  }

  return $prefer !== "" ? $prefer : "models/gemini-1.5-flash-latest";
}

function gemini_score(string $apiKey, string $model, array $problem, string $studentCode): array {
  $instruction =
    "You are a programming contest judge. Compare a student's solution with the reference solution.\n".
    "Return ONLY valid JSON with this exact schema:\n".
    "{ \"score\": <number 0..1>, \"reason\": \"...\", \"highlights\": [\"...\"], \"issues\": [\"...\"] }\n".
    "Score should represent how close the student code is to producing the same behavior as the reference, given the problem statement.\n".
    "Do NOT require identical code. Focus on semantics/behavior. If unclear, be conservative.\n".
    "Respond in Arabic.";

  $content =
    "Problem Title: ".$problem["title"]."\n".
    "Language: ".$problem["language"]."\n".
    "Problem Statement:\n".$problem["prompt"]."\n\n".
    "Reference Solution:\n```".$problem["language"]."\n".$problem["solution_code"]."\n```\n\n".
    "Student Submission:\n```".$problem["language"]."\n".$studentCode."\n```\n";

  $payload = [
    "contents" => [[
      "role" => "user",
      "parts" => [["text" => $instruction."\n\n".$content]]
    ]],
    "generationConfig" => [
      "temperature" => 0.2,
      "maxOutputTokens" => 1024,
    ]
  ];

  $url = "https://generativelanguage.googleapis.com/v1/".$model.":generateContent?key=".urlencode($apiKey);

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 90,
  ]);
  $resp = curl_exec($ch);
  $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $cerr = curl_error($ch);
  curl_close($ch);

  if (!$resp || $http >= 400) {
    return ["ok"=>false, "error"=>"GEMINI_HTTP_ERROR", "http"=>$http, "details"=>$cerr ?: $resp];
  }

  $j = json_decode($resp, true);
  $text = $j["candidates"][0]["content"]["parts"][0]["text"] ?? null;
  if (!$text) return ["ok"=>false,"error"=>"GEMINI_BAD_RESPONSE","raw"=>$j];

  if (strpos($text, "{") !== false && strrpos($text, "}") === false) {
    $payload["contents"][0]["parts"][0]["text"] =
      $instruction .
      "\n\nأعد الإخراج الآن JSON فقط وبشكل مختصر جدًا (سبب قصير + 3 highlights + 3 issues كحد أقصى). بدون أي نص إضافي.\n\n" .
      $content;

    $ch2 = curl_init($url);
    curl_setopt_array($ch2, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
      CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
      CURLOPT_TIMEOUT => 90,
    ]);
    $resp2 = curl_exec($ch2);
    $http2 = (int)curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if ($resp2 && $http2 < 400) {
      $j2 = json_decode($resp2, true);
      $t2 = $j2["candidates"][0]["content"]["parts"][0]["text"] ?? null;
      if ($t2) $text = $t2;
    }
  }

  $clean = trim((string)$text);

  $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
  $clean = preg_replace('/\s*```$/', '', $clean);

  $start = strpos($clean, "{");
  $end   = strrpos($clean, "}");
  if ($start !== false && $end !== false && $end > $start) {
    $clean = substr($clean, $start, $end - $start + 1);
  }

  $out = json_decode($clean, true);
  if (!is_array($out)) {
    return ["ok"=>false,"error"=>"GEMINI_NOT_JSON","text"=>$text, "clean"=>$clean];
  }

  $score = $out["score"] ?? 0;
  if (!is_numeric($score)) $score = 0;
  $score = max(0, min(1, (float)$score));

  return [
    "ok"=>true,
    "score"=>$score,
    "reason"=>(string)($out["reason"] ?? ""),
    "highlights"=>$out["highlights"] ?? [],
    "issues"=>$out["issues"] ?? []
  ];
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];

$problem_id = (int)($in["problem_id"] ?? 0);
$student_code = (string)($in["code"] ?? "");

if ($problem_id <= 0 || trim($student_code) === "") {
  json_out(["ok"=>false,"error"=>"MISSING_FIELDS"], 400);
}

$conn->query("CREATE TABLE IF NOT EXISTS partner_video_code_problems (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  partner_user_id INT UNSIGNED NOT NULL,
  partner_video_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  prompt MEDIUMTEXT NOT NULL,
  language VARCHAR(40) NOT NULL DEFAULT 'python',
  starter_code MEDIUMTEXT NULL,
  solution_code MEDIUMTEXT NOT NULL,
  max_coin INT NOT NULL DEFAULT 50,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pvcp_video (partner_video_id),
  KEY idx_pvcp_partner (partner_user_id, partner_video_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS code_rewards (
  user_id INT UNSIGNED NOT NULL,
  problem_id INT UNSIGNED NOT NULL,
  score DECIMAL(5,4) NOT NULL DEFAULT 0,
  coin_awarded INT NOT NULL DEFAULT 0,
  feedback_json MEDIUMTEXT NULL,
  rewarded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, problem_id),
  KEY idx_cr_user (user_id),
  KEY idx_cr_problem (problem_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$st = $conn->prepare("SELECT id, title, prompt, language, solution_code, max_coin FROM partner_video_code_problems WHERE id=? LIMIT 1");
$st->bind_param("i", $problem_id);
$st->execute();
$problem = $st->get_result()->fetch_assoc();
$st->close();

if (!$problem) json_out(["ok"=>false,"error"=>"PROBLEM_NOT_FOUND"], 404);

$apiKey = load_gemini_key();
if (!$apiKey) json_out(["ok"=>false,"error"=>"GEMINI_API_KEY_MISSING","msg"=>"ضع GEMINI_API_KEY في utbn-backend/.env أو Environment"], 400);

$envModel = trim((string)(getenv("GEMINI_MODEL") ?: ""));
$model = pick_model_for_generate($apiKey, $envModel);

$grade = gemini_score($apiKey, $model, $problem, $student_code);
if (!$grade["ok"]) {
  $status = ($grade["http"] ?? 500);
  if ($status < 400) $status = 500;
  json_out(["ok"=>false,"error"=>$grade["error"],"details"=>$grade], $status);
}

$score = (float)$grade["score"];
$max_coin = (int)$problem["max_coin"];
$coin = (int)round($max_coin * $score);

// ✅ Award ONCE only
$prev = null;
$g = $conn->prepare("SELECT score, coin_awarded FROM code_rewards WHERE user_id=? AND problem_id=? LIMIT 1");
$g->bind_param("ii", $user_id, $problem_id);
$g->execute();
$prev = $g->get_result()->fetch_assoc();
$g->close();

$already_rewarded = (bool)$prev;

$feedback_json = json_encode([
  "reason"=>$grade["reason"],
  "highlights"=>$grade["highlights"],
  "issues"=>$grade["issues"]
], JSON_UNESCAPED_UNICODE);

$kept = false;
$coin_awarded_now = 0;

if (!$already_rewarded) {
  $up = $conn->prepare("
    INSERT INTO code_rewards (user_id, problem_id, score, coin_awarded, feedback_json, rewarded_at)
    VALUES (?, ?, ?, ?, ?, NOW())
  ");
  $stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
if ($stu) {
  $stu->bind_param("ii", $coin, $user_id);
  $stu->execute();
  $stu->close();
}
  $up->bind_param("iidis", $user_id, $problem_id, $score, $coin, $feedback_json);
  if (!$up->execute()) {
    $err = $up->error;
    $up->close();
    json_out(["ok"=>false,"error"=>"DB_INSERT_FAILED","details"=>$err], 500);
  }
  $up->close();

  $kept = true;
  $coin_awarded_now = $coin;
}

// coins_total = video_rewards + code_rewards
$sum1 = 0; $sum2 = 0;

$hasVR = $conn->query("SHOW TABLES LIKE 'video_rewards'");
if ($hasVR && $hasVR->num_rows > 0) {
  $s = $conn->prepare("SELECT COALESCE(SUM(total_coin),0) AS s FROM video_rewards WHERE user_id=?");
  $s->bind_param("i", $user_id);
  $s->execute();
  $sum1 = (int)($s->get_result()->fetch_assoc()["s"] ?? 0);
  $s->close();
}

// ✅ safe sum (MAX per problem_id)
$s2 = $conn->prepare("
  SELECT COALESCE(SUM(mx),0) AS s
  FROM (
    SELECT MAX(coin_awarded) AS mx
    FROM code_rewards
    WHERE user_id=?
    GROUP BY problem_id
  ) t
");
$s2->bind_param("i", $user_id);
$s2->execute();
$sum2 = (int)($s2->get_result()->fetch_assoc()["s"] ?? 0);
$s2->close();

json_out([
  "ok"=>true,
  "score"=>$score,
  "coin_awarded"=>$coin_awarded_now, // ✅ 0 إذا ثاني مرة
  "kept"=>$kept,
  "already_rewarded"=>$already_rewarded,
  "coins_total"=>$sum1 + $sum2,
  "feedback"=>json_decode($feedback_json, true)
]);
