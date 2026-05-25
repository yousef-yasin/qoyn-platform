<?php
require __DIR__ . "/../db.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$user_id = (int)$_SESSION["user_id"];

$type  = $_POST["type"] ?? "";
$title = trim($_POST["title"] ?? "");

if ($type !== "plan" && $type !== "experience") {
  http_response_code(400);
  echo json_encode(["error" => "INVALID_TYPE"], JSON_UNESCAPED_UNICODE);
  exit;
}

if (!isset($_FILES["file"])) {
  http_response_code(400);
  echo json_encode(["error" => "NO_FILE"], JSON_UNESCAPED_UNICODE);
  exit;
}

// السماح فقط: PDF + صور
$allowed = [
  "application/pdf",
  "image/png",
  "image/jpeg",
  "image/webp"
];

// حد الحجم 10MB لكل ملف
$maxBytes = 10 * 1024 * 1024;

$uploadsDir = realpath(__DIR__ . "/../../uploads");
if (!$uploadsDir) {
  http_response_code(500);
  echo json_encode(["error" => "UPLOADS_DIR_NOT_FOUND"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ يدعم رفع ملف واحد أو عدة ملفات (file[])
$files = $_FILES["file"];
$isMulti = is_array($files["name"] ?? null);

$toProcess = [];
if ($isMulti) {
  $count = count($files["name"]);
  for ($i = 0; $i < $count; $i++) {
    $toProcess[] = [
      "name" => $files["name"][$i],
      "type" => $files["type"][$i] ?? "",
      "tmp_name" => $files["tmp_name"][$i],
      "error" => $files["error"][$i],
      "size" => (int)($files["size"][$i] ?? 0),
      "index" => $i
    ];
  }
} else {
  $toProcess[] = [
    "name" => $files["name"],
    "type" => $files["type"] ?? "",
    "tmp_name" => $files["tmp_name"],
    "error" => $files["error"],
    "size" => (int)($files["size"] ?? 0),
    "index" => 0
  ];
}

$inserted = [];
$errors = [];

// إذا المستخدم رفع خطة جديدة: امسح التحليل السابق مرّة واحدة قبل إدخال الملفات (عشان يبقى الملخص صحيح)
if ($type === "plan") {
  // قد لا يكون جدول plan_analysis موجوداً بعد — تجاهل الخطأ
  @$conn->query("DELETE FROM plan_analysis WHERE user_id=" . (int)$user_id);
  // كمان امسح القائمة السابقة للمساقات الإجباريّة (إذا موجودة)
  @$conn->query("DELETE FROM user_plan_courses WHERE user_id=" . (int)$user_id);
  @$conn->query("DELETE FROM user_plan_profile WHERE user_id=" . (int)$user_id);
}

$stmt = $conn->prepare("
  INSERT INTO student_attachments (user_id, type, title, file_path, original_name, mime_type, file_size)
  VALUES (?,?,?,?,?,?,?)
");

foreach ($toProcess as $f) {
  if (($f["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $errors[] = ["index"=>$f["index"], "error"=>"UPLOAD_ERROR", "code"=>$f["error"]];
    continue;
  }

  if (($f["size"] ?? 0) <= 0) {
    $errors[] = ["index"=>$f["index"], "error"=>"EMPTY_FILE"];
    continue;
  }

  if ($f["size"] > $maxBytes) {
    $errors[] = ["index"=>$f["index"], "error"=>"FILE_TOO_LARGE"];
    continue;
  }

  $mime = @mime_content_type($f["tmp_name"]);
  if (!$mime || !in_array($mime, $allowed, true)) {
    $errors[] = ["index"=>$f["index"], "error"=>"UNSUPPORTED_FILE_TYPE", "mime"=>$mime];
    continue;
  }

  $ext = pathinfo($f["name"], PATHINFO_EXTENSION);
  $ext = $ext ? "." . strtolower($ext) : "";
  $filename = $type . "_" . $user_id . "_" . time() . "_" . bin2hex(random_bytes(4)) . $ext;

  $relPath = "uploads/" . $filename;
  $dest = $uploadsDir . DIRECTORY_SEPARATOR . $filename;

  if (!move_uploaded_file($f["tmp_name"], $dest)) {
    $errors[] = ["index"=>$f["index"], "error"=>"MOVE_FAILED"];
    continue;
  }

  $orig = $f["name"];
  $size = (int)$f["size"];

  $stmt->bind_param("isssssi", $user_id, $type, $title, $relPath, $orig, $mime, $size);
  if (!$stmt->execute()) {
    $errors[] = ["index"=>$f["index"], "error"=>"DB_INSERT_FAILED"];
    continue;
  }

  $inserted[] = [
    "id" => (int)$stmt->insert_id,
    "file_path" => $relPath,
    "original_name" => $orig,
    "mime_type" => $mime,
    "file_size" => $size
  ];
}
$stmt->close();

echo json_encode([
  "ok" => true,
  "uploaded" => $inserted,
  "errors" => $errors
], JSON_UNESCAPED_UNICODE);
