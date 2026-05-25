<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") { header("Location: index.php"); exit; }

$video_id = (int)($_GET["video_id"] ?? 0);
$student_id = (int)($_GET["student_id"] ?? 0);
if(!$video_id || !$student_id) die("missing");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>تفاصيل التسليم</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    .q{border:1px solid rgba(255,255,255,.08);padding:12px;border-radius:14px;margin-top:10px;background:rgba(255,255,255,.03)}
    .muted2{opacity:.8;font-size:13px}
  </style>
</head>
<body style="display:block">
  <div class="container">
    <div class="topbar">
      <div>
        <h1 id="t">تفاصيل التسليم</h1>
        <div class="muted" id="s">...</div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn ghost" href="company_video_viewers.php?video_id=<?= (int)$video_id ?>">رجوع</a>
      </div>
    </div>

    <div class="card" style="margin-top:14px">
      <div id="box" class="muted2">جاري التحميل...</div>
    </div>
  </div>

<script>
const API="/utbn-backend/api";
const video_id = <?= (int)$video_id ?>;
const student_id = <?= (int)$student_id ?>;

function esc(s){return String(s??"").replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));}

async function load(){
  const box=document.getElementById("box");
  const res = await fetch(`${API}/partner_analytics_submission.php?video_id=${video_id}&student_id=${student_id}`, {credentials:"include"});
  const j = await res.json().catch(()=>({}));
  if(!j.ok){ box.innerHTML=`<div class="muted2">فشل: ${esc(j.error||"")}</div>`; return; }

  document.getElementById("t").textContent = "تفاصيل: " + (j.video?.title || "");
  if(!j.submission){
    document.getElementById("s").textContent = "الطالب لم يسلّم الأسئلة بعد.";
    box.innerHTML = `<div class="muted2">لا يوجد تسليم</div>`;
    return;
  }

  document.getElementById("s").textContent =
    `النتيجة: ${j.submission.score} / ${j.submission.total} — ${esc(j.submission.submitted_at||"")}`;

  const detail = j.submission.data?.detail || [];
  if(!detail.length){
    box.innerHTML = `<div class="muted2">لا توجد تفاصيل محفوظة</div>`;
    return;
  }

  box.innerHTML = detail.map((d,i)=>`
    <div class="q">
      <b>${i+1}) ${esc(d.q||"")}</b>
      <div class="muted2" style="margin-top:8px">
        إجابة الطالب: <b>${d.chosen}</b> — الصحيح: <b>${d.correct}</b>
        ${Number(d.chosen)===Number(d.correct) ? "✅" : "❌"}
      </div>
    </div>
  `).join("");
}
load();
</script>
</body>
</html>
