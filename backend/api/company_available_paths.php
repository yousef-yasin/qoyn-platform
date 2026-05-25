<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../config/db.php"; // عدّل المسار حسب مشروعك

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

// 1) جيب ايميل اليوزر
$st = $conn->prepare("SELECT email FROM users WHERE id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_USERS_FAILED"]); exit; }
$st->bind_param("i", $user_id);
$st->execute();
$u = $st->get_result()->fetch_assoc();
$email = $u["email"] ?? "";
if($email === ""){
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"USER_EMAIL_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

// 2) حوّل email -> partner_id
$st = $conn->prepare("SELECT id FROM partners WHERE email=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_PARTNER_FAILED"]); exit; }
$st->bind_param("s", $email);
$st->execute();
$p = $st->get_result()->fetch_assoc();
$partner_id = (int)($p["id"] ?? 0);

if($partner_id <= 0){
  // يعني هذا المستخدم مش شركة بالـ partners
  echo json_encode(["ok"=>true, "items"=>[]], JSON_UNESCAPED_UNICODE);
  exit;
}

// 3) هات المسارات المفعّلة للشركة + منشورة
$sql = "
SELECT lp.id, lp.title, lp.role_key
FROM company_path_offers cpo
JOIN learning_paths lp ON lp.id = cpo.path_id
WHERE cpo.company_id = ?
  AND cpo.is_active = 1
  AND lp.is_published = 1
ORDER BY lp.id DESC
";

$st = $conn->prepare($sql);
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_PATHS_FAILED"]); exit; }
$st->bind_param("i", $partner_id);
$st->execute();
$rs = $st->get_result();

$items = [];
while($row = $rs->fetch_assoc()){
  $items[] = [
    "id" => (int)$row["id"],
    "title" => $row["title"],
    "role_key" => $row["role_key"],
    "role_name" => $row["role_key"], // اختياري
  ];
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);