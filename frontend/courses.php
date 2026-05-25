<?php
require_once __DIR__ . "/../utbn-backend/api/session_bootstrap.php";

if (
  !isset($_SESSION["user_id"]) ||
  !isset($_SESSION["role"]) ||
  $_SESSION["role"] !== "student"
) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <title>QOYN - Courses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <script src="assets/js/i18n.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --navy:#082f63;
  --navy2:#0b376f;
  --yellow:#ffb31f;
  --yellowSoft:#fff4df;
  --bg:#fff;
  --card:#ffffff;
  --text:#0b2d55;
  --muted:#7c8498;
  --line:#edf0f6;
  --shadow:0 18px 45px rgba(18,45,78,.10);
}

*{box-sizing:border-box}

body{
  margin:0;
  background:#fff;
  color:var(--text);
  font-family:"Poppins",sans-serif;
  overflow-x:hidden;
}

/* HEADER */
.qoyn-topbar{
  width:100%;
  height:80px;
  background:#fff;
  border-bottom:1px solid #eef1f6;
  box-shadow:0 8px 28px rgba(20,43,70,.05);
  position:sticky;
  top:0;
  z-index:999;
}

.qoyn-topbar-inner{
  width:calc(100% - 100px);
  height:80px;
  margin:0 auto;
  display:flex;
  align-items:center;
  justify-content:space-between;
  direction:ltr;
}

.qoyn-logo{
  text-decoration:none;
  font-family:"Montserrat",sans-serif;
  font-size:30px;
  font-weight:900;
  color:#082f63;
  letter-spacing:.5px;
}

.qoyn-navlinks{
  display:flex;
  align-items:center;
  gap:22px;
}

.lang-btn{
  border:0;
  background:transparent;
  color:#4d5569;
  font-size:14px;
  font-weight:700;
  display:flex;
  align-items:center;
  gap:8px;
  cursor:pointer;
  padding:12px 8px;
}

.qoyn-navlinks a{
  text-decoration:none;
  color:#4d5569;
  font-size:14px;
  font-weight:700;
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:13px 17px;
  border-radius:18px;
  transition:.2s ease;
  white-space:nowrap;
}

.qoyn-navlinks a:hover{
  color:var(--yellow);
}

.qoyn-navlinks a.active{
  color:var(--yellow);
  background:var(--yellowSoft);
}

.nav-icon{
  font-size:16px;
}

/* PAGE */
.page-wrap{
  min-height:calc(100vh - 80px);
  position:relative;
  padding-bottom:32px;
}

.page-wrap:before{
  content:"";
  position:absolute;
  right:-120px;
  top:0;
  width:330px;
  height:330px;
  background:radial-gradient(circle, rgba(255,179,31,.18), rgba(255,179,31,0) 65%);
  border-radius:50%;
  pointer-events:none;
}

.dots{
  position:absolute;
  right:330px;
  top:62px;
  width:95px;
  height:85px;
  background-image:radial-gradient(rgba(10,46,93,.18) 1.6px, transparent 1.6px);
  background-size:18px 18px;
  opacity:.75;
  pointer-events:none;
}

.container{
  width:calc(100% - 130px);
  max-width:1360px;
  margin:0 auto;
  padding:50px 0 0;
  position:relative;
  z-index:2;
}

/* HERO */
.courses-hero{
  text-align:left;
  direction:ltr;
  margin-bottom:32px;
}

.courses-hero h1{
  margin:0;
  font-family:"Montserrat",sans-serif;
  font-size:40px;
  line-height:1.15;
  font-weight:900;
  letter-spacing:-1px;
  color:#061f42;
}

.courses-hero p{
  margin:10px 0 0;
  font-size:15px;
  font-weight:500;
  color:#788297;
}

.hero-line{
  width:44px;
  height:3px;
  border-radius:99px;
  background:var(--yellow);
  margin-top:20px;
}

/* GRID CARDS */
.courses-list{
  display:grid;
  grid-template-columns:repeat(4, minmax(0,1fr));
  gap:30px;
}

.courseCard{
  background:#fff;
  border:1px solid #eef1f6;
  border-radius:12px;
  box-shadow:var(--shadow);
  overflow:hidden;
  cursor:pointer;
  transition:.22s ease;
  min-height:390px;
  display:flex;
  flex-direction:column;
}

.courseCard:hover{
  transform:translateY(-5px);
  box-shadow:0 22px 55px rgba(18,45,78,.15);
}

.thumbWrap{
  width:100%;
  height:165px;
  position:relative;
  overflow:hidden;
  background:#0b2d55;
}

.thumb{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}

.featureBadge{
  position:absolute;
  left:14px;
  top:14px;
  background:var(--yellow);
  color:#fff;
  border-radius:999px;
  padding:6px 10px;
  font-size:11px;
  font-weight:900;
  box-shadow:0 8px 18px rgba(255,179,31,.25);
  z-index:2;
}

.saveBadge{
  background:#e92f64;
}

.courseInfo{
  padding:22px 18px 0;
  flex:1;
}

.titleRow{
  display:flex;
  align-items:flex-start;
  gap:13px;
  margin-bottom:22px;
}

.courseIcon{
  width:32px;
  height:32px;
  border-radius:50%;
  color:var(--navy);
  display:grid;
  place-items:center;
  font-size:14px;
  flex:0 0 auto;
  border:1px solid rgba(8,47,99,0.10);
}

.courseTitle{
  margin:0;
  color:#0b2d55;
  font-family:"Montserrat",sans-serif;
  font-size:16px;
  font-weight:900;
  line-height:1.35;
}

.courseMeta{
  display:flex;
  flex-direction:column;
  gap:12px;
  color:#7c8498;
  font-size:12px;
  font-weight:600;
}

.metaItem{
  display:flex;
  align-items:flex-start;
  gap:10px;
  line-height:1.45;
}

.metaIcon{
  color:#69728b;
  width:14px;
  flex:0 0 14px;
  text-align:center;
}

.actions{
  display:flex;
  align-items:center;
  gap:18px;
  padding:18px;
  margin-top:auto;
}

.playlistBtn{
  height:34px;
  padding:0 12px;
  border-radius:12px;
  border:1px solid #e9edf4;
  background:#fff;
  color:#0b2d55;
  font-family:"Montserrat",sans-serif;
  font-size:10px;
  font-weight:900;
  display:inline-flex;
  align-items:center;
  gap:7px;
  white-space:nowrap;
  cursor:pointer;
}

.openBtn{
  height:34px;
  min-width:76px;
  padding:0 16px;
  border-radius:14px;
  border:0;
  background:var(--navy);
  color:#fff;
  font-family:"Montserrat",sans-serif;
  font-size:11px;
  font-weight:900;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  cursor:pointer;
  box-shadow:0 13px 28px rgba(8,47,99,.18);
  transition:.2s ease;
  margin-left:auto;
}

.openBtn:hover{
  background:var(--navy2);
  transform:translateY(-1px);
}

/* COMING SOON */
.comingSoon{
  margin-top:36px;
  background:#fff;
  min-height:98px;
  border-radius:12px;
  box-shadow:var(--shadow);
  border:1px solid #eef1f6;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:20px 70px 20px 34px;
  overflow:hidden;
}

.comingLeft{
  display:flex;
  align-items:center;
  gap:22px;
}

.comingIcon{
  width:58px;
  height:58px;
  border-radius:50%;
  background:var(--yellowSoft);
  color:var(--yellow);
  display:grid;
  place-items:center;
  font-size:28px;
}

.comingSoon h3{
  margin:0;
  font-family:"Montserrat",sans-serif;
  font-size:18px;
  font-weight:900;
  color:#0b2d55;
}

.comingSoon p{
  margin:5px 0 0;
  color:#7c8498;
  font-size:13px;
  font-weight:500;
}

.comingArt{
  font-size:80px;
  opacity:.07;
  color:#0b2d55;
  line-height:1;
}

.loadingBox,
.emptyBox{
  grid-column:1 / -1;
  background:#fff;
  padding:24px;
  border-radius:18px;
  border:1px solid #eef1f6;
  box-shadow:var(--shadow);
  color:#7c8498;
  font-weight:700;
}

/* AR */
html[lang="ar"] .courses-hero{
  direction:rtl;
  text-align:right;
}

html[lang="ar"] .courseCard{
  direction:rtl;
}

html[lang="ar"] .featureBadge{
  left:auto;
  right:14px;
}

html[lang="ar"] .openBtn{
  margin-left:0;
  margin-right:auto;
}

/* RESPONSIVE */
@media(max-width:1150px){
  .courses-list{
    grid-template-columns:repeat(3, minmax(0,1fr));
  }
}

@media(max-width:900px){
  .qoyn-topbar-inner{
    width:100%;
    height:auto;
    padding:18px;
    flex-direction:column;
    align-items:flex-start;
    gap:12px;
  }

  .qoyn-topbar{
    height:auto;
  }

  .qoyn-navlinks{
    width:100%;
    overflow-x:auto;
  }

  .container{
    width:100%;
    padding:35px 18px 0;
  }

  .courses-list{
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:18px;
  }

  .dots,
  .page-wrap:before{
    display:none;
  }
}

@media(max-width:560px){
  .courses-list{
    grid-template-columns:1fr;
  }

  .courses-hero h1{
    font-size:30px;
  }

  .actions{
    flex-direction:column;
    align-items:stretch;
  }

  .playlistBtn,
  .openBtn{
    width:100%;
    justify-content:center;
    margin:0;
  }

  .comingArt{
    display:none;
  }

  .comingSoon{
    padding:18px;
  }
}
.nav-wrap{
  position:sticky;
  top:0;
  z-index:999;
  padding:14px 22px;
  background:rgba(255,255,255,.92);
  backdrop-filter:blur(10px);
  box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.nav{
  max-width:1200px;
  margin:0 auto;
  display:flex;
  align-items:center;
  gap:16px;
}

.logo{
  font-family:"Montserrat";
  font-weight:800;
  font-size:26px;
  color:#0A2E5D;
  text-decoration:none;
}

.nav-spacer{flex:1}

.nav-links{
  display:flex;
  align-items:center;
  gap:14px;
  list-style:none;
}

.nav-links a{
  text-decoration:none;
  color:#111;
  font-weight:600;
  padding:10px 14px;
  border-radius:999px;
  transition:.2s;
}

.nav-links a:hover{
  color:#ffb31f;
}

.nav-links a.active{
  background:#ffb31f;
  color:#fff;
}

.nav-logout{
  border:1px solid #0A2E5D;
}

.nav-logout:hover{
  background:#0A2E5D;
  color:#fff !important;
}

/* language switch */
.lang-switch{
  display:flex;
  background:#f1f3f7;
  border-radius:999px;
  padding:4px;
}

.lang-option{
  border:none;
  background:transparent;
  padding:6px 10px;
  border-radius:999px;
  font-weight:700;
  cursor:pointer;
}

.lang-option.active{
  background:#0A2E5D;
  color:#fff;
}
.logo-wrap{
  display:flex;
  align-items:center;
  gap:10px;
}

.nav-monkey{
  height:40px;
  width:auto;
  object-fit:contain;
  transition:.2s;
}

.nav-monkey:hover{
  transform:translateY(-2px) scale(1.05);
}
</style>
</head>

<body>

<header class="nav-wrap" id="navWrap">
  <div class="progress"><div id="progressBar"></div></div>

  <nav class="nav">
<div class="logo-wrap">
  <img 
    src="uploads/MONKEY.png" 
    alt="logo" 
    class="nav-monkey"
    onerror="this.style.display='none'"
  >

  <a class="qoyn-logo" href="student-dashboard.php#home">QOYN</a>
</div>
    <div class="nav-spacer"></div>

    <!-- LANGUAGE -->
    <div class="lang-dropdown">
     
    </div>

    <!-- LINKS -->
    <ul class="nav-links">
      <li><a href="student-dashboard.php#home">Home</a></li>

      <li><a href="my_courses.php">My Courses</a></li>

<li><a href="courses.php" class="active">All Courses</a></li>


      <li><a href="#" id="logoutBtn" class="nav-logout">Logout</a></li>
    </ul>
  </nav>
</header>

<div class="page-wrap">
  <div class="dots"></div>

  <main class="container">
    <section class="courses-hero">
      <h1 data-i18n="discover_our_courses" data-i18n-html="true">Discover Our Courses</h1>
      <p data-i18n="courses_subtitle">Explore curated tracks to boost your skills and advance your career</p>
      <div class="hero-line"></div>
    </section>

    <section id="partnerPackagesBox">
      <div id="partnerGrid" class="courses-list">
        <div class="loadingBox">Loading courses...</div>
      </div>
    </section>

    <section class="comingSoon">
      <div class="comingLeft">
        <div class="comingIcon">⌂</div>
        <div>
          <h3 data-i18n="more_coming_soon">More coming soon!</h3>
          <p data-i18n="more_coming_soon_text">We're adding more high-quality courses to help you grow.</p>
        </div>
      </div>
      <div class="comingArt">▱⌂</div>
    </section>
  </main>
</div>

<script>
function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, m => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[m]));
}

function coverUrlFromPath(p){
  p = String(p || "").trim();
  if (!p) return "";
  p = p.replace(/\\/g, "/");
  if (p.startsWith("http://") || p.startsWith("https://")) return p;
  if (p.startsWith("/")) return p;
  return "/utbn-backend/" + p;
}

function fmtDate(s){
  if (!s) return "";
  const d = new Date(String(s).replace(" ", "T"));
  if (isNaN(d.getTime())) return String(s);
  return d.toLocaleString("en", {
    month:"2-digit",
    day:"2-digit",
    year:"numeric",
    hour:"2-digit",
    minute:"2-digit"
  });
}

const FALLBACK_THUMB =
  "data:image/svg+xml;utf8," +
  encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="420" height="240">
    <defs>
      <linearGradient id="g" x1="0" x2="1">
        <stop offset="0" stop-color="#082f63"/>
        <stop offset="1" stop-color="#03162e"/>
      </linearGradient>
    </defs>
    <rect width="100%" height="100%" fill="url(#g)"/>
    <circle cx="210" cy="105" r="55" fill="none" stroke="#ffb31f" stroke-width="5" opacity=".9"/>
    <text x="34" y="58" fill="#ffffff" font-size="26" font-family="Arial" font-weight="700">QOYN</text>
    <text x="34" y="200" fill="#ffffff" font-size="22" font-family="Arial" font-weight="700">COURSE</text>
  </svg>`);

function iconForCourse(name, index){
  const n = String(name || "").toLowerCase();
  if (n.includes("security") || n.includes("crypto")) return "◈";
  if (n.includes("web")) return "⬢";
  if (n.includes("deep")) return "◩";
  if (n.includes("machine")) return "⬟";
  const icons = ["◈","⬢","◩","⬟","◆"];
  return icons[index % icons.length];
}

function iconBg(index){
  const colors = ["#fff4e3","#f3e9ff","#eaf5ff","#eef8e7","#fff4e3"];
  return colors[index % colors.length];
}

function groupPartnerPackages(items){
  const map = new Map();

  for (const it of (items || [])) {
    if (String(it.type || "") !== "playlist") continue;

    const pid = it.playlist_id;
    if (!pid) continue;

    const key = String(pid);

    if (!map.has(key)) {
      const cover_url = String(it.cover_url || "").trim();
      const cover_path = String(it.cover_path || "").trim();

      map.set(key, {
        playlist_id: pid,
        video_id: it.video_id || it.id || it.vid || it.first_video_id || 0,
        playlist: String(it.playlist_name || "Playlist").trim(),
        partner: String(it.partner_name || "qoyn").trim(),
        course: String(it.course_name || it.course || it.playlist_name || "").trim(),
        difficulty: it.difficulty ?? 0,
        coin_pool: it.coin_pool ?? 0,
        published_at: it.published_at || it.created_at || "",
        cover_url: cover_url || coverUrlFromPath(cover_path)
      });
    }
  }

  return Array.from(map.values());
}

function makePartnerPackageCard(pkg, index){
  const card = document.createElement("article");
  card.className = "courseCard";

  const isFirst = index === 0;
  const isFourth = index === 3;

  card.innerHTML = `
    <div class="thumbWrap">
      ${isFirst ? `<span class="featureBadge">★ Featured</span>` : ``}
      ${isFourth ? `<span class="featureBadge saveBadge">☮ Save</span>` : ``}
      <img class="thumb" src="${esc(pkg.cover_url || FALLBACK_THUMB)}" alt="">
    </div>

    <div class="courseInfo">
      <div class="titleRow">
        <div class="courseIcon" style="background:${iconBg(index)}">${iconForCourse(pkg.course || pkg.playlist, index)}</div>
        <h2 class="courseTitle">${esc(pkg.course || pkg.playlist)}</h2>
      </div>

      <div class="courseMeta">
        <span class="metaItem"><span class="metaIcon">♙</span> Teacher: ${esc(pkg.partner || "qoyn")}</span>
        <span class="metaItem"><span class="metaIcon">▥</span> Course: ${esc(pkg.course || pkg.playlist)}</span>
        <span class="metaItem"><span class="metaIcon">▣</span> Publish Date: ${esc(fmtDate(pkg.published_at))}</span>
      </div>
    </div>

    <div class="actions">
      <button class="playlistBtn" type="button">☷ Instructor's Playlist</button>
      <button class="openBtn" type="button">Open <span>→</span></button>
    </div>
  `;

  const go = () => {
    if (pkg.video_id && Number(pkg.video_id) > 0) {
      window.location.href =
        "partner_video.php?vid=" + encodeURIComponent(pkg.video_id) +
        "&course=" + encodeURIComponent(pkg.course || "") +
        "&playlist=" + encodeURIComponent(pkg.playlist || "") +
        "&playlist_id=" + encodeURIComponent(pkg.playlist_id || "") +
        "&partner=" + encodeURIComponent(pkg.partner || "");
    } else {
      window.location.href =
        "partner_playlist.php?playlist_id=" + encodeURIComponent(pkg.playlist_id || "") +
        "&course=" + encodeURIComponent(pkg.course || "") +
        "&playlist=" + encodeURIComponent(pkg.playlist || "") +
        "&partner=" + encodeURIComponent(pkg.partner || "");
    }
  };

  card.addEventListener("click", go);

  card.querySelector(".openBtn").addEventListener("click", (e)=>{
    e.preventDefault();
    e.stopPropagation();
    go();
  });

  card.querySelector(".playlistBtn").addEventListener("click", (e)=>{
    e.preventDefault();
    e.stopPropagation();
    go();
  });

  return card;
}

async function fetchPartnerFeed(){
  const res = await fetch("/utbn-backend/api/student_partner_feed.php", {
    credentials: "include",
    cache: "no-store"
  });
  return res.json();
}

async function loadAll(){
  const grid = document.getElementById("partnerGrid");
  grid.innerHTML = `<div class="loadingBox">Loading courses...</div>`;

  let data;

  try{
    data = await fetchPartnerFeed();
  }catch(e){
    grid.innerHTML = `<div class="emptyBox">Failed loading courses.</div>`;
    return;
  }

  const raw = (data && data.ok && Array.isArray(data.items)) ? data.items : [];
  const packages = groupPartnerPackages(raw);

  grid.innerHTML = "";

  if (!packages.length) {
    grid.innerHTML = `<div class="emptyBox">No courses available yet.</div>`;
    return;
  }

  packages.slice(0, 30).forEach((pkg, index) => {
    grid.appendChild(makePartnerPackageCard(pkg, index));
  });
}

loadAll();
</script>

<script>
(function () {
  const langBtn = document.getElementById("langBtn");
  const langText = document.getElementById("langText");

  function getLangSafe() {
    if (typeof getCurrentLang === "function") {
      return getCurrentLang() || "en";
    }
    return localStorage.getItem("lang") || localStorage.getItem("qoyn_lang") || "en";
  }

  function updateLangUI(lang) {
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
    langText.textContent = lang === "ar" ? "العربية" : "English";
  }

  function setLangSafe(lang) {
    localStorage.setItem("lang", lang);
    localStorage.setItem("qoyn_lang", lang);

    if (typeof setLanguage === "function") {
      setLanguage(lang);
    } else if (typeof changeLanguage === "function") {
      changeLanguage(lang);
    } else if (window.i18n && typeof window.i18n.setLanguage === "function") {
      window.i18n.setLanguage(lang);
    }

    updateLangUI(lang);
    document.dispatchEvent(new CustomEvent("languageChanged"));
  }

  langBtn?.addEventListener("click", function () {
    const current = getLangSafe();
    setLangSafe(current === "ar" ? "en" : "ar");
  });

  document.addEventListener("languageChanged", function () {
    updateLangUI(getLangSafe());
  });

  updateLangUI(getLangSafe());
})();
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