<?php
// utbn-backend/api/partner_me.php
session_start();
require_once __DIR__ . "/../config/db.php";
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

/**
 * اسم الشركة:
 * - جرّب company_name (إذا عندك)
 * - أو partner_name
 * - أو full_name
 * - أو email
 */
$company = $_SESSION["company_name"] ?? $_SESSION["partner_name"] ?? $_SESSION["full_name"] ?? $_SESSION["email"] ?? "Partner";

echo json_encode([
  "user_id" => (int)$_SESSION["user_id"],
  "company_name" => $company
], JSON_UNESCAPED_UNICODE);
