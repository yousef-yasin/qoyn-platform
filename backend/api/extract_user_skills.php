<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php";
if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"]);
  exit;
}
$user_id = (int)$_SESSION["user_id"];

/**
 * 1) اجمع النص من عناوين + أوصاف الدورات
 */
$stmt = $conn->prepare("SELECT course_title, description FROM course_submissions WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$textParts = [];
foreach ($rows as $r) {
  $t = trim((string)($r["course_title"] ?? ""));
  $d = trim((string)($r["description"] ?? ""));
  if ($t !== "") $textParts[] = $t;
  if ($d !== "") $textParts[] = $d;
}

$fullText = mb_strtolower(implode(" . ", $textParts), "UTF-8");
if (trim($fullText) === "") {
  echo json_encode(["ok"=>false,"error"=>"NO_COURSE_TEXT"]);
  exit;
}

/**
 * 2) Keyword matching against ESCO skills (skill_name)
 *    - نبحث عن skills اللي اسمها موجود بالنص
 *    - عشان الأداء: نجيب فقط skills الطويلة/المفيدة
 */
$skills = [];
$res = $conn->query("SELECT id, skill_name FROM skills WHERE CHAR_LENGTH(skill_name) >= 5");
while ($s = $res->fetch_assoc()) {
  $name = mb_strtolower($s["skill_name"], "UTF-8");
  // match بسيط: وجود skill_name داخل النص
  if (mb_strpos($fullText, $name) !== false) {
    $skills[] = (int)$s["id"];
  }
}

/**
 * 3) خزّن النتائج في user_skills
 */
$inserted = 0;
if (!empty($skills)) {
  $ins = $conn->prepare("
    INSERT INTO user_skills (user_id, skill_id, source, confidence)
    VALUES (?, ?, 'keyword', 1)
    ON DUPLICATE KEY UPDATE confidence=GREATEST(confidence, VALUES(confidence))
  ");
  foreach ($skills as $skill_id) {
    $ins->bind_param("ii", $user_id, $skill_id);
    $ins->execute();
    $inserted += $ins->affected_rows ? 1 : 0;
  }
}

/**
 * 4) (اختياري) إذا النتائج قليلة، نرجع ok بس ونكمل Gemini بالمرحلة القادمة
 */
echo json_encode([
  "ok" => true,
  "matched_skills_count" => count($skills),
  "saved_count" => $inserted
], JSON_UNESCAPED_UNICODE);