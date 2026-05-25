<?php
require __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];

$full_name = trim((string)($data["full_name"] ?? ""));
$email     = trim(strtolower((string)($data["email"] ?? "")));
$phone     = trim((string)($data["phone"] ?? ""));
$password  = (string)($data["password"] ?? "");

// اختياري: major_id لو بدك تختاره من الواجهة
$major_id  = (int)($data["major_id"] ?? 1);

if ($full_name === "" || $email === "" || $password === "") {
  json_out(["ok" => false, "error" => "MISSING_FIELDS"], 400);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$conn->begin_transaction();

try {
  // يمنع نفس الإيميل ينحجز لنوع ثاني
  $stmt0 = $conn->prepare("INSERT INTO auth_emails (email, account_type) VALUES (?, 'student')");
  $stmt0->bind_param("s", $email);
  if (!$stmt0->execute()) throw new Exception("AUTH_EMAILS_INSERT_FAILED");

  // users (بدون role)
  $stmt = $conn->prepare("
    INSERT INTO users (full_name, email, phone, password_hash)
    VALUES (?,?,?,?)
  ");
$stmt->bind_param("ssss", $full_name, $email, $phone, $hash);

if (!$stmt->execute()) {
  throw new Exception("USERS_INSERT_FAILED");
}

$new_user_id = (int)$conn->insert_id;


  // ✅ أنشئ student profile عشان coins تشتغل
  $sp = $conn->prepare("INSERT INTO student_profiles (user_id, major_id, level, coins_total) VALUES (?,?,1,0)");
  $sp->bind_param("ii", $new_user_id, $major_id);
  if (!$sp->execute()) throw new Exception("PROFILE_INSERT_FAILED");

  $conn->commit();
  json_out(["ok" => true]);

} catch (Exception $e) {
  $conn->rollback();

  if ($conn->errno === 1062) {
    json_out(["ok" => false, "error" => "EMAIL_EXISTS_ANY"], 409);
  }

  json_out(["ok" => false, "error" => "SIGNUP_FAILED"], 500);
}
