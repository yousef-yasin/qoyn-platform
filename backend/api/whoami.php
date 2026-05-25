<?php
require __DIR__ . "/db.php";
header("Content-Type: application/json; charset=utf-8");

echo json_encode([
  "session_status" => session_status(),
  "user_id" => $_SESSION["user_id"] ?? null,
  "session_id" => session_id()
], JSON_UNESCAPED_UNICODE);
