<?php
// utbn-backend/api/certificate_download.php
// ✅ Generates PDF safely (no hanging) using wkhtmltopdf with timeout.
// ✅ Self-contained HTML (template embedded as base64) to avoid remote loading.

require __DIR__ . "/db.php";
require_login();

$user_id = (int)($_SESSION["user_id"] ?? 0);
$id = (int)($_GET["id"] ?? 0);
if (!$id) json_out(["ok"=>false,"error"=>"MISSING_ID"], 400);

// 1) Get certificate row (and any stored pdf)
$stmt = $conn->prepare("
  SELECT id, title, issued_at,
         COALESCE(student_name,'') AS student_name,
         COALESCE(major_name,'')   AS major_name,
         COALESCE(pdf_path,'')     AS pdf_path
  FROM certificates
  WHERE id=? AND user_id=?
  LIMIT 1
");
if (!$stmt) json_out(["ok"=>false,"error"=>"DB_PREPARE_FAILED","details"=>$conn->error], 500);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) json_out(["ok"=>false,"error"=>"NOT_FOUND"], 404);

// 2) If PDF already exists, just serve it (fast)
$existing = (string)($row["pdf_path"] ?? "");
if ($existing !== "") {
  $absExisting = realpath(__DIR__ . "/../" . $existing);
  if ($absExisting && is_file($absExisting)) {
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=certificate-{$id}.pdf");
    readfile($absExisting);
    exit;
  }
}

// 3) Find wkhtmltopdf binary (Windows OR Linux in PATH)
$binCandidates = [
  "C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe",
  "C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltopdf.exe",
  "wkhtmltopdf", // Linux/Windows if in PATH
];

$bin = "";
foreach ($binCandidates as $c) {
  if ($c === "wkhtmltopdf") { $bin = $c; break; }
  if (is_file($c)) { $bin = $c; break; }
}

if ($bin === "") {
  json_out([
    "ok"=>false,
    "error"=>"WKHTMLTOPDF_NOT_FOUND",
    "hint"=>"ثبّت wkhtmltopdf أو ضيفه للـ PATH (أو عدّل المسار داخل certificate_download.php).",
    "view_url"=>"/utbn-backend/api/certificate_view.php?id=".$id
  ], 500);
}

// 4) Build self-contained HTML (embed template image as base64 to avoid remote loads)
$title  = htmlspecialchars((string)$row["title"], ENT_QUOTES, "UTF-8");
$name   = htmlspecialchars((string)$row["student_name"], ENT_QUOTES, "UTF-8");
$major  = htmlspecialchars((string)$row["major_name"], ENT_QUOTES, "UTF-8");
$issued = htmlspecialchars((string)$row["issued_at"], ENT_QUOTES, "UTF-8");

// Template image (PNG)
$templateAbs = dirname(__DIR__) . "/assets/cert_templates/phase1.png";
$bgDataUri = "";
if (is_file($templateAbs)) {
  $png = @file_get_contents($templateAbs);
  if ($png !== false) {
    $bgDataUri = "data:image/png;base64," . base64_encode($png);
  }
}

$html = '<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<style>
 @page { size: 1280px 720px; margin: 0; }
  body { margin:0; font-family: Arial, Tahoma, sans-serif; }
  .paper{
    position:relative;
   width:1280px; 
   height:720px;
    overflow:hidden;
    background:#fff;
  }
  .bgimg{
    position:absolute; inset:0;
    width:100%; height:100%;
    object-fit:cover;
    display:block;
  }
  .t{
    position:absolute;
    left:0; right:0;
    text-align:center;
    font-weight:800;
  }
  .t.name { top: 45%; font-size: 42px; color:#0A2E5D; }
  .t.major{ top: 58%; font-size: 28px; color:#111; font-weight:700; }
  .t.issued{ top: 73%; font-size: 18px; color:#111; font-weight:700; opacity:.85; }
</style>
</head>
<body>
  <div class="paper">
    <img class="bgimg" src="' . $bgDataUri . '" alt="template"/>
    <div class="t name">' . ($name !== "" ? $name : "—") . '</div>
    <div class="t major">' . ($major !== "" ? $major : "—") . '</div>
    <div class="t issued">' . $issued . '</div>
  </div>
</body>
</html>';



// 5) Output path
$dir = __DIR__ . "/../uploads/certificates";
if (!is_dir($dir)) @mkdir($dir, 0775, true);

$rel = "uploads/certificates/certificate-{$id}.pdf";
$out = __DIR__ . "/../" . $rel;

// 6) Run wkhtmltopdf with timeout using proc_open (prevents hanging)
// 6) Run wkhtmltopdf via temp HTML file (Windows-safe, prevents Bad FD)
$tmpHtml = tempnam(sys_get_temp_dir(), "cert_") . ".html";
file_put_contents($tmpHtml, $html);

// مهم في ويندوز: حط مسار exe بين ""
$binQuoted = '"' . str_replace('"', '', $bin) . '"';

// ✅ شيلنا --quiet عشان لو صار خطأ نشوفه في stderr
$cmd = $binQuoted
  . " --print-media-type --page-size A4 --enable-local-file-access "
  . escapeshellarg($tmpHtml) . " "
  . escapeshellarg($out);

$descriptors = [
  0 => ["pipe","r"], // stdin (مش مستخدم)
  1 => ["pipe","w"], // stdout
  2 => ["pipe","w"], // stderr
];

$process = @proc_open($cmd, $descriptors, $pipes);
if (!is_resource($process)) {
  @unlink($tmpHtml);
  json_out(["ok"=>false,"error"=>"WKHTMLTOPDF_PROC_OPEN_FAILED","cmd"=>$cmd], 500);
}

// timeout
$timeout = 25;
$start = time();
while (true) {
  $status = proc_get_status($process);
  if (!$status["running"]) break;

  if ((time() - $start) > $timeout) {
    proc_terminate($process, 9);
    @unlink($tmpHtml);
    json_out(["ok"=>false,"error"=>"WKHTMLTOPDF_TIMEOUT"], 504);
  }
  usleep(100000);
}

$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);
$exitCode = proc_close($process);

@unlink($tmpHtml);

if ($exitCode !== 0 || !is_file($out)) {
  json_out([
    "ok"=>false,
    "error"=>"WKHTMLTOPDF_FAILED",
    "code"=>$exitCode,
    "stderr"=>mb_substr($stderr, 0, 1500),
    "cmd"=>$cmd
  ], 500);
}


// 7) Store pdf_path (optional)
$upd = $conn->prepare("UPDATE certificates SET pdf_path=? WHERE id=? AND user_id=?");
if ($upd) {
  $upd->bind_param("sii", $rel, $id, $user_id);
  $upd->execute();
  $upd->close();
}

// 8) Serve PDF
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=certificate-{$id}.pdf");
readfile($out);
exit;
