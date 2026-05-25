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
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>My Courses</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0b2f6b;
      --navy2:#163d81;
      --gold:#f2c14e;
      --bg:#f5f6fb;
      --text:#112240;
      --muted:#7f8aa3;
      --card:#ffffff;
      --line:#e8ebf3;
      --shadow:0 18px 40px rgba(17,34,64,.08);
      --radius:26px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      font-family:"Poppins",sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at left bottom, rgba(227,231,255,.7) 0, rgba(227,231,255,0) 28%),
        linear-gradient(180deg,#fafbff 0%, #f5f6fb 100%);
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

    .page{
      width:min(96%, 1540px);
      margin:22px auto 34px;
    }

   .hero{
  min-height:190px;
  border-radius:32px;
  padding:34px 42px;
  background:#f7f8fd url('uploads/') no-repeat right 35px bottom / 260px auto;
  display:flex;
  align-items:center;
  position:relative;
  overflow:hidden;
}

   

    .hero-left{
      max-width:600px;
      display:flex;
      gap:22px;
      align-items:flex-start;
      position:relative;
      z-index:2;
    }

    .hero-icon{
      width:110px;
      height:110px;
      min-width:110px;
      border-radius:50%;
      background:#fff;
      display:grid;
      place-items:center;
      box-shadow:0 10px 26px rgba(30,45,90,.08);
      border:6px solid rgba(255,255,255,.8);
    }

    .hero h1{
      margin:0;
      font-family:"Montserrat",sans-serif;
      font-size:62px;
      line-height:1;
      color:var(--navy);
      font-weight:900;
      letter-spacing:-1px;
    }

    .hero p{
      margin:14px 0 0;
      color:#6f7588;
      font-size:18px;
    }

    .hero-line{
      width:56px;
      height:5px;
      border-radius:999px;
      background:var(--gold);
      margin-top:18px;
    }

    .courses-wrap{
      margin-top:18px;
      display:flex;
      flex-direction:column;
      gap:16px;
    }

    .course-card{
      background:var(--card);
      border-radius:24px;
      box-shadow:var(--shadow);
      padding:14px 18px;
      display:grid;
      grid-template-columns:280px 1fr 230px 54px;
      gap:20px;
      align-items:center;
      border:1px solid #eef1f7;
    }

    .cover-box{
      display:flex;
      align-items:center;
      gap:14px;
      position:relative;
    }

    .cover-accent{
      width:8px;
      height:114px;
      border-radius:999px;
      background:linear-gradient(180deg,#f0b72f,#ffdb78);
      flex:0 0 8px;
    }

    .cover-accent.blue{
      background:linear-gradient(180deg,#5a7eff,#89a6ff);
    }

    .cover-accent.purple{
      background:linear-gradient(180deg,#b76cff,#d0a8ff);
    }

    .cover{
      width:240px;
      height:114px;
      border-radius:22px;
      background:#eef3fb center/cover no-repeat;
      box-shadow:inset 0 0 0 1px rgba(17,34,64,.05);
    }

    .course-main{
      display:flex;
      gap:18px;
      align-items:flex-start;
    }

    .mini-icon{
      width:54px;
      height:54px;
      border-radius:50%;
      background:#f4f6ff;
      display:grid;
      place-items:center;
      flex:0 0 54px;
    }

    .course-title{
      margin:2px 0 6px;
      font-size:21px;
      font-weight:800;
      color:#111d45;
    }

    .meta{
      font-size:14px;
      color:#7f869c;
      margin-bottom:18px;
    }

    .progress-row{
      display:flex;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .progress-label{
      font-size:14px;
      color:#788099;
      min-width:100px;
    }

    .progress-bar{
      width:220px;
      height:8px;
      border-radius:999px;
      background:#e9edf5;
      overflow:hidden;
      position:relative;
    }

    .progress-fill{
      height:100%;
      border-radius:999px;
      background:linear-gradient(90deg,var(--navy),#2f5aa8);
      width:0%;
    }

    .progress-value{
      font-size:14px;
      color:#7a8092;
      min-width:44px;
    }

    .action-box{
      display:flex;
      justify-content:center;
    }

    .open-btn{
      background:var(--navy);
      color:#fff;
      text-decoration:none;
      padding:18px 28px;
      border-radius:16px;
      font-weight:700;
      display:inline-flex;
      align-items:center;
      gap:12px;
      box-shadow:0 10px 24px rgba(11,47,107,.18);
      transition:.2s ease;
      white-space:nowrap;
    }

    .open-btn:hover{
      transform:translateY(-2px);
      background:var(--navy2);
    }

    .arrow-btn{
      width:46px;
      height:46px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:#fff;
      border:1px solid #eef1f7;
      box-shadow:0 8px 18px rgba(20,25,40,.05);
      text-decoration:none;
      color:#24345f;
      transition:.2s ease;
    }

    .arrow-btn:hover{
      transform:translateY(-2px);
      background:#f9fbff;
    }

    .loading,
    .empty{
      background:#fff;
      border-radius:22px;
      padding:24px;
      text-align:center;
      color:#788099;
      box-shadow:var(--shadow);
    }

    @media (max-width:1200px){
      .course-card{
        grid-template-columns:240px 1fr;
      }

      .action-box{
        justify-content:flex-start;
      }

      .arrow-box{
        display:none;
      }

      .cover{
        width:200px;
      }
    }

    @media (max-width:768px){
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
        padding:28px 20px;
        background:#f7f8fd;
      }

      .hero-left{
        flex-direction:column;
      }

      .hero h1{
        font-size:42px;
      }

      .course-card{
        grid-template-columns:1fr;
      }

      .cover{
        width:100%;
        height:180px;
      }

      .cover-box{
        width:100%;
      }

      .progress-bar{
        width:100%;
        max-width:260px;
      }
    }
    /* ===== Make My Courses same scale as All Courses ===== */

.nav-wrap{
  padding:14px 22px;
}

.nav{
  max-width:1200px;
}

.nav-monkey{
  height:40px;
}

.logo{
  font-size:26px;
}

.nav-links a{
  font-size:14px;
  padding:10px 14px;
}

.page{
  width:calc(100% - 130px);
  max-width:1360px;
  margin:0 auto;
  padding:50px 0 0;
}

.hero{
  min-height:auto;
  border-radius:12px;
  padding:0 0 28px;
  background:transparent;
  box-shadow:none;
}

.hero-left{
  gap:0;
  max-width:100%;
}

.hero-icon{
  display:none;
}

.hero h1{
  font-size:40px;
  line-height:1.15;
  letter-spacing:-1px;
}

.hero p{
  font-size:15px;
  margin-top:10px;
  color:#788297;
}

.hero-line{
  width:44px;
  height:3px;
  margin-top:20px;
  background:#ffb31f;
}

.courses-wrap{
  margin-top:0;
  display:grid;
  grid-template-columns:repeat(4, minmax(0,1fr));
  gap:30px;
}

.course-card{
  min-height:390px;
  padding:0;
  border-radius:12px;
  display:flex;
  flex-direction:column;
  gap:0;
  overflow:hidden;
}

.cover-box{
  display:block;
  width:100%;
}

.cover-accent{
  display:none;
}

.cover{
  width:100%;
  height:165px;
  border-radius:0;
  box-shadow:none;
}

.course-main{
  padding:22px 18px 0;
  display:flex;
  gap:13px;
  flex:1;
}

.mini-icon{
  width:32px;
  height:32px;
  flex:0 0 32px;
}

.mini-icon svg{
  width:18px;
  height:18px;
}

.course-title{
  font-size:16px;
  line-height:1.35;
  margin:0 0 18px;
}

.meta{
  font-size:12px;
  margin-bottom:12px;
}

.progress-row{
  gap:8px;
}

.progress-label{
  font-size:12px;
  min-width:auto;
}

.progress-bar{
  width:120px;
  height:7px;
}

.progress-value{
  font-size:12px;
}

.action-box{
  padding:18px;
  justify-content:flex-start;
}

.open-btn{
  height:34px;
  padding:0 14px;
  border-radius:12px;
  font-size:11px;
  gap:8px;
}

.arrow-box{
  display:none;
}

@media(max-width:1150px){
  .courses-wrap{
    grid-template-columns:repeat(3, minmax(0,1fr));
  }
}

@media(max-width:900px){
  .page{
    width:100%;
    padding:35px 18px 0;
  }

  .courses-wrap{
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:18px;
  }
}

@media(max-width:560px){
  .courses-wrap{
    grid-template-columns:1fr;
  }

  .hero h1{
    font-size:30px;
  }

  .open-btn{
    width:100%;
    justify-content:center;
  }
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
      <li><a href="my_courses.php" class="active">My courses</a></li>
      <li><a href="courses.php">All courses</a></li>
      <li><a href="student_profile.php">Achievement</a></li>
      <li><a href="#" id="logoutBtn" class="nav-logout">Logout</a></li>
    </ul>

  </nav>
</header>

<main class="page">
  <section class="hero">
    <div class="hero-left">
      <div class="hero-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
          <path d="M3 6.75C3 5.78 3.78 5 4.75 5H10.5C11.33 5 12 5.67 12 6.5V19C12 18.17 11.33 17.5 10.5 17.5H4.75C3.78 17.5 3 18.28 3 19.25V6.75Z" stroke="#20408a" stroke-width="1.8"/>
          <path d="M12 6.5C12 5.67 12.67 5 13.5 5H19.25C20.22 5 21 5.78 21 6.75V19.25C21 18.28 20.22 17.5 19.25 17.5H13.5C12.67 17.5 12 18.17 12 19V6.5Z" stroke="#20408a" stroke-width="1.8"/>
          <circle cx="18.5" cy="5.5" r="2" fill="#20408a"/>
        </svg>
      </div>

      <div>
        <h1>My Courses</h1>
        <p>Continue learning and improve your skills every day.</p>
        <div class="hero-line"></div>
      </div>
    </div>
  </section>

  <section class="courses-wrap" id="coursesWrap">
    <div class="loading">Loading courses...</div>
  </section>
</main>

<script>
  const API_BASE = "/utbn-backend/api";

  function esc(str){
    return String(str ?? "").replace(/[&<>"']/g, s => ({
      "&":"&amp;",
      "<":"&lt;",
      ">":"&gt;",
      '"':"&quot;",
      "'":"&#039;"
    }[s]));
  }

  function formatDate(dateStr){
    if(!dateStr) return "";
    return dateStr.replace("T"," ").trim();
  }

  function getAccentClass(index){
    if(index % 3 === 1) return "blue";
    if(index % 3 === 2) return "purple";
    return "";
  }

  function defaultCover(title){
    const t = String(title || "").toLowerCase();

    if (t.includes("sql")) return "uploads/QOIN.png";
    if (t.includes("web")) return "uploads/look.png";
    if (t.includes("network") || t.includes("cyber")) return "uploads/ph3.png";
    return "uploads/Study.png";
  }

  function courseIcon(title){
    const t = String(title || "").toLowerCase();

    if (t.includes("sql")) {
      return `
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
          <ellipse cx="12" cy="6" rx="6.5" ry="3.2" stroke="#5b73d8" stroke-width="1.8"/>
          <path d="M5.5 6V12C5.5 13.77 8.41 15.2 12 15.2C15.59 15.2 18.5 13.77 18.5 12V6" stroke="#5b73d8" stroke-width="1.8"/>
          <path d="M5.5 12V18C5.5 19.77 8.41 21.2 12 21.2C15.59 21.2 18.5 19.77 18.5 18V12" stroke="#5b73d8" stroke-width="1.8"/>
        </svg>
      `;
    }

    if (t.includes("web")) {
      return `
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="8.5" stroke="#5b73d8" stroke-width="1.8"/>
          <path d="M3.8 12H20.2" stroke="#5b73d8" stroke-width="1.8"/>
          <path d="M12 3.5C14.6 5.8 16 8.8 16 12C16 15.2 14.6 18.2 12 20.5C9.4 18.2 8 15.2 8 12C8 8.8 9.4 5.8 12 3.5Z" stroke="#5b73d8" stroke-width="1.8"/>
        </svg>
      `;
    }

    return `
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
        <path d="M12 21S4.5 17.4 4.5 10.3C4.5 7.6 6.4 5.3 9 4.7L12 4L15 4.7C17.6 5.3 19.5 7.6 19.5 10.3C19.5 17.4 12 21 12 21Z" stroke="#5b73d8" stroke-width="1.8"/>
      </svg>
    `;
  }
async function openFirstVideo(playlistId){
  try{
    const res = await fetch(API_BASE + "/student_playlist_videos.php?playlist_id=" + encodeURIComponent(playlistId), {
      credentials:"include"
    });

    const data = await res.json();
    const videos = Array.isArray(data.videos) ? data.videos : [];

    if(videos.length && videos[0].id){
      window.location.href = "student_watch.php?video_id=" + encodeURIComponent(videos[0].id);
    }else{
      window.location.href = "student_playlist.php?playlist_id=" + encodeURIComponent(playlistId);
    }
  }catch(e){
    window.location.href = "student_playlist.php?playlist_id=" + encodeURIComponent(playlistId);
  }
}
  function renderCourseCard(item, index){
    const id = item.playlist_id || "";
    const title = item.course_name || item.playlist_name || "Course";
    const author = item.partner_name || "qoyn";
    const date = formatDate(item.published_at || "");
    const progress = Math.max(0, Math.min(100, parseInt(item.progress_percent || 0, 10)));
    const cover = (item.cover_url && item.cover_url.trim()) ? item.cover_url.trim() : defaultCover(title);
    const accentClass = getAccentClass(index);

    return `
      <article class="course-card">
        <div class="cover-box">
          <div class="cover-accent ${accentClass}"></div>
          <div class="cover" style="background-image:url('${esc(cover)}')"></div>
        </div>

        <div class="course-main">
          <div class="mini-icon">${courseIcon(title)}</div>

          <div>
            <div class="course-title">${esc(title)}</div>
            <div class="meta">By ${esc(author)} &nbsp; • &nbsp; ${esc(date)}</div>

            <div class="progress-row">
              <div class="progress-label">Continue learning</div>
              <div class="progress-bar">
                <div class="progress-fill" style="width:${progress}%"></div>
              </div>
              <div class="progress-value">${progress}%</div>
            </div>
          </div>
        </div>

        <div class="action-box">
<a class="open-btn" href="#" onclick="openFirstVideo('${encodeURIComponent(id)}'); return false;">
            <span>▶</span>
            <span>Open the Playlist</span>
          </a>
        </div>

        <div class="arrow-box">
<a class="arrow-btn" href="#" onclick="openFirstVideo('${encodeURIComponent(id)}'); return false;">›</a>
        </div>
      </article>
    `;
  }

  async function loadCourses(){
    const wrap = document.getElementById("coursesWrap");
    wrap.innerHTML = '<div class="loading">Loading courses...</div>';

    try{
      const res = await fetch(API_BASE + "/student_path_playlists.php", {
        credentials:"include"
      });

      const data = await res.json();
      const items = Array.isArray(data.items) ? data.items : [];

      if(!items.length){
        wrap.innerHTML = '<div class="empty">No courses found yet.</div>';
        return;
      }

      wrap.innerHTML = items.map((item, index) => renderCourseCard(item, index)).join("");
    }catch(err){
      wrap.innerHTML = '<div class="empty">Failed to load courses.</div>';
    }
  }

  document.getElementById("logoutBtn")?.addEventListener("click", async function(e){
    e.preventDefault();

    try{
      await fetch(API_BASE + "/logout.php", {
        method:"POST",
        credentials:"include",
        headers:{
          "X-CSRF-Token": localStorage.getItem("csrf_token") || ""
        }
      });
    } catch(e){}

    localStorage.removeItem("csrf_token");
    window.location.href = "login.html";
  });

  loadCourses();
</script>
</body>
</html>