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

// اختياري: لو عندك اسم المستخدم/الإيميل مخزن بالسيشن
$full_name = isset($_SESSION["full_name"]) ? trim($_SESSION["full_name"]) : "";
$email     = isset($_SESSION["email"]) ? trim($_SESSION["email"]) : "";

require_once __DIR__ . "/../utbn-backend/api/db.php";

$user_id = (int)($_SESSION["user_id"] ?? 0);
$show_phase3_level2_btn = false;
$phase3_level2_project_id = 0;

$sql = "
  SELECT project_id
  FROM phase3_task_submissions
  WHERE student_id = ?
    AND submitted_at IS NOT NULL
  ORDER BY submitted_at DESC
  LIMIT 1
";

$stmt = $conn->prepare($sql);
if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($row && !empty($row["project_id"])) {
    $show_phase3_level2_btn = true;
    $phase3_level2_project_id = (int)$row["project_id"];
  }
}

// حرف الأفاتار من الاسم أو الإيميل
$avatar_initial = "U";
if ($full_name !== "") {
  $avatar_initial = mb_strtoupper(mb_substr($full_name, 0, 1, "UTF-8"), "UTF-8");
} elseif ($email !== "") {
  $avatar_initial = strtoupper(substr($email, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title data-i18n="student_dashboard_title">QOYN | Student Dashboard</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --yellow:#FFC24A;
      --bg:#F6F7F9;
      --card:#ffffff;
      --text:#0B0B0B;
      --shadow: 0 10px 30px rgba(0,0,0,.08);
      --radius: 999px;
      --container: 1200px;
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: "Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text);
      background:var(--bg);
      overflow-x:hidden;
    }

    .nav-wrap{
      position: fixed;
      top:0; left:0; right:0;
      z-index:9999;
      padding: 18px 22px;
      transition: background .25s ease, box-shadow .25s ease, padding .25s ease;
      background: transparent;
    }

    .nav-wrap.scrolled{
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
      padding: 12px 22px;
    }

    .nav{
      max-width: var(--container);
      margin: 0 auto;
      display:flex;
      align-items:center;
      gap:16px;
    }

    .nav-right{
      display:flex;
      align-items:center;
      gap:14px;
    }

    .nav-monkey-wrap{
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-left: 50px;
    }

    .nav-right .nav-monkey{
      height:60px;
      width:auto;
      display:block;
      margin-left:0;
    }

    .nav-coin-mini{
      position:absolute;
      top:-14px;
      left:50%;
      transform:translateX(-50%);
      display:inline-flex;
      align-items:center;
      gap:6px;
      min-height:28px;
      padding:6px 10px;
      border-radius:999px;
      background:rgba(255,255,255,.98);
      border:1px solid rgba(10,46,93,.12);
      box-shadow:0 10px 24px rgba(10,46,93,.16);
      white-space:nowrap;
      z-index:3;
      transition:transform .22s ease, box-shadow .22s ease, opacity .22s ease;
    }

    .nav-coin-mini.bump{
      transform:translateX(-50%) scale(1.08);
      box-shadow:0 14px 30px rgba(10,46,93,.22);
    }

    .nav-coin-mini-icon{
      width:18px;
      height:18px;
      border-radius:50%;
      background:linear-gradient(180deg, #FFD36A 0%, #FFC24A 100%);
      display:grid;
      place-items:center;
      flex:0 0 18px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.5);
    }

    .nav-coin-mini-icon img{
      width:10px;
      height:10px;
      object-fit:contain;
      display:block;
    }

    .nav-coin-mini-value{
      font-family:"Montserrat", sans-serif;
      font-size:12px;
      font-weight:800;
      color:var(--navy);
      line-height:1;
      letter-spacing:.1px;
    }

    .nav-coin-mini-delta{
      position:absolute;
      top:-20px;
      right:-6px;
      padding:4px 8px;
      border-radius:999px;
      background:rgba(255,194,74,.95);
      color:var(--navy);
      font-size:11px;
      font-weight:800;
      box-shadow:0 8px 18px rgba(255,194,74,.25);
      opacity:0;
      transform:translateY(8px) scale(.92);
      transition:.28s ease;
      pointer-events:none;
    }

    .nav-coin-mini-delta.show{
      opacity:1;
      transform:translateY(0) scale(1);
    }

    .logo{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 28px;
      letter-spacing:.5px;
      color:var(--navy);
      text-decoration:none;
      user-select:none;
    }

    .nav-spacer{flex:1}

    .nav-links{
      display:flex;
      align-items:center;
      gap:18px;
      margin:0;
      padding:0;
      list-style:none;
      justify-content:flex-end;
    }

    .nav-links a{
      position:relative;
      text-decoration:none;
      color:#111;
      font-weight:500;
      font-size:15px;
      padding:10px 14px;
      border-radius: var(--radius);
      transition: color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
      white-space:nowrap;
    }

    .nav-links > li > a:hover:not(.phases-trigger){
  color: var(--yellow);
  transform: translateY(-2px);
  font-weight:700;
}

    .nav-links a.active{
      background: var(--yellow);
      color:#fff !important;
      font-weight:800;
    }

    .nav-logout{
      border: 1px solid rgba(10,46,93,.25);
      font-weight: 700;
      background: transparent;
    }

    .nav-logout:hover{
      background: var(--navy);
      color:#fff !important;
    }

    .avatar{
      width: 42px;
      height: 42px;
      border-radius: 999px;
      display:grid;
      place-items:center;
      text-decoration:none;
      background: rgba(10,46,93,.08);
      border: 1px solid rgba(10,46,93,.18);
      color: var(--navy);
      font-family:"Montserrat", sans-serif;
      font-weight:900;
      letter-spacing:.2px;
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
      box-shadow: 0 10px 22px rgba(0,0,0,.06);
    }
    .avatar:hover{
      transform: translateY(-2px);
      background: rgba(10,46,93,.12);
      border-color: rgba(10,46,93,.28);
      box-shadow: 0 16px 34px rgba(0,0,0,.10);
    }

    .avatar-dropdown{
      position: relative;
      display: inline-block;
    }

    .avatar-btn{
      cursor: pointer;
      border: none;
      outline: none;
    }

    .avatar-menu{
      position: absolute;
      top: calc(100% + 10px);
      right: 0;
      min-width: 180px;
      background: rgba(255,255,255,.98);
      border: 1px solid rgba(10,46,93,.10);
      border-radius: 18px;
      box-shadow: 0 18px 40px rgba(0,0,0,.12);
      padding: 10px;
      display: none;
      flex-direction: column;
      gap: 8px;
      z-index: 10000;
      backdrop-filter: blur(10px);
    }

    .avatar-dropdown.open .avatar-menu{
      display: flex;
    }

    .avatar-menu-item{
      text-decoration: none;
      color: var(--navy);
      background: #fff;
      border-radius: 14px;
      padding: 12px 14px;
      font-weight: 700;
      font-size: 14px;
      transition: background .18s ease, color .18s ease, transform .18s ease;
    }

    .avatar-menu-item:hover{
      background: var(--yellow);
      color: #fff;
      transform: translateY(-2px);
    }

    /* phases dropdown */
    .phases-dropdown{
  position: relative;
}

.phases-trigger{
  display:inline-flex;
  align-items:center;
  gap:8px;
  text-decoration:none;
  color:#111 !important;
  font-weight:600;
  font-size:15px;
  padding:10px 14px;
  border-radius:999px;
  cursor:pointer;
  user-select:none;
  transition: none;
  background: transparent !important;
  white-space: nowrap;
  transform:none !important;
  position: relative;
  z-index: 10001;
}

.nav-links .phases-trigger:hover,
.nav-links .phases-dropdown.open .phases-trigger,
.nav-links .phases-trigger:focus,
.nav-links .phases-trigger:active{
  color:#111 !important;
  background: transparent !important;
  transform:none !important;
  font-weight:600 !important;
}

.phases-trigger .phases-caret{
  font-size:12px;
  transition: transform .22s ease;
}

.phases-dropdown.open .phases-trigger .phases-caret{
  transform: rotate(180deg);
}

.phases-menu{
  position:absolute;
  top: calc(100% + 10px);
  left: 0;
  min-width: 240px;
  background: rgba(255,255,255,.98);
  border: 1px solid rgba(10,46,93,.10);
  border-radius: 22px;
  box-shadow: 0 18px 40px rgba(0,0,0,.12);
  padding: 12px;
  display: none;
  flex-direction: column;
  gap: 8px;
  z-index: 10000;
  backdrop-filter: blur(10px);
}

.phases-dropdown.open .phases-menu{
  display:flex;
}

.phases-item{
  display:block;
  text-decoration:none;
  color: var(--navy);
  background:#fff;
  border-radius:14px;
  padding:12px 14px;
  font-weight:700;
  font-size:14px;
  transition: background .18s ease, color .18s ease, transform .18s ease;
}

.phases-item:hover{
  background: var(--yellow);
  color:#111;
  transform: translateY(-2px);
}
.phases-menu .phases-item:hover,
.phases-menu .phases-item:focus,
.phases-menu .phases-item:active{
  background: var(--yellow) !important;
  color: #111 !important;
  transform: none !important;
  font-weight: 700 !important;
}
.phases-item.empty{
  pointer-events:none;
  opacity:.7;
  background:#f8fafc;
  color:#64748b;
}

    .progress{
      position:absolute;
      top:0; left:0;
      height:3px;
      width:100%;
      opacity:0;
      transition: opacity .25s ease;
      background: transparent;
    }
    .nav-wrap.scrolled .progress{ opacity:1; }
    .progress > div{
      height:100%;
      width:0%;
      background: var(--navy);
      transition: width .08s linear;
    }

    main{ padding-top: 92px; }

    section{
      min-height:100vh;
      display:flex;
      align-items:center;
      scroll-margin-top: 92px;
    }

    .container{
      max-width: var(--container);
      margin: 0 auto;
      padding: 0 22px;
      width:100%;
    }

    .home-grid{
      display:grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 40px;
      align-items:center;
    }

    .hero-title{
      font-family:"Montserrat", sans-serif;
      font-size: 64px;
      line-height: 1.05;
      margin:0 0 18px 0;
      font-weight:800;
      letter-spacing:-.5px;
    }
    .hero-title-wrap{
      position:relative;
    }

    .hero-title-en,
    .hero-title-i18n{
      display:block;
    }

    .hero-title .hero-navy{
      color: var(--navy) !important;
    }

    html[lang="en"] .hero-title-en{ display:block; }
    html[lang="en"] .hero-title-i18n{ display:none; }
    html[lang="ar"] .hero-title-en{ display:none; }
    html[lang="ar"] .hero-title-i18n{ display:block; }

    .navy-letter{ color: var(--navy); }

    .hero-text{
      font-size: 15.5px;
      line-height: 1.8;
      color: #111;
      max-width: 560px;
      margin:0;
      white-space: pre-line;
    }

    .hero-visual{
      position:relative;
      width:100%;
      height: 520px;
      display:flex;
      justify-content:flex-end;
      align-items:center;
    }

    .circle-wrap{
      position: relative;
      width: 490px;
      height: 490px;
      overflow: visible;
      margin-top: 26px;
    }

    .circle{
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: var(--navy);
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow);
    }

    .circle .hero-in{
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      width: 490px;
      height: auto;
      bottom: -140px;
    }

    .circle-wrap .hero-top{
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      width: 490px;
      height: auto;
      bottom: -140px;
      z-index: 10;
      pointer-events: none;
      clip-path: inset(0 0 62% 0);
    }

    .coin{
      position:absolute;
      width: 70px;
      height: auto;
      z-index: 20;
      pointer-events:none;
      filter: drop-shadow(0 12px 18px rgba(0,0,0,.18));
      animation: coinFloat 3.2s ease-in-out infinite;
    }

    .coin.coin-right{
      right: -30px;
      top: 70px;
      transform: rotate(12deg);
      animation-delay: .2s;
    }

    .coin.coin-left{
      left: -12px;
      bottom: 70px;
      transform: rotate(10deg);
      animation-delay: .6s;
    }

    .coin.coin-left-top{
      left: -22px;
      top: 28px;
      transform: rotate(-10deg);
      animation-delay: .35s;
    }

    @keyframes coinFloat{
      0%,100%{ transform: translateY(0) rotate(var(--rot, 0deg)); }
      50%{ transform: translateY(-10px) rotate(var(--rot, 0deg)); }
    }
    .coin-right{ --rot: 18deg; }
    .coin-left{ --rot: -14deg; }
    .coin-left-top{ --rot: -8deg; }

    .coins-badge{
      position:absolute;
      right:-12px;
      bottom:60px;
      display:flex;
      align-items:center;
      gap:12px;
      padding: 14px 18px;
      border-radius: 999px;
      background: rgba(104, 164, 194, 0.35);
      border: 1px solid rgba(255,255,255,.35);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 18px 40px rgba(0,0,0,.12);
      z-index: 60;
    }

    .coins-ic{
      width: 44px;
      height: 44px;
      border-radius: 999px;
      display:grid;
      place-items:center;
      background: rgba(255, 194, 74, .85);
      box-shadow: inset 0 0 0 2px rgba(255,255,255,.35);
      flex: 0 0 44px;
    }

    .coins-ic img{
      width: 22px;
      height: 22px;
      object-fit: contain;
      display:block;
    }

    .coins-txt{
      display:flex;
      flex-direction:column;
      line-height: 1.05;
    }

    .coins-num{
      font-family:"Montserrat", sans-serif;
      font-weight: 900;
      font-size: 22px;
      color:#fff;
      letter-spacing: .2px;
    }

    .coins-lbl{
      margin-top: 4px;
      font-family:"Poppins", sans-serif;
      font-weight: 500;
      font-size: 14px;
      color: rgba(255,255,255,.92);
    }

    #partner{ background: var(--bg); }

    .partner-wrap{ width: 100%; padding: 18px 0 0; }

    .partner-title{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 54px;
      letter-spacing:-.6px;
      line-height:1.05;
      color: var(--navy);
      margin: 0 0 26px 0;
      text-align:center;
    }

    .partner-viewport{
      position: relative;
      overflow-x: auto;
      overflow-y: hidden;
      padding: 18px 6px 22px;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      border-radius: 22px;
    }

    .partner-viewport::-webkit-scrollbar{ height: 10px; }
    .partner-viewport::-webkit-scrollbar-track{ background: rgba(0,0,0,.06); border-radius: 999px; }
    .partner-viewport::-webkit-scrollbar-thumb{ background: rgba(0,0,0,.16); border-radius: 999px; }
    .partner-viewport::-webkit-scrollbar-thumb:hover{ background: rgba(0,0,0,.22); }

    .partner-track{
      display:flex;
      gap: 18px;
      width: max-content;
      padding: 4px 10px;
      align-items: stretch;
    }

    .partner-card{
      flex: 0 0 auto;
      width: 300px;
      height: 290px;
      border-radius: 22px;
      overflow:hidden;
      position:relative;
      border: 1px solid rgba(0,0,0,.10);
      box-shadow: 0 12px 34px rgba(0,0,0,.10);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
      transform-origin: center;
      background:#000;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .partner-card:hover{
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 22px 60px rgba(0,0,0,.16);
      border-color: rgba(10,46,93,.25);
    }

    .partner-card::after{
      content:"";
      position:absolute;
      left:0; right:0; bottom:0;
      height: 56%;
      background: linear-gradient(to top, rgba(0,0,0,.72), rgba(0,0,0,0));
      pointer-events:none;
    }

    .partner-content{
      position:absolute;
      left: 16px;
      right: 16px;
      bottom: 14px;
      z-index:2;
      color:#fff;
    }

    .partner-name{
      margin:0 0 6px 0;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 18px;
      letter-spacing:-.2px;
      color:#fff;
    }

    .partner-desc{
      margin:0;
      font-size: 13.2px;
      line-height: 1.75;
      color: rgba(255,255,255,.92);
      max-width: 40ch;
    }

    .about-section{
      min-height: 80vh;
      background: var(--bg);
      align-items: center;
    }

    .about-inner{
      width:100%;
      position:relative;
      padding: 10px 0 0;
      display:flex;
      flex-direction:column;
      align-items:flex-start;
    }

    .about-title .qoyn{
      color: var(--navy);
    }

    .about-canvas{
      position:relative;
      width:100%;
      height: 520px;
    }

    .phase{
      position:absolute;
      width: 320px;
      color: var(--navy);
    }

    .phase .big-num{
      position:absolute;
      top: -62px;
      left: -10px;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 120px;
      color: rgba(0,0,0,.12);
      letter-spacing: -2px;
      line-height: 1;
      -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
      mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
    }

    .phase h3{
      margin: 0 0 6px 0;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 15px;
      color:#111;
    }

    .phase h4{
      margin: 0 0 10px 0;
      font-family:"Montserrat", sans-serif;
      font-weight:700;
      font-size: 14px;
      color: var(--navy);
    }

    .phase p{
      margin:0;
      font-size: 13px;
      line-height: 1.7;
      color: rgba(10,46,93,.85);
    }

    .phase1{ left: 6%;  top: 33%; }
    .phase2{ left: 38%; top: 18%; }
    .phase3{ right: 6%; top: 8%; }

    #about{
      position: relative;
      overflow: hidden;
      background: var(--bg);
      min-height: 62vh !important;
      padding: 34px 0 !important;
      align-items: center;
    }

    .about-box{
      max-width: 900px;
      margin: 0 auto;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .about-text{
      font-size: 18px;
      line-height: 1.95;
      color: #111;
      margin: 0 0 20px 0;
    }

    .about-help{
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      font-size: 18px;
      color: #111;
    }

    .about-btn{
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 14px 28px;
      border-radius: 14px;
      background: #111;
      color: #fff;
      text-decoration: none;
      font-family: "Montserrat", sans-serif;
      font-weight: 800;
      font-size: 14px;
      letter-spacing: .3px;
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
      box-shadow: 0 14px 28px rgba(0,0,0,.14);
    }

    .about-btn:hover{
      transform: translateY(-3px);
      background: var(--navy);
      box-shadow: 0 20px 42px rgba(0,0,0,.22);
    }

    .about-visual{
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1;
      pointer-events: none;
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    .about-visual img{
      width: 280px;
      max-height: 100%;
      height: auto;
      opacity: 0.15;
      filter: grayscale(100%);
      object-fit: contain;
    }

    #courses{
      background: var(--bg);
      min-height: 78vh;
      padding: 58px 0 64px;
      overflow: hidden;
      display: flex;
      align-items: center;
    }

    #courses .container{
      max-width: var(--container);
      padding: 0 22px;
    }

    .courses-title{
      text-align:center;
      font-family:"Montserrat", sans-serif;
      font-weight: 900;
      font-size: 54px;
      letter-spacing:-.9px;
      margin: 0 0 34px 0;
      line-height:1.05;
    }

    .courses-title .disc{ color:#111; }
    .courses-title .rest{ color: var(--navy); }

    #courses .it-courses-viewport{
      position: relative;
      overflow-x: auto;
      overflow-y: hidden;
      padding: 8px 0 18px;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      border-radius: 18px;
    }

    #courses .it-courses-viewport::-webkit-scrollbar{
      display: none;
    }

    #courses #itTrack{
      display:flex;
      gap: 24px;
      width: max-content;
      padding: 0;
      align-items: stretch;
    }

    #courses .partner-card.playlist{
      flex: 0 0 auto;
      width: 270px;
      height: 390px;
      background: #fff !important;
      border: 1px solid rgba(10,46,93,.06) !important;
      border-radius: 16px !important;
      overflow: hidden;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      position: relative;
      box-shadow: 0 12px 28px rgba(0,0,0,.07);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    #courses .partner-card.playlist::after{
      display: none !important;
    }

    #courses .partner-card.playlist:hover{
      transform: translateY(-5px);
      box-shadow: 0 20px 42px rgba(0,0,0,.12);
      border-color: rgba(10,46,93,.14) !important;
      background: #fff !important;
    }

    #courses .course-cover{
      width: 100%;
      height: 150px;
      flex: 0 0 150px;
      background: #0b0b0b;
      overflow: hidden;
    }

    #courses .course-cover-img{
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    #courses .course-cover-fallback{
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #0A2E5D 0%, #06172f 100%);
    }

    #courses .partner-content{
      position: static !important;
      z-index: 1;
      padding: 18px 18px 16px;
      margin-top: 0;
      color: #111;
      text-align: left;
      direction: ltr;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    #courses .partner-name{
      color: var(--navy) !important;
      font-family:"Montserrat", sans-serif;
      font-size: 20px;
      font-weight: 900;
      letter-spacing: -.25px;
      line-height: 1.15;
      margin: 0 0 12px 0;
    }

    #courses .partner-desc{
      color: rgba(0,0,0,.58) !important;
      font-size: 14px;
      font-weight: 500;
      line-height: 1.45;
      margin: 0;
      max-width: none;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    #courses .course-rating{
      display: flex;
      align-items: center;
      gap: 9px;
      margin-top: 18px;
      color: #626b76;
      font-size: 14px;
      font-weight: 700;
      line-height: 1;
    }

    #courses .course-rating .star{
      color: #FFC24A;
      font-size: 22px;
      line-height: 1;
      transform: translateY(-1px);
    }

    #courses .course-bottom{
      margin-top: auto;
      padding-top: 18px;
      border-top: 1px solid rgba(0,0,0,.07);
      display: flex;
      align-items: center;
      gap: 16px;
      color: #626b76;
      font-size: 14px;
      font-weight: 700;
      white-space: nowrap;
    }

    #courses .course-bottom .divider{
      width: 1px;
      height: 20px;
      background: rgba(0,0,0,.08);
      display: block;
      flex: 0 0 1px;
    }

    html[dir="rtl"] #courses .partner-content,
    html[dir="rtl"] #courses .courses-title{
      direction: ltr;
      text-align: left;
    }

    @media (max-width: 980px){
      #courses{
        min-height: auto;
        padding: 54px 0 54px;
      }

      .courses-title{
        font-size: 42px;
        margin-bottom: 28px;
      }

      #courses .partner-card.playlist{
        width: 250px;
        height: 370px;
      }

      #courses .course-cover{
        height: 138px;
        flex-basis: 138px;
      }

      #courses .partner-name{
        font-size: 18px;
      }
    }

    .footer2-section{
      min-height: 75vh;
      display: flex;
      align-items: center;
      background: var(--navy);
      color: #fff;
    }

    .footer2-grid{
      width: 100%;
      display: grid;
      grid-template-columns: 1.2fr 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    .footer2-brand{
      font-family:"Montserrat", sans-serif;
      font-size: 34px;
      font-weight: 900;
      letter-spacing: .2px;
      margin-bottom: 16px;
    }

    .f2-QOYN{ color: #dbdbdb; }

    .footer2-desc{
      margin: 0;
      color: rgba(255,255,255,.80);
      font-size: 14px;
      line-height: 1.9;
      max-width: 52ch;
    }

    .footer2-title{
      font-size: 16px;
      font-weight: 900;
      margin-bottom: 16px;
      color: #fff;
    }

    .footer2-links{
      display: grid;
      gap: 12px;
    }

    .footer2-links a{
      color: rgba(255,255,255,.86);
      text-decoration: none;
      font-weight: 700;
      width: fit-content;
      transition: color .15s ease, transform .15s ease;
    }

    .footer2-links a:hover{
      color: #fff;
      transform: translateX(2px);
    }

    .footer2-icons{
      display: flex;
      gap: 14px;
      margin-top: 8px;
    }

    .social-ic{
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: rgba(255,255,255,.08);
      color: rgba(255,255,255,.85);
      transition: transform .18s ease, background .18s ease;
      text-decoration: none;
    }

    .social-ic svg{ width: 20px; height: 20px; }

    .social-ic:hover{
      transform: translateY(-3px);
      background: rgba(255,255,255,.14);
    }

    .footer2-divider{
      margin: 34px 0 14px;
      height: 1px;
      background: rgba(255,255,255,.10);
    }

    .footer2-bottom{
      text-align: center;
      color: rgba(255,255,255,.75);
      font-size: 13px;
      line-height: 1.6;
    }

    @media (max-width: 980px){
      .hero-title{ font-size: 46px; }
      .hero-visual{ height: 420px; justify-content:center; }
      .circle{ width: 340px; height:340px; }
      .circle img{ width: 290px; }
      .circle-wrap{ margin-top: 14px; }

      .partner-title{ font-size: 38px; }
      .partner-card{ width: 280px; height: 180px; }

      .coin{ width: 56px; }
      .coin.coin-right{ right: 4px; top: 22px; }
      .coin.coin-left{ left: 2px; bottom: 28px; }

      #courses .it-courses-viewport .partner-card{ width: 320px; height: 410px; }

      .nav-monkey-wrap{ margin-left: 18px; }

      .nav-coin-mini{
        top:-12px;
        padding:5px 8px;
      }

      .nav-coin-mini-value{ font-size:11px; }

      .phases-menu{
        left:auto;
        right:0;
      }
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
    }

    .lang-switch-thumb{
      position:absolute;
      top:5px;
      left:5px;
      width:36px;
      height:36px;
      border-radius:50%;
      box-shadow:
        0 8px 18px rgba(10,46,93,.28),
        inset 0 1px 0 rgba(255,255,255,.20);
      transition:left .28s ease, right .28s ease, transform .28s ease;
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
      background:transparent;
      border-radius:50%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:0;
      cursor:pointer;
      font-family:"Montserrat", sans-serif;
      font-size:14px;
      font-weight:800;
      letter-spacing:.2px;
      color:rgba(10,46,93,.42);
      transition:color .22s ease, transform .18s ease;
    }

    .lang-option:hover{
      transform:scale(1.04);
    }

    .lang-option.active{
      color:#fff;
      background: #0A2E5D;
      font-weight: 800;
    }

    .lang-switch:not(.is-ar) #langOptionAr{
      color:rgba(10,46,93,.42);
    }

    .lang-switch.is-ar #langOptionEn{
      color:rgba(10,46,93,.42);
    }

    @media (max-width: 640px){
      .lang-switch{
        width:82px;
        height:44px;
      }

      .lang-switch-thumb{
        width:34px;
        height:34px;
      }

      .lang-switch.is-ar .lang-switch-thumb{
        left:43px;
      }

      .lang-option{
        width:34px;
        height:34px;
        font-size:13px;
      }
    }

    .qoyne-floating-assistant{
      position: fixed;
      right: 18px;
      bottom: 12px;
      width: 120px;
      height: 145px;
      z-index: 99999;
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      animation: qoyneRobotFloat 2.8s ease-in-out infinite;
    }

    .qoyne-floating-assistant:hover{
      transform: scale(1.04);
    }

    .qoyne-floating-assistant.is-hover .qoyne-robot{
      transform: rotate(0deg) translateY(-4px) scale(1.04);
    }

    .qoyne-floating-assistant.is-hover .qoyne-robot-img{
      filter:
        drop-shadow(0 12px 25px rgba(0,0,0,.25))
        drop-shadow(0 4px 10px rgba(0,0,0,.15));
    }

    .qoyne-floating-assistant.is-tap .qoyne-robot-img{
      animation: qoyneRobotTap .45s ease;
    }

    .qoyne-shadow{
      position: absolute;
      bottom: 2px;
      left: 50%;
      transform: translateX(-50%);
      width: 72px;
      height: 16px;
      background: rgba(0,0,0,.18);
      border-radius: 999px;
      filter: blur(4px);
      animation: qoyneShadowPulse 1s ease-in-out infinite;
    }

    .qoyne-robot{
      position: relative;
      width: 96px;
      height: 126px;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      transform-origin: bottom center;
      animation: qoyneRobotSwing 2.2s ease-in-out infinite;
      filter: drop-shadow(0 10px 22px rgba(10,46,93,.20));
    }

    .qoyne-robot::before{
      content: "";
      position: absolute;
      inset: 8px 14px 6px;
      border-radius: 26px;
      background: radial-gradient(circle at 50% 25%, rgba(255,255,255,.24), rgba(255,255,255,0) 55%);
      pointer-events: none;
    }

    .qoyne-robot-img{
      width: 100%;
      height: auto;
      display: block;
      object-fit: contain;
      transform-origin: bottom center;
      animation: qoyneRobotBounce 1.15s ease-in-out infinite;
      will-change: transform;
      filter:
        drop-shadow(0 12px 25px rgba(0,0,0,.25))
        drop-shadow(0 4px 10px rgba(0,0,0,.15));
    }

    .qoyne-ring{
      position: absolute;
      inset: auto;
      width: 92px;
      height: 92px;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 50%;
      border: 2px solid rgba(10,46,93,.12);
      animation: qoyneRingPulse 2.2s ease-out infinite;
    }

    @keyframes qoyneShadowPulse{
      0%,100%{ transform: translateX(-50%) scaleX(1); opacity:.18; }
      50%{ transform: translateX(-50%) scaleX(.82); opacity:.10; }
    }
    @keyframes qoyneRobotSwing{
      0%,100%{ transform: rotate(-4deg) translateY(0); }
      50%{ transform: rotate(4deg) translateY(-2px); }
    }
    @keyframes qoyneRobotBounce{
      0%,100%{ transform: translateY(0) scale(1); }
      50%{ transform: translateY(-6px) scale(1.02); }
    }
    @keyframes qoyneRingPulse{
      0%{ transform: translateX(-50%) scale(.9); opacity:.0; }
      20%{ opacity:.25; }
      100%{ transform: translateX(-50%) scale(1.25); opacity:0; }
    }
    @keyframes qoyneRobotFloat{
      0%,100%{ transform: translateY(0); }
      50%{ transform: translateY(-4px); }
    }
    @keyframes qoyneRobotTap{
      0%{ transform: translateY(0) scale(1) rotate(0deg); }
      35%{ transform: translateY(-8px) scale(1.05) rotate(-8deg); }
      70%{ transform: translateY(-2px) scale(.98) rotate(6deg); }
      100%{ transform: translateY(0) scale(1) rotate(0deg); }
    }

    @media (max-width: 768px){
      .qoyne-floating-assistant{
        right: 10px;
        bottom: 8px;
        transform: scale(.88);
        transform-origin: bottom right;
      }
    }

    .about-title-wrap{ width:100%; }
    .about-title-en,
    .about-title-i18n{
      display:block;
      width:100%;
    }

    .about-title{
      font-family:"Montserrat", sans-serif;
      font-weight:900;
      font-size:45px !important;
      line-height:1.08;
      letter-spacing:-0.8px;
      margin-top:0 !important;
      margin-bottom:0;
      max-width:720px !important;
    }

    .about-title .about-navy{ color:var(--navy); }

    html[lang="en"] .about-title-en{ display:block; }
    html[lang="en"] .about-title-i18n{ display:none; }
    html[lang="ar"] .about-title-en{ display:none; }
    html[lang="ar"] .about-title-i18n{ display:block; }

    html[lang="en"] .about-title{
      text-align:left !important;
      direction:ltr !important;
      margin-left:0 !important;
      margin-right:0 !important;
    }

    html[lang="ar"] .about-title{
      text-align:right !important;
      direction:rtl !important;
      max-width:100% !important;
    }

    @media (max-width: 980px){
      .about-title{
        font-size:40px !important;
        max-width:100% !important;
      }
    }

    @media (max-width: 640px){
      .about-title{
        font-size:32px !important;
        line-height:1.15;
      }
    }

    .about-journey-title{
      width:100%;
      max-width:720px !important;
    }

    html[lang="en"] .about-journey-title{
      direction:ltr !important;
      text-align:left !important;
    }

    html[lang="ar"] .about-journey-title{
      direction:rtl !important;
      text-align:right !important;
      max-width:100% !important;
    }

    .about-box .about-title{
      text-align:center !important;
      max-width:100% !important;
      align-self:center !important;
    }
  </style>

  <script src="assets/js/i18n.js"></script>
</head>

<body>
  <header class="nav-wrap" id="navWrap">
    <div class="progress"><div id="progressBar"></div></div>

    <nav class="nav">
      <a class="logo" href="#home">QOYN</a>

      <div class="nav-spacer"></div>

      <div class="lang-dropdown" id="langDropdown">
        <div class="lang-switch" id="langSwitch" role="tablist" aria-label="Language switcher">
          <span class="lang-switch-thumb" id="langSwitchThumb"></span>

          <button class="lang-option" id="langOptionEn" data-lang="en" type="button" role="tab" aria-selected="true">EN</button>
          <button class="lang-option" id="langOptionAr" data-lang="ar" type="button" role="tab" aria-selected="false">AR</button>
        </div>

        <span id="currentLangText" style="display:none;">English</span>
        <button id="langTrigger" type="button" style="display:none;"></button>
        <div id="langMenu" style="display:none;"></div>
      </div>

      <ul class="nav-links" id="navLinks">
        <li><a data-section="home" href="#home" class="active" data-i18n="nav_home">Home</a></li>

        <li class="phases-dropdown" id="phasesDropdown">
          <a href="#" class="phases-trigger" id="phasesTrigger">
            <span>Phases</span>
<span class="phases-caret">▲</span>          </a>

          <div class="phases-menu" id="phasesMenu">
            <a href="my_project.php" class="phases-item" id="phase2MenuItem" style="display:none;">Phase 2</a>
            <a href="my_capstone.php" class="phases-item" id="phase3MenuItem" style="display:none;">Phase 3</a>

            <?php if ($show_phase3_level2_btn): ?>
              <a href="phase3_level2.php?project_id=<?php echo $phase3_level2_project_id; ?>" class="phases-item">
                Phase 3 Level 2
              </a>
              <a href="job_simulator.php" class="phases-item">
                AI Job Simulator
              </a>
            <?php endif; ?>

            <span class="phases-item empty" id="phasesEmptyItem" style="display:none;">No opened phases yet</span>
          </div>
        </li>

      
        <li><a href="index.php" data-i18n="my_page">My page</a></li>
        <li><a href="courses.php" data-i18n="all_courses">All courses</a></li>
        <li><a href="my_courses.php" data-i18n="my_courses">My courses</a></li>
        <li><a href="student_profile.php" data-i18n="achievement">achievement</a></li>
<li><a href="#" id="logoutBtn" class="nav-logout" data-i18n="logout">Logout</a></li>
      </ul>

      <div class="nav-right">
        <div class="avatar-dropdown" id="avatarDropdown">
          <button class="avatar avatar-btn" id="avatarBtn" type="button" aria-label="My menu">
            <?php echo htmlspecialchars($avatar_initial); ?>
          </button>

          <div class="avatar-menu" id="avatarMenu">
            <a href="student-info.php" class="avatar-menu-item">My Profile</a>
          </div>
        </div>

        <div class="nav-monkey-wrap">
          <div class="nav-coin-mini" id="navCoinMini">
            <span class="nav-coin-mini-icon">
              <img src="uploads/qoinn.png" alt="coin" onerror="this.style.display='none'">
            </span>
            <span class="nav-coin-mini-value" id="navCoinMiniValue">0</span>
            <span class="nav-coin-mini-delta" id="navCoinMiniDelta">+0</span>
          </div>

          <img src="uploads/MONKEY.png" alt="QOYN Logo" class="nav-monkey">
        </div>
      </div>
    </nav>
  </header>

  <main>
    <section id="home">
      <div class="container">
        <div class="home-grid">
          <div>
            <h1 class="hero-title hero-title-wrap">
              <span class="hero-title-en" id="heroTitleEn" aria-hidden="true">
                <span class="hero-navy">Q</span>uality
                <span class="hero-navy">O</span>ptimizes
                <span class="hero-navy">Y</span>our Value
                <span class="hero-navy">N</span>exus
              </span>
              <span class="hero-title-i18n" id="heroTitleI18n" data-i18n="hero_title">Quality Optimizes Your Value Nexus</span>
            </h1>

            <p class="hero-text" data-i18n="hero_text" data-i18n-html="true">
A smart AI-powered platform that helps students transform their learning, skills, and real achievements into measurable value.
QOYN connects academic data, courses, projects, and experiences into one personalized journey — guiding students from university to real career opportunities.

Through intelligent skill analysis and a value-based reward system, every effort you make is recognized, measured, and rewarded.
            </p>
          </div>

          <div class="hero-visual">
            <div class="circle-wrap">
              <img class="coin coin-right" src="uploads/qoinn.png" alt="" aria-hidden="true" onerror="this.style.display='none'">
              <img class="coin coin-left" src="uploads/qoinn.png" alt="" aria-hidden="true" onerror="this.style.display='none'">
              <img class="coin coin-left-top" src="uploads/qoinn.png" alt="" aria-hidden="true" onerror="this.style.display='none'">

              <div class="coins-badge">
                <div class="coins-ic">
                  <img src="uploads/qoinn.png" alt="coin" onerror="this.style.display='none'">
                </div>
                <div class="coins-txt">
                  <div class="coins-num">+20,000</div>
                  <div class="coins-lbl">coins</div>
                </div>
              </div>

              <div class="circle">
                <img class="hero-in" src="uploads/PERSON.png" alt="Hero Image" onerror="this.style.display='none'">
              </div>

              <img class="hero-top" src="uploads/PERSON.png" alt="" aria-hidden="true" onerror="this.style.display='none'">
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about" class="about-section">
      <div class="container">
        <div class="about-inner">
          <h2 class="about-title about-title-wrap about-journey-title">
            <span class="about-title-en" aria-hidden="true">
              A Smarter Journey from<br>
              Learning to Opportunity<br>
              with <span class="about-navy">QOYN</span>
            </span>

            <span class="about-title-i18n" data-i18n="about_title" data-i18n-html="true">
              A Smarter Journey from<br>
              Learning to Opportunity<br>
              with <span class="qoyn">QOYN</span>
            </span>
          </h2>

          <div class="about-canvas">
            <div class="phase phase1">
              <div class="big-num">1</div>
              <h3 data-i18n="phase_1_title">Phase 1</h3>
              <h4 data-i18n="phase_1_heading">Learn &amp; Earn Value</h4>
              <p data-i18n="phase_1_text" data-i18n-html="true">
                Build in-demand skills through practical, AI-evaluated courses.<br>
                Earn QOYN Coins based on the quality and impact of what you learn.
              </p>
            </div>

            <div class="phase phase2">
              <div class="big-num">2</div>
              <h3 data-i18n="phase_2_title">Phase 2</h3>
              <h4 data-i18n="phase_2_heading">Practice on Real Projects</h4>
              <p data-i18n="phase_2_text">
Apply your skills to real-world projects and simulations.
Get evaluated by AI and earn more value through hands-on experience.
              </p>
            </div>

            <div class="phase phase3">
              <div class="big-num">3</div>
              <h3 data-i18n="phase_3_title">Phase 3</h3>
              <h4 data-i18n="phase_3_heading">Collaborate & Access Opportunities</h4>
              <p data-i18n="phase_3_text">
Work in AI-matched teams and access real projects, internships, and jobs.
Turn your skills into verified career opportunities before graduation.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about">
      <div class="container">
        <div class="about-grid">
          <div class="about-box">
            <h2 class="about-title" data-i18n="welcome_title" data-i18n-html="true">
              Welcome to <span class="qoyn">QOYN</span>
            </h2>

            <p class="about-text" data-i18n="welcome_text" data-i18n-html="true">
              QOYN helps you build measurable skills through courses, tasks, and practical projects.<br>
              Earn Coins for every achievement and track your growth with AI-powered evaluation.<br>
              Join real-world challenges, collaborate with teams, and gain career-ready experience.<br>
              Start today — your skills are your value, and your future begins here
            </p>

            <div class="about-help">
              <span data-i18n="need_help">if you need any help ask</span>
              <a class="about-btn" href="#" data-i18n="qoyne">QOYNE</a>
            </div>
          </div>

          <div class="about-visual">
            <img src="uploads/MONKEY.png" alt="About Image" onerror="this.style.display='none'">
          </div>
        </div>
      </div>
    </section>

    <section id="partner">
      <div class="container">
        <div class="partner-wrap">
          <h2 class="partner-title" data-i18n="our_partner">Our Partner</h2>

          <div class="partner-viewport" id="partnerViewport" aria-label="Partners slider">
            <div class="partner-track" id="partnerTrack">
              <article class="partner-card has-cover" style="background-image:url('assets/Google.png')">
                <div class="partner-content">
                  <h3 class="partner-name">Google</h3>
                  <p class="partner-desc">Global leader in search technology</p>
                </div>
              </article>

              <article class="partner-card has-cover" style="background-image:url('assets/Microsoft.png')">
                <div class="partner-content">
                  <h3 class="partner-name">Microsoft</h3>
                  <p class="partner-desc">Enterprise software cloud computing leader</p>
                </div>
              </article>

              <article class="partner-card has-cover" style="background-image:url('assets/Amazon.png')">
                <div class="partner-content">
                  <h3 class="partner-name">Amazon</h3>
                  <p class="partner-desc">E-commerce giant and cloud innovator</p>
                </div>
              </article>

              <article class="partner-card has-cover" style="background-image:url('assets/Meta.png')">
                <div class="partner-content">
                  <h3 class="partner-name">Meta</h3>
                  <p class="partner-desc">Social platforms and virtual reality</p>
                </div>
              </article>

              <article class="partner-card has-cover" style="background-image:url('assets/IBM.png')">
                <div class="partner-content">
                  <h3 class="partner-name">IBM</h3>
                  <p class="partner-desc">AI research enterprise technology pioneer</p>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="courses">
      <div class="container">
        <h2 class="courses-title" data-i18n="discover_our_courses" data-i18n-html="true">
          <span class="disc">Discover</span>
          <span class="rest">Our Courses</span>
        </h2>

        <div class="partner-viewport it-courses-viewport" id="itViewport" aria-label="IT playlists slider">
          <div class="partner-track" id="itTrack"></div>
        </div>

        <div id="itPartnerEmpty" class="muted" style="display:none;margin-top:10px;text-align:center" data-i18n="no_playlists_now">
          لا يوجد بلاي ليستات حالياً.
        </div>
      </div>
    </section>

    <section id="social" class="footer2-section">
      <div class="container" dir="ltr">
        <div class="footer2-grid">
          <div>
            <div class="footer2-brand"><span class="f2-QOYN">QOYN</span></div>
            <p class="footer2-desc" data-i18n="footer_desc" data-i18n-html="true">
              QOYN is an AI-powered platform that transforms learning into measurable value.
              We guide students through structured skill paths, reward achievements with Coins, and connect them to real projects and career opportunities before graduation.
            </p>
          </div>

          <div>
            <div class="footer2-title" data-i18n="quick_links">Quick Links</div>
            <nav class="footer2-links">
              <a href="#home" data-i18n="nav_home">Home</a>
              <a href="#about" data-i18n="nav_about">About</a>
              <a href="#partner" data-i18n="partner">Partner</a>
              <a href="#courses" data-i18n="nav_courses">Courses</a>
            </nav>
          </div>

          <div>
            <div class="footer2-title" data-i18n="follow_us">Follow Us</div>
            <div class="footer2-icons">
              <a class="social-ic" href="https://www.instagram.com/qoyn.jo?igsh=dnFoZ3pmMWZodzNo" target="_blank" rel="noopener" aria-label="Instagram">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M17.5 6.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
              </a>

              <a class="social-ic" href="https://www.linkedin.com/in/qoyn-jo-0b3aab3aa" target="_blank" rel="noopener" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M4 4h4v16H4V4Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M10 10h4v10h-4V10Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M14 11c1-1 2-1.5 3.5-1.5 2.5 0 4.5 1.8 4.5 5.5V20h-4v-4.5c0-1.8-.7-2.8-2-2.8-1 0-1.6.5-2 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M6 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <div class="footer2-divider"></div>

        <div class="footer2-bottom" data-i18n="footer_bottom_text">
          QOYN is a skill-based learning and career platform powered by AI. © 2026 QOYN.
        </div>
      </div>
    </section>
  </main>

  <script>
    const navWrap = document.getElementById("navWrap");
    const progressBar = document.getElementById("progressBar");

    function updateScrollUI(){
      const y = window.scrollY || document.documentElement.scrollTop;
      navWrap.classList.toggle("scrolled", y > 10);

      const doc = document.documentElement;
      const scrollTop = doc.scrollTop;
      const scrollHeight = doc.scrollHeight - doc.clientHeight;
      const p = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
      progressBar.style.width = p.toFixed(2) + "%";
    }
    window.addEventListener("scroll", updateScrollUI, {passive:true});
    updateScrollUI();

    const links = document.querySelectorAll("#navLinks a[data-section]");
    const homeSection = document.getElementById("home");

    function setActive(id){
      links.forEach(a => a.classList.toggle("active", a.dataset.section === id));
    }

    const observer = new IntersectionObserver((entries) => {
      const visible = entries.filter(e => e.isIntersecting).sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];

      if(visible && visible.target && visible.target.id){
        setActive(visible.target.id);
        history.replaceState(null, "", "#" + visible.target.id);
      }
    }, { root:null, threshold:[0.25, 0.4, 0.6] });

    if(homeSection) observer.observe(homeSection);
    setActive("home");

    const pViewport = document.getElementById("partnerViewport");
    const pTrack = document.getElementById("partnerTrack");

    pTrack.innerHTML = pTrack.innerHTML + pTrack.innerHTML;

    let pPaused = false;
    let pSpeed = 0.55;

    function partnerTick(){
      if(!pPaused && pViewport){
        pViewport.scrollLeft += pSpeed;
        const half = pTrack.scrollWidth / 2;
        if(pViewport.scrollLeft >= half){
          pViewport.scrollLeft = pViewport.scrollLeft - half;
        }
      }
      requestAnimationFrame(partnerTick);
    }

    pViewport.addEventListener("mouseover", (e) => {
      const card = e.target.closest(".partner-card");
      if(card) pPaused = true;
    });

    pViewport.addEventListener("mouseout", (e) => {
      const card = e.target.closest(".partner-card");
      if(card) pPaused = false;
    });

    pViewport.addEventListener("wheel", (e) => {
      if(Math.abs(e.deltaY) > Math.abs(e.deltaX)){
        e.preventDefault();
        pViewport.scrollLeft += e.deltaY;
      }
    }, {passive:false});

    let isDrag = false;
    let startX = 0;
    let startScrollLeft = 0;

    pViewport.style.cursor = "grab";

    pViewport.addEventListener("mousedown", (e) => {
      isDrag = true;
      pPaused = true;
      pViewport.style.cursor = "grabbing";
      startX = e.pageX;
      startScrollLeft = pViewport.scrollLeft;
    });

    window.addEventListener("mouseup", () => {
      if(!isDrag) return;
      isDrag = false;
      pPaused = false;
      pViewport.style.cursor = "grab";
    });

    pViewport.addEventListener("mousemove", (e) => {
      if(!isDrag) return;
      e.preventDefault();
      const dx = e.pageX - startX;
      pViewport.scrollLeft = startScrollLeft - dx;
    });

    pViewport.addEventListener("mouseleave", () => {
      if(!isDrag) return;
      isDrag = false;
      pPaused = false;
      pViewport.style.cursor = "grab";
    });

    partnerTick();
  </script>

  <script>
    const IT_MAJORS_ALL = [
      "ai",
      "computer science",
      "cyber",
      "cyber security",
      "cybersecurity",
      "information technology",
      "it",
      "software engineering",
      "data science",
      "cs",
      "se"
    ];

    function normMajor(s){ return String(s||"").trim().toLowerCase(); }
    function isITMajorText(s){ return IT_MAJORS_ALL.includes(normMajor(s)); }

    function esc(s){
      return String(s ?? "").replace(/[&<>"']/g, m => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
      }[m]));
    }

    function coverUrlFromPath(p){
      p = String(p || "").trim();
      if (!p) return "";
      if (p.startsWith("http://") || p.startsWith("https://")) return p;
      p = p.replace(/\\/g, "/");
      if (p.startsWith("/")) return p;
      return "/utbn-backend/" + p;
    }

    async function fetchJSON(url){
      const r = await fetch(url, { credentials: "include" });
      return await r.json();
    }

    function makePartnerStyleCard(p){
      const card = document.createElement("article");
      card.className = "partner-card playlist";

      const plid    = (p.playlist_id || p.id || 0);
      const course  = (p.course_name || p.course || p.template_subject || "");
      const name    = (p.playlist_name || p.name || p.title || course || "Course");
      const partner = (p.partner_name || p.partner_username || p.partner || "");

      const cover = coverUrlFromPath(
        p.cover_url || p.cover || p.thumbnail || p.cover_path || p.image || p.photo || ""
      );

      const title = course || name;
      let categoryLabel =
        p.category_name ||
        p.category ||
        p.course_category ||
        p.major_text ||
        p.major ||
        p.track ||
        p.specialization ||
        p.partner_name ||
        p.partner_username ||
        "Course";

      if (String(categoryLabel).trim().toLowerCase() === String(title).trim().toLowerCase()) {
        categoryLabel = partner || "Course";
      }

      const rating = p.rating || p.course_rating || "4.8";
      const reviews = p.reviews || p.review_count || p.rating_count || "1.2k";
      const difficulty = p.difficulty ?? 100;
      const students = p.students || p.students_count || p.enrolled_students || p.coin_pool || 2000;

      card.innerHTML = `
        <div class="course-cover">
          ${
            cover
              ? `<img src="${esc(cover)}" alt="${esc(name)}" class="course-cover-img">`
              : `<div class="course-cover-fallback"></div>`
          }
        </div>
        <div class="partner-content">
          <h3 class="partner-name">${esc(title)}</h3>
          <p class="partner-desc">${esc(categoryLabel)}</p>

          <div class="course-rating">
            <span class="star">★</span>
            <span>${esc(rating)} (${esc(reviews)})</span>
          </div>

          <div class="course-bottom">
            <span>${esc(difficulty)}%</span>
            <span class="divider"></span>
            <span>${esc(students)} Students</span>
          </div>
        </div>
      `;

      card.style.cursor = "pointer";

      card.addEventListener("click", function () {
        if (!plid || plid === 0) {
          console.log("playlist_id missing:", p);
          return;
        }

        window.location.href =
          "partner_playlist.php?playlist_id=" + encodeURIComponent(plid) +
          "&course=" + encodeURIComponent(course) +
          "&playlist=" + encodeURIComponent(name) +
          "&partner=" + encodeURIComponent(partner);
      });

      return card;
    }

    async function loadITPlaylistsHere(){
      const box = document.getElementById("itTrack");
      const empty = document.getElementById("itPartnerEmpty");
      if(!box) return;

      box.innerHTML = "";
      empty.style.display = "none";

      const mj = await fetchJSON("/utbn-backend/api/get_major.php");
      const myMajor = normMajor(mj.major_text || "");

      if(!isITMajorText(myMajor)) return;

      const map = new Map();
      for(const major of IT_MAJORS_ALL){
        try{
          const j = await fetchJSON("/utbn-backend/api/student_partner_major_playlists.php?major=" + encodeURIComponent(major));
          const items = (j && j.ok && Array.isArray(j.items)) ? j.items : [];
          for(const p of items){
            const id = String(p.playlist_id || p.id || "");
            if(id) map.set(id, p);
          }
        }catch(e){}
      }

      const arr = Array.from(map.values());
      if(arr.length === 0){
        empty.style.display = "block";
        return;
      }

      arr.slice(0, 60).forEach(p => box.appendChild(makePartnerStyleCard(p)));
      initCoursesAutoScroll();
    }

    document.addEventListener("DOMContentLoaded", () => {
      loadITPlaylistsHere().catch(console.error);
    });

    let coursesRAF = null;
    let coursesPaused = false;

    function initCoursesAutoScroll(){
      const viewport = document.getElementById("itViewport");
      const track = document.getElementById("itTrack");
      if(!viewport || !track) return;

      if(track.children.length < 2) return;

      if(!track.dataset.duplicated){
        track.innerHTML = track.innerHTML + track.innerHTML;
        track.dataset.duplicated = "1";
      }

      let speed = 0.55;

      function tick(){
        if(!coursesPaused){
          viewport.scrollLeft += speed;
          const half = track.scrollWidth / 2;
          if(viewport.scrollLeft >= half){
            viewport.scrollLeft = viewport.scrollLeft - half;
          }
        }
        coursesRAF = requestAnimationFrame(tick);
      }

      viewport.addEventListener("mouseover", (e) => {
        const card = e.target.closest(".partner-card");
        if(card){ coursesPaused = true; }
      });

      viewport.addEventListener("mouseout", (e) => {
        const card = e.target.closest(".partner-card");
        if(card){ coursesPaused = false; }
      });

      viewport.addEventListener("wheel", (e) => {
        if(Math.abs(e.deltaY) > Math.abs(e.deltaX)){
          e.preventDefault();
          viewport.scrollLeft += e.deltaY;
        }
      }, {passive:false});

      let isDrag = false;
      let startX = 0;
      let startScrollLeft = 0;
      viewport.style.cursor = "grab";

      viewport.addEventListener("mousedown", (e) => {
        isDrag = true;
        coursesPaused = true;
        viewport.style.cursor = "grabbing";
        startX = e.pageX;
        startScrollLeft = viewport.scrollLeft;
      });

      window.addEventListener("mouseup", () => {
        if(!isDrag) return;
        isDrag = false;
        coursesPaused = false;
        viewport.style.cursor = "grab";
      });

      viewport.addEventListener("mousemove", (e) => {
        if(!isDrag) return;
        e.preventDefault();
        const dx = e.pageX - startX;
        viewport.scrollLeft = startScrollLeft - dx;
      });

      viewport.addEventListener("mouseleave", () => {
        if(!isDrag) return;
        isDrag = false;
        coursesPaused = false;
        viewport.style.cursor = "grab";
      });

      if(coursesRAF) cancelAnimationFrame(coursesRAF);
      tick();
    }
  </script>

  <script>
    async function loadChatUnreadBadge(){
      try{
        const r = await fetch("../utbn-backend/api/chat/unread_count.php", {credentials:"include"});
        const j = await r.json();
        const badge = document.getElementById("chatBadge");
        if(!badge || !j.ok) return;

        const c = Number(j.count || 0);
        if(c > 0){
          badge.style.display = "inline-block";
          badge.textContent = c > 99 ? "99+" : String(c);
        }else{
          badge.style.display = "none";
        }
      }catch(e){}
    }
    loadChatUnreadBadge();
    setInterval(loadChatUnreadBadge, 15000);
  </script>

  <script>
    const avatarDropdown = document.getElementById("avatarDropdown");
    const avatarBtn = document.getElementById("avatarBtn");

    if (avatarDropdown && avatarBtn) {
      avatarBtn.addEventListener("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        avatarDropdown.classList.toggle("open");
      });

      document.addEventListener("click", function(e){
        if (!avatarDropdown.contains(e.target)) {
          avatarDropdown.classList.remove("open");
        }
      });
    }
  </script>

  <div id="qoyneFloatingAssistant" class="qoyne-floating-assistant" title="Ask QOYNE">
    <div class="qoyne-ring"></div>
    <div class="qoyne-shadow"></div>

    <div class="qoyne-robot">
      <img
        src="uploads/robot-floating.png"
        alt="QOYNE Robot"
        class="qoyne-robot-img"
        draggable="false"
      >
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const floatingAssistant = document.getElementById("qoyneFloatingAssistant");

      if (floatingAssistant) {
        let tapTimer = null;

        floatingAssistant.addEventListener("mouseenter", function () {
          floatingAssistant.classList.add("is-hover");
        });

        floatingAssistant.addEventListener("mouseleave", function () {
          floatingAssistant.classList.remove("is-hover");
        });

        floatingAssistant.addEventListener("click", function (e) {
          e.preventDefault();

          floatingAssistant.classList.remove("is-tap");
          void floatingAssistant.offsetWidth;
          floatingAssistant.classList.add("is-tap");
          clearTimeout(tapTimer);
          tapTimer = setTimeout(function(){
            floatingAssistant.classList.remove("is-tap");
          }, 460);

          if (typeof openQoyneChat === "function") {
            openQoyneChat();
            return;
          }

          const qoyneBtn = document.querySelector(".about-btn");
          if (qoyneBtn) {
            qoyneBtn.click();
          }
        });
      }
    });
  </script>

  <script src="assets/js/qoyne-chat.js"></script>

  <script>
    (function () {
      const langSwitch = document.getElementById("langSwitch");
      const enBtn = document.getElementById("langOptionEn");
      const arBtn = document.getElementById("langOptionAr");
      const currentLangText = document.getElementById("currentLangText");

      if (!langSwitch || !enBtn || !arBtn) return;

      function getLangSafe() {
        if (typeof getCurrentLang === "function") {
          return getCurrentLang() || "en";
        }
        return localStorage.getItem("lang") || "en";
      }

      function updateLangSwitchUI(lang) {
        const isAr = lang === "ar";

        langSwitch.classList.toggle("is-ar", isAr);

        enBtn.classList.toggle("active", !isAr);
        arBtn.classList.toggle("active", isAr);

        enBtn.setAttribute("aria-selected", !isAr ? "true" : "false");
        arBtn.setAttribute("aria-selected", isAr ? "true" : "false");

        if (currentLangText) {
          currentLangText.textContent = isAr ? "العربية" : "English";
        }
      }

      function setLangSafe(lang) {
        if (typeof setLanguage === "function") {
          setLanguage(lang);
          return;
        }

        if (typeof changeLanguage === "function") {
          changeLanguage(lang);
          return;
        }

        localStorage.setItem("lang", lang);
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
        document.dispatchEvent(new CustomEvent("languageChanged"));
      }

      enBtn.addEventListener("click", function () {
        setLangSafe("en");
        updateLangSwitchUI("en");
      });

      arBtn.addEventListener("click", function () {
        setLangSafe("ar");
        updateLangSwitchUI("ar");
      });

      document.addEventListener("languageChanged", function () {
        updateLangSwitchUI(getLangSafe());
      });

      updateLangSwitchUI(getLangSafe());
    })();
  </script>

  <script>
    (function () {
      function syncHeroTitleLang() {
        const lang =
          (typeof getCurrentLang === "function" ? getCurrentLang() : null) ||
          localStorage.getItem("lang") ||
          document.documentElement.lang ||
          "en";

        document.documentElement.lang = lang;
      }

      document.addEventListener("DOMContentLoaded", syncHeroTitleLang);
      window.addEventListener("load", syncHeroTitleLang);
      document.addEventListener("languageChanged", function () {
        setTimeout(syncHeroTitleLang, 0);
        setTimeout(syncHeroTitleLang, 80);
      });
    })();
  </script>

  <script>
    (function () {
      const miniWrap  = document.getElementById("navCoinMini");
      const miniValue = document.getElementById("navCoinMiniValue");
      const miniDelta = document.getElementById("navCoinMiniDelta");

      if (!miniWrap || !miniValue) return;

      let currentCoins = null;

      function formatCoins(num) {
        return Number(num || 0).toLocaleString("en-US");
      }

      function animateMiniValue(from, to, duration = 700) {
        from = Number(from || 0);
        to   = Number(to || 0);

        if (from === to) {
          miniValue.textContent = formatCoins(to);
          return;
        }

        const start = performance.now();

        function tick(now) {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = Math.round(from + (to - from) * eased);
          miniValue.textContent = formatCoins(value);

          if (progress < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
      }

      function bumpMini() {
        miniWrap.classList.remove("bump");
        void miniWrap.offsetWidth;
        miniWrap.classList.add("bump");
        setTimeout(() => miniWrap.classList.remove("bump"), 260);
      }

      function showDelta(diff) {
        if (!miniDelta || !diff) return;
        miniDelta.textContent = (diff > 0 ? "+" : "") + formatCoins(diff);
        miniDelta.classList.add("show");

        clearTimeout(miniDelta._timer);
        miniDelta._timer = setTimeout(() => {
          miniDelta.classList.remove("show");
        }, 2200);
      }

      async function loadMiniCoins() {
        try {
          const res = await fetch("/utbn-backend/api/coins.php", {
            credentials: "include",
            cache: "no-store"
          });

          const data = await res.json();
          if (!data || !data.ok) return;

          const newCoins = Number(data.coins_total || 0);

          if (currentCoins === null) {
            currentCoins = newCoins;
            animateMiniValue(0, newCoins, 850);
            return;
          }

          if (newCoins !== currentCoins) {
            const diff = newCoins - currentCoins;
            animateMiniValue(currentCoins, newCoins, 650);
            showDelta(diff);
            bumpMini();
            currentCoins = newCoins;
          } else {
            miniValue.textContent = formatCoins(newCoins);
          }
        } catch (e) {
          console.error("Mini coins load failed:", e);
        }
      }

      document.addEventListener("DOMContentLoaded", function () {
        loadMiniCoins();
        setInterval(loadMiniCoins, 10000);
      });
    })();
  </script>

  <script>
    function updatePhasesDropdown(total){
      const phase2MenuItem = document.getElementById("phase2MenuItem");
      const phase3MenuItem = document.getElementById("phase3MenuItem");
      const phasesEmptyItem = document.getElementById("phasesEmptyItem");

      let visibleCount = 0;

      if (phase2MenuItem) {
        phase2MenuItem.style.display = total >= 10000 ? "block" : "none";
        if (total >= 10000) visibleCount++;
      }

      if (phase3MenuItem) {
        phase3MenuItem.style.display = total >= 20000 ? "block" : "none";
        if (total >= 20000) visibleCount++;
      }

      const level2Items = document.querySelectorAll("#phasesMenu .phases-item:not(.empty)");
      let actualVisible = 0;
      level2Items.forEach(item => {
        const style = window.getComputedStyle(item);
        if (style.display !== "none") actualVisible++;
      });

      if (phasesEmptyItem) {
        phasesEmptyItem.style.display = actualVisible > 0 ? "none" : "block";
      }
    }

    (function () {
      const phasesDropdown = document.getElementById("phasesDropdown");
      const phasesTrigger = document.getElementById("phasesTrigger");

      if (!phasesDropdown || !phasesTrigger) return;

      phasesTrigger.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        phasesDropdown.classList.toggle("open");
      });

      document.addEventListener("click", function (e) {
        if (!phasesDropdown.contains(e.target)) {
          phasesDropdown.classList.remove("open");
        }
      });
    })();
  </script>

  <script>
    async function loadPhasesFromCoins(){
      try{
        const res = await fetch("/utbn-backend/api/coins.php", {
          credentials: "include",
          cache: "no-store"
        });
        const data = await res.json();
        if (!data || !data.ok) return;
        const total = parseInt(data.coins_total || "0", 10);
        updatePhasesDropdown(total);
      }catch(e){}
    }

    document.addEventListener("DOMContentLoaded", loadPhasesFromCoins);
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