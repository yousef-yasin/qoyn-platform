<?php
require_once __DIR__ . "/../db.php";
require_login();

// PHP7 compatibility (for XAMPP/PHP 7.x)
if (!function_exists("str_contains")) {
  function str_contains($haystack, $needle) { return $needle === "" || strpos($haystack, $needle) !== false; }
}
if (!function_exists("str_starts_with")) {
  function str_starts_with($haystack, $needle) { return $needle === "" || substr($haystack, 0, strlen($needle)) === $needle; }
}

function ensure_ai_tables(mysqli $conn) {
  // user_settings (term credits per user)
  $conn->query("CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT UNSIGNED PRIMARY KEY,
    term_credits INT NOT NULL DEFAULT 15,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  // plan_analysis (latest analysis per user)
  $conn->query("CREATE TABLE IF NOT EXISTS plan_analysis (
    user_id INT UNSIGNED PRIMARY KEY,
    source_attachment_id INT UNSIGNED NULL,
    analysis_json MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

ensure_ai_tables($conn);

$user_id = (int)$_SESSION["user_id"];

$q = $conn->prepare("SELECT analysis_json, updated_at FROM plan_analysis WHERE user_id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
$row = $r->fetch_assoc();
$q->close();

if (!$row) {
  json_out(["ok"=>false,"error"=>"NO_ANALYSIS","msg"=>"اعمل تحليل للخطة أولاً"], 404);
}

$data = json_decode($row["analysis_json"], true);
json_out(["ok"=>true,"data"=>$data,"updated_at"=>$row["updated_at"]]);
