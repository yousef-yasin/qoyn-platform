<?php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$video_id = (int)($_POST["video_id"] ?? 0);

if ($video_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_VIDEO_ID"], 400);
if (!isset($_FILES["cover"]) || $_FILES["cover"]["error"] !== UPLOAD_ERR_OK) {
  json_out(["ok"=>false,"error"=>"MISSING_COVER_FILE"], 400);
}

// تأكد الفيديو لصاحب الحساب
$st = $conn->prepare("SELECT id FROM partner_videos WHERE id=? AND partner_user_id=? LIMIT 1");
$st->bind_param("ii", $video_id, $user_id);
$st->execute();
$ok = $st->get_result()->fetch_assoc();
$st->close();
if (!$ok) json_out(["ok"=>false,"error"=>"VIDEO_NOT_FOUND"], 404);

// حفظ الملف
$dir = __DIR__ . "/../uploads/video_covers";
if (!is_dir($dir)) @mkdir($dir, 0777, true);

$tmp = $_FILES["cover"]["tmp_name"];
$orig = basename($_FILES["cover"]["name"]);
$ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
$allowed = ["jpg","jpeg","png","webp"];
if (!in_array($ext, $allowed, true)) json_out(["ok"=>false,"error"=>"BAD_IMAGE_TYPE"], 400);

$fname = "video_{$video_id}_" . date("Ymd_His") . "_" . bin2hex(random_bytes(6)) . "." . $ext;
$pathAbs = $dir . "/" . $fname;
if (!move_uploaded_file($tmp, $pathAbs)) {
  json_out(["ok"=>false,"error"=>"UPLOAD_FAILED"], 500);
}

$cover_path = "uploads/video_covers/" . $fname;
$cover_path = str_replace("\\", "/", $cover_path);
// تحديث قاعدة البيانات
$up = $conn->prepare("UPDATE partner_videos SET cover_path=? WHERE id=? AND partner_user_id=?");
$up->bind_param("sii", $cover_path, $video_id, $user_id);
if (!$up->execute()) {
  $err = $conn->error;
  $up->close();
  json_out(["ok"=>false,"error"=>"DB_UPDATE_FAILED","details"=>$err], 500);
}
$up->close();
$base = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http")
      . "://" . $_SERVER["HTTP_HOST"] . "/utbn-backend/";
$cover_url = $cover_path !== "" ? $base . ltrim($cover_path, "/") : "";
json_out(["ok"=>true, "video_id"=>$video_id, "cover_path"=>$cover_path, "cover_url"=>$cover_url]);