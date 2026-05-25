<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }

$playlist_id = (int)($_GET["playlist_id"] ?? 0);
if (!$playlist_id) die("playlist_id مفقود");
?>
<!doctype html>
<html lang="ar" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>QOYN | Playlist</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --navy:#0b2f6b;
  --gold:#f4bd3f;
  --bg:#f6f7fb;
  --muted:#707b96;
  --line:#edf0f7;
  --card:#fff;
  --shadow:0 18px 40px rgba(15,35,75,.08);
}

*{box-sizing:border-box}

body{
  margin:0;
  font-family:"Poppins",sans-serif;
  background:
    radial-gradient(circle at 90% 14%, rgba(244,189,63,.18) 0, rgba(244,189,63,0) 20%),
    linear-gradient(180deg,#fbfcff 0%,#f4f6fb 100%);
  color:#0b2348;
}

.nav-wrap{
  position:sticky;
  top:0;
  z-index:999;
  padding:14px 22px;
  background:#fff;
  box-shadow:0 8px 28px rgba(0,0,0,.05);
  border-bottom:1px solid rgba(10,46,93,.06);
}

.nav{
  display:flex;
  align-items:center;
  max-width:1200px;
  margin:0 auto;
  gap:16px;
  direction:ltr;
}

.logo-wrap{
  display:flex;
  align-items:center;
  gap:10px;
  direction:ltr;
}

.nav-monkey{
  height:40px;
  width:auto;
  object-fit:contain;
  transition:.2s;
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.14));
}

.nav-monkey:hover{
  transform:translateY(-2px) scale(1.05);
}

.logo{
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:26px;
  color:#0b2f6b;
  text-decoration:none;
  letter-spacing:.3px;
}

.nav-spacer{
  flex:1;
}

.nav-links{
  display:flex;
  align-items:center;
  gap:14px;
  list-style:none;
  margin:0;
  padding:0;
}

.nav-links a{
  text-decoration:none;
  color:#111;
  font-weight:700;
  font-size:14px;
  padding:10px 14px;
  border-radius:999px;
  transition:.2s ease;
  white-space:nowrap;
}

.nav-links a:hover{
  color:#ffb31f;
  transform:translateY(-1px);
}

.nav-links a.active{
  background:#ffb31f;
  color:#fff;
}

.nav-logout{
  border:1.5px solid #0b2f6b;
  color:#0b2f6b !important;
}

.nav-logout:hover{
  background:#0b2f6b;
  color:#fff !important;
}

.page{
  width:min(92%,1420px);
  margin:48px auto 50px;
}

.hero{
  min-height:220px;
  border-radius:36px;
  display:flex;
  align-items:center;
  gap:34px;
  padding:36px 42px;
  background:
    radial-gradient(circle at 78% 35%, rgba(220,229,255,.45) 0, rgba(220,229,255,0) 28%),
    radial-gradient(circle at 93% 10%, rgba(244,189,63,.2) 0, rgba(244,189,63,0) 20%),
    #f8f9fe;
  position:relative;
  overflow:hidden;
}

.hero::after{
  content:"";
  position:absolute;
  left:160px;
  top:70px;
  width:160px;
  height:80px;
  background-image:radial-gradient(#d9deee 1.5px,transparent 1.5px);
  background-size:18px 18px;
  opacity:.9;
}

.hero-icon{
  width:122px;
  height:122px;
  border-radius:50%;
  background:#fff;
  display:grid;
  place-items:center;
  box-shadow:0 18px 35px rgba(15,35,75,.08);
  flex:0 0 122px;
}

.hero-content{
  position:relative;
  z-index:2;
}

.hero h1{
  font-family:"Montserrat",sans-serif;
  font-size:56px;
  line-height:1.05;
  margin:0;
  color:var(--navy);
  font-weight:900;
}

.hero p{
  margin:16px 0 0;
  color:#5d667f;
  font-size:21px;
}

.hero-line{
  width:58px;
  height:5px;
  border-radius:999px;
  background:var(--gold);
  margin-top:24px;
}

.lessons{
  margin-top:42px;
  display:flex;
  flex-direction:column;
  gap:22px;
}

.lesson-card{
  background:#fff;
  border:1px solid var(--line);
  box-shadow:var(--shadow);
  border-radius:26px;
  min-height:180px;
  padding:20px 26px;
  grid-template-columns:200px 1fr 210px;
  display:grid;
  gap:34px;
  align-items:center;
}
.play-box{
  width:200px;
  height:110px;
  border-radius:16px;
  background:#eaf0fb center/cover no-repeat;
  box-shadow:0 10px 22px rgba(15,35,75,.08);
}

.play-box .play-circle{
  display:none;
}

.lesson-side{
  border-left:1px solid #dfe4ef;
  padding-left:26px;
}

.lesson-side .thumb{
  display:none;
}

.lesson-info{
  border-right:0;
  padding-right:0;
}
.play-box{
   width: 120px;  /* كان 154 */
  height: 120px;
  border-radius:20px;
  background:#f1f4fc;
  display:grid;
  place-items:center;
  justify-self:center;
}

.play-circle{
   width: 46px;   /* كان 58 */
  height: 46px;
  border-radius:50%;
  background:var(--navy);
  color:#fff;
  display:grid;
  place-items:center;
  font-size:18px;
  box-shadow:0 12px 25px rgba(11,47,107,.22);
}

.lesson-info{
  border-right:1px solid #dfe4ef;
  padding-right:34px;
}

.lesson-title{
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:24px;
  color:#0b2348;
  margin:0 0 14px;
  line-height:1.3;
}

.video-id{
  color:#68728a;
  font-size:16px;
  margin-bottom:22px;
}

.lesson-desc{
  color:#55617a;
  font-size:15px;
  line-height:1.9;
  margin-bottom:24px;
}

.lesson-meta{
  display:flex;
  align-items:center;
  gap:34px;
  flex-wrap:wrap;
}

.meta-item{
  display:flex;
  align-items:center;
  gap:12px;
  color:#0f2448;
  font-size:15px;
}

.meta-icon{
  width:26px;
  height:26px;
  border-radius:50%;
  display:grid;
  place-items:center;
  border:2px solid #0f2448;
  font-size:12px;
}

.tag{
  background:#f2f5fc;
  color:#18315d;
  padding:12px 28px;
  border-radius:999px;
  font-size:18px;
}

.lesson-side{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:22px;
}

.thumb{
  width:200px;
  height:110px;
  border-radius:16px;
  background:#eaf0fb center/cover no-repeat;
  box-shadow:0 10px 22px rgba(15,35,75,.08);
}

.watch-btn{
  width:200px;
  height:48px;
  border-radius:16px;
  background:var(--navy);
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;

  margin-left:-20px; /* 👈 هذا المهم */
}

.watch-btn:hover{
  transform:translateY(-2px);
  background:#163f83;
}

.info-box{
  margin-top:24px;
  background:#fff;
  border:1px solid var(--line);
  box-shadow:0 10px 28px rgba(15,35,75,.05);
  border-radius:20px;
  padding:22px 28px;
  color:#4e5a74;
  font-size:19px;
  display:flex;
  align-items:center;
  gap:14px;
}

.info-icon{
  width:42px;
  height:42px;
  border-radius:50%;
  background:#f2f5fc;
  display:grid;
  place-items:center;
  color:var(--navy);
  font-weight:800;
  border:1px solid #dfe6f5;
}

.empty{
  background:#fff;
  border-radius:22px;
  padding:28px;
  text-align:center;
  color:#777;
  box-shadow:var(--shadow);
}

@media(max-width:1100px){
  .lesson-card{
    grid-template-columns:1fr;
  }

  .lesson-info{
    border-right:none;
    padding-right:0;
  }

  .lesson-side{
    align-items:flex-start;
  }
}

@media(max-width:760px){
  .nav{
  flex-direction:column;
  align-items:flex-start;
}

.nav-links{
  width:100%;
  flex-wrap:wrap;
  gap:10px;
}

  .hero{
    flex-direction:column;
    align-items:flex-start;
    padding:28px 22px;
  }

  .hero h1{
    font-size:38px;
  }

  .lesson-card{
    padding:22px;
  }

  .thumb,
  .watch-btn{
    width:100%;
  }
}
/* ===== تصغير عام للصفحة ===== */

.page{
  width:min(90%,1200px);
  margin:20px auto 40px;
}

/* ===== الهيرو ===== */

.hero{
  min-height:160px;
  padding:24px 30px;
  border-radius:26px;
  gap:24px;
}

.hero-icon{
  width:85px;
  height:85px;
  flex:0 0 85px;
}

.hero-icon svg{
  width:44px;
  height:44px;
}

.hero h1{
  font-size:38px;
}

.hero p{
  font-size:15px;
  margin-top:8px;
}

.hero-line{
  margin-top:14px;
  height:4px;
}

/* ===== الكروت ===== */

.lessons{
  margin-top:24px;
  gap:14px;
}

.lesson-card{
  min-height:135px;
  padding:16px 20px;
  grid-template-columns:170px 1fr 170px;
  gap:20px;
  border-radius:20px;
}

/* ===== الصورة ===== */

.play-box{
  width:170px;
  height:95px;
  border-radius:14px;
}

/* ===== النص ===== */

.lesson-title{
  font-size:18px;
  margin-bottom:6px;
}

.video-id{
  font-size:13px;
  margin-bottom:10px;
}

.lesson-desc{
  font-size:13px;
  line-height:1.6;
  margin-bottom:12px;
}

/* ===== الميتا ===== */

.lesson-meta{
  gap:14px;
}

.meta-item{
  font-size:13px;
  gap:7px;
}

.meta-icon{
  width:22px;
  height:22px;
  font-size:10px;
}

.tag{
  padding:7px 16px;
  font-size:13px;
}

/* ===== الزر ===== */

.watch-btn{
  width:150px;
  height:38px;
  font-size:13px;
  border-radius:12px;
  gap:8px;

  margin-left:-15px; /* يخليه لليسار */
}

/* ===== الصندوق السفلي ===== */

.info-box{
  margin-top:16px;
  padding:14px 20px;
  font-size:14px;
  border-radius:16px;
}

.info-icon{
  width:32px;
  height:32px;
}
.play-box{
  width:170px !important;
  height:95px !important;
  border-radius:14px !important;
  background-color:#eaf0fb !important;
  background-position:center !important;
  background-size:cover !important;
  background-repeat:no-repeat !important;
  box-shadow:0 10px 22px rgba(15,35,75,.08) !important;
}

.play-box .play-circle{
  display:none !important;
}

.lesson-info{
  border-right:0 !important;
  padding-right:0 !important;
}

.lesson-side{
  border-left:1px solid #dfe4ef !important;
  padding-left:28px !important;
  align-items:flex-start !important;
}

.lesson-side .thumb{
  display:none !important;
}

.watch-btn{
  width:150px !important;
  height:38px !important;
  font-size:13px !important;
  border-radius:12px !important;
  gap:8px !important;
  margin-left:-15px !important;
  text-decoration:none !important;
  font-weight:800 !important;
}
</style>
</head>

<body>

<header class="nav-wrap" id="navWrap">
  <nav class="nav">
    <div class="logo-wrap">
      <img 
        src="uploads/MONKEY.png" 
        alt="logo" 
        class="nav-monkey"
        onerror="this.style.display='none'"
      >

      <a class="logo" href="student-dashboard.php#home">QOYN</a>
    </div>

    <div class="nav-spacer"></div>

    <ul class="nav-links">
      <li><a href="student-dashboard.php#home">Home</a></li>
      <li><a href="my_courses.php" class="active">My courses</a></li>
      <li><a href="courses.php">All courses</a></li>
      <li><a href="#" onclick="history.back();return false;">Back</a></li>
      <li><a href="login.html" class="nav-logout">Logout</a></li>
    </ul>
  </nav>
</header>

<main class="page">
  <section class="hero">
    <div class="hero-icon">
      <svg width="62" height="62" viewBox="0 0 24 24" fill="none">
        <path d="M3 9.5L12 4L21 9.5L12 15L3 9.5Z" stroke="#0b2f6b" stroke-width="1.8"/>
        <path d="M6.5 12V16.2C6.5 17.6 9 19 12 19C15 19 17.5 17.6 17.5 16.2V12" stroke="#0b2f6b" stroke-width="1.8"/>
        <path d="M21 10V15" stroke="#f4bd3f" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
    </div>

    <div class="hero-content">
      <h1 id="plTitle">...</h1>
      <p>كل الفيديوهات داخل هذا الملف</p>
      <div class="hero-line"></div>
    </div>
  </section>

  <section class="lessons" id="videos"></section>

  <div class="info-box">
    <div class="info-icon">i</div>
    <div>استمر في التعلم وطور مهاراتك خطوة بخطوة.</div>
  </div>
</main>

<script>
const API = "/utbn-backend/api";
const playlist_id = <?= (int)$playlist_id ?>;

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g,m=>({
    "&":"&amp;",
    "<":"&lt;",
    ">":"&gt;",
    '"':"&quot;",
    "'":"&#039;"
  }[m]));
}

function getDesc(title){
  const t = String(title || "").toLowerCase();

  if(t.includes("network")){
    return "تعرف على أساسيات الشبكات وأنواعها وكيفية عملها.";
  }

  if(t.includes("sql")){
    return "تعلم أساسيات قواعد البيانات والاستعلامات بطريقة سهلة.";
  }

  if(t.includes("python")){
    return "مقدمة عملية لتعلم أساسيات لغة بايثون خطوة بخطوة.";
  }

  return "استمر في مشاهدة الدرس وتعلم مهارة جديدة داخل هذا المسار.";
}

function getTag(title){
  const t = String(title || "").toLowerCase();

  if(t.includes("network")) return "أساسيات الشبكات";
  if(t.includes("sql")) return "قواعد البيانات";
  if(t.includes("python")) return "برمجة";
  if(t.includes("intro")) return "مقدمة";

  return "درس تعليمي";
}

function formatDate(v){
  return v?.created_at || v?.published_at || "2026-03-11";
}

function formatDuration(v){
  return v?.duration || v?.duration_text || "12:45";
}

async function load(){
  const res = await fetch(`${API}/student_playlist_videos.php?playlist_id=${playlist_id}`, {
    credentials:"include"
  });

  const j = await res.json();

  if(!j.ok){
    alert(j.error || "فشل");
    return;
  }

  document.getElementById("plTitle").textContent = j.playlist?.name || "الملف";

  const box = document.getElementById("videos");
  box.innerHTML = "";

  const arr = (j.items || j.videos || []);

  arr.forEach((v, index)=>{
    const cover = (v.cover_url || "").trim();
    const title = v.title || "Video";
    const desc = v.description || getDesc(title);
    const tag = v.tag || getTag(title);

    const article = document.createElement("article");
    article.className = "lesson-card";

    article.innerHTML = `
      <div class="play-box" ${cover ? `style="background-image:url('${esc(cover)}')"` : ""}>
  <div class="play-circle">▶</div>
</div>

      <div class="lesson-info">
        <h2 class="lesson-title">${esc(title)}</h2>
        <div class="video-id">Video ID: ${esc(v.id)}</div>
        <div class="lesson-desc">${esc(desc)}</div>

        <div class="lesson-meta">
          <div class="meta-item">
            <div class="meta-icon">◷</div>
            <div>
              <div>${esc(formatDuration(v))}</div>
              <small style="color:#7d879c">المدة</small>
            </div>
          </div>

          <div class="meta-item">
            <div class="meta-icon">▣</div>
            <div>
              <div>${esc(formatDate(v))}</div>
              <small style="color:#7d879c">تاريخ الإضافة</small>
            </div>
          </div>

          <div class="tag">${esc(tag)}</div>
        </div>
      </div>

      <div class="lesson-side">
        <div class="thumb" ${cover ? `style="background-image:url('${esc(cover)}')"` : ""}></div>
        <a class="watch-btn" href="student_watch.php?video_id=${encodeURIComponent(v.id)}">
          <span>▶</span>
          <span>مشاهدة</span>
        </a>
      </div>
    `;

    box.appendChild(article);
  });

  if(!arr.length){
    box.innerHTML = `<div class="empty">لا يوجد فيديوهات بعد داخل هذا الملف</div>`;
  }
}

load();
</script>
</body>
</html>