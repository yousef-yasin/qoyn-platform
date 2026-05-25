<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }

$vid = (int)($_GET["vid"] ?? 0);
if ($vid <= 0) { die("Video غير صالح"); }

$course      = isset($_GET["course"]) ? trim($_GET["course"]) : "";
$playlist    = isset($_GET["playlist"]) ? trim($_GET["playlist"]) : "";
$playlist_id = isset($_GET["playlist_id"]) ? (int)$_GET["playlist_id"] : 0;
$partner     = isset($_GET["partner"]) ? trim($_GET["partner"]) : "";
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>QOYN | Partner Video</title>

  <script src="assets/js/i18n.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#082f63;
      --navy2:#0b376f;
      --yellow:#082f63;
      --yellowSoft:#eaf1fb;
      --bg:#f5f8fc;
      --card:#ffffff;
      --text:#0b2d55;
      --muted:#758097;
      --line:#e9eef6;
      --shadow:0 18px 45px rgba(8,47,99,.09);
      --radius:24px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      background:linear-gradient(180deg,#f8fbff 0%,#f2f6fb 100%);
      color:var(--text);
      font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      overflow-x:hidden;
      direction:ltr;
    }

    .topNav{
      width:100%;
      background:#fff;
      border-bottom:1px solid #eef1f6;
      box-shadow:0 8px 28px rgba(20,43,70,.05);
      position:sticky;
      top:0;
      z-index:999;
    }

    .topNavInner{
      width:calc(100% - 70px);
      margin:0 auto;
      min-height:88px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:20px;
      direction:ltr;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      text-decoration:none;
    }

    .logo{
      font-family:"Montserrat",sans-serif;
      font-size:32px;
      font-weight:900;
      letter-spacing:.5px;
      color:var(--navy);
    }

    .monkey{
      height:55px;
      width:auto;
      object-fit:contain;
      filter:drop-shadow(0 10px 20px rgba(8,47,99,.16));
    }

    .navLinks{
      display:flex;
      align-items:center;
      gap:18px;
      direction:ltr;
    }

    .navLinks a{
      border:0;
      background:transparent;
      text-decoration:none;
      color:#4d5569;
      font-family:"Poppins",sans-serif;
      font-size:15px;
      font-weight:800;
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:14px 20px;
      border-radius:18px;
      cursor:pointer;
      transition:.2s ease;
      white-space:nowrap;
    }

    .navLinks a:hover{
      color:var(--navy);
      background:#f3f7fc;
      transform:translateY(-1px);
    }

    .navLinks .active{
      color:#fff;
      background:var(--navy);
      box-shadow:0 14px 28px rgba(8,47,99,.18);
    }

    .lang-dropdown{
      position:relative;
      display:inline-flex;
      align-items:center;
      justify-content:center;
    }

    .lang-switch{
      position:relative;
      width:86px;
      height:46px;
      border-radius:999px;
      background:linear-gradient(180deg, #f4f6fa 0%, #e9edf3 100%);
      border:1px solid rgba(10,46,93,.10);
      box-shadow:
        inset 0 2px 4px rgba(255,255,255,.75),
        0 8px 18px rgba(15,23,42,.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:5px;
      overflow:hidden;
      direction:ltr;
    }

    .lang-switch-thumb{
      position:absolute;
      top:5px;
      left:5px;
      width:36px;
      height:36px;
      border-radius:50%;
      background:var(--navy);
      box-shadow:
        0 8px 18px rgba(10,46,93,.28),
        inset 0 1px 0 rgba(255,255,255,.20);
      transition:left .28s ease;
      z-index:1;
    }

    .lang-switch.is-ar .lang-switch-thumb{
      left:45px;
    }

    .lang-option{
      position:relative;
      z-index:2;
      width:36px;
      height:36px;
      border:none;
      background:transparent !important;
      border-radius:50%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:0;
      cursor:pointer;
      font-family:"Montserrat",sans-serif;
      font-size:14px;
      font-weight:900;
      color:#8794a8;
    }

    .lang-option.active{
      color:#fff;
    }

    .page{
      width:calc(100% - 140px);
      max-width:1500px;
      margin:0 auto;
      padding:30px 0 50px;
      direction:ltr;
    }

    .hero{
  position:relative;
  overflow:hidden;

  /* ❌ شيل الخلفية */
  background:transparent !important;

  /* ❌ شيل الإطار */
  border:none !important;

  /* ❌ شيل التدوير */
  border-radius:0 !important;

  /* ❌ شيل الظل */
  box-shadow:none !important;

  /* ✨ خفف البادينغ بس خليه مرتب */
  padding:20px 0 !important;

  margin-bottom:28px;
}

    .hero:after{
      content:"";
      position:absolute;
      right:60px;
      top:35px;
      width:135px;
      height:90px;
      background-image:radial-gradient(rgba(8,47,99,.16) 2px, transparent 2px);
      background-size:20px 20px;
      opacity:.75;
    }

    h1{
      margin:0;
      font-family:"Montserrat",sans-serif;
      font-size:52px;
      line-height:1.08;
      font-weight:900;
      color:var(--navy);
      letter-spacing:-1.4px;
      max-width:900px;
      position:relative;
      z-index:2;
    }

    .heroSub{
      margin-top:12px;
      color:var(--muted);
      font-size:16px;
      font-weight:500;
      position:relative;
      z-index:2;
    }

    .heroLine{
      width:44px;
      height:4px;
      background:var(--navy);
      border-radius:999px;
      margin-top:20px;
      position:relative;
      z-index:2;
    }

    #meta{
      margin-top:14px;
      color:var(--muted);
      font-size:13px;
      font-weight:600;
      position:relative;
      z-index:2;
    }

    .watchGrid{
      display:grid;
      grid-template-columns:minmax(0,1fr) 345px;
      gap:28px;
      align-items:start;
    }

    .mainWatch{
      min-width:0;
    }

    .card{
      background:#fff;
      border:1px solid var(--line);
      border-radius:24px;
      box-shadow:var(--shadow);
    }

    .videoCard{
      overflow:hidden;
      padding:20px;
      margin-bottom:30px;
    }

    .playerWrap{
      width:100%;
      border-radius:20px;
      overflow:hidden;
      background:#000;
      border:1px solid rgba(8,47,99,.08);
      box-shadow:0 20px 48px rgba(8,47,99,.12);
    }

    #player{
      width:100%;
      display:block;
      aspect-ratio:16/9;
      background:#000;
      border:0;
      outline:0;
    }

    .videoInfo{
      padding:18px 6px 4px;
      color:var(--muted);
      font-size:15px;
      font-weight:600;
    }

    .sideCol{
      display:flex;
      flex-direction:column;
      gap:20px;
      position:sticky;
      top:112px;
    }

    .playlistCard,
    .progressCard{
      padding:22px;
      border-radius:24px;
    }

    .sideTitle{
      display:flex;
      align-items:center;
      gap:10px;
      margin-bottom:22px;
    }

    .sideTitle span{
      color:var(--navy);
      font-family:"Montserrat",sans-serif;
      font-size:18px;
      font-weight:900;
    }

    .smallLine{
      width:36px;
      height:3px;
      background:var(--navy);
      border-radius:999px;
      margin-bottom:16px;
    }

    .videoList{
      display:flex;
      flex-direction:column;
      gap:12px;
    }

    .lessonItem{
      display:flex;
      align-items:center;
      gap:14px;
      text-decoration:none;
      padding:14px;
      border-radius:16px;
      border:1px solid transparent;
      color:var(--navy);
      transition:.18s ease;
    }

    .lessonItem:hover{
      background:#f6f9ff;
      transform:translateY(-1px);
    }

    .lessonItem.active{
      background:#f5f9ff;
      border-color:rgba(8,47,99,.18);
    }

    .playIcon{
      width:36px;
      height:36px;
      border-radius:50%;
      display:grid;
      place-items:center;
      flex:0 0 auto;
      background:rgba(8,47,99,.08);
      color:var(--navy);
      font-weight:900;
      font-size:12px;
    }

    .lessonItem.active .playIcon{
      background:var(--navy);
      color:#fff;
    }

    .lessonName{
      font-family:"Montserrat",sans-serif;
      font-weight:900;
      font-size:14px;
      line-height:1.35;
      color:var(--navy);
    }

    .lessonId{
      color:var(--muted);
      font-size:12px;
      font-weight:600;
      margin-top:4px;
    }

    .viewAll{
      display:flex;
      align-items:center;
      justify-content:center;
      margin-top:18px;
      min-height:48px;
      border-radius:14px;
      background:#f3f6fc;
      color:var(--navy);
      text-decoration:none;
      font-family:"Montserrat",sans-serif;
      font-size:14px;
      font-weight:900;
      transition:.18s ease;
    }

    .viewAll:hover{
      background:var(--navy);
      color:#fff;
    }

    .progressNum{
      font-family:"Montserrat",sans-serif;
      font-size:24px;
      font-weight:900;
      color:var(--navy);
      margin-top:8px;
    }

    .progressText{
      color:var(--muted);
      font-size:14px;
      margin-top:4px;
    }

    .progressBar{
      width:100%;
      height:8px;
      border-radius:999px;
      background:#edf1f8;
      margin-top:18px;
      overflow:hidden;
    }

    .progressFill{
      height:100%;
      width:0%;
      background:var(--navy);
      border-radius:999px;
    }

    .questionsWrap{
      position:relative;
      padding:0 0 0 20px;
      margin-top:20px;
    }

    .questionsHead{
      display:flex;
      align-items:center;
      gap:16px;
      margin-bottom:22px;
    }

    .qHeadIcon{
      width:22px;
      height:22px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:#edf4ff;
      color:var(--navy);
      font-weight:900;
      font-size:13px;
    }

    .questionsHead h2{
      margin:0;
      color:var(--navy);
      font-family:"Montserrat",sans-serif;
      font-size:22px;
      font-weight:900;
    }

    .questionsHead .line{
      width:42px;
      height:4px;
      border-radius:999px;
      background:var(--navy);
      margin-left:8px;
    }

    .finishBox{
      display:flex;
      gap:14px;
      align-items:center;
      flex-wrap:wrap;
      margin-bottom:24px;
    }

    .btn{
      border:0;
      cursor:pointer;
      min-height:54px;
      padding:0 28px;
      border-radius:999px;
      background:var(--navy);
      color:#fff;
      font-family:"Montserrat",sans-serif;
      font-size:14px;
      font-weight:900;
      box-shadow:0 18px 40px rgba(8,47,99,.20);
      transition:.2s ease;
    }

    .btn:hover{
      background:var(--navy2);
      transform:translateY(-2px);
    }

    #msg{
      color:var(--muted);
      font-size:13px;
      font-weight:700;
    }

    #quiz{
      position:relative;
      padding-left:34px;
    }

    #quiz:before{
      content:"";
      position:absolute;
      left:14px;
      top:20px;
      bottom:20px;
      width:1px;
      background:#e2e9f4;
    }

    .q{
      position:relative;
      background:#fff;
      border:1px solid var(--line);
      border-radius:24px;
      box-shadow:0 18px 45px rgba(8,47,99,.07);
      padding:26px 26px 22px;
      margin-bottom:28px;
    }

    .qNum{
      position:absolute;
      left:-50px;
      top:20px;
      width:36px;
      height:36px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:var(--navy);
      color:#fff;
      font-family:"Montserrat",sans-serif;
      font-size:13px;
      font-weight:900;
      z-index:2;
    }

    .questionText{
      color:var(--navy);
      font-family:"Montserrat",sans-serif;
      font-size:19px;
      font-weight:900;
      line-height:1.45;
      margin-bottom:16px;
    }

    .opt{
      min-height:60px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      margin-top:12px;
      padding:0 18px;
      border-radius:16px;
      border:1px solid var(--line);
      background:#fff;
      cursor:pointer;
      color:#061b3a;
      font-size:17px;
      font-weight:500;
      transition:.18s ease;
    }

    .opt:hover{
      border-color:rgba(8,47,99,.22);
      background:#f8fbff;
      transform:translateY(-1px);
    }

    .opt input{
      order:2;
      width:22px;
      height:22px;
      accent-color:var(--navy);
      cursor:pointer;
    }

    .opt span{
      order:1;
    }

    .empty{
      padding:22px;
      border-radius:18px;
      background:#fff;
      border:1px dashed rgba(8,47,99,.18);
      color:var(--muted);
      font-weight:800;
      text-align:center;
    }

    @media(max-width:1050px){
      .page,
      .topNavInner{
        width:calc(100% - 32px);
      }

      .watchGrid{
        grid-template-columns:1fr;
      }

      .sideCol{
        position:static;
      }

      h1{
        font-size:38px;
      }
    }

    @media(max-width:600px){
      .topNavInner{
        min-height:auto;
        padding:16px 0;
        flex-direction:column;
        align-items:flex-start;
      }

      .navLinks{
        width:100%;
        overflow-x:auto;
      }

      .hero{
        padding:28px 22px;
      }

      h1{
        font-size:30px;
      }

      .videoCard{
        padding:12px;
      }

      #quiz{
        padding-left:26px;
      }

      .qNum{
        left:-42px;
      }

      .opt{
        font-size:15px;
      }
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

.nav-monkey-wrap{
  display:flex;
  align-items:center;
  gap:10px;
  direction:ltr;
}

.nav-monkey{
  height:42px;
  width:auto;
  object-fit:contain;
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.14));
}

.logo{
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:28px;
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
  font-size:15px;
  padding:10px 16px;
  border-radius:999px;
  transition:.2s ease;
  white-space:nowrap;
}

.nav-links a:hover{
  color:#f4bd3f;
  transform:translateY(-1px);
}

.nav-links a.active{
  background:#f4bd3f;
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
  </style>
</head>

<body>

<header class="nav-wrap" id="navWrap">
  <nav class="nav">

    <div class="nav-monkey-wrap">
      <img 
        src="uploads/MONKEY.png" 
        alt="QOYN Logo" 
        class="nav-monkey"
        onerror="this.style.display='none'"
      >

      <a class="logo" href="student-dashboard.php#home">QOYN</a>
    </div>

    <div class="nav-spacer"></div>

    <ul class="nav-links">
      <li><a href="student-dashboard.php#home">Home</a></li>
      <li><a href="my_courses.php">My courses</a></li>
      <li><a href="courses.php" class="active">All courses</a></li>
<li>
  <a href="partner_playlist.php?course=<?= urlencode($course) ?>&playlist=<?= urlencode($playlist) ?>&playlist_id=<?= (int)$playlist_id ?>&partner=<?= urlencode($partner) ?>">
    Back
  </a>
</li>      <li><a href="#" id="logoutBtn" class="nav-logout">Logout</a></li>
    </ul>

  </nav>
</header>

<main class="page">

  <section class="hero">
    <h1 id="pageTitle">Partner Video</h1>
    <div class="heroSub" data-i18n="watch_video_subtitle">Watch the video, continue the playlist, and answer the questions.</div>
    <div class="heroLine"></div>
  </section>

  <section class="watchGrid">
    <div class="mainWatch">
      <div class="card videoCard">
        <div class="playerWrap">
          <video id="player" controls playsinline></video>
        </div>

        <div class="videoInfo">
          <div id="videoIdText">Video ID: <?= (int)$vid ?></div>
        </div>
      </div>

      <section class="questionsWrap">
        <div class="questionsHead">
          <span class="qHeadIcon">?</span>
          <h2 data-i18n="questions">Questions</h2>
          <span class="line"></span>
        </div>

        <div class="finishBox">
          <button class="btn" id="btnFinish" type="button" data-i18n="check_submit">Check & Submit</button>
          <div id="msg"></div>
        </div>

        <div id="quiz"></div>
      </section>
    </div>

    <aside class="sideCol">
      <div class="card playlistCard">
        <div class="sideTitle">
          <span>🖼️</span>
          <span data-i18n="course_playlist">Course Playlist</span>
        </div>
        <div class="smallLine"></div>

        <div class="videoList" id="videoList">
          <div class="empty">Loading playlist...</div>
        </div>

        <a class="viewAll"
           href="partner_playlist.php?course=<?= urlencode($course) ?>&playlist=<?= urlencode($playlist) ?>&playlist_id=<?= (int)$playlist_id ?>&partner=<?= urlencode($partner) ?>"
           data-i18n="view_all_lessons">
          View all lessons ›
        </a>
      </div>

      <div class="card progressCard">
        <div class="sideTitle">
          <span>📈</span>
          <span data-i18n="your_progress">Your Progress</span>
        </div>
        <div class="progressNum" id="progressNum">0 / 0</div>
        <div class="progressText" data-i18n="lessons_completed">Lessons Completed</div>
        <div class="progressBar"><div class="progressFill" id="progressFill"></div></div>
      </div>
    </aside>
  </section>

</main>

<script>
const vid = <?= (int)$vid ?>;
const CURRENT_COURSE = <?= json_encode($course) ?>;
const CURRENT_PLAYLIST = <?= json_encode($playlist) ?>;
const CURRENT_PLAYLIST_ID = <?= (int)$playlist_id ?>;
const CURRENT_PARTNER = <?= json_encode($partner) ?>;

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, m => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[m]));
}

async function apiGet(path){
  const r = await fetch("/utbn-backend/api/" + path, { credentials:"include" });
  const j = await r.json().catch(()=>({}));
  return {ok:r.ok, status:r.status, json:j};
}

async function apiPost(path, data){
  const r = await fetch("/utbn-backend/api/" + path, {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    credentials:"include",
    body: JSON.stringify(data)
  });
  const j = await r.json().catch(()=>({}));
  return {ok:r.ok, status:r.status, json:j};
}

let quiz = [];
let answers = {};

function renderQuiz(){
  const wrap = document.getElementById("quiz");
  wrap.innerHTML = "";

  quiz.forEach((q, i) => {
    const box = document.createElement("div");
    box.className = "q";

    const num = String(i + 1).padStart(2, "0");

    box.innerHTML = `
      <div class="qNum">${num}</div>
      <div class="questionText">${esc(q.question)}</div>
      ${(q.options || []).map((opt, k) => {
        const letter = ["A","B","C","D"][k] || "";
        return `
          <label class="opt">
            <input type="radio" name="q${i}" value="${letter}">
            <span>${esc(opt)}</span>
          </label>
        `;
      }).join("")}
    `;

    box.querySelectorAll(`input[name="q${i}"]`).forEach(inp=>{
      inp.addEventListener("change", ()=>{ answers[i] = inp.value; });
    });

    wrap.appendChild(box);
  });
}

function videoTitleFromObj(v){
  return v.title || v.video_title || v.name || v.lesson_title || "Video";
}

function videoIdFromObj(v){
  return v.video_id || v.id || v.vid || 0;
}

function makePartnerVideoUrl(videoId){
  return "partner_video.php?vid=" + encodeURIComponent(videoId) +
    "&course=" + encodeURIComponent(CURRENT_COURSE || "") +
    "&playlist=" + encodeURIComponent(CURRENT_PLAYLIST || "") +
    "&playlist_id=" + encodeURIComponent(CURRENT_PLAYLIST_ID || "") +
    "&partner=" + encodeURIComponent(CURRENT_PARTNER || "");
}

function renderVideoList(items){
  const list = document.getElementById("videoList");
  if (!list) return;

  const clean = [];
  const seen = new Set();

  (items || []).forEach(v => {
    const id = Number(videoIdFromObj(v));
    if (!id || seen.has(id)) return;
    seen.add(id);
    clean.push(v);
  });

  if (!clean.length) {
    clean.push({
      video_id: vid,
      title: document.getElementById("pageTitle").textContent || "Current Video"
    });
  }

  list.innerHTML = "";

  clean.slice(0, 8).forEach(v => {
    const id = Number(videoIdFromObj(v));
    const title = videoTitleFromObj(v);
    const active = id === Number(vid);

    const a = document.createElement("a");
    a.className = "lessonItem" + (active ? " active" : "");
    a.href = makePartnerVideoUrl(id);

    a.innerHTML = `
      <span class="playIcon">${active ? "▶" : "▷"}</span>
      <span>
        <div class="lessonName">${esc(title)}</div>
        <div class="lessonId">Video ID: ${esc(id)}</div>
      </span>
    `;

    list.appendChild(a);
  });

  const total = clean.length;
  const currentIndex = Math.max(0, clean.findIndex(v => Number(videoIdFromObj(v)) === Number(vid))) + 1;
  document.getElementById("progressNum").textContent = `${currentIndex} / ${total}`;
  document.getElementById("progressFill").style.width = total ? ((currentIndex / total) * 100) + "%" : "0%";
}

async function loadPlaylistVideos(videoResponse){
  let found = [];

  const possibleArrays = [
    videoResponse.playlist_videos,
    videoResponse.videos,
    videoResponse.lessons,
    videoResponse.items,
    videoResponse.playlist_items
  ];

  possibleArrays.forEach(arr => {
    if (Array.isArray(arr)) found = found.concat(arr);
  });

  if (!found.length) {
    try {
      const feed = await apiGet("student_partner_feed.php");
      const arr = feed.json && Array.isArray(feed.json.items) ? feed.json.items : [];

      found = arr.filter(x => {
        const id = Number(x.video_id || x.id || x.vid || 0);
        if (!id) return false;

        const samePlaylistId = CURRENT_PLAYLIST_ID && Number(x.playlist_id || 0) === CURRENT_PLAYLIST_ID;
        const samePlaylistName = CURRENT_PLAYLIST && String(x.playlist_name || x.playlist || "").trim() === CURRENT_PLAYLIST;
        const sameCourse = CURRENT_COURSE && String(x.course_name || x.course || "").trim() === CURRENT_COURSE;

        return samePlaylistId || samePlaylistName || sameCourse;
      });
    } catch(e) {}
  }

  renderVideoList(found);
}

async function load(){
  const r = await apiGet("student_partner_video_get.php?video_id=" + encodeURIComponent(vid));

  if (!r.ok || !r.json || !r.json.ok) {
    document.getElementById("meta").textContent = "تعذر تحميل الفيديو";
    document.getElementById("quiz").innerHTML = `<div class="empty">تعذر تحميل بيانات الفيديو.</div>`;
    renderVideoList([]);
    return;
  }

  const V = (r.json && r.json.video) ? r.json.video : {};

  const title =
    V.title ||
    r.json.video_title ||
    "Partner Video";

  document.getElementById("pageTitle").textContent = title;
  document.getElementById("videoIdText").textContent = "Video ID: " + vid;

 
  let videoUrl = V.video_url || r.json.video_url || V.stored_path || "";
  videoUrl = String(videoUrl || "").trim();

  if (!videoUrl) {
    document.getElementById("meta").textContent = "الفيديو بدون رابط";
    renderVideoList([]);
    return;
  }

  if (!/^https?:\/\//i.test(videoUrl)) {
    if (!videoUrl.startsWith("/")) videoUrl = "/" + videoUrl;
    if (!videoUrl.startsWith("/utbn-backend/")) {
      videoUrl = "/utbn-backend" + videoUrl;
    }
  }

  const player = document.getElementById("player");
  player.src = videoUrl;
  player.load();

  quiz = Array.isArray(r.json.quiz) ? r.json.quiz : [];

  if (!quiz.length) {
    document.getElementById("quiz").innerHTML =
      `<div class="empty">لا يوجد أسئلة لهذا الفيديو بعد.</div>`;
  } else {
    renderQuiz();
  }

  loadPlaylistVideos(r.json);
}

document.getElementById("btnFinish").addEventListener("click", async ()=>{
  const msg = document.getElementById("msg");
  msg.textContent = "";

  if (!quiz.length) {
    msg.textContent = "لا يوجد أسئلة، لا يمكن حساب coins.";
    return;
  }

  let correct = 0;

  quiz.forEach((q,i)=>{
    if ((answers[i] || "") === (q.correct || "")) correct++;
  });

  const videoKey = "partner_" + String(vid);

  msg.textContent = "جاري احتساب coins...";

  const rr = await apiPost("video_reward_claim.php", {
    video_id: videoKey,
    quiz_correct: correct,
    quiz_total: quiz.length
  });

  if (!rr.ok || !rr.json || !rr.json.ok) {
    msg.textContent = rr.json && rr.json.error ? ("خطأ: " + rr.json.error) : "فشل";
    return;
  }

  if (rr.json.already_rewarded) {
    msg.textContent = `أخذت coins على هذا الفيديو سابقاً ✅ (مجموعك: ${rr.json.coins_total})`;
  } else {
    msg.textContent = `مبروك ✅ أخذت ${rr.json.total_coin} coins (مجموعك: ${rr.json.coins_total})`;
  }
});

load();
</script>

<script>
(function () {
  const langSwitch = document.getElementById("langSwitch");
  const enBtn = document.getElementById("langOptionEn");
  const arBtn = document.getElementById("langOptionAr");

  if (!langSwitch || !enBtn || !arBtn) return;

  function updateLangSwitchUI(lang) {
    const isAr = lang === "ar";

    langSwitch.classList.toggle("is-ar", isAr);
    enBtn.classList.toggle("active", !isAr);
    arBtn.classList.toggle("active", isAr);

    document.documentElement.lang = lang;

    // لا تقلب الصفحة أبداً
    document.documentElement.dir = "ltr";
    document.body.dir = "ltr";
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

    updateLangSwitchUI(lang);

    setTimeout(() => {
      document.documentElement.dir = "ltr";
      document.body.dir = "ltr";
    }, 0);
  }

  enBtn.addEventListener("click", () => setLangSafe("en"));
  arBtn.addEventListener("click", () => setLangSafe("ar"));

  updateLangSwitchUI(localStorage.getItem("lang") || localStorage.getItem("qoyn_lang") || "en");
})();
</script>

</body>
</html>