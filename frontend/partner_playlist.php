<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }

$course      = isset($_GET["course"]) ? trim($_GET["course"]) : "";
$playlist    = isset($_GET["playlist"]) ? trim($_GET["playlist"]) : "";
$playlist_id = isset($_GET["playlist_id"]) ? (int)$_GET["playlist_id"] : 0;
$partner     = isset($_GET["partner"]) ? trim($_GET["partner"]) : "";
?>
<!doctype html>
<html lang="ar" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>QOYN | Partner Playlist</title>

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


.qoyn-topbar{
  width:100%;
  background:#fff;
  border-bottom:1px solid #eef1f6;
  box-shadow:0 8px 28px rgba(20,43,70,.05);
  position:sticky;
  top:0;
  z-index:999;
}

.qoyn-topbar-inner{
  width:calc(100% - 70px);
  max-width:none;
  margin:0 auto;
  height:88px;
  padding:0;
  display:flex;
  align-items:center;
  justify-content:space-between;
  direction:ltr;
}

.nav-monkey-wrap{
  display:flex;
  align-items:center;
  gap:12px;
}

.qoyn-logo{
  text-decoration:none;
  font-family:"Montserrat",sans-serif;
  font-size:29px;
  font-weight:900;
  letter-spacing:.5px;
  color:var(--navy);
}

.nav-monkey{
  height:50px;
  width:auto;
  object-fit:contain;
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.18));
  transition:.2s;
}

.nav-monkey:hover{
  transform:translateY(-2px) scale(1.05);
}

.qoyn-navlinks{
  display:flex;
  align-items:center;
  gap:18px;
  direction:ltr;
}

.qoyn-navlinks a{
  border:0;
  background:transparent;
  text-decoration:none;
  color:#4d5569;
  font-family:"Poppins",sans-serif;
  font-size:14px;
  font-weight:700;
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:13px 17px;
  border-radius:18px;
  cursor:pointer;
  transition:.2s ease;
  white-space:nowrap;
}

.qoyn-navlinks a:hover{
  color:#f4bd3f;
  transform:translateY(-1px);
}

.qoyn-navlinks a.active{
  color:#f0a000;
  background:#fff4df;
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
  grid-template-columns:160px 1fr 240px;
  display:grid;
  gap:34px;
  align-items:center;
}

.play-box{
  width:120px;
  height:120px;
  border-radius:20px;
  background:#f1f4fc;
  display:grid;
  place-items:center;
  justify-self:center;
}

.play-circle{
  width:46px;
  height:46px;
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
  text-decoration:none;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;
  font-weight:800;
  font-size:18px;
  box-shadow:0 14px 26px rgba(11,47,107,.18);
  transition:.2s ease;
  border:0;
  cursor:pointer;
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
  .qoyn-topbar-inner{
  width:100%;
  height:auto;
  padding:18px;
  flex-direction:column;
  align-items:flex-start;
  gap:16px;
}

.qoyn-navlinks{
  width:100%;
  justify-content:flex-start;
  gap:10px;
  flex-wrap:wrap;
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
.page{
  width:min(90%,1280px);
  margin:22px auto 40px;
}

.hero{
  min-height:165px;
  border-radius:28px;
  padding:26px 32px;
  gap:26px;
}

.hero-icon{
  width:90px;
  height:90px;
  flex:0 0 90px;
}

.hero-icon svg{
  width:48px;
  height:48px;
}

.hero h1{
  font-size:42px;
}

.hero p{
  font-size:16px;
  margin-top:10px;
}

.hero-line{
  margin-top:16px;
}

.lessons{
  margin-top:26px;
  gap:16px;
}
.lesson-card{
  min-height:140px;
  padding:16px 20px;
  grid-template-columns:170px 1fr 170px;
  gap:24px;
  border-radius:22px;
}
.play-box{
  width:160px;
  height:88px;
  border-radius:13px;
  background:#eaf0fb center/cover no-repeat;
}

.play-box .play-circle{
  display:none;
}

.lesson-side{
  border-left:1px solid #dfe4ef;
  padding-left:28px;
}

.lesson-side .thumb{
  display:none;
}

.lesson-info{
  border-right:0;
  padding-right:0;
}

.watch-btn{
  margin-left:0;
  margin-right:auto;
}
.lesson-side{
  border-left:1px solid #dfe4ef;
  padding-left:28px;
  align-items:flex-start;
}
.play-box{
  width:92px;
  height:92px;
  border-radius:17px;
}

.play-circle{
  width:38px;
  height:38px;
  font-size:15px;
}

.lesson-info{
  padding-right:24px;
}

.lesson-title{
  font-size:19px;
  margin-bottom:8px;
}

.video-id{
  font-size:13px;
  margin-bottom:12px;
}

.lesson-desc{
  font-size:13px;
  line-height:1.65;
  margin-bottom:14px;
}

.lesson-meta{
  gap:18px;
}

.meta-item{
  gap:8px;
  font-size:13px;
}

.meta-icon{
  width:22px;
  height:22px;
  font-size:10px;
}

.tag{
  padding:8px 18px;
  font-size:13px;
}

.lesson-side{
  gap:14px;
}

.thumb{
  width:160px;
  height:88px;
  border-radius:13px;
}

.watch-btn{
  width:160px;
  height:40px;
  border-radius:13px;
  font-size:14px;
  gap:9px;

  margin-left:-15px;   /* هذا المهم */
}

.info-box{
  margin-top:18px;
  padding:16px 22px;
  border-radius:16px;
  font-size:15px;
}

.info-icon{
  width:34px;
  height:34px;
}
.nav-wrap{
  position:sticky;
  top:0;
  z-index:999;
  padding:14px 22px;
  background:#fff;
  box-shadow:0 8px 28px rgba(0,0,0,.05);
}

.nav{
  display:flex;
  align-items:center;
  max-width:1200px;
  margin:auto;
}

.nav-monkey-wrap{
  display:flex;
  align-items:center;
  gap:10px;
}

.nav-monkey{
  height:40px;
}

.logo{
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:26px;
  color:#0b2f6b;
  text-decoration:none;
}

.nav-spacer{
  flex:1;
}

.nav-links{
  display:flex;
  align-items:center;
  gap:14px;
  list-style:none;
}

.nav-links a{
  text-decoration:none;
  color:#333;
  font-weight:600;
  padding:10px 14px;
  border-radius:999px;
  transition:.2s;
}

.nav-links a:hover{
  color:#f4bd3f;
}

.nav-links a.active{
  background:#f4bd3f;
  color:#fff;
}

.nav-logout{
  border:1px solid #0b2f6b;
}
</style>
</head>

<body>

<header class="nav-wrap" id="navWrap">
  <nav class="nav">

    <!-- اللوقو -->
    <div class="nav-monkey-wrap">
      <img src="uploads/MONKEY.png" class="nav-monkey">
      <a class="logo" href="student-dashboard.php#home">QOYN</a>
    </div>

    <div class="nav-spacer"></div>

    <!-- اللينكات -->
    <ul class="nav-links">
      <li><a href="student-dashboard.php#home">Home</a></li>
      <li><a href="my_courses.php">My courses</a></li>
      <li><a href="courses.php" class="active">All courses</a></li>
      <li><a href="courses.php">Back</a></li>
      <li><a href="#" id="logoutBtn" class="nav-logout">Logout</a></li>
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
      <p id="plInfo">كل الفيديوهات داخل هذا الملف</p>
      <div class="hero-line"></div>
    </div>
  </section>

  <section class="lessons" id="vList"></section>

  <div class="info-box">
    <div class="info-icon">i</div>
    <div>استمر في التعلم وطور مهاراتك خطوة بخطوة.</div>
  </div>
</main>

<script>
const course     = <?= json_encode($course, JSON_UNESCAPED_UNICODE) ?>;
const playlist   = <?= json_encode($playlist, JSON_UNESCAPED_UNICODE) ?>;
const playlistId = <?= (int)$playlist_id ?>;
const partner    = <?= json_encode($partner, JSON_UNESCAPED_UNICODE) ?>;

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, m => ({
    "&":"&amp;",
    "<":"&lt;",
    ">":"&gt;",
    '"':"&quot;",
    "'":"&#039;"
  }[m]));
}

function norm(s){
  return String(s ?? "")
    .toLowerCase()
    .replace(/[\u064B-\u0652]/g, "")
    .replace(/[^\w\u0600-\u06FF\s]/g, " ")
    .replace(/\s+/g, " ")
    .trim();
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
  const info = document.getElementById("plInfo");
  const list = document.getElementById("vList");
  const titleEl = document.getElementById("plTitle");

  list.innerHTML = "";
  info.textContent = "...";
  titleEl.textContent = "...";

  if(!course || (!playlistId && !playlist)){
    titleEl.textContent = "الملف";
    list.innerHTML = `<div class="empty">بيانات البلاي ليست غير مكتملة.</div>`;
    return;
  }

  const url = "/utbn-backend/api/student_partner_course_videos.php?course=" + encodeURIComponent(course);
  const res = await fetch(url, { credentials:"include" });
  const j = await res.json().catch(()=>({}));

  if(!res.ok || !j.ok){
    titleEl.textContent = "الملف";
    list.innerHTML = `<div class="empty">${esc(j.error || "تعذر تحميل فيديوهات الأساتذة")}</div>`;
    return;
  }

  const items = Array.isArray(j.items) ? j.items : [];

  const nPlaylist = norm(playlist);
  const nPartner  = norm(partner);

  const filtered = items.filter(v => {
    const samePlaylistById =
      playlistId ? (String(v.playlist_id || "") === String(playlistId)) : false;

    const samePlaylistByName =
      !playlistId ? (
        norm(v.playlist_name || "") === nPlaylist ||
        norm(v.playlist_slug || "") === nPlaylist ||
        norm(v.playlist || "") === nPlaylist ||
        norm(v.course_name || "") === nPlaylist
      ) : false;

    const samePlaylist = samePlaylistById || samePlaylistByName;

    const partnerField = v.partner_name || v.partner_username || v.partner || "";
    const samePartner  = partner ? (norm(partnerField) === nPartner) : true;

    return samePlaylist && samePartner;
  });

  const playlistName = playlist || filtered[0]?.playlist_name || ("Playlist #" + playlistId);

  titleEl.textContent = playlistName;

  info.innerHTML = `
    كل الفيديوهات داخل هذا الملف
    ${partner ? `<br><span style="font-size:16px;color:#707b96">الأستاذ: ${esc(partner)}</span>` : ``}
    <br><span style="font-size:16px;color:#707b96">المادة: ${esc(course)} | عدد الفيديوهات: ${filtered.length}</span>
  `;

  if(!filtered.length){
    list.innerHTML = `
      <div class="empty">
        لا يوجد فيديوهات داخل هذه البلاي ليست.<br>
        <span style="display:block;margin-top:6px;color:#777">
          تأكد أن اسم البلاي ليست والأستاذ مطابق، أو جرّب إعادة فتح البكج.
        </span>
      </div>
    `;
    return;
  }

  filtered.sort((a,b)=>{
    const da = new Date(String(a.created_at||"").replace(" ","T")).getTime() || 0;
    const db = new Date(String(b.created_at||"").replace(" ","T")).getTime() || 0;
    return db - da;
  });

  const FALLBACK_THUMB =
    "data:image/svg+xml;utf8," +
    encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="200" height="110">
      <rect width="100%" height="100%" fill="#eaf0fb"/>
      <circle cx="100" cy="55" r="24" fill="#0b2f6b"/>
      <polygon points="94,43 94,67 114,55" fill="white"/>
    </svg>`);

  filtered.forEach((v, index)=>{
    const title = v.video_title || v.title || "فيديو";
    const desc = v.description || getDesc(title);
    const tag = v.tag || getTag(title);

    const cover =
      v.cover_path
        ? ("/utbn-backend/" + String(v.cover_path).replace(/^\/+/, ""))
        : (v.cover_url || FALLBACK_THUMB);

    const article = document.createElement("article");
    article.className = "lesson-card";

    article.innerHTML = `
      <div class="play-box" style="background-image:url('${esc(cover)}')">
  <div class="play-circle">▶</div>
</div>

      <div class="lesson-info">
        <h2 class="lesson-title">${esc(title)}</h2>
        <div class="video-id">Video ID: ${esc(v.video_id || v.id || "")}</div>
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
        <div class="thumb" style="background-image:url('${esc(cover)}')"></div>
        <button class="watch-btn" type="button">
          <span>▶</span>
          <span>مشاهدة</span>
        </button>
      </div>
    `;

    const go = () => {
      window.location.href =
        "partner_video.php?vid=" + encodeURIComponent(v.video_id || v.id) +
        "&course=" + encodeURIComponent(course) +
        "&playlist=" + encodeURIComponent(playlist) +
        "&playlist_id=" + encodeURIComponent(playlistId) +
        "&partner=" + encodeURIComponent(partner);
    };

    article.addEventListener("click", go);
    article.querySelector(".watch-btn").addEventListener("click", (e)=>{
      e.preventDefault();
      e.stopPropagation();
      go();
    });

    list.appendChild(article);
  });
}

load();
</script>
<script>
document.getElementById("logoutBtn")?.addEventListener("click", async function(e){
  e.preventDefault();

  try {
    await fetch("/utbn-backend/api/logout.php", {
      method: "POST",
      credentials: "include",
      headers: {
        "X-CSRF-Token": localStorage.getItem("csrf_token") || ""
      }
    });
  } catch (err) {}

  localStorage.removeItem("csrf_token");
  window.location.href = "login.html";
});
</script>
</body>
</html>