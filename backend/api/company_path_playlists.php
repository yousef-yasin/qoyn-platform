<?php
// utbn-backend/api/company_path_playlists.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
if (($_SESSION["role"] ?? "") !== "partner") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"], JSON_UNESCAPED_UNICODE); exit; }

$path_id = (int)($_GET["path_id"] ?? 0);
if($path_id <= 0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"], JSON_UNESCAPED_UNICODE); exit; }

// ✅ تأكد أن المسار منشور + مفعل لهذه الشركة
$user_id = (int)$_SESSION["user_id"];

// 1) user email
$st = $conn->prepare("SELECT email FROM users WHERE id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_USERS_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i",$user_id);
$st->execute();
$u = $st->get_result()->fetch_assoc();
$email = $u["email"] ?? "";
if($email===""){ echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE); exit; }

// 2) partners.id by email
$st = $conn->prepare("SELECT id FROM partners WHERE email=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_PARTNERS_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("s",$email);
$st->execute();
$p = $st->get_result()->fetch_assoc();
$partner_id = (int)($p["id"] ?? 0);
if($partner_id<=0){ echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE); exit; }

// 3) verify offer active + path published
$st = $conn->prepare("
  SELECT 1
  FROM company_path_offers cpo
  JOIN learning_paths lp ON lp.id=cpo.path_id
  WHERE cpo.company_id=? AND cpo.path_id=? AND cpo.is_active=1 AND lp.is_published=1
  LIMIT 1
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_CHECK_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("ii",$partner_id,$path_id);
$st->execute();
$okRow = $st->get_result()->fetch_assoc();
if(!$okRow){
  echo json_encode(["ok"=>true,"items"=>[]], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  ✅ templates عندك داخل partner_playlists (is_template=1)
  learning_path_playlists.template_playlist_id -> partner_playlists.id
*/
$sql = "
SELECT
  tp.id,
  tp.name,
  tp.template_subject AS subject,
  lpp.sort_order
FROM learning_path_playlists lpp
JOIN partner_playlists tp
  ON tp.id = lpp.template_playlist_id
 AND tp.is_template = 1
WHERE lpp.path_id = ?
ORDER BY lpp.sort_order ASC, tp.id ASC
";

$st = $conn->prepare($sql);
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i",$path_id);
$st->execute();
$rs = $st->get_result();

$items = [];
while($r = $rs->fetch_assoc()){
  $items[] = [
    "id" => (int)$r["id"],                       // template_playlist_id
    "name" => (string)$r["name"],
    "template_subject" => (string)($r["template_subject"] ?? ""),
    "is_selected" => false
  ];
}

echo json_encode(["ok"=>true,"items"=>$items], JSON_UNESCAPED_UNICODE);