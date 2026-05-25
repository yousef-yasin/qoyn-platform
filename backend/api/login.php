<?php
require_once __DIR__ . "/session_bootstrap.php";
require_once __DIR__ . "/csrf.php";
header("Content-Type: application/json; charset=utf-8");
require __DIR__ . "/db.php";
$raw = file_get_contents("php://input");
$data = json_decode($raw, true) ?: [];

$email    = trim(strtolower((string)($data["email"] ?? $_POST["email"] ?? "")));
$password = (string)($data["password"] ?? $_POST["password"] ?? "");

// لازم الواجهة تبعث type: student | partner
$type = strtolower(trim((string)($data["type"] ?? $_POST["type"] ?? "student")));
if ($type !== "student" && $type !== "partner") {
  json_out(["ok" => false, "error" => "INVALID_TYPE"], 400);
}

if ($email === "" || $password === "") {
  json_out(["ok" => false, "error" => "MISSING_FIELDS"], 400);
}

$ADMIN_EMAIL = "yousefruaa555@gmail.com";

if ($email === $ADMIN_EMAIL) {
  $stmt = $conn->prepare("SELECT id, full_name, password_hash, role FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param("s", $email);
} else {
  $stmt = $conn->prepare("SELECT id, full_name, password_hash, role FROM users WHERE email=? AND role=? LIMIT 1");
  $stmt->bind_param("ss", $email, $type);
}

$stmt->execute();
$res = $stmt->get_result();

if (!$row = $res->fetch_assoc()) {
  json_out(["ok" => false, "error" => "INVALID_LOGIN"], 401);
}

$hash = (string)$row["password_hash"];

// ✅ لا تفحص prefix $2y$ .. خلي password_verify يقرر
if (!password_verify($password, $hash)) {
  json_out(["ok" => false, "error" => "INVALID_LOGIN"], 401);
}

session_regenerate_id(true);

$_SESSION["user_id"] = (int)$row["id"];
$_SESSION["role"]    = (string)$row["role"];
$_SESSION["email"]   = $email;
$_SESSION["name"]    = (string)$row["full_name"];

$csrf_token = csrf_get_token();

// redirect
$redirect = "student-dashboard.php";
if (($_SESSION["role"] ?? "") === "admin") {
  $redirect = "dashboard.php";
} elseif (($_SESSION["role"] ?? "") === "partner") {
  $redirect = "company.php";
} else {
  $redirect = "student-dashboard.php";
}

json_out([
  "ok" => true,
  "role" => (string)$_SESSION["role"],
  "redirect" => $redirect,
  "csrf_token" => $csrf_token
]);
