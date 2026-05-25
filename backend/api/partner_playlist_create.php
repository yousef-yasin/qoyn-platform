<?php
// utbn-backend/api/partner_playlist_create.php
require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  json_out(["ok" => false, "error" => "METHOD_NOT_ALLOWED"], 405);
}

$in = json_decode(file_get_contents("php://input"), true) ?: [];
$name = trim((string)($in["name"] ?? ""));
$description = trim((string)($in["description"] ?? ""));
$expected_lectures = (int)($in["expected_lectures"] ?? 0);
$difficulty = (int)($in["difficulty"] ?? 0);

// ✅ NEW
$major_text  = trim((string)($in["major_text"] ?? ""));
$course_name = trim((string)($in["course_name"] ?? ""));


if ($name === "") {
  json_out(["ok" => false, "error" => "MISSING_NAME"], 400);
}
if ($description === "") {
  json_out(["ok" => false, "error" => "MISSING_DESCRIPTION"], 400);
}
if ($expected_lectures <= 0) {
  json_out(["ok" => false, "error" => "BAD_EXPECTED_LECTURES"], 400);
}
if ($difficulty < 0 || $difficulty > 100) {
  json_out(["ok" => false, "error" => "BAD_DIFFICULTY"], 400);
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

// ✅ slug من الاسم (لو عربي ممكن يطلع فاضي)
$slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));

// ✅ fallback لو الاسم عربي (slug ثابت حسب الاسم)
if ($slug === "") {
  $slug = "pl-" . substr(md5(mb_strtolower($name, "UTF-8")), 0, 10);
}

// ===== ensure schema (بدون ما نكسر القديم) =====
$conn->query("CREATE TABLE IF NOT EXISTS partner_playlists (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  partner_user_id INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_partner_slug (partner_user_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// أعمدة التوسعة (لو موجودة ما بنوقع Error)
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN description MEDIUMTEXT NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN expected_lectures INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN difficulty INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN coin_pool INT NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 0");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN published_at DATETIME NULL");
// ✅ NEW
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN major_text VARCHAR(220) NULL");
@$conn->query("ALTER TABLE partner_playlists ADD COLUMN course_name VARCHAR(220) NULL");

// ✅ منع تكرار الاسم أو الـ slug
$chk = $conn->prepare("SELECT id FROM partner_playlists
  WHERE partner_user_id=? AND (slug=? OR LOWER(TRIM(name)) = LOWER(TRIM(?)))
  LIMIT 1");
$chk->bind_param("iss", $user_id, $slug, $name);
$chk->execute();
$exists = $chk->get_result()->fetch_assoc();
$chk->close();

if ($exists) {
  json_out(["ok" => false, "error" => "PLAYLIST_EXISTS"], 409);
}

// ✅ إدخال (مع الحقول الجديدة)
$st = $conn->prepare("INSERT INTO partner_playlists
  (partner_user_id, name, slug, description, expected_lectures, difficulty, major_text, course_name, is_published)
  VALUES (?,?,?,?,?,?,?,?,0)");
if (!$st) {
  json_out(["ok"=>false,"error"=>"SQL_PREPARE_FAILED","details"=>$conn->error], 500);
}

$st->bind_param("isssiiss", $user_id, $name, $slug, $description, $expected_lectures, $difficulty, $major_text, $course_name);

if (!$st->execute()) {
  $err = $st->error;
  $st->close();
  json_out(["ok" => false, "error" => "DB_INSERT_FAILED", "details" => $err], 500);
}

$id = (int)$st->insert_id;
$st->close();

json_out([
  "ok" => true,
  "id" => $id,
  "name" => $name,
  "slug" => $slug,
  "description" => $description,
  "expected_lectures" => $expected_lectures,
  "difficulty" => $difficulty
]);
