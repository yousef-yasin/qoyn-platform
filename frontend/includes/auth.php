<?php
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

if (!function_exists("require_login_page")) {
  function require_login_page() {
    if (!isset($_SESSION["user_id"])) {
      header("Location: login.php");
      exit;
    }
  }
}

if (!function_exists("require_role_page")) {
  function require_role_page(string $role) {
    require_login_page();
    if ((string)($_SESSION["role"] ?? "") !== $role) {
      header("Location: login.php");
      exit;
    }
  }
}
