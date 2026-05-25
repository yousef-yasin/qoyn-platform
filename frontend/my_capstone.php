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

$user_id = (int)$_SESSION["user_id"];
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title data-i18n="phase3_my_tasks_page_title">Phase 3 - My Tasks</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <script src="assets/js/i18n.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --navy2:#082B58;
      --blue:#2563eb;
      --yellow:#FFC24A;
      --bg:#F4F7FC;
      --card:#ffffff;
      --text:#0B2144;
      --muted:#5F6F86;
      --line:#E4EBF5;
      --shadow:0 20px 45px rgba(10,46,93,.10);
      --soft:#F2F6FD;
      --container:1268px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      background:var(--bg);
      color:var(--text);
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      direction:ltr;
      text-align:left;
      overflow-x:hidden;
    }

    h1,h2,h3,h4,b{font-family:"Montserrat", sans-serif;}

    /* ===== TOP BAR exactly like reference ===== */
    .qoyn-topbar{
      position:sticky;
      top:0;
      z-index:9999;
      width:100%;
      padding:0 10px;
      background:#fff;
      box-shadow:0 8px 24px rgba(10,46,93,.06);
      border-bottom:1px solid rgba(10,46,93,.05);
      border-bottom-left-radius:34px;
      border-bottom-right-radius:34px;
    }

    .qoyn-topbar-inner{
      width:100%;
      min-height:88px;
      padding:0 26px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
    }

    .qoyn-logo{
      font-family:"Montserrat", sans-serif;
      font-weight:900;
      font-size:35px;
      line-height:1;
      color:var(--navy);
      letter-spacing:.7px;
      text-decoration:none;
      white-space:nowrap;
    }

    .qoyn-right{display:flex;align-items:center;gap:18px;}

    .qoyn-link{
      height:48px;
      min-width:108px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      text-decoration:none;
      color:#18243A;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:15px;
      padding:0 20px;
      border-radius:999px;
      border:1px solid rgba(10,46,93,.12);
      background:#fff;
      box-shadow:0 8px 18px rgba(10,46,93,.05);
      transition:.22s ease;
      white-space:nowrap;
    }

    .qoyn-link:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(10,46,93,.12);}
    .qoyn-link[href*="student-dashboard"]{background:var(--navy);color:#fff;box-shadow:0 12px 24px rgba(10,46,93,.25);}
    .qoyn-link[href*="student-dashboard"]::before{content:"⌂";font-size:20px;line-height:1;}
    .qoyn-link[href*="student_chat"]::before{content:"○";font-size:20px;line-height:1;}
    .qoyn-link[href="index.php"]::before{content:"‹";font-size:28px;line-height:.8;}
    .qoyn-link.logout::before{content:"↪";font-size:20px;line-height:1;}
    .qoyn-link.logout:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

    .topbar-logo{height:58px;width:auto;display:block;flex:0 0 auto;}

    .lang-dropdown{position:relative;display:inline-flex;align-items:center;}
    .lang-trigger{
      border:none;background:transparent;color:#17243b;font-weight:600;font-size:15px;
      font-family:"Poppins",sans-serif;padding:10px 8px;border-radius:999px;cursor:pointer;
      display:inline-flex;align-items:center;gap:8px;white-space:nowrap;transition:.2s ease;
    }
    .lang-trigger:hover{color:var(--navy);transform:translateY(-1px)}
    .lang-arrow{font-size:11px;transition:.2s ease}.lang-dropdown.open .lang-arrow{transform:rotate(180deg)}
    .lang-menu{position:absolute;top:calc(100% + 10px);left:0;display:none;min-width:170px;padding:8px;background:#fff;border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.10);z-index:9999;}
    .lang-dropdown.open .lang-menu{display:flex;flex-direction:column;gap:8px;}
    .lang-option{border:none;background:transparent;color:#111;font-weight:500;font-size:15px;font-family:"Poppins",sans-serif;padding:10px 14px;border-radius:999px;text-align:left;cursor:pointer;transition:.2s ease;}
    .lang-option:hover,.lang-option.active{color:#fff;background:var(--yellow);font-weight:800;}

    /* ===== HERO: white page, right navy wave/image like reference ===== */
    .phase3-hero{
      position:relative;
      min-height:560px;
      width:100%;
      margin:-28px 0 0;
      display:flex;
      align-items:stretch;
      background:#fff;
      overflow:hidden;
    }

    .phase3-hero::before{
      content:"";
      position:absolute;
      left:380px;
      top:-165px;
      width:340px;
      height:460px;
      background:rgba(10,46,93,.045);
      border-radius:0 0 190px 190px;
      transform:rotate(-38deg);
      z-index:0;
    }

    .phase3-hero::after{
      content:"";
      position:absolute;
      left:710px;
      top:285px;
      width:250px;
      height:160px;
      opacity:.38;
      background-image:radial-gradient(rgba(10,46,93,.20) 1.4px, transparent 1.4px);
      background-size:18px 18px;
      z-index:0;
    }

    .phase3-hero .container{
      width:100%;
      max-width:1405px;
      margin:0 auto;
      padding:82px 68px 55px;
      position:relative;
      z-index:2;
    }

    .phase3-frame{
      position:relative;
      min-height:430px;
      display:grid;
      grid-template-columns: 48% 52%;
      align-items:center;
      gap:0;
    }

    .phase3-center{position:relative;z-index:5;max-width:570px;text-align:left;margin:0;padding:0;}
    .phase3-center::before{
      content:"★  Save";
      position:absolute;
      left:0;
      top:-60px;
      color:#3c5d91;
      font-family:"Montserrat",sans-serif;
      font-weight:800;
      font-size:14px;
      border:2px solid rgba(255,194,74,.55);
      border-radius:999px;
      padding:6px 14px;
      background:#fff;
      box-shadow:0 6px 14px rgba(10,46,93,.05);
    }

    .phase3-title{
      margin:0 0 26px 0;
      font-family:"Montserrat",sans-serif;
      font-weight:900;
      font-size:58px;
      line-height:1.03;
      letter-spacing:-1.9px;
      color:var(--navy);
      max-width:560px;
    }
    .phase3-title .navy{color:var(--yellow);display:block;}

    .phase3-title::after{
      content:"";
      display:block;
      width:50px;height:4px;background:var(--yellow);border-radius:10px;margin-top:18px;
      box-shadow:24px 0 0 -1px var(--navy);
    }

    .phase3-desc{margin:0;max-width:555px;font-size:16px;line-height:1.86;color:#303A4B;}
    .phase3-desc b,.phase3-desc strong{color:var(--yellow)}

    .hero-visual{
      position:absolute;
      right:-58px;
      top:-20px;
      width:790px;
      height:585px;
      display:flex;
      align-items:flex-end;
      justify-content:center;
      z-index:2;
      overflow:visible;
    }

    .hero-visual::before{
      content:"";
      position:absolute;
      right:-170px;
      top:20px;
      width:820px;
      height:535px;
      background:var(--navy);
      border-radius:55% 0 0 68%;
      z-index:0;
    }

    .hero-visual::after{
      content:"🚀";
      position:absolute;
      right:118px;
      top:170px;
      font-size:44px;
      transform:rotate(-15deg);
      filter:drop-shadow(0 6px 10px rgba(0,0,0,.12));
      z-index:5;
    }

    .hero-effects{position:absolute;inset:0;pointer-events:none;z-index:1;}
    .hero-effects span{position:absolute;display:block;border-radius:999px;}
    .hero-effects .e1{right:295px;top:105px;width:560px;height:270px;border-top:2px solid rgba(255,194,74,.60);border-radius:50%;background:transparent;transform:rotate(-9deg);}
    .hero-effects .e2{right:430px;top:54px;width:92px;height:92px;background:rgba(10,46,93,.055);}
    .hero-effects .e3{right:105px;top:145px;width:12px;height:12px;background:var(--yellow);box-shadow:0 112px 0 var(--yellow), -305px 160px 0 var(--yellow);}
    .hero-effects .e4{right:100px;top:82px;width:510px;height:335px;background-image:radial-gradient(var(--yellow) 1.35px, transparent 1.35px);background-size:34px 34px;border-radius:0;opacity:.90;}
    .hero-effects .e5{right:42px;top:104px;width:420px;height:365px;border:1px solid rgba(255,194,74,.20);background:transparent;}

    .hero-image-wrap{position:absolute;right:112px;bottom:18px;z-index:4;width:560px;display:flex;align-items:flex-end;justify-content:center;}
    .hero-image-wrap img{display:block;width:100%;height:auto;object-fit:contain;filter:drop-shadow(0 22px 32px rgba(0,0,0,.20));transform:none;}

    /* ===== Content cards ===== */
    .wrap{max-width:1268px;margin:-78px auto 0;padding:0 20px 60px;direction:ltr;position:relative;z-index:5;}
    .card{background:transparent;border:0;border-radius:0;padding:0;box-shadow:none;}
    .card-intro{display:none}.muted{color:var(--muted);opacity:1}.small{font-size:13px}.hr{display:none}
    #tasksBox{min-height:80px;}
    #tasksBox.loading-center,#tasksBox.empty-center{text-align:center;padding:30px;border-radius:24px;background:#fff;box-shadow:var(--shadow);border:1px solid var(--line);}

    .task{margin:0 0 38px;background:transparent;border:0;padding:0;box-shadow:none;}
    .task-layout{display:grid;grid-template-columns: 1fr 1.25fr;gap:18px;align-items:stretch;direction:ltr;}
    .task-panel{position:relative;background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);overflow:hidden;min-height:420px;display:flex;flex-direction:column;}
    .task-panel:first-child::before{content:"";position:absolute;left:0;top:0;bottom:0;width:24px;background:var(--navy);border-radius:18px 0 0 18px;}
    .task-panel-head{background:#fff;color:var(--navy);padding:34px 34px 14px 78px;font-family:"Montserrat",sans-serif;font-weight:900;font-size:20px;line-height:1.35;text-align:left;border:0;}
    .task-panel:first-child .task-panel-head::before{content:"";position:absolute;left:42px;top:44px;width:70px;height:70px;border-radius:50%;background:var(--navy);box-shadow:0 10px 24px rgba(10,46,93,.18);}
    .task-panel:first-child .task-panel-head{padding-left:132px;}
    .task-panel:first-child .task-panel-head::after{content:"🧠";position:absolute;left:58px;top:56px;font-size:34px;}
    .task-panel:nth-child(2) .task-panel-head{padding-left:82px;}
    .task-panel:nth-child(2) .task-panel-head::before{content:"▦";position:absolute;left:30px;top:32px;width:46px;height:46px;border-radius:50%;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;}
    .task-panel-body{background:#fff;padding:8px 34px 28px;display:flex;flex-direction:column;gap:14px;flex:1;text-align:left;}
    .task-panel:first-child .task-panel-body{padding-left:72px;}
    .task-line{font-size:14.5px;line-height:1.78;color:#242B3B;text-align:left;word-break:break-word;}
    .task-line b{color:#17243B;}
    .task-group{display:flex;flex-direction:row;align-items:center;gap:14px;flex-wrap:wrap;}
    .task-group .small{margin:0;min-width:112px;font-weight:800;color:#17243B;}
    .pill{display:inline-block;padding:8px 18px;border-radius:999px;border:1px solid #D7E1F2;font-size:13px;margin:4px 6px 0 0;background:#F2F6FD;color:#2359A5;font-family:"Montserrat",sans-serif;font-weight:800;}
    .status-box{margin-top:auto;display:block;padding:22px 20px;border-radius:12px;background:#F0F4FC;border:1px solid #D7E1F2;color:#17243B;font-family:"Poppins",sans-serif;font-weight:500;}
    .status-box b{color:#1B5AA6;}
    details{margin-top:auto;text-align:left;background:#F1F5FB;border:1px solid #DDE7F5;border-radius:12px;padding:17px 18px;}
    details summary{cursor:pointer;font-family:"Montserrat",sans-serif;font-weight:800;color:#2760AE;text-align:left;list-style:none;}
    details summary::-webkit-details-marker{display:none;}
    details summary::after{content:"⌄";float:right;color:#2760AE;font-size:22px;line-height:.8;}

    .row{display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;padding:13px 20px;border-radius:14px;text-decoration:none;border:1px solid #DCE5F2;background:#fff;color:var(--navy);cursor:pointer;font-family:"Montserrat",sans-serif;font-weight:900;transition:.18s ease;}
    .btn:hover{transform:translateY(-2px);box-shadow:0 14px 24px rgba(10,46,93,.12);}
    .btn.primary{background:var(--navy);color:#fff;border-color:var(--navy);box-shadow:0 14px 26px rgba(10,46,93,.18);}
    .btn.primary:hover{background:#0D3A75;}
    .btn.back-main{min-width:150px;padding-left:28px;padding-right:28px;}

    textarea,input[type="text"],input[type="file"]{width:100%;padding:17px 18px;border-radius:12px;border:1px solid rgba(10,46,93,.18);font-family:"Poppins",sans-serif;background:#fff;box-sizing:border-box;font-size:15px;color:#18243A;}
    textarea:focus,input[type="text"]:focus,input[type="file"]:focus{outline:none;border-color:rgba(10,46,93,.42);box-shadow:0 0 0 4px rgba(10,46,93,.08);}
    textarea{min-height:126px;resize:vertical;}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

    /* Submit section matches second reference screen */
    .submit-box{position:relative;margin-top:56px;background:#fff;border:0;border-radius:0;padding:50px 92px 70px;box-shadow:none;overflow:hidden;}
    .submit-box::before{content:"";position:absolute;right:-115px;top:-115px;width:315px;height:315px;border-radius:50%;background:rgba(10,46,93,.035);border:1px solid rgba(10,46,93,.10);z-index:0;}
    .submit-box::after{content:"";position:absolute;right:18px;top:42px;width:175px;height:150px;background-image:radial-gradient(rgba(10,46,93,.24) 1.2px, transparent 1.2px);background-size:15px 15px;opacity:.55;z-index:0;}
    .submit-box > *{position:relative;z-index:1;}
    .submit-title{margin:0 0 38px;color:var(--navy);font-family:"Montserrat",sans-serif;font-size:50px;font-weight:900;line-height:1.08;text-align:left;letter-spacing:-1px;}
    .submit-title::after{content:"";display:block;width:86px;height:6px;background:var(--yellow);border-radius:20px;margin-top:18px;box-shadow:104px 0 0 -2px rgba(255,194,74,.7);}
    .submit-field{position:relative;margin-top:30px;padding-left:86px;text-align:left;}
    .submit-field::before{content:"";position:absolute;left:0;top:5px;width:56px;height:56px;border-radius:50%;background:#fff;border:1px solid #DDE6F3;box-shadow:0 10px 22px rgba(10,46,93,.05);display:block;}
    .submit-field:nth-of-type(1)::after{content:"🔗";position:absolute;left:16px;top:18px;font-size:24px;}
    .submit-field:nth-of-type(2)::after{content:"▣";position:absolute;left:17px;top:18px;font-size:24px;color:var(--navy);}
    .submit-field:nth-of-type(3)::after{content:"☁";position:absolute;left:14px;top:17px;font-size:26px;color:var(--navy);}
    .submit-field label{display:block;margin:0 0 10px;color:#17243B;font-family:"Montserrat",sans-serif;font-weight:900;font-size:16px;line-height:1.5;text-align:left;}
    .submit-note{margin-top:12px;color:#6A7380;font-size:14px;}
    .submit-actions{margin-top:42px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:flex-start;}
    .submit-actions .btn.primary{min-width:220px;height:64px;border-radius:14px;font-size:22px;}
    .submit-actions .btn.primary::before{content:"➤";font-size:22px;}
    .section-soft{background:transparent;border-radius:0;padding:0;}

    @media (max-width:1100px){
      .phase3-hero .container{padding:70px 34px 70px}.phase3-frame{grid-template-columns:1fr;min-height:auto}.hero-visual{position:relative;right:auto;top:auto;width:100%;height:420px;margin-top:20px}.phase3-title{font-size:48px}.task-layout{grid-template-columns:1fr}.submit-box{padding:42px 28px 56px}.submit-field{padding-left:0}.submit-field::before,.submit-field::after{display:none}.submit-title{font-size:42px}
    }
    @media (max-width:720px){
      .qoyn-topbar-inner{min-height:auto;padding:14px;align-items:flex-start}.qoyn-right{gap:8px;flex-wrap:wrap;justify-content:flex-end}.qoyn-logo{font-size:28px}.topbar-logo{height:44px}.qoyn-link{height:40px;min-width:auto;padding:0 12px;font-size:12px}.phase3-hero{margin:0}.phase3-hero .container{padding:56px 18px 54px}.phase3-title{font-size:36px}.phase3-desc{font-size:14px}.hero-visual{height:310px;right:0}.hero-visual::before{right:-260px;width:620px;height:360px}.hero-image-wrap{right:46px;width:260px}.wrap{padding:0 14px 38px}.task-panel-head{font-size:17px}.task-panel:first-child .task-panel-head{padding-left:100px}.task-panel:first-child .task-panel-head::before{left:32px}.task-panel:first-child .task-panel-head::after{left:48px}.task-panel:first-child .task-panel-body{padding-left:28px}.task-group{flex-direction:column;align-items:flex-start}.submit-title{font-size:32px}.submit-box{padding:34px 18px 46px}.submit-actions .btn.primary{min-width:100%;font-size:18px}.grid2{grid-template-columns:1fr;}
    }
  </style>
</head>
<body data-project-id="<?= (int)($_GET['project_id'] ?? 0) ?>" data-team-id="<?= (int)($_GET['team_id'] ?? 0) ?>">
  <div class="qoyn-topbar">
    <div class="qoyn-topbar-inner">
      <a href="index.php" class="qoyn-logo">QOYN</a>

      <div class="qoyn-right">
        <div class="lang-dropdown" id="langDropdown">
          <button class="lang-trigger" id="langTrigger" type="button">
            <span id="currentLangText">English</span>
            <span class="lang-arrow">▼</span>
          </button>

          <div class="lang-menu" id="langMenu">
            <button class="lang-option" data-lang="ar" type="button">العربية</button>
            <button class="lang-option" data-lang="en" type="button">English</button>
          </div>
        </div>

      <a href="student-dashboard.php#home" class="qoyn-link nav-pill" data-i18n="HOME">HOME</a>
<a href="student_chat.php" class="qoyn-link nav-pill">Chat</a>
<a href="index.php" class="qoyn-link nav-pill" data-i18n="back">BACK</a>
<a href="#" id="logoutBtn" class="qoyn-link nav-pill logout" data-i18n="logout">LOGOUT</a>
        <img src="uploads/MONKEY.png" class="topbar-logo" alt="Logo">
      </div>
    </div>
  </div>

  <section class="phase3-hero">
    <div class="container">
      <div class="phase3-frame">
        <div class="phase3-center">
          <h1 class="phase3-title" data-i18n="phase3_hero_title" data-i18n-html="true">
            <span class="navy">Phase3 Unlocked!</span> Time for the Big Project
          </h1>

          <p class="phase3-desc" data-i18n="phase3_hero_desc">
            Congratulations! By collecting 20,000 coins, you've proven your abilities and unlocked Phase Three.
            Now it's time to step into the big project, collaborate with others, and turn your knowledge into real impact.
          </p>
        </div>

        <div class="hero-visual">
          <div class="hero-effects" aria-hidden="true">
            <span class="e1"></span>
            <span class="e2"></span>
            <span class="e3"></span>
            <span class="e4"></span>
            <span class="e5"></span>
          </div>

          <div class="hero-image-wrap">
            <img src="uploads/ph3.png" alt="Phase 3 Visual">
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="wrap">
    <div class="card">
      <div id="tasksBox" class="muted loading-center" data-i18n="loading">Loading...</div>

      <div class="section-soft">
        <div class="hr"><hr/></div>

        <div class="row">
          <a class="btn primary back-main" href="index.php" data-i18n="back">Back</a>
        </div>

        <div class="card" style="margin-top:28px;">
          <div class="card-intro">
            <h2>AI Tools</h2>
            <p class="muted">Analyze your skill gap, get recommended playlists, and ask the AI mentor about your current task.</p>
          </div>

          <div class="task" style="margin-top:18px;">
            <div class="task-layout">
              <div class="task-panel">
                <div class="task-panel-head">Skill Gap Analyzer</div>
                <div class="task-panel-body">
                  <div class="task-line">See which skills are missing for your selected role.</div>
                  <div class="row">
                    <button id="btnLoadSkillGap" class="btn primary" type="button">Load Skill Gap</button>
                  </div>
                  <div id="skillGapBox" style="margin-top:14px;"></div>
                </div>
              </div>

              <div class="task-panel">
                <div class="task-panel-head">Recommended Playlists</div>
                <div class="task-panel-body">
                  <div class="task-line">Get AI-powered playlist recommendations based on your path and skill gaps.</div>
                  <div class="row">
                    <button id="btnLoadRecommendations" class="btn primary" type="button">Load Recommendations</button>
                  </div>
                  <div id="recommendBox" style="margin-top:14px;"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="submit-box" style="margin-top:24px;">
            <h3 class="submit-title">AI Mentor</h3>

            <div class="submit-field">
              <label for="mentorQuestion">Ask about your current task</label>
              <textarea id="mentorQuestion" rows="4" placeholder="Example: What should I learn first to succeed in my current task?"></textarea>
            </div>

            <div class="submit-actions">
              <button id="btnAskMentor" class="btn primary" type="button">Ask Mentor</button>
            </div>

            <div id="mentorAnswerBox" style="margin-top:14px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
const API_BASE = "../utbn-backend/api";

function __t(key, fallback = ""){
  if (typeof MESSAGES !== "undefined" && MESSAGES && MESSAGES[key]) return MESSAGES[key];
  return fallback || key;
}

async function apiGet(path){
  const res = await fetch(`${API_BASE}/${path}`, { credentials:"include" });
  const j = await res.json().catch(()=>({}));
  return { ok:res.ok, status:res.status, json:j };
}

async function apiPostForm(path, formData){
  const res = await fetch(`${API_BASE}/${path}`, {
    method:"POST",
    credentials:"include",
    body: formData
  });
  const text = await res.text();
  let j = {};
  try { j = JSON.parse(text); } catch { j = {_raw:text}; }
  return { ok:res.ok, status:res.status, json:j };
}

function esc(s){
  return (s ?? "").toString()
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

function arrSafe(v){
  if (Array.isArray(v)) return v;
  try {
    const j = JSON.parse(v);
    return Array.isArray(j) ? j : [];
  } catch {
    return [];
  }
}

function pills(items){
  items = items || [];
  if (!items.length) return `<span class="muted small">—</span>`;
  return items.map(x => `<span class="pill">${esc(x)}</span>`).join("");
}

function getStatusText(st){
  st = (st || "ASSIGNED").toUpperCase();

  if (st === "WAITING_COMPANY_REVIEW") return __t("phase3_status_waiting_review", "Submitted and waiting for company review");
  if (st === "REVIEWED") return __t("phase3_status_reviewed", "Your submission has been reviewed");
  if (st === "SELECTED") return __t("phase3_status_selected", "You have been selected in the project");
  if (st === "NOT_SELECTED") return __t("phase3_status_not_selected", "You were not selected");
  return __t("phase3_status_assigned", "Task assigned to you");
}

async function loadTasks(){
  const box = document.getElementById("tasksBox");
  box.classList.add("loading-center");
  box.textContent = __t("loading", "Loading...");

  const r = await apiGet("phase3/my_tasks.php");

  if (!r.ok) {
    box.textContent = __t("failed_label", "Failed") + ": " + (r.json.error || r.status);
    return;
  }

  const tasks = r.json.tasks || [];
  if (!tasks.length) {
    box.classList.remove("loading-center");
    box.classList.add("empty-center");
    box.textContent = __t("phase3_no_assigned_tasks", "No assigned tasks yet.");
    return;
  }

  box.classList.remove("loading-center", "empty-center");

  box.innerHTML = tasks.map(t => {
    const skills = arrSafe(t.skills || t.skills_json);
    const acc    = arrSafe(t.acceptance || t.acceptance_json);
    const deps   = arrSafe(t.dependencies || t.dependencies_json);

    const st = (t.assignment_status || t.status || "ASSIGNED").toUpperCase();
    const statusText = getStatusText(st);

    const taskId = t.task_id || t.id || 0;
    const submitted = !!t.submission_id;
    const canSubmit = !submitted && st === "ASSIGNED";

    return `
      <div class="task">
        <div class="task-layout">
          <div class="task-panel">
            <div class="task-panel-head">${esc(t.task_code || "—")} — ${esc(t.role_name || "—")}</div>

            <div class="task-panel-body">
              <div class="task-line">${esc(t.description || "")}</div>

              <div class="task-line"><b>${esc(__t("task_id", "Task ID"))}:</b> ${esc(taskId)}</div>

              <div class="task-group">
                <div class="small"><b>${esc(__t("skills", "Skills"))}:</b></div>
                <div>${pills(skills)}</div>
              </div>

              <div class="task-group">
                <div class="small"><b>${esc(__t("dependencies", "Dependencies"))}:</b></div>
                <div>${pills(deps)}</div>
              </div>

              <div class="status-box small">
                <b>${esc(__t("status", "Status"))}:</b> ${esc(statusText)}
              </div>
            </div>
          </div>

          <div class="task-panel">
            <div class="task-panel-head">${esc(__t("company", "Company"))}: ${esc(t.company_name || "—")}</div>

            <div class="task-panel-body">
              <div class="task-line"><b>${esc(__t("big_project", "Big Project"))}:</b> ${esc(t.capstone_title || "—")}</div>
              <div class="task-line"><b>${esc(__t("project_description", "Project Description"))}:</b> ${esc(t.capstone_description || "—")}</div>
              <div class="task-line"><b>${esc(__t("team_size", "Team Size"))}:</b> ${esc(t.team_size || "?")} ${esc(__t("students", "Students"))}</div>

              <details>
                <summary><b>${esc(__t("acceptance_criteria", "Acceptance Criteria"))}</b> (${esc(__t("click_to_show_details", "click to show details"))})</summary>
                <div style="margin-top:10px">
                  ${acc.length
                    ? `<ul class="small">${acc.map(x => `<li>${esc(x)}</li>`).join("")}</ul>`
                    : `<div class="muted small">—</div>`
                  }
                </div>
              </details>
            </div>
          </div>
        </div>

        <div class="submit-box">
          <h3 class="submit-title">${esc(__t("submit_the_project", "Submit the Project"))}</h3>

          <div class="submit-field">
            <label for="repo_${taskId}">${esc(__t("repo_url_optional", "Repo URL (optional)"))}</label>
            <input type="text" placeholder="${esc(__t("repo_url_optional", "Repo URL (optional)"))}" id="repo_${taskId}" ${canSubmit ? "" : "disabled"}/>
          </div>

          <div class="submit-field">
            <label for="zip_${taskId}">${esc(__t("zip_file", "ZIP File"))}</label>
            <input type="file" id="zip_${taskId}" ${canSubmit ? "" : "disabled"}/>
            <div class="muted small submit-note">${esc(__t("phase3_zip_note", "Preferably a ZIP containing your part files + a small README."))}</div>
          </div>

          <div class="submit-field">
            <label for="notes_${taskId}">${esc(__t("comment_optional", "Comment / note for reviewer or company (optional)"))}</label>
            <textarea rows="3" placeholder="${esc(__t("comment_optional", "Comment / note for reviewer or company (optional)"))}" id="notes_${taskId}" ${canSubmit ? "" : "disabled"}></textarea>
          </div>

          <div class="submit-actions">
            ${canSubmit
              ? `<button class="btn primary" type="button" onclick="submitTask(${taskId})">${esc(__t("submit_task", "Submit Task"))}</button>`
              : `<span class="muted">${esc(__t("cannot_resubmit_now", "You cannot resubmit now."))}</span>`
            }
            <span class="muted" id="msg_${taskId}"></span>
          </div>

          <div id="ai_${taskId}" style="margin-top:10px"></div>

          ${submitted ? `
            <div class="small muted" style="margin-top:10px">
              <b>${esc(__t("last_submission", "Last Submission"))}:</b> ${esc(t.latest_submitted_at || t.submitted_at || "Saved")}
            </div>
          ` : ``}
        </div>
      </div>
    `;
  }).join("");
}

async function submitTask(taskId){
  const msg = document.getElementById("msg_" + taskId);
  const aiBox = document.getElementById("ai_" + taskId);

  msg.textContent = __t("submitting", "Submitting...");
  aiBox.innerHTML = "";

  const repoEl = document.getElementById("repo_" + taskId);
  const zipEl  = document.getElementById("zip_" + taskId);
  const notesEl = document.getElementById("notes_" + taskId);

  const repo = repoEl ? repoEl.value.trim() : "";
  const file = zipEl && zipEl.files ? zipEl.files[0] : null;
  const notes = notesEl ? notesEl.value.trim() : "";

  const fd = new FormData();
  fd.append("task_id", String(taskId));
  fd.append("repo_url", repo);
  fd.append("notes", notes);
  if (file) fd.append("zip", file);

  const r = await apiPostForm("phase3/task_submit.php", fd);
  if (!r.ok) {
    msg.textContent = __t("failed_label", "Failed") + ": " + (r.json.error || r.status);
    return;
  }

  const g = r.json.grade?.grade || {};
  const score = (g.score ?? r.json.score ?? 0);
  const decision = (g.decision ?? r.json.decision ?? "PENDING");

  msg.textContent = `${__t("done", "Done")} ✔ score=${score} decision=${decision}`;

  const feedback = g.feedback || r.json.grade?.feedback || "";
  const fixes = g.fixes || r.json.grade?.fixes || [];

  if (feedback || (Array.isArray(fixes) && fixes.length)) {
    aiBox.innerHTML = `
      <div style="padding:12px;border:1px dashed rgba(0,0,0,.2);border-radius:12px">
        ${feedback ? `<div class="small"><b>${esc(__t("ai_feedback", "AI Feedback"))}:</b> ${esc(feedback)}</div>` : ``}
        ${Array.isArray(fixes) && fixes.length
          ? `<div class="small" style="margin-top:8px"><b>${esc(__t("suggested_fixes", "Suggested Fixes"))}:</b><ul>${fixes.map(x => `<li>${esc(x)}</li>`).join("")}</ul></div>`
          : ``
        }
      </div>
    `;
  }

  await loadTasks();
}

document.addEventListener("languageChanged", async () => {
  await loadTasks();
});

document.addEventListener("DOMContentLoaded", async () => {
  await loadTasks();
});

document.addEventListener("languageChanged", async () => {
  await loadTasks();
});
</script>
<script src="assets/js/phase3_ai_tools.js"></script>
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