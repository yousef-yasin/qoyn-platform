<?php
// utbn-backend/api/student_partner_major_playlists.php
session_start();
header("Content-Type: application/json; charset=utf-8");

$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["ok"=>false,"error"=>"DB_FILE_NOT_FOUND"], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

$major = trim((string)($_GET["major"] ?? ""));
if ($major === "") {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"MISSING_MAJOR"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ حوّل للتوحيد (عشان "Computer Science" و "computer science")
$major_lc = mb_strtolower($major, 'UTF-8');

// ✅ ملاحظة: انا ما بعرف أسماء جداولك 100%، فعملت "كشف" سريع
// جرّب جدول playlists لو موجود، وإلا جرّب partner_playlists لو موجود.
// عدّل أسماء الجداول/الأعمدة حسب الموجود عندك لو اختلفت.

$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) { while($row=$res->fetch_array()) { $tables[] = $row[0]; } }

$playlistTable = null;
foreach (["playlists","partner_playlists","partner_playlist","course_playlists"] as $t) {
  if (in_array($t, $tables, true)) { $playlistTable = $t; break; }
}

if (!$playlistTable) {
  http_response_code(500);
  echo json_encode(["ok"=>false,"error"=>"PLAYLIST_TABLE_NOT_FOUND","tables"=>$tables], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ حاول نعرف اسم عمود التخصص داخل جدول البلاي ليست
$cols = [];
$cres = $conn->query("SHOW COLUMNS FROM `$playlistTable`");
if ($cres) { while($r=$cres->fetch_assoc()) { $cols[] = $r["Field"]; } }

$majorCol = null;
foreach (["major","major_text","department","category","field","specialty"] as $c) {
  if (in_array($c, $cols, true)) { $majorCol = $c; break; }
}

// إذا ما في عمود تخصص بالجدول، احتمال عندك جدول ربط playlist_majors
// بس حالياً رح نوقف ونطبع الأعمدة عشان نعرف شو اسم العمود الصح.
if (!$majorCol) {
  http_response_code(500);
  echo json_encode([
    "ok"=>false,
    "error"=>"MAJOR_COLUMN_NOT_FOUND",
    "playlist_table"=>$playlistTable,
    "columns"=>$cols
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ حاول نعرف اسم عمود publish (اختياري)
$publishCol = null;
foreach (["is_published","published","status"] as $c) {
  if (in_array($c, $cols, true)) { $publishCol = $c; break; }
}

// ✅ استعلام
$sql = "SELECT * FROM `$playlistTable` WHERE LOWER(`$majorCol`) = ? ";
if ($publishCol) {
  // إذا status نصي، خليها published
  if ($publishCol === "status") $sql .= " AND `$publishCol`='published' ";
  else $sql .= " AND `$publishCol`=1 ";
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    "ok"=>false,
    "error"=>"PREPARE_FAILED",
    "mysql_error"=>$conn->error,
    "sql"=>$sql,
    "playlist_table"=>$playlistTable,
    "major_col"=>$majorCol
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt->bind_param("s", $major_lc);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
if ($result) {
  while($row = $result->fetch_assoc()) $items[] = $row;
}

echo json_encode([
  "ok"=>true,
  "playlist_table"=>$playlistTable,
  "major_col"=>$majorCol,
  "items"=>$items
], JSON_UNESCAPED_UNICODE);