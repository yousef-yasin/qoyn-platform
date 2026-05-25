<?php
require_once __DIR__ . "/db.php";

// Returns the user's extracted major + required courses list (remaining) to drive courses.php
require_login();

$user_id = (int)($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) json_out(["error"=>"NO_SESSION"], 401);

// If the tables don't exist yet (first run), respond gracefully
$conn->query("CREATE TABLE IF NOT EXISTS user_plan_profile (
  user_id INT UNSIGNED PRIMARY KEY,
  major_name VARCHAR(200) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_upp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS user_plan_courses (
  user_id INT UNSIGNED NOT NULL,
  course_code VARCHAR(40) NOT NULL,
  course_name VARCHAR(220) NOT NULL,
  group_title VARCHAR(220) NULL,
  is_required TINYINT(1) NOT NULL DEFAULT 0,
  credits INT NOT NULL DEFAULT 0,
  PRIMARY KEY (user_id, course_code),
  KEY idx_upc_required (user_id, is_required),
  CONSTRAINT fk_upc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$major = "";
$q = $conn->prepare("SELECT major_name FROM user_plan_profile WHERE user_id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
if ($row = $r->fetch_assoc()) $major = (string)($row["major_name"] ?? "");
$q->close();

// Required courses
$courses = [];
$q = $conn->prepare("SELECT course_code, course_name, group_title, credits FROM user_plan_courses WHERE user_id=? AND is_required=1 ORDER BY group_title, course_code");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $courses[] = [
    "code" => (string)$row["course_code"],
    "name" => (string)$row["course_name"],
    "group_title" => (string)($row["group_title"] ?? ""),
    "credits" => (int)($row["credits"] ?? 0)
  ];
}
$q->close();

json_out(["ok"=>true, "major_name"=>$major, "required_courses"=>$courses]);
