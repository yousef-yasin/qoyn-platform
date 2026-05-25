<?php
header("Content-Type: application/json; charset=utf-8");
session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/openai.php";

$user_id = (int)($_SESSION["user_id"] ?? 0);
if (!$user_id) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"msg"=>"Not logged in"]);
  exit;
}

if (!OPENAI_API_KEY) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"msg"=>"OpenAI API key missing in .env"]);
  exit;
}

/** ============ Helper ============ */
function openai_responses_json($payload) {
  $ch = curl_init("https://api.openai.com/v1/responses");
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      "Authorization: Bearer " . OPENAI_API_KEY,
      "Content-Type: application/json"
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE)
  ]);

  $response = curl_exec($ch);
  $err = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($err) return [false, "cURL error: $err", null];

  if ($code < 200 || $code >= 300) {
    return [false, "OpenAI error HTTP=$code", $response];
  }

  $data = json_decode($response, true);
  $jsonText = $data["output"][0]["content"][0]["text"] ?? null;

  if (!$jsonText) return [false, "No JSON returned", $response];

  return [true, null, $jsonText];
}

/** ============ 1) Get last plan attachment ============ */
$stmt = $conn->prepare("
  SELECT id, file_path, mime_type, original_name
  FROM student_attachments
  WHERE user_id=? AND type='plan'
  ORDER BY created_at DESC
  LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$att = $res->fetch_assoc();
$stmt->close();

if (!$att) {
  echo json_encode(["ok"=>false,"msg"=>"No plan uploaded yet"]);
  exit;
}

/** ============ 2) Read file bytes ============ */
$root = realpath(__DIR__ . "/../../");
$fullPath = $root . DIRECTORY_SEPARATOR . str_replace(["/","\\"], DIRECTORY_SEPARATOR, $att["file_path"]);

if (!file_exists($fullPath)) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"msg"=>"File not found on server","path"=>$fullPath]);
  exit;
}

$bytes = file_get_contents($fullPath);
$b64 = base64_encode($bytes);
$mime = $att["mime_type"] ?: "application/pdf";
$filename = $att["original_name"] ?: "plan.pdf";

/** ============ 3) Prompt 1: Plan analysis summary ============ */
$payload1 = [
  "model" => OPENAI_MODEL,
  "input" => [[
    "role" => "user",
    "content" => [
      [
        "type" => "input_text",
        "text" =>
"حلّل هذا الملف (كشف/خطة مواد). أرجع JSON فقط بالشكل التالي (بدون أي نص إضافي):
{
  \"completed_by_term\": {\"20231\": [{\"code\":\"\",\"name\":\"\",\"credits\":0,\"grade\":\"\",\"status\":\"ناجح\"}]},
  \"registered_by_term\": {\"20252\": [{\"code\":\"\",\"name\":\"\",\"credits\":0,\"grade\":\"\",\"status\":\"مسجلة\"}]},
  \"remaining_courses\": [{\"code\":\"\",\"name\":\"\",\"credits\":0,\"prerequisite\":\"\"}],
  \"meta\": {\"gpa\":\"\",\"remaining_hours\":\"\"}
}
ملاحظات:
- اعتبر الحالة (ناجح) = completed
- (مسجلة) أو وجود X = registered
- استخرج الفصل من عمود (الفصل)"
      ],
      [
        "type" => "input_file",
        "filename" => $filename,
        "file_data" => "data:$mime;base64,$b64"
      ]
    ]
  ]],
  "text" => ["format" => ["type" => "json_object"]]
];

list($ok1, $err1, $jsonText1) = openai_responses_json($payload1);
if (!$ok1) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"msg"=>$err1,"raw"=>$jsonText1]);
  exit;
}

/** Save analysis_json */
$save = $conn->prepare("
  INSERT INTO plan_analysis (user_id, source_attachment_id, analysis_json)
  VALUES (?,?,?)
  ON DUPLICATE KEY UPDATE source_attachment_id=VALUES(source_attachment_id), analysis_json=VALUES(analysis_json)
");
$save->bind_param("iis", $user_id, $att["id"], $jsonText1);
$save->execute();
$save->close();

/** ============ 4) Create tables for required courses ============ */
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

/** ============ 5) Prompt 2: Extract mandatory/required courses ============ */
$payload2 = [
  "model" => OPENAI_MODEL,
  "input" => [[
    "role" => "user",
    "content" => [
      [
        "type" => "input_text",
        "text" =>
"استخرج من هذا الملف فقط معلومات (الخطة الدراسية/جدول المواد) التالية وأرجع JSON فقط بدون أي كلام:
{
  \"major_name\": \"\",
  \"required_courses\": [
    {\"code\":\"\",\"name\":\"\",\"credits\":0,\"group_title\":\"\"}
  ]
}
شروط مهمة:
- المطلوب فقط المواد (الإجباري/متطلبات التخصص الإجباري/mandatory/required).
- تجاهل الاختياري.
- إذا ما لقيت اسم التخصص خليه فارغ."
      ],
      [
        "type" => "input_file",
        "filename" => $filename,
        "file_data" => "data:$mime;base64,$b64"
      ]
    ]
  ]],
  "text" => ["format" => ["type" => "json_object"]]
];

list($ok2, $err2, $jsonText2) = openai_responses_json($payload2);
if (!$ok2) {
  // ما نوقف النظام؛ نخلي التحليل saved بس نقول فشل استخراج الإجباري
  echo json_encode(["ok"=>true,"msg"=>"Analysis saved, but required courses extraction failed","error"=>$err2]);
  exit;
}

$requiredObj = json_decode($jsonText2, true);
$major_name = trim((string)($requiredObj["major_name"] ?? ""));
$required_courses = $requiredObj["required_courses"] ?? [];
if (!is_array($required_courses)) $required_courses = [];

/** ============ 6) Save required courses to DB ============ */
// امسح القديم عشان ما يضل خلط
$del = $conn->prepare("DELETE FROM user_plan_courses WHERE user_id=?");
$del->bind_param("i", $user_id);
$del->execute();
$del->close();

// upsert major name
$upm = $conn->prepare("
  INSERT INTO user_plan_profile (user_id, major_name)
  VALUES (?, ?)
  ON DUPLICATE KEY UPDATE major_name=VALUES(major_name)
");
$upm->bind_param("is", $user_id, $major_name);
$upm->execute();
$upm->close();

// insert required courses
$ins = $conn->prepare("
  INSERT INTO user_plan_courses (user_id, course_code, course_name, group_title, is_required, credits)
  VALUES (?, ?, ?, ?, 1, ?)
  ON DUPLICATE KEY UPDATE
    course_name=VALUES(course_name),
    group_title=VALUES(group_title),
    is_required=1,
    credits=VALUES(credits)
");

$inserted = 0;
foreach ($required_courses as $c) {
  if (!is_array($c)) continue;
  $code = trim((string)($c["code"] ?? ""));
  $name = trim((string)($c["name"] ?? ""));
  $group = trim((string)($c["group_title"] ?? ""));
  $credits = (int)($c["credits"] ?? 0);

  if ($code === "" || $name === "") continue;

  $ins->bind_param("isssi", $user_id, $code, $name, $group, $credits);
  $ins->execute();
  $inserted++;
}
$ins->close();

echo json_encode([
  "ok"=>true,
  "msg"=>"Analysis saved + required courses saved",
  "major_name"=>$major_name,
  "required_saved"=>$inserted
]);
