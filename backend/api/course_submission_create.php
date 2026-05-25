<?php
// utbn-backend/api/course_submission_create.php
header("Content-Type: application/json; charset=utf-8");

$secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
if (session_status() === PHP_SESSION_NONE) {
  if (defined("PHP_VERSION_ID") && PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      "lifetime" => 0,
      "path"     => "/",
      "secure"   => $secure,
      "httponly" => true,
      "samesite" => "Lax",
    ]);
  } else {
    session_set_cookie_params(0, "/");
  }
  session_start();
}

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ DB include مثل ملفاتك
$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null;
foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}
require_once $found;

// عندك بعض الملفات تستخدم $conn (mysqli)
$user_id = (int)$_SESSION["user_id"];

$title = trim((string)($_POST["course_title"] ?? ""));
$desc  = trim((string)($_POST["description"] ?? ""));

if ($title === "" || $desc === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_FIELDS"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ رفع ملف (اختياري)
$filePath = null;
$fileMime = null;
$fileSize = 0;

// الحد الأقصى (مثلاً 25MB)
$MAX = 25 * 1024 * 1024;

if (isset($_FILES["file"]) && $_FILES["file"]["error"] !== UPLOAD_ERR_NO_FILE) {
  if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"UPLOAD_ERROR"], JSON_UNESCAPED_UNICODE);
    exit;
  }

  $tmp  = $_FILES["file"]["tmp_name"];
  $size = (int)$_FILES["file"]["size"];
  $name = (string)($_FILES["file"]["name"] ?? "");

  if ($size <= 0 || $size > $MAX) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"FILE_TOO_LARGE"], JSON_UNESCAPED_UNICODE);
    exit;
  }

  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $tmp);
  finfo_close($finfo);

  // ✅ سماح PDF + صور فقط
  $allowed = [
    "application/pdf" => "pdf",
    "image/png"       => "png",
    "image/jpeg"      => "jpg",
    "image/webp"      => "webp",
  ];
  if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(["ok"=>false,"error"=>"INVALID_FILE_TYPE","mime"=>$mime], JSON_UNESCAPED_UNICODE);
    exit;
  }

  $ext = $allowed[$mime];

  // ✅ مكان التخزين داخل utbn-backend/uploads
  $dir = __DIR__ . "/../uploads/course_submissions";
  if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
  }

  $safeBase = preg_replace("/[^a-zA-Z0-9_\-]/", "_", pathinfo($name, PATHINFO_FILENAME));
  if ($safeBase === "") $safeBase = "file";

  $newName = "u{$user_id}_" . date("Ymd_His") . "_" . bin2hex(random_bytes(6)) . "_" . $safeBase . "." . $ext;
  $destAbs = $dir . "/" . $newName;

  if (!move_uploaded_file($tmp, $destAbs)) {
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>"MOVE_FAILED"], JSON_UNESCAPED_UNICODE);
    exit;
  }

  // ✅ المسار اللي رح تخزّنه بالداتا بيس (نسبي)
  $filePath = "uploads/course_submissions/" . $newName;
  $fileMime = $mime;
  $fileSize = $size;
}

// ✅ إدخال بالداتا بيس
$stmt = $conn->prepare("INSERT INTO course_submissions (user_id, course_title, description, file_path, file_mime, file_size) VALUES (?,?,?,?,?,?)");
if (!$stmt) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"STMT_PREPARE_FAILED"], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt->bind_param("issssi", $user_id, $title, $desc, $filePath, $fileMime, $fileSize);
$ok = $stmt->execute();
$id = $stmt->insert_id;
$stmt->close();

if (!$ok) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"DB_INSERT_FAILED"], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  "ok" => true,
  "id" => $id,
  "file_path" => $filePath
], JSON_UNESCAPED_UNICODE);