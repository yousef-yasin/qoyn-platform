<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
$type = $_GET["type"] ?? "";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if (($type !== "plan" && $type !== "experience") || $id <= 0) {
  header("Location: index.php");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>عرض المرفق</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <style>
    body{background:#0f172a;color:#fff;margin:0}
    .wrap{max-width:1100px;margin:20px auto;padding:0 14px}
    .top{display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between}
    .btn{display:inline-block;padding:10px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);color:#fff;text-decoration:none}
    .btn.primary{background:rgba(59,130,246,.25);border-color:rgba(59,130,246,.55)}
    .card{margin-top:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:12px}
    .title{font-size:18px;font-weight:700}
    .muted{opacity:.85;font-size:13px}
    .viewer{margin-top:10px;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:#0b1220}
    iframe{width:100%;height:78vh;border:0}
    img{max-width:100%;height:auto;display:block;margin:0 auto}
    .center{display:flex;align-items:center;justify-content:center}
  </style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <a class="btn" href="<?php echo ($type==='plan') ? 'plan.php' : 'experiences.php'; ?>">⬅ رجوع</a>
    <a class="btn" href="index.php">الرئيسية</a>
    <a class="btn primary" id="openDirect" href="#" target="_blank" rel="noopener">فتح مباشر</a>
  </div>

  <div class="card">
    <div class="title" id="t">...</div>
    <div class="muted" id="m">...</div>
    <div class="viewer" id="viewer">
      <div class="center" style="padding:30px" id="loading">جاري التحميل...</div>
    </div>
  </div>
</div>

<script>
const API = "http://localhost/utbn-backend/api/attachments";
const TYPE = <?php echo json_encode($type); ?>;
const ID = <?php echo (int)$id; ?>;

function esc(s){return String(s||"").replace(/[&<>"']/g, c=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[c]));}

async function load(){
  const res = await fetch(`${API}/list.php?type=${TYPE}`, {credentials:"include"});
  if(!res.ok){ location.replace("login.html"); return; }
  const arr = await res.json();
  const it = arr.find(x => Number(x.id) === Number(ID));
  if(!it){
    document.getElementById("t").textContent = "المرفق غير موجود";
    document.getElementById("m").textContent = "";
    document.getElementById("loading").textContent = "لا يوجد ملف بهذا الرقم.";
    return;
  }

  const url = `http://localhost/utbn-backend/${it.file_path}`;
  document.getElementById("t").textContent = it.title || it.original_name;
  document.getElementById("m").textContent = (it.created_at ? `تاريخ الرفع: ${it.created_at}` : "");
  const open = document.getElementById("openDirect");
  open.href = url;

  const viewer = document.getElementById("viewer");
  const isImg = (it.mime_type || "").startsWith("image/");
  viewer.innerHTML = isImg
    ? `<img src="${url}" alt="${esc(it.title || it.original_name)}">`
    : `<iframe src="${url}"></iframe>`;
}

load();
</script>
</body>
</html>
