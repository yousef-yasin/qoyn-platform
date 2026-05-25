<?php
// utbn-backend/api/video_reward_claim.php
// Awards coins ONCE per (user_id + video_id).
// - Partner video ids supported: "partner_39" OR "p_39"
// - Normal youtube ids supported as before.

require __DIR__ . "/db.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$in = json_decode(file_get_contents("php://input"), true) ?: [];

// Accept multiple client key names
$raw_video = $in["video_id"] ?? ($in["videoId"] ?? ($in["youtube_id"] ?? ($in["youtubeId"] ?? "")));

// Keep underscore/dash (partner_39, p_39, youtube ids, ...)
$video_id = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$raw_video);
if ($video_id === "") {
  json_out(["ok" => false, "error" => "INVALID_VIDEO"], 400);
}

// Optional: from client
$quiz_correct = (int)($in["quiz_correct"] ?? ($in["score"] ?? 0));
$quiz_total   = (int)($in["quiz_total"]   ?? ($in["total"] ?? 0));

// ===== Already rewarded? (user_id + video_id) =====
$st = $conn->prepare("SELECT total_coin FROM video_rewards WHERE user_id=? AND video_id=? LIMIT 1");
if (!$st) json_out(["ok"=>false,"error"=>"SELECT_PREPARE_FAILED","details"=>$conn->error], 500);
$st->bind_param("is", $user_id, $video_id);
$st->execute();
$ex = $st->get_result()->fetch_assoc();
$st->close();

// sum for UI
$stSum = $conn->prepare("SELECT COALESCE(SUM(total_coin),0) AS s FROM video_rewards WHERE user_id=?");
if (!$stSum) json_out(["ok"=>false,"error"=>"SUM_PREPARE_FAILED","details"=>$conn->error], 500);
$stSum->bind_param("i", $user_id);
$stSum->execute();
$rSum = $stSum->get_result()->fetch_assoc();
$stSum->close();
$totalCoins = (int)($rSum["s"] ?? 0);

if ($ex) {
  json_out([
    "ok" => true,
    "already_rewarded" => true,
    "total_coin" => 0,
    "video_id" => $video_id,
    "coins_total" => $totalCoins,
    "quiz_correct" => $quiz_correct,
    "quiz_total" => $quiz_total,
  ]);
}

// =====================================================
// Coins calculation
// =====================================================
$base_coin = 0;
$quiz_coin = 20; // fallback if we cannot resolve pool
$total = $base_coin + $quiz_coin;

// ---- Partner video? partner_39 OR p_39 ----
$partner_vid_id = 0;
if (preg_match('/^(partner|p)_(\d+)$/', $video_id, $m)) {
  $partner_vid_id = (int)$m[2];
}

if ($partner_vid_id > 0) {

  // Get playlist_id for this partner video
  $pv = $conn->prepare("SELECT playlist_id FROM partner_videos WHERE id=? LIMIT 1");
  if ($pv) {
    $pv->bind_param("i", $partner_vid_id);
    $pv->execute();
    $pvRow = $pv->get_result()->fetch_assoc();
    $pv->close();
    $playlist_id = (int)($pvRow["playlist_id"] ?? 0);

    if ($playlist_id > 0) {
      // coin_pool for playlist
      // ✅ ملاحظة: coin_pool الحقيقي لازم يكون حسب الـ Path.
      // أحياناً partner_playlists.coin_pool بيكون 0 (إذا التوزيع صار على learning_path_playlists)
      // لذلك نجيب template_playlist_id + source_path_id ونعمل fallback.
      $pl = $conn->prepare("SELECT coin_pool, template_playlist_id, source_path_id FROM partner_playlists WHERE id=? LIMIT 1");
      if ($pl) {
        $pl->bind_param("i", $playlist_id);
        $pl->execute();
        $plRow = $pl->get_result()->fetch_assoc();
        $pl->close();
        $coin_pool   = (int)($plRow["coin_pool"] ?? 0);
        $tpl_id      = (int)($plRow["template_playlist_id"] ?? 0);
        $src_path_id = (int)($plRow["source_path_id"] ?? 0);

        // ✅ fallback من learning_path_playlists (coin_pool per path)
        if($coin_pool <= 0 && $src_path_id > 0 && $tpl_id > 0){
          $stCP = $conn->prepare("SELECT coin_pool FROM learning_path_playlists WHERE path_id=? AND template_playlist_id=? LIMIT 1");
          if($stCP){
            $stCP->bind_param("ii", $src_path_id, $tpl_id);
            $stCP->execute();
            $rowCP = $stCP->get_result()->fetch_assoc();
            $coin_pool = (int)($rowCP["coin_pool"] ?? 0);
            $stCP->close();
          }
        }

        // count videos in playlist
        $cnt = $conn->prepare("SELECT COUNT(*) AS c FROM partner_videos WHERE playlist_id=?");
        if ($cnt) {
          $cnt->bind_param("i", $playlist_id);
          $cnt->execute();
          $cRow = $cnt->get_result()->fetch_assoc();
          $cnt->close();
          $videos_count = (int)($cRow["c"] ?? 0);

          if ($coin_pool > 0 && $videos_count > 0) {
            $coins_per_video = (int)floor($coin_pool / $videos_count);
            if ($coins_per_video < 0) $coins_per_video = 0;

            // If quiz_total provided, award proportionally; otherwise award full per-video
            if ($quiz_total > 0) {
              $ratio = $quiz_correct / $quiz_total;
              if ($ratio < 0) $ratio = 0;
              if ($ratio > 1) $ratio = 1;
              $quiz_coin = (int)round($coins_per_video * $ratio);
            } else {
              $quiz_coin = $coins_per_video;
            }

            if ($quiz_coin < 0) $quiz_coin = 0;
            if ($quiz_coin > $coins_per_video) $quiz_coin = $coins_per_video;
            $total = $base_coin + $quiz_coin;
          }
        }
      }
    }
  }
} else {
  // ---- Non-partner: Try to resolve training coin_reward distribution (optional) ----
  // If your normal videos are linked to trainings via videos.youtube_id, this keeps working.
  $training_id = 0;
  $stV = $conn->prepare("SELECT training_id FROM videos WHERE youtube_id=? LIMIT 1");
  if ($stV) {
    $stV->bind_param("s", $video_id);
    $stV->execute();
    $vRow = $stV->get_result()->fetch_assoc();
    $stV->close();
    $training_id = (int)($vRow["training_id"] ?? 0);
  }

  if ($training_id > 0) {
    $stT = $conn->prepare("SELECT coin_reward FROM trainings WHERE id=? LIMIT 1");
    if ($stT) {
      $stT->bind_param("i", $training_id);
      $stT->execute();
      $tRow = $stT->get_result()->fetch_assoc();
      $stT->close();
      $coin_reward = (int)($tRow["coin_reward"] ?? 0);

      $stC = $conn->prepare("SELECT COUNT(*) AS c FROM videos WHERE training_id=?");
      if ($stC) {
        $stC->bind_param("i", $training_id);
        $stC->execute();
        $cRow = $stC->get_result()->fetch_assoc();
        $stC->close();
        $videos_count = (int)($cRow["c"] ?? 0);

        if ($coin_reward > 0 && $videos_count > 0) {
          $coins_per_video = (int)floor($coin_reward / $videos_count);
          if ($coins_per_video < 0) $coins_per_video = 0;

          if ($quiz_total > 0) {
            $ratio = $quiz_correct / $quiz_total;
            if ($ratio < 0) $ratio = 0;
            if ($ratio > 1) $ratio = 1;
            $quiz_coin = (int)round($coins_per_video * $ratio);
          } else {
            $quiz_coin = $coins_per_video;
          }
          if ($quiz_coin < 0) $quiz_coin = 0;
          if ($quiz_coin > $coins_per_video) $quiz_coin = $coins_per_video;
          $total = $base_coin + $quiz_coin;
        }
      }
    }
  }
}

// ===== Insert reward row =====
$ins = $conn->prepare("
  INSERT INTO video_rewards (user_id, video_id, base_coin, quiz_coin, total_coin, rewarded_at, youtube_id)
  VALUES (?, ?, ?, ?, ?, NOW(), ?)
");
if (!$ins) json_out(["ok"=>false,"error"=>"INSERT_PREPARE_FAILED","details"=>$conn->error], 500);

$ins->bind_param("isiiis", $user_id, $video_id, $base_coin, $quiz_coin, $total, $video_id);

if (!$ins->execute()) {
  if ($conn->errno === 1062) {
    $ins->close();
    json_out([
      "ok" => true,
      "already_rewarded" => true,
      "total_coin" => 0,
      "video_id" => $video_id,
      "coins_total" => $totalCoins,
      "quiz_correct" => $quiz_correct,
      "quiz_total" => $quiz_total,
    ]);
  }
  $err = $conn->error;
  $ins->close();
  json_out(["ok"=>false,"error"=>"INSERT_FAILED","details"=>$err], 500);
}
$ins->close();

// ===== Update student_profiles (optional) =====
$hasSP = $conn->query("SHOW TABLES LIKE 'student_profiles'");
if ($hasSP && $hasSP->num_rows > 0) {
  $stSp = $conn->prepare("
    INSERT INTO student_profiles (user_id, major_id, level, coins_total)
    VALUES (?, 1, 1, ?)
    ON DUPLICATE KEY UPDATE coins_total = coins_total + VALUES(coins_total)
  ");
  if ($stSp) {
    $stSp->bind_param("ii", $user_id, $total);
    $stSp->execute();
    $stSp->close();
  }
}
$stu = $conn->prepare("UPDATE users SET coins = COALESCE(coins,0) + ? WHERE id=?");
if ($stu) {
  $stu->bind_param("ii", $total, $user_id);
  $stu->execute();
  $stu->close();
}
// sum after
$stSum2 = $conn->prepare("SELECT COALESCE(SUM(total_coin),0) AS s FROM video_rewards WHERE user_id=?");
$stSum2->bind_param("i", $user_id);
$stSum2->execute();
$rSum2 = $stSum2->get_result()->fetch_assoc();
$stSum2->close();
$totalCoins2 = (int)($rSum2["s"] ?? 0);

json_out([
  "ok" => true,
  "already_rewarded" => false,
  "total_coin" => $total,
  "video_id" => $video_id,
  "coins_total" => $totalCoins2,
  "quiz_correct" => $quiz_correct,
  "quiz_total" => $quiz_total,
  "base_coin" => $base_coin,
  "quiz_coin" => $quiz_coin
]);
