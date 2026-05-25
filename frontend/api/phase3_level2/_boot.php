<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../../utbn-backend/api/db.php";
if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok" => false, "error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

function json_out($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}