<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
$video_id = (int)($_GET["video_id"] ?? 0);
if (!$video_id) die("video_id مفقود");
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>QOYN | Watch Video</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0b2f6b;
      --navy2:#173d82;
      --gold:#f2bf4b;
      --bg:#f5f6fb;
      --soft:#fbfbfe;
      --card:#ffffff;
      --text:#102145;
      --muted:#7d88a5;
      --line:#ebeff6;
      --shadow:0 18px 38px rgba(16,33,69,.08);
      --radius:22px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      font-family:"Poppins",sans-serif;
      background:
        radial-gradient(circle at left bottom, rgba(227,231,255,.7) 0, rgba(227,231,255,0) 28%),
        linear-gradient(180deg,#fafbff 0%, #f5f6fb 100%);
      color:var(--text);
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
      width:min(94%, 1400px);
      margin:22px auto 40px;
    }

    .hero{
      background:
        radial-gradient(circle at 90% 15%, rgba(245,231,199,.5) 0, rgba(245,231,199,0) 18%),
        radial-gradient(circle at 80% 12%, rgba(209,220,255,.45) 0, rgba(209,220,255,0) 22%),
        #f7f8fd;
      border-radius:28px;
      padding:34px 36px 24px;
      margin-bottom:26px;
      position:relative;
      overflow:hidden;
    }

    .hero::after{
      content:"";
      position:absolute;
      right:36px;
      top:28px;
      width:110px;
      height:90px;
      background-image:radial-gradient(#d7dceb 1.3px, transparent 1.3px);
      background-size:14px 14px;
      opacity:.85;
    }

    .hero-title{
      font-family:"Montserrat",sans-serif;
      font-weight:900;
      font-size:54px;
      line-height:1.05;
      color:var(--navy);
      margin:0 0 10px;
      letter-spacing:-1px;
    }

    .hero-sub{
      color:#7b86a0;
      font-size:16px;
      margin:0;
    }

    .hero-line{
      width:42px;
      height:4px;
      border-radius:999px;
      background:var(--gold);
      margin-top:14px;
    }

    .layout{
      display:grid;
      grid-template-columns:minmax(0, 1.7fr) 320px;
      gap:26px;
      align-items:start;
    }

    .card{
      background:#fff;
      border:1px solid var(--line);
      border-radius:24px;
      box-shadow:var(--shadow);
      padding:20px;
    }

    .video-wrap video{
      width:100%;
      display:block;
      border-radius:18px;
      background:#061a42;
      overflow:hidden;
    }

    .video-meta{
      margin-top:12px;
      color:#7e88a3;
      font-size:14px;
    }

    .section-head{
      display:flex;
      align-items:center;
      gap:12px;
      margin:28px 0 14px;
      font-weight:800;
      color:#203055;
      font-size:16px;
    }

    .section-head .icon{
      width:30px;
      height:30px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:#f3f6fd;
      color:#6074b7;
      font-size:14px;
      flex:0 0 30px;
    }

    .section-head .line{
      width:34px;
      height:4px;
      border-radius:999px;
      background:var(--gold);
      margin-inline-start:6px;
    }

    .timeline{
      position:relative;
      padding-left:18px;
    }

    .timeline::before{
      content:"";
      position:absolute;
      left:13px;
      top:8px;
      bottom:10px;
      width:2px;
      background:#edf0f6;
    }

    .q{
      position:relative;
      border:1px solid var(--line);
      padding:22px 18px 18px;
      border-radius:20px;
      margin:0 0 22px 24px;
      background:#fff;
      box-shadow:0 12px 30px rgba(16,33,69,.05);
    }

    .qnum{
      position:absolute;
      left:-34px;
      top:14px;
      width:30px;
      height:30px;
      border-radius:50%;
      background:var(--navy);
      color:#fff;
      display:grid;
      place-items:center;
      font-weight:800;
      font-size:12px;
      font-family:"Montserrat",sans-serif;
    }

    .q b{
      display:block;
      color:#203055;
      margin-bottom:14px;
      font-size:15px;
    }

    .opt-list{
      display:grid;
      gap:10px;
      margin-top:8px;
    }

    .opt{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      padding:12px 14px;
      border-radius:14px;
      border:1px solid #eef1f7;
      background:#fff;
      transition:.2s ease;
      cursor:pointer;
    }

    .opt:hover{
      background:#fafbfd;
      border-color:#e4e9f2;
    }

    .opt.active{
      background:#fff8e9;
      border-color:#f6e4af;
    }

    .opt .right{
      width:18px;
      height:18px;
      border-radius:50%;
      border:1.8px solid #cfd7e7;
      display:grid;
      place-items:center;
      flex:0 0 18px;
      background:#fff;
    }

    .opt.active .right::after{
      content:"";
      width:8px;
      height:8px;
      border-radius:50%;
      background:#e1a809;
      display:block;
    }

    .muted{
      color:var(--muted);
      font-size:14px;
      line-height:1.8;
    }

    #score,
    #codeResult{
      margin-top:10px;
      color:#60708f;
      font-size:14px;
      line-height:1.9;
    }

    .side{
      display:flex;
      flex-direction:column;
      gap:18px;
    }

    .side-title{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight:800;
      color:#25355f;
      margin-bottom:14px;
      font-size:15px;
    }

    .side-line{
      width:34px;
      height:4px;
      border-radius:999px;
      background:var(--gold);
      margin-top:10px;
    }

    .playlist-list{
      display:flex;
      flex-direction:column;
      gap:8px;
      margin-top:14px;
    }

    .playlist-item{
      display:flex;
      align-items:flex-start;
      gap:12px;
      padding:14px 12px;
      border-radius:14px;
      text-decoration:none;
      color:inherit;
      border:1px solid transparent;
      transition:.2s ease;
    }

    .playlist-item:hover{
      background:#fafbfd;
      border-color:#eef2f8;
    }

    .playlist-item.active{
      background:#fff8e9;
      border-color:#f6e4af;
    }

    .play-dot{
      width:28px;
      height:28px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:#eef3ff;
      color:#5770c6;
      flex:0 0 28px;
      font-size:11px;
      margin-top:2px;
    }

    .playlist-item.active .play-dot{
      background:#0b2f6b;
      color:#fff;
    }

    .playlist-name{
      font-size:14px;
      font-weight:600;
      line-height:1.5;
      color:#24345f;
      margin-bottom:3px;
    }

    .playlist-sub{
      font-size:12px;
      color:#97a1b8;
    }

    .view-all{
      margin-top:12px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      background:#f4f7fe;
      color:#2b3b68;
      border-radius:14px;
      padding:12px;
      text-decoration:none;
      font-weight:700;
      font-size:14px;
    }

    .progress-number{
      font-family:"Montserrat",sans-serif;
      font-size:36px;
      font-weight:900;
      color:var(--navy);
      line-height:1;
    }

    .progress-number span{
      font-size:18px;
      font-weight:700;
    }

    .progress-sub{
      color:var(--muted);
      font-size:13px;
      margin-top:6px;
    }

    .progressbar{
      margin-top:14px;
      height:8px;
      border-radius:999px;
      background:#edf1f7;
      overflow:hidden;
    }

    .progressbar > span{
      display:block;
      height:100%;
      width:0%;
      background:linear-gradient(90deg,#f3bc45,#e3a91c);
      border-radius:999px;
    }

    .btn{
      appearance:none;
      border:none;
      outline:none;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      min-height:48px;
      padding:0 22px;
      border-radius:999px;
      text-decoration:none;
      cursor:pointer;
      font-family:"Montserrat",sans-serif;
      font-size:14px;
      font-weight:800;
      background:linear-gradient(135deg, var(--yellow) 0%, #ffd977 100%);
      color:#fff;
      box-shadow:0 10px 24px rgba(255,194,74,.24);
      transition:.18s ease;
    }

    .btn:hover{
      transform:translateY(-2px);
      background:linear-gradient(135deg, var(--navy) 0%, #1B5FAE 100%);
      color:#fff;
      box-shadow:0 16px 30px rgba(10,46,93,.20);
    }

    .btn:disabled{
      opacity:.65;
      cursor:not-allowed;
      transform:none;
      box-shadow:none;
    }

    .btn.primary{
      background:linear-gradient(135deg, var(--navy) 0%, #1B5FAE 100%);
      color:#fff;
      box-shadow:0 10px 24px rgba(10,46,93,.20);
    }

    .btn.primary:hover{
      background:linear-gradient(135deg, var(--yellow) 0%, #ffd977 100%);
      color:#fff;
    }

    .codeEditor{
      border:1px solid rgba(10,46,93,.10);
      border-radius:24px;
      overflow:hidden;
      background:#0f1729;
      box-shadow:0 18px 50px rgba(10,46,93,.18);
      direction:ltr;
      text-align:left;
    }

    .codeTop{
      display:flex;
      align-items:center;
      gap:10px;
      padding:12px 16px;
      background:rgba(255,255,255,.06);
      border-bottom:1px solid rgba(255,255,255,.08);
    }

    .codeTop .dot{
      width:12px;
      height:12px;
      border-radius:50%;
      display:inline-block;
      opacity:.95;
    }

    .codeTop .red{ background:#ff5f56; }
    .codeTop .yellow{ background:#ffbd2e; }
    .codeTop .green{ background:#27c93f; }

    .codeTitle{
      margin-left:auto;
      color:rgba(255,255,255,.82);
      font-size:13px;
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .codeBody{
      display:flex;
      height:320px;
      max-height:520px;
    }

    .lineNums{
      margin:0;
      padding:14px 10px;
      width:56px;
      text-align:right;
      color:rgba(255,255,255,.35);
      background:rgba(255,255,255,.03);
      border-right:1px solid rgba(255,255,255,.08);
      font-size:13px;
      line-height:1.65;
      overflow:hidden;
      user-select:none;
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .codeInput{
      flex:1;
      border:0;
      outline:0;
      resize:none;
      padding:14px 14px;
      background:transparent;
      color:rgba(255,255,255,.94);
      font-size:13.5px;
      line-height:1.65;
      white-space:pre;
      overflow:auto;
      tab-size:2;
      font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .codeInput::placeholder{
      color:rgba(255,255,255,.35);
    }

    .codeInput:focus{
      box-shadow:inset 0 0 0 2px rgba(255,194,74,.35);
    }

    .problem-foot{
      margin-top:22px;
      display:grid;
      grid-template-columns:54px 1fr 150px;
      gap:16px;
      align-items:center;
      padding:18px 0 0;
      border-top:1px solid #edf0f5;
    }

    .tip-icon{
      width:44px;
      height:44px;
      border-radius:50%;
      background:#fff7e8;
      color:#e0a61d;
      display:grid;
      place-items:center;
      font-size:20px;
    }

    .tip-title{
      font-weight:700;
      color:#24345f;
      margin-bottom:4px;
    }

    .tip-sub{
      color:#8b95ab;
      font-size:13px;
    }

    .tip-illus{
      height:74px;
      border-radius:14px;
      background:linear-gradient(135deg,#eef3ff 0%,#ffffff 100%);
      position:relative;
      overflow:hidden;
    }

    .tip-illus::before{
      content:"</>";
      position:absolute;
      right:14px;
      bottom:10px;
      font-weight:800;
      color:#f0aa19;
      background:#fff3cf;
      padding:4px 8px;
      border-radius:8px;
      font-size:13px;
    }

    .tip-illus::after{
      content:"";
      position:absolute;
      left:16px;
      top:14px;
      width:74px;
      height:44px;
      border-radius:10px;
      background:linear-gradient(180deg,#ffffff 0%, #eef3ff 100%);
      box-shadow:0 10px 20px rgba(28,56,114,.08);
      border:1px solid #e6ebf6;
    }

    @media (max-width:1100px){
      .layout{
        grid-template-columns:1fr;
      }
      .side{
        order:-1;
      }
    }

    @media (max-width:768px){
      .topbar{
        padding:16px 18px;
        flex-direction:column;
        align-items:flex-start;
      }

      .page{
        width:min(96%, 100%);
        margin:16px auto 28px;
      }

      .hero{
        padding:24px 18px;
      }

      .hero-title{
        font-size:34px;
      }

      .card{
        padding:16px;
      }

      .problem-foot{
        grid-template-columns:1fr;
      }
    }
    /* ===== same scale as All Courses ===== */

.page{
  width:calc(100% - 130px);
  max-width:1360px;
  margin:0 auto;
  padding:50px 0 0;
}

.hero{
  border-radius:12px;
  padding:0 0 28px;
  margin-bottom:22px;
  background:transparent;
  box-shadow:none;
}

.hero::after{
  display:none;
}

.hero-title{
  font-size:40px;
  line-height:1.15;
  letter-spacing:-1px;
}

.hero-sub{
  font-size:15px;
  margin-top:10px;
}

.hero-line{
  width:44px;
  height:3px;
  margin-top:20px;
  background:#ffb31f;
}

.layout{
  grid-template-columns:minmax(0,1fr) 300px;
  gap:24px;
}

.card{
  border-radius:12px;
  padding:18px;
}

.video-wrap video{
  border-radius:12px;
}

.section-head{
  margin:24px 0 14px;
}

.q{
  border-radius:14px;
  padding:18px 16px 16px;
  margin-bottom:18px;
}

.opt{
  border-radius:12px;
  padding:11px 13px;
}

.side{
  gap:16px;
}

.progress-number{
  font-size:30px;
}

.codeEditor{
  border-radius:16px;
}

.codeBody{
  height:280px;
}

.problem-foot{
  grid-template-columns:46px 1fr 130px;
  gap:14px;
}

@media(max-width:900px){
  .page{
    width:100%;
    padding:35px 18px 0;
  }

  .layout{
    grid-template-columns:1fr;
  }

  .nav{
    flex-direction:column;
    align-items:flex-start;
  }

  .nav-links{
    width:100%;
    flex-wrap:wrap;
    gap:10px;
  }
}

@media(max-width:560px){
  .hero-title{
    font-size:30px;
  }

  .problem-foot{
    grid-template-columns:1fr;
  }
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
    <h1 class="hero-title" id="vTitle">Course Playlist</h1>
    <p class="hero-sub">Watch the video, continue the playlist, and answer the questions.</p>
    <div class="hero-line"></div>
  </section>

  <div class="layout">
    <section>
      <div class="card">
        <div class="video-wrap">
          <video id="vid" controls playsinline></video>
        </div>
        <div class="video-meta" id="vMeta"></div>
      </div>

      <div class="section-head">
        <div class="icon">?</div>
        <div>Questions</div>
        <div class="line"></div>
      </div>

      <div class="timeline">
        <div id="quizBox"></div>
      </div>

      <div class="card" style="margin-top:22px">
        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center">
          <b style="font-family:'Montserrat',sans-serif;color:var(--navy)">Problem Solving (Writing Code)</b>
          <button class="btn primary" id="codeSubmitBtn" type="button" style="display:none">Submit Code</button>
        </div>

        <div id="codeBox" style="margin-top:12px"></div>
        <div id="codeResult"></div>

        <div class="problem-foot">
          <div class="tip-icon">💡</div>
          <div>
            <div class="tip-title">Problem Solving</div>
            <div class="tip-sub">After watching the video, solve the code problem to improve your learning.</div>
          </div>
          <div class="tip-illus"></div>
        </div>
      </div>
    </section>

    <aside class="side">
      <div class="card">
        <div class="side-title">🖼️ <span>Course Playlist</span></div>
        <div class="side-line"></div>
        <div class="playlist-list" id="playlistBox"></div>
        <a class="view-all" id="backBtn" href="#">View all lessons <span>›</span></a>
      </div>

      <div class="card">
        <div class="side-title">📈 <span>Your Progress</span></div>
        <div class="progress-number"><span id="progressDone">0</span> <span>/ <span id="progressTotal">0</span></span></div>
        <div class="progress-sub">Lessons Completed</div>
        <div class="progressbar"><span id="progressBar"></span></div>
      </div>
    </aside>
  </div>
</main>

<script>
const API = "/utbn-backend/api";
const video_id = <?= (int)$video_id ?>;

function esc(s){return String(s??"").replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));}

let QUIZ = null;
let VIDEO_INFO = null;
window.currentPlaylistId = null;

document.getElementById("backBtn").addEventListener("click", (e) => {
  e.preventDefault();
  if (window.currentPlaylistId) {
    window.location.href = "student_playlist.php?playlist_id=" + window.currentPlaylistId;
  } else {
    window.location.href = "my_courses.php";
  }
});

let quizStartAt = null;
function startQuizTimerOnce(){
  if (!quizStartAt) quizStartAt = Date.now();
}

function renderPlaylist(video, playlist){
  const box = document.getElementById("playlistBox");
  const items = Array.isArray(playlist?.videos) ? playlist.videos : [];
  box.innerHTML = "";

  if(!items.length){
    box.innerHTML = `<div class="muted">No lessons found.</div>`;
    return;
  }

  items.forEach(item => {
    const a = document.createElement("a");
    a.className = "playlist-item" + (Number(item.id) === Number(video_id) ? " active" : "");
    a.href = `student_watch.php?video_id=${item.id}`;
    a.innerHTML = `
      <div class="play-dot">${Number(item.id) === Number(video_id) ? "▶" : "▷"}</div>
      <div>
        <div class="playlist-name">${esc(item.title || "Lesson")}</div>
        <div class="playlist-sub">Video ID: ${esc(item.id)}</div>
      </div>
    `;
    box.appendChild(a);
  });
}

function updateProgress(playlist){
  const items = Array.isArray(playlist?.videos) ? playlist.videos : [];
  const total = items.length;
  const currentIndex = Math.max(0, items.findIndex(v => Number(v.id) === Number(video_id)));
  const done = total ? Math.min(total, currentIndex + 1) : 0;
  const percent = total ? Math.round((done / total) * 100) : 0;

  document.getElementById("progressDone").textContent = done;
  document.getElementById("progressTotal").textContent = total;
  document.getElementById("progressBar").style.width = percent + "%";
}

async function loadVideo(){
  const res = await fetch(`${API}/student_video_info.php?video_id=${video_id}`, {credentials:"include"});
  const j = await res.json();
  if(!j.ok){ alert(j.error||"فشل جلب الفيديو"); return; }

  VIDEO_INFO = j;
  window.currentPlaylistId = j.playlist?.id || null;

  document.getElementById("vTitle").textContent = j.video.title || "Video";
  document.getElementById("vMeta").textContent = "Video ID: " + j.video.id;
  document.getElementById("vid").src = "/utbn-backend/" + j.video.stored_path;

  renderPlaylist(j.video, j.playlist || {});
  updateProgress(j.playlist || {});
}

async function loadQuiz(){
  const res = await fetch(`${API}/partner_video_quiz_get.php?video_id=${video_id}`, {credentials:"include"});
  const j = await res.json();
  QUIZ = j.quiz || null;

  const box = document.getElementById("quizBox");
  box.innerHTML = "";

  if(!QUIZ || !QUIZ.length){
    box.innerHTML = `<div class="card muted">No questions for this video yet.</div>`;
    return;
  }

  QUIZ.forEach((q,i)=>{
    const div = document.createElement("div");
    div.className = "q";
    div.innerHTML = `
      <div class="qnum">${String(i+1).padStart(2,"0")}</div>
      <b>${esc(q.question)}</b>
      <div class="opt-list">
        ${(q.options||[]).map((opt,idx)=>`
          <label class="opt">
            <span>${esc(opt)}</span>
            <span class="right"></span>
            <input type="radio" name="q_${i}" value="${idx}" style="display:none">
          </label>
        `).join("")}
      </div>
      <div class="muted" data-exp style="margin-top:8px;display:none"></div>
    `;
    box.appendChild(div);
  });

  box.querySelectorAll(".opt").forEach(label=>{
    label.addEventListener("click", ()=>{
      const wrap = label.closest(".opt-list");
      wrap.querySelectorAll(".opt").forEach(x=>x.classList.remove("active"));
      label.classList.add("active");
      const input = label.querySelector('input[type="radio"]');
      if(input) input.checked = true;
      startQuizTimerOnce();
    });
  });
}

function hookProgressSave(){
  const v = document.getElementById("vid");
  let lastSent = 0;

  v.addEventListener("timeupdate", async () => {
    if (!v.duration || isNaN(v.duration)) return;

    const t = Math.floor(v.currentTime || 0);
    if (t - lastSent < 10) return;
    lastSent = t;

    const completed = (v.currentTime >= v.duration * 0.5) ? 1 : 0;
    try {
      await fetch(`${API}/student_video_progress_save.php`, {
        method:"POST",
        headers: {"Content-Type":"application/json"},
        credentials:"include",
        body: JSON.stringify({
          video_id: video_id,
          watched_seconds: t,
          completed: completed,
          duration_seconds: Math.floor(v.duration || 0)
        })
      });
    } catch(e){}
  });
}

async function claimCoins(score, total){
  const syntheticId = "p_" + String(video_id);

  const res = await fetch(`${API}/video_reward_claim.php`, {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    credentials: "include",
    body: JSON.stringify({
      video_id: syntheticId,
      quiz_correct: score,
      quiz_total: total
    })
  });

  const j = await res.json().catch(()=>({}));

  if (!j.ok){
    alert(j.error || "فشل إضافة الكوين");
    return;
  }

  if (j.already_rewarded){
    alert("✅ تم حل هذا الفيديو من قبل — لا يوجد coin إضافي");
    return;
  }

  alert(`✅ مبروك! انضاف لك ${j.total_coin} coin — مجموعك الآن: ${j.coins_total}`);
}

document.getElementById("checkBtn")?.remove();

const quizSubmitWrap = document.createElement("div");
quizSubmitWrap.style.margin = "0 0 18px 0";
quizSubmitWrap.innerHTML = `<button class="btn" id="checkBtn" type="button">Check & Submit</button>`;
document.querySelector(".timeline").insertAdjacentElement("beforebegin", quizSubmitWrap);

document.getElementById("checkBtn").onclick = async ()=>{
  if(!QUIZ || !QUIZ.length) return;

  const v = document.getElementById("vid");
  if (v.duration && !isNaN(v.duration)) {
    if ((v.currentTime || 0) < v.duration * 0.5) {
      alert("لازم تشاهد على الأقل نصف الفيديو قبل تسليم الحل ✅");
      return;
    }
  }

  let score=0,total=QUIZ.length;
  let allAnswered = true;

  QUIZ.forEach((q,i)=>{
    const correctIndex = ["A","B","C","D"].indexOf(String(q.correct||"A"));
    const chosen = document.querySelector(`input[name="q_${i}"]:checked`);
    if(!chosen) allAnswered = false;
    if(chosen && Number(chosen.value)===correctIndex) score++;

    const card = document.querySelectorAll(".q")[i];
    const exp = card.querySelector("[data-exp]");
    exp.style.display="block";
    exp.innerHTML = `✅ الصحيح: <b>${esc(q.correct)}</b> — ${esc(q.explanation||"")}`;
  });

  if(!allAnswered){
    alert("اختار إجابة لكل سؤال قبل التسليم ✅");
    return;
  }

  document.getElementById("score").textContent = `نتيجتك: ${score} / ${total}`;

  try{
    const answers = [];
    for(let i=0;i<QUIZ.length;i++){
      const chosen = document.querySelector(`input[name="q_${i}"]:checked`);
      answers.push(chosen ? Number(chosen.value) : -1);
    }

    const time_spent_seconds = quizStartAt ? Math.floor((Date.now() - quizStartAt)/1000) : 0;
    const safe_time_spent_seconds = Math.max(1, time_spent_seconds);

    await fetch(`${API}/partner_video_submit.php`, {
      method:"POST",
      headers: {"Content-Type":"application/json"},
      credentials:"include",
      body: JSON.stringify({ video_id: video_id, answers, time_spent_seconds: safe_time_spent_seconds })
    });
  }catch(e){}

  if (score === total){
    await claimCoins(score, total);
  } else {
    alert("لازم تحل كل الأسئلة صح عشان تاخذ coin ✅");
  }
};

let CODE_PROBLEM = null;

function initEditor(){
  const ta = document.getElementById("codeInput");
  const ln = document.getElementById("lineNums");
  if (!ta || !ln) return;

  function updateLines(){
    const lines = (ta.value.match(/\n/g) || []).length + 1;
    let out = "";
    for (let i=1;i<=lines;i++) out += i + "\n";
    ln.textContent = out.trimEnd();
  }

  ta.addEventListener("scroll", () => {
    ln.scrollTop = ta.scrollTop;
  });

  ta.addEventListener("input", updateLines);
  updateLines();

  ta.addEventListener("keydown", (e) => {
    if (e.key === "Tab") {
      e.preventDefault();
      const start = ta.selectionStart;
      const end = ta.selectionEnd;
      const before = ta.value.substring(0, start);
      const after  = ta.value.substring(end);
      ta.value = before + "  " + after;
      ta.selectionStart = ta.selectionEnd = start + 2;
      updateLines();
    }
  });
}

async function loadCodeProblem(){
  const box = document.getElementById("codeBox");
  const btn = document.getElementById("codeSubmitBtn");
  const result = document.getElementById("codeResult");

  box.innerHTML = "";
  result.textContent = "";
  btn.style.display = "none";

  try{
    const res = await fetch(`${API}/partner_video_code_problem_get.php?video_id=${video_id}`, {credentials:"include"});
    const j = await res.json().catch(()=>({}));

    if(!j.ok){
      box.innerHTML = `<div class="muted">تعذر تحميل مسألة الكود</div>`;
      return;
    }

    CODE_PROBLEM = j.problem || null;

    if(!CODE_PROBLEM){
      box.innerHTML = `<div class="muted">لا يوجد Problem Solving لهذا الفيديو</div>`;
      return;
    }

    const lang = CODE_PROBLEM.language || "cpp";
    const maxCoin = Number(CODE_PROBLEM.max_coin || 50);

    box.innerHTML = `
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <div class="view-all" style="margin-top:0;padding:8px 14px;background:#f7f9fe">Language: <b>${esc(lang)}</b></div>
        <div class="view-all" style="margin-top:0;padding:8px 14px;background:#f7f9fe">Max coin: <b>${esc(maxCoin)}</b></div>
      </div>

      <div style="margin-top:14px">
        <b style="color:var(--navy)">${esc(CODE_PROBLEM.title || "Problem")}</b>
        <div class="muted" style="margin-top:8px;white-space:pre-wrap">${esc(CODE_PROBLEM.prompt || "")}</div>
      </div>

      <div style="margin-top:14px">
        <div class="codeEditor">
          <div class="codeTop">
            <span class="dot red"></span>
            <span class="dot yellow"></span>
            <span class="dot green"></span>
            <span class="codeTitle">main.${esc(lang)}</span>
          </div>
          <div class="codeBody">
            <pre id="lineNums" class="lineNums">1</pre>
            <textarea id="codeInput" class="codeInput" spellcheck="false" placeholder="اكتب الكود هنا...">${esc(CODE_PROBLEM.starter_code || "")}</textarea>
          </div>
        </div>
      </div>

      <div class="muted" style="margin-top:10px">
        التقييم يتم عبر AI بمقارنة الحلّ مع حلّ الأستاذ (مش لازم نفس الكود 100%).
      </div>
    `;

    btn.style.display = "inline-flex";
    initEditor();
  }catch(e){
    box.innerHTML = `<div class="muted">تعذر تحميل مسألة الكود</div>`;
  }
}

async function submitCode(){
  if(!CODE_PROBLEM) return;

  const v = document.getElementById("vid");
  if (v.duration && !isNaN(v.duration)) {
    if ((v.currentTime || 0) < v.duration * 0.5) {
      alert("لازم تشاهد على الأقل نصف الفيديو قبل تسليم الكود ✅");
      return;
    }
  }

  const code = (document.getElementById("codeInput")?.value || "").trim();
  if(!code){
    alert("اكتب الكود قبل التسليم ✅");
    return;
  }

  const btn = document.getElementById("codeSubmitBtn");
  const result = document.getElementById("codeResult");
  btn.disabled = true;
  result.textContent = "جاري التقييم...";

  try{
    const res = await fetch(`${API}/student_code_submit.php`, {
      method:"POST",
      headers: {"Content-Type":"application/json"},
      credentials:"include",
      body: JSON.stringify({
        problem_id: Number(CODE_PROBLEM.id),
        code
      })
    });

    const j = await res.json().catch(()=>({}));

    if(!j.ok){
      result.textContent = (j.error ? `خطأ: ${j.error}` : "فشل التقييم");
      btn.disabled = false;
      return;
    }

    const pct = Math.round((Number(j.score || 0) * 100));
    const coin = Number(j.coin_awarded || 0);

    const kept = j.already_rewarded ? "ℹ️ تم تسليمها سابقًا — لا يوجد coin إضافي" : "✅ تم اعتمادها";
    const fb = j.feedback || {};

    result.innerHTML = `
      <div>Score: <b>${esc(pct)}%</b> — Coin: <b>${esc(coin)}</b> — ${kept}</div>
      ${fb.reason ? `<div style="margin-top:8px">السبب: ${esc(fb.reason)}</div>` : ``}
      ${(fb.highlights && fb.highlights.length) ? `<div style="margin-top:8px">نقاط قوية:<ul>${fb.highlights.map(x=>`<li>${esc(x)}</li>`).join("")}</ul></div>` : ``}
      ${(fb.issues && fb.issues.length) ? `<div style="margin-top:8px">ملاحظات:<ul>${fb.issues.map(x=>`<li>${esc(x)}</li>`).join("")}</ul></div>` : ``}
      ${(j.coins_total != null) ? `<div class="muted" style="margin-top:8px">مجموع Coins الآن: ${esc(j.coins_total)}</div>` : ``}
    `;

    if (j.already_rewarded) {
      btn.style.display = "none";
    }

    btn.disabled = false;
  }catch(e){
    result.textContent = "تعذر إرسال الكود";
    btn.disabled = false;
  }
}

document.getElementById("codeSubmitBtn").onclick = submitCode;

loadVideo();
loadQuiz();
loadCodeProblem();
hookProgressSave();
</script>
</body>
</html>