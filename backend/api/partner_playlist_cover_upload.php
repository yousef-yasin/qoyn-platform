<?php
require_once __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$playlist_id = (int)($_POST["playlist_id"] ?? 0);
if ($playlist_id <= 0) json_out(["ok"=>false,"error"=>"MISSING_PLAYLIST_ID"], 400);

$chk = $conn->prepare("SELECT id FROM partner_playlists WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $playlist_id, $user_id);
$chk->execute();
$pl = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$pl) json_out(["ok"=>false,"error"=>"PLAYLIST_NOT_OWNED"], 403);

if (!isset($_FILES["cover_image"]) || ($_FILES["cover_image"]["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
  json_out(["ok"=>false,"error"=>"COVER_FILE_MISSING_OR_ERROR"], 400);
}

$tmp  = $_FILES["cover_image"]["tmp_name"];
$orig = basename($_FILES["cover_image"]["name"] ?? "cover.jpg");

$ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
$allowed = ["jpg","jpeg","png","webp"];
if (!in_array($ext, $allowed, true)) {
  json_out(["ok"=>false,"error"=>"INVALID_IMAGE_EXTENSION","allowed"=>$allowed], 400);
}

$dir = realpath(__DIR__ . "/.."); // utbn-backend root
$uploadRel = "uploads/playlist_covers";
$uploadAbs = $dir . DIRECTORY_SEPARATOR . $uploadRel;

if (!is_dir($uploadAbs)) @mkdir($uploadAbs, 0777, true);
if (!is_dir($uploadAbs)) json_out(["ok"=>false,"error"=>"UPLOAD_DIR_CREATE_FAILED","path"=>$uploadAbs], 500);

$safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($orig, PATHINFO_FILENAME));
if ($safeBase === "") $safeBase = "cover";

$filename = $safeBase . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $ext;
$targetAbs = $uploadAbs . DIRECTORY_SEPARATOR . $filename;

if (!move_uploaded_file($tmp, $targetAbs)) {
  json_out(["ok"=>false,"error"=>"MOVE_UPLOAD_FAILED"], 500);
}

$stored_path = $uploadRel . "/" . $filename;
// ✅ مهم: توحيد السلاشات (لو صار أي backslash)
$stored_path = str_replace("\\", "/", $stored_path);
// تأكد العمود موجود
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN cover_path VARCHAR(255) NULL");

$up = $conn->prepare("UPDATE partner_playlists SET cover_path=? WHERE id=? AND partner_user_id=?");
$up->bind_param("sii", $stored_path, $playlist_id, $user_id);
if (!$up->execute()) {
  $err = $up->error;
  $up->close();
  json_out(["ok"=>false,"error"=>"DB_UPDATE_FAILED","details"=>$err], 500);
}
$up->close();
$base = ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http")
      . "://" . $_SERVER["HTTP_HOST"] . "/utbn-backend/";

$cover_url = $stored_path !== "" ? $base . ltrim($stored_path, "/") : "";
json_out(["ok"=>true,"playlist_id"=>$playlist_id,"cover_path"=>$stored_path,"cover_url"=>$cover_url]);