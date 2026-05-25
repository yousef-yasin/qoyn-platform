<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") { header("Location: index.php"); exit; }

$video_id = (int)($_GET["video_id"] ?? 0);
if(!$video_id) die("video_id مفقود");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>طلاب الفيديو</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.08);font-size:13px;text-align:right}
    tr:hover{background:rgba(255,255,255,.03)}
    .muted2{opacity:.8;font-size:13px}
    a.link{color:inherit;text-decoration:underline;cursor:pointer}
  </style>
</head>
<body style="display:block">
  <div class="container">
    <div class="topbar">
      <div>
        <h1 id="title">طلاب الفيديو</h1>
        <div class="muted" id="sub">...</div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn ghost" href="company_analytics.php">رجوع</a>
      </div>
    </div>

    <div class="card" style="margin-top:14px">
      <div id="box" class="muted2">جاري التحميل...</div>
    </div>
  </div>

<script>
const API="/utbn-backend/api";
const video_id = <?= (int)$video_id ?>;

function esc(s){return String(s??"").replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));}

async function load(){
  const box=document.getElementById("box");
  box.textContent="جاري التحميل...";

  const res = await fetch(`${API}/partner_analytics_viewers.php?video_id=${video_id}`, {credentials:"include"});
  const j = await res.json().catch(()=>({}));
  if(!j.ok){ box.innerHTML=`<div class="muted2">فشل: ${esc(j.error||"")}</div>`; return; }

  document.getElementById("title").textContent = j.video?.title ? ("طلاب: " + j.video.title) : "طلاب الفيديو";
  document.getElementById("sub").textContent = "اضغط على اسم الطالب لرؤية كيف حل الأسئلة";

  const items = j.items || [];
  if(!items.length){ box.innerHTML = `<div class="muted2">لا يوجد طلاب شاهدوا هذا الفيديو بعد</div>`; return; }

  box.innerHTML = `
    <table>
      <thead>
        <tr>
          <th>الطالب</th>
          <th>Watched</th>
          <th>Completed</th>
          <th>Score</th>
          <th>آخر تسليم</th>
        </tr>
      </thead>
      <tbody>
        ${items.map(s=>`
          <tr>
            <td>
              <a class="link" href="company_student_submission.php?video_id=${video_id}&student_id=${encodeURIComponent(s.student_id)}">
                ${esc(s.full_name)} (${esc(s.email)})
              </a>
            </td>
            <td>${Number(s.watched_seconds||0)}s</td>
            <td>${Number(s.completed||0) ? "✅" : "—"}</td>
            <td>${(s.score==null) ? "—" : (Number(s.score)+" / "+Number(s.total||0))}</td>
            <td class="muted2">${esc(s.submitted_at||"—")}</td>
          </tr>
        `).join("")}
      </tbody>
    </table>
  `;
}
load();
</script>
</body>
</html>
