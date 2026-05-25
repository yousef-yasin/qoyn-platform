<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
$course = trim($_GET["course"] ?? "");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>فيديوهات الأساتذة</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body style="display:block">
<div class="container">

  <div class="topbar">
    <div>
      <h1>فيديوهات الأساتذة</h1>
      <div class="muted" id="sub"><?= htmlspecialchars($course, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn ghost" href="courses.php">⬅ رجوع</a>
    </div>
  </div>

  <div class="card" style="margin-top:14px">
    <ul class="list" id="list"></ul>
    <div class="muted" id="msg" style="min-height:22px"></div>
  </div>

</div>

<script>
const course = <?= json_encode($course, JSON_UNESCAPED_UNICODE) ?>;

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, m => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[m]));
}

async function load(){
  const msg = document.getElementById("msg");
  const ul = document.getElementById("list");
  msg.textContent = "جاري التحميل...";
  ul.innerHTML = "";

  const r = await fetch("/utbn-backend/api/student_partner_course_videos.php?course=" + encodeURIComponent(course), {credentials:"include"});
  const j = await r.json().catch(()=>({}));
  if (!r.ok || !j.ok) {
    msg.textContent = "تعذر تحميل الفيديوهات";
    return;
  }

  const items = j.items || [];
  if (!items.length) {
    msg.textContent = "لا يوجد فيديوهات لهذه المادة بعد.";
    return;
  }

  msg.textContent = "";
  ul.innerHTML = items.map(v => `
    <li style="display:flex;justify-content:space-between;gap:10px;align-items:center">
      <div>
        <b>${esc(v.video_title)}</b>
        <div class="muted">${esc(v.partner_name)} — ${esc(v.playlist_name)}</div>
      </div>
      <a class="btn ghost" href="partner_video.php?vid=${encodeURIComponent(v.video_id)}">فتح</a>
    </li>
  `).join("");
}

load();
</script>
</body>
</html>
