<?php
require __DIR__ . "/db.php";
require_login();
require __DIR__ . "/_ensure_tracking_tables.php";
ensure_tracking_tables($conn);
header("Content-Type: application/json; charset=utf-8");

$user_id = (int)$_SESSION["user_id"];
$d = json_decode(file_get_contents("php://input"), true) ?: [];

// يقبل: videoId أو youtube_id أو video_id (سترينغ)
$video_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($d["videoId"] ?? $d["youtube_id"] ?? $d["video_id"] ?? ""));
if ($video_id === "") json_out(["ok"=>false,"error"=>"INVALID_VIDEO"], 400);

// يقبل نسبة watched مباشرة أو ثواني
$watched = null;
if (isset($d["watched"])) {
  $watched = (float)$d["watched"]; // 0..1
} else if (isset($d["watched_seconds"]) && isset($d["duration_seconds"])) {
  $dur = (float)$d["duration_seconds"];
  $cur = (float)$d["watched_seconds"];
  if ($dur > 0) $watched = $cur / $dur;
}

if ($watched === null) $watched = 0.0;
if ($watched < 0) $watched = 0;
if ($watched > 1) $watched = 1;

$is_completed = ($watched >= 0.5) ? 1 : 0;

// ✅ حفظ التقدم (مرة واحدة) + حفظ completed_at فقط عند أول اكتمال
$stmt = $conn->prepare("
  INSERT INTO video_progress (user_id, video_id, is_completed, completed_at)
  VALUES (?, ?, ?, IF(?, NOW(), NULL))
  ON DUPLICATE KEY UPDATE
    is_completed = GREATEST(is_completed, VALUES(is_completed)),
    completed_at = IF(
      GREATEST(is_completed, VALUES(is_completed)) = 1,
      IF(completed_at IS NULL, NOW(), completed_at),
      completed_at
    )
");
// ✅ log behavior (safe add-on)
$meta = json_encode([
  "watched_ratio" => $watched,
  "watched_seconds" => $d["watched_seconds"] ?? null,
  "duration_seconds" => $d["duration_seconds"] ?? null
], JSON_UNESCAPED_UNICODE);

$ins = $conn->prepare("
  INSERT INTO user_behavior (user_id, event_type, video_id, value_float, meta_json)
  VALUES (?, 'watch_progress', ?, ?, ?)
");
if ($ins) {
  $ins->bind_param("isds", $user_id, $video_id, $watched, $meta);
  @$ins->execute();
  // ✅ If no student_performance row exists yet, store last watch into behavior only (already)
// The quiz_submit will later read behavior to set watched_percent (we can add this next if needed)
  $ins->close();
}

if (!$stmt) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);

$stmt->bind_param("isii", $user_id, $video_id, $is_completed, $is_completed);
$stmt->execute();
$stmt->close();

// ✅ also update latest student_performance watched_percent for this video (add-on)
// (so ai_predict_level.php can compute avg_watch correctly)
// ✅ Update latest student_performance watched_percent for this video (add-on)
$upd = $conn->prepare("
  UPDATE student_performance
  SET watched_percent = GREATEST(COALESCE(watched_percent,0), ?)
  WHERE user_id=? AND video_id=?
  ORDER BY id DESC
  LIMIT 1
");
if ($upd) {
  $upd->bind_param("dis", $watched, $user_id, $video_id);
  @$upd->execute();
  $upd->close();
}


json_out(["ok"=>true, "video_id"=>$video_id, "completed"=>$is_completed, "watched"=>$watched]);
