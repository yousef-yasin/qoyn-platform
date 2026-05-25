<?php
require_once __DIR__ . "/db.php";
header("Content-Type: application/json; charset=utf-8");

require_login();

$user_id = (int)$_SESSION["user_id"];
$playlist_id = (int)($_GET["playlist_id"] ?? 0);

if ($playlist_id <= 0) {
  http_response_code(400);
  echo json_encode(["error"=>"INVALID_PLAYLIST_ID"], JSON_UNESCAPED_UNICODE);
  exit;
}

// تأكد ان الملف ملك المستخدم
$chk = $conn->prepare("SELECT id FROM partner_playlists WHERE id=? AND partner_user_id=? LIMIT 1");
$chk->bind_param("ii", $playlist_id, $user_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows === 0) {
  http_response_code(403);
  echo json_encode(["error"=>"FORBIDDEN"], JSON_UNESCAPED_UNICODE);
  exit;
}

// هات الفيديوهات
$stmt = $conn->prepare("
  SELECT id, title, duration_seconds, created_at, original_name
  FROM partner_videos
  WHERE partner_user_id = ? AND playlist_id = ?
  ORDER BY id DESC
");
$stmt->bind_param("ii", $user_id, $playlist_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while ($row = $res->fetch_assoc()) {
  $items[] = [
    "id" => (int)$row["id"],
    "title" => (string)$row["title"],
    "duration_seconds" => (int)$row["duration_seconds"],
    "created_at" => (string)$row["created_at"],
    "original_name" => (string)($row["original_name"] ?? ""),
  ];
}

echo json_encode(["items"=>$items], JSON_UNESCAPED_UNICODE);
