<?php
session_start();

$email = $_SESSION['email'] ?? '';
if (!$email) { header("Location: login.html"); exit; }

$type = $_POST['type'] ?? '';
$allowed = ['tree','courses','certs'];
if (!in_array($type, $allowed)) die("نوع غير صحيح");

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  header("Location: index.php");
  exit;
}

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$okExt = ['jpg','jpeg','png','webp'];
if (!in_array($ext, $okExt)) die("ارفع صورة فقط (jpg/png/webp)");

$uploadsDir = __DIR__ . "/uploads";
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

$filename = md5($email) . "_" . $type . ".jpg";
$target = $uploadsDir . "/" . $filename;

move_uploaded_file($_FILES['image']['tmp_name'], $target);

header("Location: index.php");
exit;
