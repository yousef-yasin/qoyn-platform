<?php
header("Content-Type: application/json; charset=utf-8");
http_response_code(410);
echo json_encode([
  "ok"=>false,
  "error"=>"GEMINI_DISABLED",
  "message"=>"AI extraction disabled. Use extract_user_skills.php (local matching)."
], JSON_UNESCAPED_UNICODE);