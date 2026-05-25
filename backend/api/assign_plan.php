<?php
require_once __DIR__ . "/db.php";
require_login();
header("Content-Type: application/json; charset=utf-8");

function json_out($d, $c=200){
  http_response_code($c);
  echo json_encode($d, JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$major   = trim($_POST["major"] ?? "");
$major_l = mb_strtolower($major, "UTF-8");
if ($major_l === "") json_out(["ok"=>false,"error"=>"MAJOR_REQUIRED"], 400);

/* ===== تعريف الخطط (نفس مبدأ علم بيانات، أضفنا CS) ===== */
$plans = [
  [
    "plan_key"  => "AI_DS",
    "plan_name" => "علم البيانات والذكاء الاصطناعي",
    "group"     => "خطة AI/DS",
    "keywords"  => ["ai","ds","data","science","ذكاء","اصطناعي","بيانات","علم البيانات"]
  ],
  [
    "plan_key"  => "CS",
    "plan_name" => "علم الحاسوب",
    "group"     => "خطة CS",
    "keywords"  => ["cs","computer science","computer","science","علم الحاسوب","علوم الحاسوب","علم حاسوب"]
  ],
    [
    "plan_key"  => "SE",
    "plan_name" => "هندسة البرمجيات (Software Engineering)",
    "group"     => "خطة SE",
    "keywords"  => [
      "software",
      "software engineering",
      "softwre enginer",
      "softwre engineer",
      "softwre",
      "se",
      "هندسة البرمجيات",
      "هندسه البرمجيات",
      "هندسة برمجيات",
      "هندسه برمجيات"
    ]
  ],
[
  "plan_key"  => "CYBER",
  "plan_name" => "Cyber Security",
  "group"     => "خطة Cyber",
  "keywords"  => [
    "cyber",
    "cybersecurity",
    "cyber security",
    "سايبر",
    "امن سيبراني",
    "الأمن السيبراني",
    "الامن السيبراني"
  ]
],

];

/* ===== اختيار الخطة حسب الكلمات ===== */
$selected = null;
foreach ($plans as $p) {
  foreach ($p["keywords"] as $k) {
    $k_l = mb_strtolower($k, "UTF-8");
    if ($k_l !== "" && mb_strpos($major_l, $k_l) !== false) { $selected = $p; break 2; }
  }
}
if (!$selected) json_out(["ok"=>false,"error"=>"NO_MATCHING_PLAN"], 400);

/* ===== جلب plan_id حسب plan_key المختار ===== */
$q = $conn->prepare("SELECT id FROM study_plans WHERE plan_key=? LIMIT 1");
$q->bind_param("s", $selected["plan_key"]);
$q->execute();
$r = $q->get_result()->fetch_assoc();
$q->close();

if (!$r) json_out(["ok"=>false,"error"=>"PLAN_NOT_FOUND","plan_key"=>$selected["plan_key"]], 500);
$plan_id = (int)$r["id"];

/* ===== امسح مواد المستخدم الحالية ===== */
$del = $conn->prepare("DELETE FROM user_plan_courses WHERE user_id=?");
$del->bind_param("i", $user_id);
$del->execute();
$del->close();

/* ===== انسخ مواد الخطة للمستخدم ===== */
$ins = $conn->prepare("
  INSERT INTO user_plan_courses (user_id, course_code, course_name, is_required, credits, group_title)
  SELECT ?, course_name, course_name, is_required, 0, ?
  FROM study_plan_courses
  WHERE plan_id=?
");
$ins->bind_param("isi", $user_id, $selected["group"], $plan_id);
$ins->execute();
$ins->close();

/* ===== OK ===== */
json_out([
  "ok" => true,
  "plan_key" => $selected["plan_key"],
  "plan_name" => $selected["plan_name"],
  "plan_id" => $plan_id
]);
