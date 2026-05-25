<?php
require __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true) ?: [];

$company_name = trim((string)($data["company_name"] ?? $data["full_name"] ?? ""));
$email        = trim(strtolower((string)($data["email"] ?? "")));
$partner_type = trim((string)($data["partner_type"] ?? ""));
$phone        = trim((string)($data["phone"] ?? ""));
$password     = (string)($data["password"] ?? "");

if ($company_name === "" || $email === "" || $partner_type === "" || $password === "") {
  json_out(["ok" => false, "error" => "MISSING_FIELDS"], 400);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$conn->begin_transaction();

try {
  // 1) احجز الإيميل (يمنع يكون طالب/شريك بنفس الوقت)
  $stmt0 = $conn->prepare("INSERT INTO auth_emails (email, account_type) VALUES (?, 'partner')");
  if (!$stmt0) throw new Exception("PREPARE_AUTH_EMAILS_FAILED: " . $conn->error);
  $stmt0->bind_param("s", $email);

  if (!$stmt0->execute()) {
    if ($conn->errno === 1062) {
      $conn->rollback();
      json_out(["ok" => false, "error" => "EMAIL_EXISTS_ANY"], 409);
    }
    throw new Exception("AUTH_EMAILS_INSERT_FAILED: " . $conn->error);
  }

  // 2) أضف الشريك بجدول partners
  $stmt = $conn->prepare("
    INSERT INTO partners (company_name, email, partner_type, phone, password_hash)
    VALUES (?,?,?,?,?)
  ");
  if (!$stmt) throw new Exception("PREPARE_PARTNERS_FAILED: " . $conn->error);

  $stmt->bind_param("sssss", $company_name, $email, $partner_type, $phone, $hash);

  if (!$stmt->execute()) {
    if ($conn->errno === 1062) {
      $conn->rollback();
      json_out(["ok" => false, "error" => "EMAIL_EXISTS_ANY"], 409);
    }
    throw new Exception("PARTNERS_INSERT_FAILED: " . $conn->error);
  }

  // 3) الأهم: أضف كمان بجدول users عشان login.php يلاقيه
  $stmtU = $conn->prepare("
    INSERT INTO users (full_name, email, phone, password_hash, role)
    VALUES (?,?,?,?, 'partner')
  ");
  if (!$stmtU) throw new Exception("PREPARE_USERS_FAILED: " . $conn->error);

  $stmtU->bind_param("ssss", $company_name, $email, $phone, $hash);

  if (!$stmtU->execute()) {
    if ($conn->errno === 1062) {
      $conn->rollback();
      json_out(["ok" => false, "error" => "EMAIL_EXISTS_ANY"], 409);
    }
    throw new Exception("USERS_INSERT_FAILED: " . $conn->error);
  }

  $conn->commit();
  json_out(["ok" => true]);

} catch (Exception $e) {
  $conn->rollback();
  json_out(["ok" => false, "error" => "SIGNUP_FAILED", "details" => $e->getMessage()], 500);
}
