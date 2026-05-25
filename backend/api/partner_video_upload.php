<?php
// utbn-backend/api/partner_video_upload.php
require_once __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok"=>false, "error"=>"METHOD_NOT_ALLOWED"], 405);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

// ---- playlist ----
$playlist_id = (int)($_POST["playlist_id"] ?? 0);
if ($playlist_id <= 0) {
  json_out(["ok"=>false, "error"=>"INVALID_PLAYLIST_ID"], 400);
}

// تأكد إن الملف (playlist) ملك الشريك
$chk = $conn->prepare("SELECT id, name FROM partner_playlists WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $playlist_id, $user_id);
$chk->execute();
$pl = $chk->get_result()->fetch_assoc();
$chk->close();

if (!$pl) {
  json_out(["ok"=>false, "error"=>"PLAYLIST_NOT_OWNED"], 403);
}

// ---- title ----
$title = trim((string)($_POST["title"] ?? ""));
if ($title === "") {
  json_out(["ok"=>false, "error"=>"MISSING_TITLE"], 400);
}

// ---- file ----
if (!isset($_FILES["video"]) || ($_FILES["video"]["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
  json_out(["ok"=>false, "error"=>"VIDEO_FILE_MISSING_OR_ERROR"], 400);
}

$tmp  = $_FILES["video"]["tmp_name"];
$orig = basename($_FILES["video"]["name"] ?? "video.mp4");

// Allowed extensions
$ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
$allowed = ["mp4","webm","mov","m4v","avi","mkv"];
if (!in_array($ext, $allowed, true)) {
  json_out(["ok"=>false, "error"=>"INVALID_VIDEO_EXTENSION", "allowed"=>$allowed], 400);
}

// Ensure upload dir
$dir = realpath(__DIR__ . "/.."); // utbn-backend root
$uploadRel = "uploads/videos";
$uploadAbs = $dir . DIRECTORY_SEPARATOR . $uploadRel;

if (!is_dir($uploadAbs)) {
  @mkdir($uploadAbs, 0777, true);
}
if (!is_dir($uploadAbs)) {
  json_out(["ok"=>false, "error"=>"UPLOAD_DIR_CREATE_FAILED", "path"=>$uploadAbs], 500);
}

// Unique filename
$safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($orig, PATHINFO_FILENAME));
if ($safeBase === "") $safeBase = "video";

$filename = $safeBase . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $ext;
$targetAbs = $uploadAbs . DIRECTORY_SEPARATOR . $filename;

// Move file
if (!move_uploaded_file($tmp, $targetAbs)) {
  json_out(["ok"=>false, "error"=>"MOVE_UPLOAD_FAILED"], 500);
}

// stored_path: relative to utbn-backend root (عشان /utbn-backend/ + stored_path)
$stored_path = $uploadRel . "/" . $filename;
// ---- optional cover image ----
$cover_path = null;

if (isset($_FILES["cover_image"]) && ($_FILES["cover_image"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {

  $ctmp  = $_FILES["cover_image"]["tmp_name"];
  $corig = basename($_FILES["cover_image"]["name"] ?? "cover.png");

  $cext = strtolower(pathinfo($corig, PATHINFO_EXTENSION));
  $allowed_img = ["png","jpg","jpeg","webp"];
  if (!in_array($cext, $allowed_img, true)) {
    json_out(["ok"=>false, "error"=>"INVALID_COVER_EXTENSION", "allowed"=>$allowed_img], 400);
  }

  // cover upload dir
  $coverRel = "uploads/covers";
  $coverAbs = $dir . DIRECTORY_SEPARATOR . $coverRel;
  if (!is_dir($coverAbs)) { @mkdir($coverAbs, 0777, true); }
  if (!is_dir($coverAbs)) {
    json_out(["ok"=>false, "error"=>"COVER_DIR_CREATE_FAILED", "path"=>$coverAbs], 500);
  }

  $csafeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($corig, PATHINFO_FILENAME));
  if ($csafeBase === "") $csafeBase = "cover";

  $cfilename = $csafeBase . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $cext;
  $ctargetAbs = $coverAbs . DIRECTORY_SEPARATOR . $cfilename;

  if (!move_uploaded_file($ctmp, $ctargetAbs)) {
    json_out(["ok"=>false, "error"=>"MOVE_COVER_FAILED"], 500);
  }

  // stored relative path
  $cover_path = $coverRel . "/" . $cfilename;
}

// Insert into partner_videos
// (نفترض جدول partner_videos موجود عندك مثل باقي الملفات)
$st = $conn->prepare("
  INSERT INTO partner_videos (partner_user_id, playlist_id, title, stored_path, cover_path, created_at)
VALUES (?, ?, ?, ?, ?, NOW())

");
if (!$st) {
  json_out(["ok"=>false, "error"=>"PREPARE_FAILED", "details"=>$conn->error], 500);
}

$st->bind_param("iisss", $user_id, $playlist_id, $title, $stored_path, $cover_path);

if (!$st->execute()) {
  $err = $st->error;
  $st->close();
  json_out(["ok"=>false, "error"=>"DB_INSERT_FAILED", "details"=>$err], 500);
}

$video_id = (int)$st->insert_id;
$st->close();
// ---- cover image (optional) ----
$cover_path = null;

if (isset($_FILES["cover_image"]) && ($_FILES["cover_image"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {

  $ctmp  = $_FILES["cover_image"]["tmp_name"];
  $corig = basename($_FILES["cover_image"]["name"] ?? "cover.jpg");

  $cext = strtolower(pathinfo($corig, PATHINFO_EXTENSION));
  $coverAllowed = ["jpg","jpeg","png","webp","gif"];
  if (!in_array($cext, $coverAllowed, true)) {
    // اذا امتداد غلط: ما منوقف رفع الفيديو، بس منطنّش الغلاف
    $cext = "jpg";
  }

  // Ensure cover upload dir
  $coverRel = "uploads/covers";
  $coverAbs = $dir . DIRECTORY_SEPARATOR . $coverRel;

  if (!is_dir($coverAbs)) {
    @mkdir($coverAbs, 0777, true);
  }

  if (is_dir($coverAbs)) {
    $safeCoverBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($corig, PATHINFO_FILENAME));
    if ($safeCoverBase === "") $safeCoverBase = "cover";

    $coverFilename = $safeCoverBase . "_" . $video_id . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $cext;
    $coverTargetAbs = $coverAbs . DIRECTORY_SEPARATOR . $coverFilename;

    if (move_uploaded_file($ctmp, $coverTargetAbs)) {
      $cover_path = $coverRel . "/" . $coverFilename;

      // update row
      $up = $conn->prepare("UPDATE partner_videos SET cover_path=? WHERE id=? AND partner_user_id=? LIMIT 1");
      if ($up) {
        $up->bind_param("sii", $cover_path, $video_id, $user_id);
        $up->execute();
        $up->close();
      }
    }
  }
}

json_out([
  "ok" => true,
  "video" => [
"cover_path" => $cover_path,
    "id" => $video_id,
    "title" => $title,
    "stored_path" => $stored_path,
    "playlist_id" => $playlist_id,
    "playlist_name" => $pl["name"] ?? ""
  ]
]);
