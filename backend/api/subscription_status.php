<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["user_id"])) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ مؤقتاً: رجّع بدون اشتراك دائماً (لحد ما تجهز جدول الاشتراكات)
echo json_encode(["ok"=>true, "active"=>false], JSON_UNESCAPED_UNICODE);