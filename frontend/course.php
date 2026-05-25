<?php
require_once __DIR__ . "/session_bootstrap.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.html");
    exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>تفاصيل المادة</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body style="display:block">
  <div class="container">
    <div class="topbar">
      <div>
        <h1 id="title">...</h1>
        <div class="muted" id="desc"></div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn ghost" href="index.php">رجوع</a>
        <a class="btn ghost" href="subscription.html">الاشتراك</a>
      </div>
    </div>

    <div class="card" style="margin-top:14px">
      <h2>الدورات/التدريبات داخل المادة</h2>
      <div class="muted" id="subNote" style="margin-bottom:10px"></div>
      <ul class="list" id="trainings"></ul>
    </div>
  </div>

  <script src="assets/js/app.js"></script>
  <script>
    async function load() {
      const course_id = Number(qs("course_id")||0);
      if (!course_id) return location.replace("index.php");

      const data = await apiGet(`course_detail.php?course_id=${course_id}`);
      document.getElementById("title").textContent = `${data.course.code} - ${data.course.name}`;
      document.getElementById("desc").textContent = data.course.description || "";
      document.getElementById("subNote").textContent = data.subscription_active
        ? "اشتراكك فعّال ✅ الفيديوهات المدفوعة مفتوحة"
        : "بدون اشتراك: الفيديوهات المدفوعة ستكون مقفلة 🔒";

      const ul = document.getElementById("trainings");
      ul.innerHTML = "";

      data.trainings.forEach(t => {
        const wrap = document.createElement("li");
        wrap.className = "item";
        wrap.style.alignItems = "flex-start";
        wrap.innerHTML = `
          <div style="flex:1">
            <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
              <div>
                <b>${escapeHtml(t.title)}</b>
                <div class="muted">${escapeHtml(t.description||"")}</div>
              </div>
              <span class="badge ok">+${t.coin_reward} coins</span>
            </div>

            <div style="margin-top:10px" class="muted">الفيديوهات</div>
            <ul class="list" style="margin-top:8px">
              ${t.videos.map(v => `
                <li class="item">
                  <div style="flex:1">
                    ${escapeHtml(v.title)}
                    ${v.is_paid ? '<span class="badge warn">Paid</span>' : '<span class="badge ok">Free</span>'}
                    ${v.watched ? '<span class="badge ok">Watched</span>' : ''}
                    ${v.locked ? '<span class="badge danger">Locked</span>' : ''}
                  </div>
                  ${v.locked ? '<a class="btn ghost" href="subscription.html">فتح</a>' : `<a class="btn ghost" href="video.php?video_id=${v.id}">مشاهدة</a>`}
                </li>
              `).join("")}
            </ul>

            <div style="margin-top:10px" class="muted">الأسئلة (Quiz)</div>
            <div style="margin-top:8px">
              ${t.quiz ? `
                <div class="item">
                  <div style="flex:1">
                    <b>${escapeHtml(t.quiz.title)}</b>
                    <div class="muted">Pass score: ${t.quiz.pass_score}%</div>
                    ${t.quiz.last_score !== null ? `<div class="muted">Last score: ${t.quiz.last_score}% ${t.quiz.passed ? "✅" : "❌"}</div>` : ''}
                  </div>
                  <a class="btn primary" href="quiz.php?quiz_id=${t.quiz.id}">ابدأ</a>
                </div>
              ` : '<div class="muted">لا يوجد أسئلة لهذا التدريب</div>'}
            </div>
          </div>
        `;
        ul.appendChild(wrap);
      });
    }

    load().catch(err => {
      console.error(err);
      alert("حدث خطأ. تأكد أنك مسجل دخول.");
      location.replace("login.php");
    });
  </script>
</body>
</html>
