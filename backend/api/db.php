<?php
// utbn-backend/api/db.php

mysqli_report(MYSQLI_REPORT_OFF);

// ===== Database =====
$host   = "127.0.0.1";
$port   = 3306;        // إذا MySQL عندك على 3307 غيّرها
$user   = "root";
$pass   = "";
$dbname = "utbn_db";   // غيّرها إذا اسم الداتابيس مختلف

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
  http_response_code(500);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode([
    "error" => "DB_CONNECTION_FAILED",
    "details" => $conn->connect_error
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$conn->set_charset("utf8mb4");

// ===== Sessions (FIX: cookie path must be / so utbn-web can read it) =====
if (session_status() === PHP_SESSION_NONE) {
  ini_set("session.use_strict_mode", 1);

  $secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");

  // PHP 7.3+ supports array options
  if (defined("PHP_VERSION_ID") && PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      "lifetime" => 0,
      "path"     => "/",
      "secure"   => $secure,
      "httponly" => true,
      "samesite" => "Lax",
    ]);
  } else {
    // Legacy fallback
    session_set_cookie_params(0, "/");
  }

  session_start();
}

// ===== JSON helper =====
if (!function_exists("json_out")) {
  function json_out($data, int $code = 200) {
    http_response_code($code);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
  }
}

// ===== Auth guard =====
if (!function_exists("require_login")) {
  function require_login() {
    if (!isset($_SESSION["user_id"])) {
      json_out(["error" => "NOT_LOGGED_IN"], 401);
    }
  }
}
