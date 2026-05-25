<?php
// utbn-backend/api/video_quiz.php

require __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

// تنظيف المدخلات
$videoId = preg_replace("/[^a-zA-Z0-9_\-]/", "", $_GET["videoId"] ?? "");
$type = (($_GET["type"] ?? "quick") === "deep") ? "deep" : "quick";

$courseName = trim($_GET["course"] ?? "");
$courseName = mb_substr($courseName, 0, 120, "UTF-8");

if ($videoId === "") json_out(["ok"=>false,"error"=>"MISSING_VIDEO_ID"], 400);

function str_starts_with2(string $h, string $n): bool {
  return $n === "" || substr($h, 0, strlen($n)) === $n;
}

function extract_json_object(string $text): ?array {
  $t = trim($text);
  $t = preg_replace('/^```(?:json)?\s*/i', '', $t);
  $t = preg_replace('/\s*```\s*$/', '', $t);

  $start = strpos($t, '{');
  $end = strrpos($t, '}');
  if ($start === false || $end === false || $end <= $start) return null;

  $chunk = substr($t, $start, $end - $start + 1);
  $j = json_decode($chunk, true);
  if (is_array($j)) return $j;

  $chunk2 = preg_replace('/\xEF\xBB\xBF/', '', $chunk);
  $j2 = json_decode($chunk2, true);
  return is_array($j2) ? $j2 : null;
}

function fallback_quiz(string $videoId, string $title, string $type, string $courseName): array {
  $topic = $courseName !== "" ? $courseName : ($title !== "" ? $title : "محتوى الفيديو");

  if ($type === "deep") {
    return [
      "ok"=>true,"videoId"=>$videoId,"videoTitle"=>($title?: "اختبار الفيديو"),"type"=>"deep",
      "quiz"=>[
        "mcq"=>[
          ["q"=>"ما الهدف الأساسي من هذا الفيديو ضمن سياق ($topic)؟","choices"=>["شرح مفهوم/مهارة","عرض أخبار فقط","تجربة عشوائية","بدون هدف"],"answerIndex"=>0,"explain"=>"الأقرب عادةً: شرح مفهوم/مهارة مرتبطة بالعنوان."],
          ["q"=>"أي خيار يصف أفضل مبدأ للتعلّم من الفيديو؟","choices"=>["المشاهدة فقط","التطبيق العملي بعد كل جزء","حفظ الكلمات","تجاهل الأمثلة"],"answerIndex"=>1,"explain"=>"التطبيق يثبت الفهم."],
          ["q"=>"إذا واجهت خطأ أثناء التطبيق، ما أول خطوة؟","choices"=>["إيقاف التعلّم","قراءة رسالة الخطأ وتتبع السبب","حذف المشروع","تغيير الموضوع"],"answerIndex"=>1,"explain"=>"رسالة الخطأ دليل مباشر."],
          ["q"=>"كيف تقيس فهمك بعد الفيديو؟","choices"=>["تخمين","حل أسئلة وشرح السبب","تجاوز","مشاهدة غير مرتبط"],"answerIndex"=>1,"explain"=>"الأسئلة + شرح السبب أفضل."],
          ["q"=>"أفضل طريقة لتوثيق ما تعلمته؟","choices"=>["بدون ملاحظات","ملاحظات مختصرة + مثال","نسخ كل شيء","الذاكرة فقط"],"answerIndex"=>1,"explain"=>"ملاحظات + مثال."],
          ["q"=>"علامة أن الشرح كان واضح؟","choices"=>["لا تستطيع تطبيق","تستطيع شرح وتطبيق مثال","لا تتذكر العنوان","تتشتت"],"answerIndex"=>1,"explain"=>"التطبيق دليل فهم."],
        ],
        "trueFalse"=>[
          ["q"=>"المشاهدة وحدها تكفي بدون تطبيق.","answer"=>false,"explain"=>"التطبيق ضروري."],
          ["q"=>"قراءة رسالة الخطأ تساعد على الحل.","answer"=>true,"explain"=>"صحيح."],
          ["q"=>"التدرج أفضل من محاولة فهم كل شيء دفعة واحدة.","answer"=>true,"explain"=>"صحيح."],
          ["q"=>"تخطي الأمثلة أفضل طريقة للتعلم.","answer"=>false,"explain"=>"الأمثلة مهمة."],
        ],
        "short"=>[
          ["q"=>"اكتب أهم نقطة تعلمتها من الفيديو.","answer"=>"الفكرة الأساسية مرتبطة بعنوان الفيديو مع خطوة تطبيقية.","rubric"=>"اذكر الفكرة + مثال."],
          ["q"=>"اذكر خطأ شائع وكيف تتعامل معه.","answer"=>"أقرأ رسالة الخطأ ثم أتبع السبب وأصلحه.","rubric"=>"خطأ + حل."],
          ["q"=>"ما خطوة عملية ستنفذها اليوم؟","answer"=>"أطبق مثال صغير ثم أحسّنه تدريجيًا.","rubric"=>"خطوة قابلة للتنفيذ."],
        ],
        "application"=>[
          ["q"=>"اكتب خطة من 3 خطوات لتطبيق مفهوم الفيديو.","answer"=>"1) تحديد الهدف 2) مثال بسيط 3) اختبار وتحسين","rubric"=>"خطة واضحة."],
          ["q"=>"ما معيار نجاح تطبيقك؟","answer"=>"أن تعمل النتيجة المتوقعة بدون أخطاء مع فهم السبب.","rubric"=>"معيار قابل للقياس."],
        ]
      ]
    ];
  }

  // ✅ quick صار أقوى
  return [
    "ok"=>true,"videoId"=>$videoId,"videoTitle"=>($title?: "اختبار الفيديو"),"type"=>"quick",
    "quiz"=>[
      "mcq"=>[
        ["q"=>"ما الموضوع العام للفيديو؟","choices"=>[$topic,"رياضيات","طبخ","رياضة"],"answerIndex"=>0,"explain"=>"الأقرب: سياق المادة/العنوان."],
        ["q"=>"أفضل طريقة للاستفادة من الفيديو؟","choices"=>["مشاهدة فقط","مشاهدة + تطبيق","تخطي","مشاهدة بسرعة"],"answerIndex"=>1,"explain"=>"التطبيق يثبت المعلومة."],
        ["q"=>"متى تعتبر أنك فهمت؟","choices"=>["إذا قدرت تشرح وتطبق","إذا حفظت العنوان","إذا شاهدت مرة","إذا كتبت تعليق"],"answerIndex"=>0,"explain"=>"الشرح + التطبيق أفضل دليل."],
      ],
      "trueFalse"=>[
        ["q"=>"التطبيق العملي بعد الفيديو يساعد على الفهم.","answer"=>true,"explain"=>"صحيح."],
        ["q"=>"تجاهل الأخطاء أفضل من حلها.","answer"=>false,"explain"=>"الخطأ جزء من التعلّم."],
      ],
      "short"=>[
        ["q"=>"بجملة واحدة: ما الفكرة الأساسية التي يتناولها الفيديو؟","answer"=>"الفكرة الأساسية مرتبطة بعنوان الفيديو وسياق المادة.","rubric"=>"جملة واضحة مرتبطة بالعنوان."],
        ["q"=>"اذكر مثالًا واحدًا على تطبيق ما تعلمته.","answer"=>"أطبق مثال بسيط مرتبط بموضوع الفيديو ثم أتحقق من النتيجة.","rubric"=>"مثال عملي واضح."],
      ],
      "application"=>[
        ["q"=>"اكتب 3 خطوات عملية ستنفذها بعد مشاهدة الفيديو لتثبيت الفهم.","answer"=>"1) تلخيص 2) تطبيق مثال 3) اختبار وتصحيح","rubric"=>"ثلاث خطوات قابلة للتنفيذ."],
      ]
    ]
  ];
}

// ===== اقرأ .env لو موجود =====
$envPath = realpath(__DIR__ . "/../.env");
if ($envPath && is_file($envPath) && is_readable($envPath)) {
  $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === "" || str_starts_with2($line, "#")) continue;
    $pos = strpos($line, "=");
    if ($pos === false) continue;
    $k = trim(substr($line, 0, $pos));
    $v = trim(substr($line, $pos + 1));
    if ((str_starts_with2($v, '"') && substr($v, -1) === '"') || (str_starts_with2($v, "'") && substr($v, -1) === "'")) {
      $v = substr($v, 1, -1);
    }
    if ($k !== "" && getenv($k) === false) {
      putenv($k . "=" . $v);
      $_ENV[$k] = $v;
    }
  }
}

$YOUTUBE_API_KEY = getenv("YOUTUBE_API_KEY") ?: "";
$GEMINI_API_KEY  = getenv("GEMINI_API_KEY") ?: "";
$GEMINI_MODEL    = getenv("GEMINI_MODEL") ?: "gemini-2.5-flash";

// ===== Cache (SAFE, تعريف مرة واحدة فقط) =====
$conn->query("
  CREATE TABLE IF NOT EXISTS video_quiz_cache (
    cache_key VARCHAR(200) PRIMARY KEY,
    quiz_json MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$cache_key = $videoId . "|t:" . $type . "|c:" . mb_strtolower($courseName, "UTF-8");

// حاول ترجع من الكاش (لو موجود خلال 30 يوم)
$stmt = $conn->prepare("SELECT quiz_json, updated_at FROM video_quiz_cache WHERE cache_key=? LIMIT 1");
if ($stmt) {
  $stmt->bind_param("s", $cache_key);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($row) {
    $updated = strtotime($row["updated_at"]);
    if ($updated && (time() - $updated) < (30 * 24 * 3600)) {
      echo $row["quiz_json"];
      exit;
    }
  }
}
// ===== End Cache =====

// ===== جلب عنوان الفيديو (YouTube API لو key موجود، وإلا oEmbed) =====
$title = "";
$descShort = "";

if ($YOUTUBE_API_KEY && strlen($YOUTUBE_API_KEY) > 20) {
  $params = http_build_query(["part"=>"snippet","id"=>$videoId,"key"=>$YOUTUBE_API_KEY]);
  $videoUrl = "https://www.googleapis.com/youtube/v3/videos?$params";
  $ch = curl_init($videoUrl);
  curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>15]);
  $out = curl_exec($ch);
  $err = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if (!$err && $code >= 200 && $code < 300) {
    $data = json_decode($out, true);
    $item = $data["items"][0] ?? null;
    if ($item) {
      $title = trim($item["snippet"]["title"] ?? "");
      $desc  = trim($item["snippet"]["description"] ?? "");
      $descShort = mb_substr($desc, 0, 900, "UTF-8");
    }
  }
}

if ($title === "") {
  $oUrl = "https://www.youtube.com/oembed?format=json&url=" . urlencode("https://www.youtube.com/watch?v=" . $videoId);
  $ch = curl_init($oUrl);
  curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10]);
  $oOut = curl_exec($ch);
  $oErr = curl_error($ch);
  $oCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if (!$oErr && $oCode >= 200 && $oCode < 300) {
    $o = json_decode($oOut, true);
    $title = trim($o["title"] ?? "");
  }
}

// إذا ما قدرنا نجيب عنوان → fallback
if ($title === "") {
  $fallback = fallback_quiz($videoId, "اختبار الفيديو", $type, $courseName);
  $json = json_encode($fallback, JSON_UNESCAPED_UNICODE);

  $ins = $conn->prepare("INSERT INTO video_quiz_cache (cache_key, quiz_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE quiz_json=VALUES(quiz_json)");
  if ($ins) {
    $ins->bind_param("ss", $cache_key, $json);
    $ins->execute();
    $ins->close();
  }
  echo $json;
  exit;
}

// ===== بناء السياق =====
$ctx = "عنوان الفيديو:\n{$title}\n\n";
if ($descShort !== "") $ctx .= "وصف الفيديو (مختصر):\n{$descShort}\n\n";
if ($courseName !== "") $ctx .= "اسم المادة (سياق):\n{$courseName}\n\n";

// ✅ Prompts (quick صار فيه short/application)
$promptQuick = $ctx . <<<PROMPT
أنشئ اختبارًا سريعًا للتأكد أن الطالب فهم موضوع الفيديو بناءً على العنوان والوصف واسم المادة فقط.
ممنوع افتراض تفاصيل غير موجودة. ممنوع أسئلة عامة غير مرتبطة بالعنوان/الوصف.

أخرج JSON فقط بالشكل:
{
  "ok": true,
  "videoId": "...",
  "videoTitle": "...",
  "type":"quick",
  "quiz": {
    "mcq": [
      {"q":"...", "choices":["A","B","C","D"], "answerIndex":0, "explain":"شرح قصير جدًا"}
    ],
    "trueFalse": [
      {"q":"...", "answer": true, "explain":"شرح قصير جدًا"}
    ],
    "short": [
      {"q":"...", "answer":"إجابة نموذجية قصيرة", "rubric":"نقاط التصحيح"}
    ],
    "application": [
      {"q":"...", "answer":"حل/شرح مختصر", "rubric":"نقاط الحل"}
    ]
  }
}

الشروط:
- 3 MCQ فقط
- 2 صح/خطأ فقط
- 2 سؤال قصير فقط
- 1 سؤال تطبيقي فقط
PROMPT;

$promptDeep = $ctx . <<<PROMPT
أنشئ اختبارًا متقدمًا وقويًا باللغة العربية الفصحى مبنيًا فقط على العنوان والوصف واسم المادة.
ممنوع Transcript وممنوع اختراع حقائق غير موجودة. تجنب الأسئلة العامة.

أخرج JSON فقط بالشكل:
{
  "ok": true,
  "videoId": "...",
  "videoTitle": "...",
  "type":"deep",
  "quiz": {
    "mcq": [
      {"q":"...", "choices":["A","B","C","D"], "answerIndex":0, "explain":"شرح لماذا"}
    ],
    "trueFalse": [
      {"q":"...", "answer": true, "explain":"..."}
    ],
    "short": [
      {"q":"...", "answer":"إجابة نموذجية قصيرة", "rubric":"نقاط التصحيح"}
    ],
    "application": [
      {"q":"سؤال تطبيقي/تحليلي", "answer":"حل/شرح", "rubric":"نقاط الحل"}
    ]
  }
}

الشروط:
- 6 MCQ
- 4 صح/خطأ
- 3 أسئلة قصيرة
- 2 تطبيق/تحليل
PROMPT;

$prompt = ($type === "deep") ? $promptDeep : $promptQuick;

// إذا ما في Gemini key → fallback
if (!$GEMINI_API_KEY || strlen($GEMINI_API_KEY) < 20) {
  $fallback = fallback_quiz($videoId, $title, $type, $courseName);
  $json = json_encode($fallback, JSON_UNESCAPED_UNICODE);

  $ins = $conn->prepare("INSERT INTO video_quiz_cache (cache_key, quiz_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE quiz_json=VALUES(quiz_json)");
  if ($ins) {
    $ins->bind_param("ss", $cache_key, $json);
    $ins->execute();
    $ins->close();
  }
  echo $json;
  exit;
}

// ===== Gemini call =====
$geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" . rawurlencode($GEMINI_MODEL) . ":generateContent?key=" . urlencode($GEMINI_API_KEY);

$payload = [
  "contents" => [[ "role" => "user", "parts" => [["text" => $prompt]] ]],
  "generationConfig" => [
    "temperature" => 0.35,
    "topP" => 0.9,
    "maxOutputTokens" => ($type === "deep") ? 4096 : 1600
  ]
];

$ch = curl_init($geminiUrl);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => ($type === "deep") ? 35 : 18,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE)
]);

$gOut = curl_exec($ch);
$gErr = curl_error($ch);
$gCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($gErr || $gCode < 200 || $gCode >= 300) {
  $fallback = fallback_quiz($videoId, $title, $type, $courseName);
  $fallback["_note"] = "GEMINI_FAILED";
  $json = json_encode($fallback, JSON_UNESCAPED_UNICODE);

  $ins = $conn->prepare("INSERT INTO video_quiz_cache (cache_key, quiz_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE quiz_json=VALUES(quiz_json)");
  if ($ins) {
    $ins->bind_param("ss", $cache_key, $json);
    $ins->execute();
    $ins->close();
  }
  echo $json;
  exit;
}

$g = json_decode($gOut, true);
$text = $g["candidates"][0]["content"]["parts"][0]["text"] ?? "";

$quiz = extract_json_object($text);
if (!is_array($quiz) || !($quiz["ok"] ?? false)) {
  $fallback = fallback_quiz($videoId, $title, $type, $courseName);
  $fallback["_note"] = "QUIZ_JSON_PARSE_FAILED";
  $json = json_encode($fallback, JSON_UNESCAPED_UNICODE);

  $ins = $conn->prepare("INSERT INTO video_quiz_cache (cache_key, quiz_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE quiz_json=VALUES(quiz_json)");
  if ($ins) {
    $ins->bind_param("ss", $cache_key, $json);
    $ins->execute();
    $ins->close();
  }
  echo $json;
  exit;
}

$quiz["videoId"] = $videoId;
$quiz["videoTitle"] = $quiz["videoTitle"] ?? $title;
$quiz["type"] = $quiz["type"] ?? $type;

$json = json_encode($quiz, JSON_UNESCAPED_UNICODE);

$ins = $conn->prepare("INSERT INTO video_quiz_cache (cache_key, quiz_json) VALUES (?, ?) ON DUPLICATE KEY UPDATE quiz_json=VALUES(quiz_json)");
if ($ins) {
  $ins->bind_param("ss", $cache_key, $json);
  $ins->execute();
  $ins->close();
}

echo $json;
