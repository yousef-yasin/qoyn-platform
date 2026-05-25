<?php
// utbn-backend/api/judge0_run.php
header("Content-Type: application/json; charset=utf-8");

function json_out($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

$in = json_decode(file_get_contents("php://input"), true) ?: [];

$source_code = (string)($in["source_code"] ?? "");
$stdin       = (string)($in["stdin"] ?? "");
$language_id = (int)($in["language_id"] ?? 71); // default Python 3 (Judge0 CE غالباً 71)
$cpu_time    = (float)($in["cpu_time_limit"] ?? 2);
$mem_limit   = (int)($in["memory_limit"] ?? 128000); // KB

if (trim($source_code) === "") {
  json_out(["ok" => false, "error" => "EMPTY_CODE"], 400);
}

// ✅ عدّل هذا لو Judge0 على سيرفر غير localhost
$JUDGE0 = "http://localhost:2358";

// 1) create submission (wait=true)
$payload = [
  "language_id" => $language_id,
  "source_code" => $source_code,
  "stdin" => $stdin,
  "cpu_time_limit" => $cpu_time,
  "memory_limit" => $mem_limit
];

$ch = curl_init($JUDGE0 . "/submissions?base64_encoded=false&wait=true");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_TIMEOUT => 60,
]);

$res = curl_exec($ch);
$err = curl_error($ch);
$http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($res === false) {
  json_out(["ok" => false, "error" => "CURL_ERROR", "details" => $err], 500);
}

$data = json_decode($res, true);
if (!is_array($data)) {
  json_out(["ok" => false, "error" => "BAD_RESPONSE", "http" => $http, "raw" => $res], 502);
}

if ($http < 200 || $http >= 300) {
  json_out(["ok" => false, "error" => "JUDGE0_HTTP_ERROR", "http" => $http, "details" => $data], 502);
}

// ✅ رجّع أهم الأشياء
json_out([
  "ok" => true,
  "status" => $data["status"] ?? null,
  "stdout" => $data["stdout"] ?? "",
  "stderr" => $data["stderr"] ?? "",
  "compile_output" => $data["compile_output"] ?? "",
  "message" => $data["message"] ?? "",
  "time" => $data["time"] ?? null,
  "memory" => $data["memory"] ?? null,
  "token" => $data["token"] ?? null,
]);
