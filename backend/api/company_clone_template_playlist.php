<?php
// utbn-backend/api/company_clone_template_playlist.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
if (($_SESSION["role"] ?? "") !== "partner") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"], JSON_UNESCAPED_UNICODE); exit; }

$template_id = (int)($_POST["template_playlist_id"] ?? 0);
$path_id     = (int)($_POST["path_id"] ?? 0);

if($template_id <= 0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_TEMPLATE_ID"], JSON_UNESCAPED_UNICODE); exit; }
if($path_id <= 0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"], JSON_UNESCAPED_UNICODE); exit; }

$user_id = (int)$_SESSION["user_id"];

/*
  1) جيب partner_id الحقيقي من جدول partners عبر email
  (لأنه company_path_offers مربوط على partners.id مش users.id)
*/
$st = $conn->prepare("SELECT email FROM users WHERE id=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_USERS_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i",$user_id);
$st->execute();
$u = $st->get_result()->fetch_assoc();
$email = $u["email"] ?? "";
if($email===""){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"USER_EMAIL_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }

$st = $conn->prepare("SELECT id FROM partners WHERE email=? LIMIT 1");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_PARTNER_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("s",$email);
$st->execute();
$p = $st->get_result()->fetch_assoc();
$partner_id = (int)($p["id"] ?? 0);
if($partner_id<=0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"PARTNER_NOT_FOUND_IN_PARTNERS"], JSON_UNESCAPED_UNICODE); exit; }

/*
  2) تأكد أنه هذا path مفعل للشركة + منشور
*/
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
  echo json_encode(["ok"=>false,"error"=>"PATH_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  3) هات الـ template من partner_playlists (is_template=1)
*/
$st = $conn->prepare("
  SELECT *
  FROM partner_playlists
  WHERE id=? AND is_template=1
  LIMIT 1
");
if(!$st){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"PREP_TPL_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE); exit; }
$st->bind_param("i",$template_id);
$st->execute();
$tpl = $st->get_result()->fetch_assoc();
if(!$tpl){
  http_response_code(404);
  echo json_encode(["ok"=>false,"error"=>"TEMPLATE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

/*
  4) إذا الشركة كانت ناسخة نفس template من نفس path قبل -> رجّع الموجود (بدون تكرار)
*/
$st = $conn->prepare("
  SELECT id
  FROM partner_playlists
  WHERE partner_user_id=? AND template_playlist_id=? AND source_path_id=?
  LIMIT 1
");
if($st){
  $st->bind_param("iii",$user_id,$template_id,$path_id);
  $st->execute();
  $ex = $st->get_result()->fetch_assoc();
  if($ex){
    echo json_encode(["ok"=>true,"new_playlist_id"=>(int)$ex["id"],"already"=>true], JSON_UNESCAPED_UNICODE);
    exit;
  }
}

/*
  5) اعمل slug فريد
*/
$base = strtolower(trim($tpl["name"] ?? "playlist"));
$base = preg_replace('/[^a-z0-9]+/i', '-', $base);
$base = trim($base,'-');
if($base==="") $base = "playlist";
$slug = $base . "-c" . $user_id . "-t" . $template_id . "-" . time();

/*
  6) انسخ السجل للشركة (نسخة قابلة لرفع فيديوهات)
*/
$name             = (string)($tpl["name"] ?? "");
$description      = (string)($tpl["description"] ?? "");
$expected         = (int)($tpl["expected_lectures"] ?? 0);
$difficulty       = (int)($tpl["difficulty"] ?? 0);
// ✅ coin_pool لازم يكون حسب الـ path (لأن نفس الـ template ممكن يكون داخل أكثر من Path)
// نحاول نقرأه من learning_path_playlists.coin_pool، وإذا مش موجود نرجع لقيمة الـ template نفسها.
$coin_pool = 0;
$stC = $conn->prepare("SELECT coin_pool FROM learning_path_playlists WHERE path_id=? AND template_playlist_id=? LIMIT 1");
if($stC){
  $stC->bind_param("ii", $path_id, $template_id);
  $stC->execute();
  $rowC = $stC->get_result()->fetch_assoc();
  $coin_pool = (int)($rowC["coin_pool"] ?? 0);
  $stC->close();
}
if($coin_pool <= 0){
  $coin_pool = (int)($tpl["coin_pool"] ?? 0);
}
$is_published     = 0;
$published_at     = null;
$major_text       = (string)($tpl["major_text"] ?? "");
$course_name      = (string)($tpl["course_name"] ?? "");
$cover_path       = (string)($tpl["cover_path"] ?? "");
$template_subject = (string)($tpl["template_subject"] ?? "");

$st = $conn->prepare("
  INSERT INTO partner_playlists
  (partner_user_id, name, slug, created_at, description, expected_lectures, difficulty, coin_pool,
   is_published, published_at, major_text, course_name, cover_path,
   path_id, is_template, template_subject,
   source_template_playlist_id, source_path_id, template_playlist_id)
  VALUES
  (?, ?, ?, NOW(), ?, ?, ?, ?,
   ?, ?, ?, ?, ?,
   ?, 0, ?,
   ?, ?, ?)
");
if(!$st){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"PREP_INSERT_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$published_at = null;

$st->bind_param(
  "isssiiiissssisiii",
  $user_id, $name, $slug, $description,
  $expected, $difficulty, $coin_pool,
  $is_published, $published_at,
  $major_text, $course_name, $cover_path,
  $path_id, $template_subject,
  $template_id, $path_id, $template_id
);

$st->execute();
$new_id = (int)$conn->insert_id;
// ✅ اربط النسخة الجديدة بالشركة و بالـ path داخل company_path_playlists
$st2 = $conn->prepare("
  INSERT INTO company_path_playlists (company_id, path_id, template_playlist_id, new_playlist_id, created_at)
  VALUES (?, ?, ?, ?, NOW())
  ON DUPLICATE KEY UPDATE new_playlist_id=VALUES(new_playlist_id)
");
if($st2){
  $st2->bind_param("iiii", $partner_id, $path_id, $template_id, $new_id);
  $st2->execute();
  $st2->close();
}
echo json_encode(["ok"=>true,"new_playlist_id"=>$new_id], JSON_UNESCAPED_UNICODE);

// ملاحظة: bind_param ما بيقبل مسافات بالأنواع، فصححها: