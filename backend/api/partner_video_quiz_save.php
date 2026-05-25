<?php
// utbn-backend/api/partner_video_quiz_save.php
session_start();
header("Content-Type: application/json; charset=utf-8");

// حاول تلاقي ملف DB (عشان اختلاف المسارات بمشروعك)
$try = [__DIR__."/db.php", __DIR__."/../db.php", __DIR__."/../config/db.php", __DIR__."/../includes/db.php"];
$found = null; foreach ($try as $p) { if (file_exists($p)) { $found = $p; break; } }
if (!$found) { http_response_code(500); echo json_encode(["error"=>"DB_FILE_NOT_FOUND","tried"=>$try], JSON_UNESCAPED_UNICODE); exit; }
require_once $found;

if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["error" => "NOT_LOGGED_IN"], JSON_UNESCAPED_UNICODE); exit; }
$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") { http_response_code(405); echo json_encode(["error" => "METHOD_NOT_ALLOWED"], JSON_UNESCAPED_UNICODE); exit; }

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) { http_response_code(400); echo json_encode(["error" => "INVALID_JSON"], JSON_UNESCAPED_UNICODE); exit; }

$video_id = (int)($data["video_id"] ?? 0);
$quiz = $data["quiz"] ?? null;

if ($video_id <= 0 || !is_array($quiz)) {
  http_response_code(400);
  echo json_encode(["error" => "MISSING_FIELDS"], JSON_UNESCAPED_UNICODE);
  exit;
}

// ensure table exists (آخر نسخة للأسئلة)
$conn->query("CREATE TABLE IF NOT EXISTS partner_video_quizzes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  partner_user_id INT UNSIGNED NOT NULL,
  partner_video_id INT UNSIGNED NOT NULL,
  quiz_json MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pvq_video (partner_video_id),
  KEY idx_pvq_partner_video (partner_user_id, partner_video_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// تأكد الفيديو تابع لنفس الشريك
$chk = $conn->prepare("SELECT id FROM partner_videos WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $video_id, $user_id);
$chk->execute();
$res = $chk->get_result();
$owned = ($res && $res->num_rows > 0);
$chk->close();

if (!$owned) { http_response_code(403); echo json_encode(["error" => "VIDEO_NOT_OWNED"], JSON_UNESCAPED_UNICODE); exit; }

// ✅ حذف الأسئلة (لو الواجهة شالت النوع)
if (count($quiz) === 0) {
  $del = $conn->prepare("DELETE FROM partner_video_quizzes WHERE partner_video_id=? AND partner_user_id=?");
  $del->bind_param("ii", $video_id, $user_id);
  $del->execute();
  $del->close();

  echo json_encode(["ok"=>true, "deleted"=>true], JSON_UNESCAPED_UNICODE);
  exit;
}

// ✅ تحقق مرن: MCQ (4 خيارات) أو Circle (خيارين)
foreach ($quiz as $i => $q) {
  if (!is_array($q)) { http_response_code(400); echo json_encode(["error"=>"BAD_QUIZ_ITEM","index"=>$i], JSON_UNESCAPED_UNICODE); exit; }

  $question = trim((string)($q["question"] ?? ""));
  $options = $q["options"] ?? [];

  if ($question === "" || !is_array($options)) {
    http_response_code(400);
    echo json_encode(["error"=>"BAD_QUIZ_FORMAT","index"=>$i], JSON_UNESCAPED_UNICODE);
    exit;
  }

  $optCount = count($options);

  // MCQ
  if ($optCount === 4) {
    $correct = strtoupper(trim((string)($q["correct"] ?? ""))); // A/B/C/D
    if (!in_array($correct, ["A","B","C","D"], true)) {
      http_response_code(400);
      echo json_encode(["error"=>"BAD_QUIZ_FORMAT","index"=>$i], JSON_UNESCAPED_UNICODE);
      exit;
    }
    continue;
  }

  // Circle (A/B أو correct_bool)
  if ($optCount === 2) {
    if (isset($q["correct_bool"])) {
      if (!is_bool($q["correct_bool"])) {
        http_response_code(400);
        echo json_encode(["error"=>"BAD_QUIZ_FORMAT","index"=>$i], JSON_UNESCAPED_UNICODE);
        exit;
      }
    } else {
      $correct = strtoupper(trim((string)($q["correct"] ?? ""))); // A/B
      if (!in_array($correct, ["A","B"], true)) {
        http_response_code(400);
        echo json_encode(["error"=>"BAD_QUIZ_FORMAT","index"=>$i], JSON_UNESCAPED_UNICODE);
        exit;
      }
    }
    continue;
  }

  http_response_code(400);
  echo json_encode(["error"=>"BAD_QUIZ_FORMAT","index"=>$i], JSON_UNESCAPED_UNICODE);
  exit;
}

$quiz_json = json_encode($quiz, JSON_UNESCAPED_UNICODE);

$stmt = $conn->prepare("INSERT INTO partner_video_quizzes (partner_user_id, partner_video_id, quiz_json) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $video_id, $quiz_json);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["error" => "DB_INSERT_FAILED", "details" => $stmt->error], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode(["ok" => true, "quiz_id" => (int)$stmt->insert_id], JSON_UNESCAPED_UNICODE);
