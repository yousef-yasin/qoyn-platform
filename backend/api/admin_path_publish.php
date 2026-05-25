<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found=null; foreach($try as $p){ if(file_exists($p)){ $found=$p; break; } }
if(!$found){ http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"],JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
if (($_SESSION["role"] ?? "") !== "admin") { http_response_code(403); echo json_encode(["ok"=>false,"error"=>"FORBIDDEN"], JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE); exit; }

$path_id = (int)($_POST["path_id"] ?? 0);
$is_published = (int)($_POST["is_published"] ?? 0);
if($path_id<=0){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"MISSING_PATH_ID"], JSON_UNESCAPED_UNICODE); exit; }
$is_published = ($is_published===1) ? 1 : 0;

// ✅ ثابت: كل Path عليه 15000 coin تتوزّع على الـ playlists الموجودة داخله
$PATH_COIN_BUDGET = 15000;

// ✅ أهم نقطة: التوزيع لازم يكون على جدول learning_path_playlists (per path)
// لأن نفس template_playlist_id ممكن يكون مستخدم في أكثر من Path.

if ($is_published === 1) {
  // تأكد العمود موجود
  @$conn->query("ALTER TABLE learning_path_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");

  // 1) جيب كل صفوف lpp الخاصة بهذا الـ path (فقط اللي مربوطة بـ template playlists)
  $st = $conn->prepare("
    SELECT lpp.id AS lpp_id
    FROM learning_path_playlists lpp
    JOIN partner_playlists tp ON tp.id = lpp.template_playlist_id AND tp.is_template = 1
    WHERE lpp.path_id = ?
    ORDER BY lpp.sort_order ASC, lpp.id ASC
  ");
  if(!$st){
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"PREP_LPP_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
    exit;
  }
  $st->bind_param("i", $path_id);
  $st->execute();
  $rs = $st->get_result();
  $lppIds = [];
  while($row = $rs->fetch_assoc()){
    $lppIds[] = (int)$row["lpp_id"];
  }
  $st->close();

  // 2) صفّر coin_pool لهذا الـ path
  $rst = $conn->prepare("UPDATE learning_path_playlists SET coin_pool=0 WHERE path_id=?");
  if($rst){
    $rst->bind_param("i", $path_id);
    $rst->execute();
    $rst->close();
  }

  // 3) وزّع 15000 على عدد الـ playlists داخل الـ path
  $n = count($lppIds);
  if($n > 0){
    $base = intdiv($PATH_COIN_BUDGET, $n);
    $rem  = $PATH_COIN_BUDGET % $n;

    $up = $conn->prepare("UPDATE learning_path_playlists SET coin_pool=? WHERE id=? AND path_id=?");
    if(!$up){
      http_response_code(500);
      echo json_encode(["ok"=>false,"error"=>"PREP_UPDATE_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
      exit;
    }

    foreach($lppIds as $i => $lpp_id){
      $coins = $base + (($i < $rem) ? 1 : 0);
      $up->bind_param("iii", $coins, $lpp_id, $path_id);
      if(!$up->execute()){
        $err = $conn->error;
        $up->close();
        http_response_code(500);
        echo json_encode(["ok"=>false,"error"=>"UPDATE_FAILED","details"=>$err], JSON_UNESCAPED_UNICODE);
        exit;
      }
    }
    $up->close();
  }
}

// أخيراً: حدّث حالة النشر
$st2 = $conn->prepare("UPDATE learning_paths SET is_published=? WHERE id=?");
if(!$st2){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"PREP_PUBLISH_FAILED","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}
$st2->bind_param("ii", $is_published, $path_id);
if(!$st2->execute()){
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_ERROR","details"=>$conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}
$st2->close();

echo json_encode(["ok"=>true], JSON_UNESCAPED_UNICODE);
