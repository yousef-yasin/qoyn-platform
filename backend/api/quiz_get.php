<?php
require __DIR__ . "/db.php";
header("Content-Type: application/json; charset=utf-8");

// -------- basic rate limit (per IP, per endpoint, per minute) --------
function rate_limit(mysqli $conn, string $endpoint, int $limitPerMin = 20) {
  $ip = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
  $window = (int)floor(time() / 60); // minute bucket
  $stmt = $conn->prepare("INSERT INTO rate_limits (ip, endpoint, window_start, hits)
    VALUES (?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE hits = hits + 1");
  $stmt->bind_param("ssi", $ip, $endpoint, $window);
  $stmt->execute();
  $stmt->close();

  $stmt = $conn->prepare("SELECT hits FROM rate_limits WHERE ip=? AND endpoint=? AND window_start=?");
  $stmt->bind_param("ssi", $ip, $endpoint, $window);
  $stmt->execute();
  $hits = (int)($stmt->get_result()->fetch_assoc()["hits"] ?? 0);
  $stmt->close();

  if ($hits > $limitPerMin) {
    http_response_code(429);
    echo json_encode(["error"=>"RATE_LIMITED"], JSON_UNESCAPED_UNICODE);
    exit;
  }
}

rate_limit($conn, "quiz_get", 25);

// -------- input validation --------
$video_id = preg_replace("/[^a-zA-Z0-9_\-]/", "", $_GET["video_id"] ?? "");
if ($video_id === "" || strlen($video_id) > 32) {
  http_response_code(400);
  echo json_encode(["error"=>"INVALID_VIDEO_ID"], JSON_UNESCAPED_UNICODE);
  exit;
}

// admin regenerate flag (must be server-side protected in real admin flow)
$force = (($_GET["force"] ?? "") === "1");

// -------- cache check --------
if (!$force) {
  $stmt = $conn->prepare("SELECT quiz_json FROM video_quiz_cache WHERE video_id=?");
  $stmt->bind_param("s", $video_id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if ($row && !empty($row["quiz_json"])) {
    echo $row["quiz_json"];
    exit;
  }
}

// -------- transcript fetch: prefer YouTube captions, fallback to STT --------
function get_env(string $k, string $default=""): string {
  $v = getenv($k);
  return ($v === false || $v === null) ? $default : $v;
}

/**
 * Option A (Recommended): use yt-dlp on server (best practical way to get captions + audio).
 * Requires installing yt-dlp and ffmpeg.
 */
function fetch_youtube_transcript_with_ytdlp(string $video_id): ?string {
  $url = "https://www.youtube.com/watch?v=" . $video_id;

  // Try manual subtitles English, then auto subtitles English.
  // Output to stdout as VTT then strip timestamps.
  $cmds = [
    // manual subs
    "yt-dlp --skip-download --write-subs --sub-langs en --sub-format vtt -o - " . escapeshellarg($url) . " 2>/dev/null",
    // auto subs
    "yt-dlp --skip-download --write-auto-subs --sub-langs en --sub-format vtt -o - " . escapeshellarg($url) . " 2>/dev/null",
  ];

  foreach ($cmds as $cmd) {
    $out = shell_exec($cmd);
    if (!$out) continue;
    // crude VTT cleanup: remove headers + timestamps
    $lines = preg_split("/\r?\n/", $out);
    $buf = [];
    foreach ($lines as $ln) {
      $ln = trim($ln);
      if ($ln === "" || stripos($ln, "WEBVTT") === 0) continue;
      if (preg_match("/^\d+$/", $ln)) continue;
      if (preg_match("/^\d\d:\d\d:\d\d\.\d\d\d\s+-->\s+\d\d:\d\d:\d\d\.\d\d\d/", $ln)) continue;
      $buf[] = $ln;
    }
    $txt = trim(implode(" ", $buf));
    if (mb_strlen($txt, "UTF-8") >= 200) return $txt;
  }
  return null;
}

/**
 * Fallback STT via OpenAI /audio/transcriptions
 * Docs: Speech-to-text guide + createTranscription endpoint. :contentReference[oaicite:1]{index=1}
 */
function transcribe_with_openai(string $video_id): ?array {
  $apiKey = get_env("OPENAI_API_KEY", "");
  if (strlen($apiKey) < 20) return null;

  $url = "https://www.youtube.com/watch?v=" . $video_id;

  // 1) download audio with yt-dlp (still needed) then call OpenAI STT
  $tmpDir = sys_get_temp_dir();
  $outFile = $tmpDir . DIRECTORY_SEPARATOR . "utbn_" . $video_id . ".m4a";

  @unlink($outFile);
  $dl = shell_exec("yt-dlp -f bestaudio --extract-audio --audio-format m4a -o " . escapeshellarg($outFile) . " " . escapeshellarg($url) . " 2>/dev/null");
  if (!file_exists($outFile) || filesize($outFile) < 10000) return null;

  $ch = curl_init("https://api.openai.com/v1/audio/transcriptions");
  $post = [
    "model" => "whisper-1", // or gpt-4o-transcribe for higher accuracy :contentReference[oaicite:2]{index=2}
    "file" => new CURLFile($outFile, "audio/mp4", basename($outFile)),
    "response_format" => "json"
  ];

  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $apiKey],
    CURLOPT_POSTFIELDS => $post,
    CURLOPT_TIMEOUT => 120
  ]);

  $resp = curl_exec($ch);
  $err  = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  @unlink($outFile);

  if ($err || $code < 200 || $code >= 300) return null;

  $j = json_decode($resp, true);
  $text = trim($j["text"] ?? "");
  if (mb_strlen($text, "UTF-8") < 200) return null;

  return ["text"=>$text, "language_detected"=>($j["language"] ?? "unknown")];
}

// -------- get transcript --------
$transcript = fetch_youtube_transcript_with_ytdlp($video_id);
$source = "youtube";
$langDetected = "en";

if (!$transcript) {
  $stt = transcribe_with_openai($video_id);
  if (!$stt) {
    http_response_code(500);
    echo json_encode(["error"=>"TRANSCRIPT_UNAVAILABLE"], JSON_UNESCAPED_UNICODE);
    exit;
  }
  $transcript = $stt["text"];
  $langDetected = $stt["language_detected"] ?? "unknown";
  $source = "whisper";
}

$transcript_hash = hash("sha256", $transcript);

// -------- question generation (LLM) --------
// NOTE: use your existing Gemini call if you want. Here I show OpenAI Responses API pattern.
// If you prefer Gemini, keep your existing and only change the prompt + schema.
function generate_questions_en(string $video_id, string $transcript): array {
  // Keep it vendor-agnostic: return array ready to json_encode
  // You can swap in Gemini or OpenAI text model.
  $prompt = <<<PROMPT
You are generating a quiz for a YouTube video transcript.

RULES:
- Output ENGLISH ONLY.
- Create exactly 8 multiple-choice questions (MCQ).
- Each question has 4 choices A–D, exactly 1 correct.
- Provide a 1-sentence explanation.
- Questions must test understanding of the transcript content (not trivia).
- Return JSON ONLY in this exact schema:
{
  "video_id": "...",
  "language_detected": "...",
  "questions": [
    {
      "question": "...",
      "choices": {"A":"...","B":"...","C":"...","D":"..."},
      "correct": "A",
      "explanation": "..."
    }
  ]
}

TRANSCRIPT:
{$transcript}
PROMPT;

  // If you already use Gemini in PHP, reuse it here.
  // For brevity: return a placeholder structure (you will plug your model call).
  return [
    "video_id" => $video_id,
    "language_detected" => "en",
    "questions" => []
  ];
}

// TODO: Replace this with your real LLM call and strict JSON parse.
// In production, you MUST parse model output + validate schema.
$quizArr = generate_questions_en($video_id, $transcript);
$quizArr["video_id"] = $video_id;
$quizArr["language_detected"] = $quizArr["language_detected"] ?? $langDetected;

// Minimal schema guard (ensure 8 questions)
if (!isset($quizArr["questions"]) || !is_array($quizArr["questions"]) || count($quizArr["questions"]) !== 8) {
  // fail safe
  $quizArr["questions"] = [];
}

$quiz_json = json_encode($quizArr, JSON_UNESCAPED_UNICODE);

// -------- store cache --------
$stmt = $conn->prepare("
  INSERT INTO video_quiz_cache (video_id, language_detected, quiz_json, transcript_source, transcript_hash)
  VALUES (?, ?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    language_detected=VALUES(language_detected),
    quiz_json=VALUES(quiz_json),
    transcript_source=VALUES(transcript_source),
    transcript_hash=VALUES(transcript_hash)
");
$stmt->bind_param("sssss", $video_id, $langDetected, $quiz_json, $source, $transcript_hash);
$stmt->execute();
$stmt->close();

echo $quiz_json;
