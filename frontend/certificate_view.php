<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
$cert_id = (int)($_GET["cert_id"] ?? 0);
if ($cert_id <= 0) { header("Location: certificates.html"); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Certificate</title>
  <style>
    body{margin:0;font-family:Arial;background:#f3f4f6}
    .page{max-width:900px;margin:24px auto;padding:24px}
    .cert{
      background:#fff;border:10px solid #111827;border-radius:18px;
      padding:36px;text-align:center
    }
    h1{margin:0 0 12px}
    .muted{color:#475569}
    .name{font-size:34px;font-weight:800;margin:18px 0;color:#111827}
    .btn{margin-top:16px;padding:10px 14px;border:none;border-radius:12px;background:#111827;color:#fff;cursor:pointer}
    @media print {.btn{display:none} body{background:#fff} .page{margin:0;padding:0} .cert{border-radius:0}}
  </style>
</head>
<body>
  <div class="page">
    <div class="cert">
      <h1 id="title">...</h1>
      <div class="muted">هذه الشهادة تُثبت إتمامك للمرحلة الأولى في UTBN</div>
      <div class="name" id="studentName">...</div>
      <div class="muted" id="issuedAt">...</div>
      <button class="btn" onclick="window.print()">طباعة / حفظ PDF</button>
    </div>
  </div>

  <script>
    const API_BASE = "/utbn-backend/api";
    async function apiGet(path){
      const res = await fetch(API_BASE + "/" + path, {credentials:"include"});
      const j = await res.json();
      if (!res.ok) throw j;
      return j;
    }
    async function load(){
      const me = await apiGet("me.php");
      const r = await apiGet("certificate_my.php");
      const cert = r.certificates.find(x => Number(x.id) === <?php echo $cert_id; ?>);
      if (!cert) { location.href="certificates.html"; return; }
      document.getElementById("title").textContent = cert.title;
      document.getElementById("studentName").textContent = me.full_name;
      document.getElementById("issuedAt").textContent = "Issued at: " + cert.issued_at;
    }
    load().catch(()=>location.href="certificates.html");
  </script>
</body>
</html>
