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

$project_id = (int)($_GET["project_id"] ?? 0);
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>QOYN | Phase 3 - Level 2</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css"/>

  <style>
    :root{
      --navy:#0A2E5D;
      --navy-deep:#07234A;
      --navy-2:#123C75;
      --navy-3:#1B5FAE;
      --navy-4:#2D6FC2;

      --yellow:#FFC24A;
      --yellow-2:#FFCF69;
      --yellow-3:#FFB932;

      --green:#45C13D;
      --green-soft:#EAF7E8;

      --blue-soft:#EAF3FF;
      --blue-card:#0C2D63;
      --blue-card-2:#0A2660;

      --bg:#F6F7F9;
      --card:#FFFFFF;
      --text:#16213E;
      --muted:#536B8F;
      --line:rgba(10,46,93,.12);

      --shadow-sm:0 8px 22px rgba(10,46,93,.06);
      --shadow-md:0 16px 36px rgba(10,46,93,.10);
      --shadow-lg:0 24px 50px rgba(10,46,93,.14);

      --radius-xl:30px;
      --radius-lg:24px;
      --radius-md:20px;
      --radius-sm:16px;
    }

    *{box-sizing:border-box}

    html{
      scroll-behavior:smooth;
    }

    body{
      margin:0;
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at 95% 12%, rgba(255,194,74,.12), transparent 18%),
        radial-gradient(circle at 8% 88%, rgba(10,46,93,.06), transparent 18%),
        linear-gradient(180deg, #F8F9FB 0%, #F4F6FA 52%, #F1F4F8 100%);
      overflow-x:hidden;
      position:relative;
        padding-top:90px;

    }

    body::before{
      content:"";
      position:fixed;
      top:82px;
      right:-120px;
      width:370px;
      height:370px;
      border-radius:50%;
      background:
        radial-gradient(circle at center, rgba(255,194,74,.18), rgba(255,194,74,.05) 58%, transparent 59%);
      pointer-events:none;
      z-index:0;
      filter:blur(.2px);
    }

    body::after{
      content:"";
      position:fixed;
      bottom:-120px;
      left:-120px;
      width:240px;
      height:240px;
      border-radius:50%;
      background:rgba(10,46,93,.05);
      filter:blur(10px);
      pointer-events:none;
      z-index:0;
    }

    .page{
      position:relative;
      z-index:1;
      width:100%;
      max-width:100%;
      margin:0;
      padding:0 0 72px;
    }
/* ===== QOYN NAVBAR (NEW DESIGN) ===== */

.topbar{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  z-index:1000;
  background:#fff;
  border-bottom:1px solid rgba(10,46,93,.08);
  box-shadow:0 4px 18px rgba(10,46,93,.05);
  transition:all .3s ease;
}

.topbar.scrolled{
  background:#fff;
  border-bottom:1px solid rgba(10,46,93,.08);
  box-shadow:0 4px 18px rgba(10,46,93,.05);
}

.topbar-inner{
  width:100%;
  height:80px;
  padding:0 42px;
  display:flex;
  align-items:center;
  justify-content:space-between;
}

.topbar-logo{
  font-family:"Montserrat", sans-serif;
  font-weight:900;
  font-size:26px;
  color:var(--navy);
  text-decoration:none;
}

.topbar-nav{
  display:flex;
  align-items:center;
  gap:10px;
  margin-left:auto; /* 👈 هذا أهم سطر */
}

.topbar-link{
  padding:10px 16px;
  border-radius:999px;
  font-weight:600;
  text-decoration:none;
  color:#333;
  transition:.25s;
}

.topbar-link:hover{
  background:#FFC24A;
  color:#fff;
  transform:translateY(-2px);
}
.topbar-link.back-link{
  background:transparent !important;
}

.topbar-link.back-link:hover{
  background:transparent !important; /* ❌ بدون خلفية */
  color:#FFC24A; /* ✅ النص يصير أصفر */
  transform:none; /* ❌ بدون حركة */
  font-weight:700; /* ✨ يبرز */
}

.topbar-link.active{
  background:#FFC24A;
  color:#fff;
}

.logout-btn{
  padding:10px 18px;
  border-radius:999px;
  border:2px solid var(--navy);
  background:transparent;
  font-weight:600;
  text-decoration:none;
  color:var(--navy);
  transition:.25s;
}

.logout-btn:hover{
  background:var(--navy);
  color:#fff;
}
.nav-right-group{
  display:flex;
  align-items:center;
  gap:10px;
}

.nav-monkey{
  width:40px;
  height:40px;
  object-fit:contain;
}

    /* =========================
       HERO
    ========================= */
 .hero{
  position:relative;
  padding:8px 54px 18px;
}

   .hero-grid{
  display:grid;
  grid-template-columns:1fr;
  gap:24px;
  align-items:start;
}

    .hero-left{
      min-width:0;
    }

   .hero-topbar{
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:12px;
  margin-bottom:8px;
}

    .hero-badge{
      display:inline-flex;
      align-items:center;
      gap:12px;
      min-height:48px;
      padding:10px 18px 10px 14px;
      border-radius:999px;
      background:linear-gradient(180deg, #FFC94E 0%, #F2B734 100%);
      color:var(--navy);
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:14px;
      box-shadow:
        0 10px 24px rgba(255,194,74,.30),
        inset 0 1px 0 rgba(255,255,255,.55);
      border:1px solid rgba(223,157,18,.28);
      position:relative;
    }

    .hero-badge::before{
      content:"";
      width:12px;
      height:12px;
      border-radius:50%;
      background:#fff7e8;
      box-shadow:0 0 0 6px rgba(255,255,255,.28);
      flex-shrink:0;
    }

    .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:12px;
      min-height:50px;
      padding:11px 18px 11px 16px;
      border-radius:999px;
      background:linear-gradient(180deg, rgba(72,87,115,.92) 0%, rgba(61,74,99,.96) 100%);
      color:#fff;
      font-family:"Poppins", sans-serif;
      font-size:13px;
      font-weight:700;
      border:1px solid rgba(255,255,255,.28);
      box-shadow:0 16px 28px rgba(10,46,93,.14);
      position:relative;
    }

    .eyebrow::before{
      content:"✦";
      display:grid;
      place-items:center;
      width:20px;
      height:20px;
      border-radius:50%;
      background:linear-gradient(180deg, #FFD36C 0%, #F4B83B 100%);
      color:#fff;
      font-size:11px;
      box-shadow:0 6px 12px rgba(255,194,74,.28);
      flex-shrink:0;
    }

    .hero-copy{
      margin-top:0;
    }

    .hero-copy h1{
      margin:0 0 18px;
      font-family:"Montserrat", sans-serif;
      font-size:clamp(42px, 5.2vw, 66px);
      line-height:1.02;
      font-weight:900;
      letter-spacing:-1.8px;
      color:var(--navy);
    }

    .hero-copy h1::after{
      content:"";
      display:block;
      width:58px;
      height:4px;
      border-radius:999px;
      margin-top:18px;
      background:linear-gradient(90deg, #F2AE18 0%, #FFC857 100%);
    }

    .hero-copy p{
      margin:0;
      max-width:760px;
      font-size:15px;
      line-height:1.8;
      color:#44597D;
      font-weight:500;
    }

   .hero-mini-stats{
  display:grid;
  grid-template-columns:repeat(3, minmax(0,1fr));
  gap:22px;
  margin-top:14px;
}

    .mini-stat{
      position:relative;
      overflow:hidden;
      min-height:144px;
      border-radius:24px;
      background:rgba(255,255,255,.84);
      border:1px solid rgba(10,46,93,.10);
      box-shadow:var(--shadow-md);
      padding:20px 26px 20px 134px;
      transition:transform .22s ease, box-shadow .22s ease;
      backdrop-filter:blur(8px);
      -webkit-backdrop-filter:blur(8px);
    }

    .mini-stat:hover{
      transform:translateY(-4px);
      box-shadow:0 22px 42px rgba(10,46,93,.12);
    }

    .mini-stat::before{
      content:"";
      position:absolute;
      left:20px;
      top:50%;
      transform:translateY(-50%);
      width:86px;
      height:86px;
      border-radius:50%;
      z-index:1;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.65), 0 10px 20px rgba(10,46,93,.08);
    }

    .mini-stat::after{
      content:"";
      position:absolute;
      right:22px;
      top:18px;
      width:3px;
      height:108px;
      border-radius:999px;
      opacity:.95;
    }

    .mini-stat .icon{
      position:absolute;
      left:42px;
      top:50%;
      transform:translateY(-50%);
      width:44px;
      height:44px;
      z-index:2;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .mini-stat .icon svg{
      width:44px;
      height:44px;
      stroke-width:1.9;
      fill:none;
      stroke-linecap:round;
      stroke-linejoin:round;
    }

    .mini-stat strong,
    .mini-stat span{
      position:relative;
      z-index:2;
      display:block;
    }

    .mini-stat strong{
      font-family:"Montserrat", sans-serif;
      font-size:20px;
      line-height:1.2;
      color:var(--navy-deep);
      margin-bottom:8px;
      letter-spacing:-.4px;
    }

    .mini-stat span{
      color:#2D3D63;
      font-size:15px;
      line-height:1.55;
      font-weight:500;
      max-width:260px;
    }

    .mini-stat.challenge{
      background:linear-gradient(180deg, rgba(255,255,255,.92) 0%, rgba(255,250,240,.96) 100%);
      border-color:rgba(240,193,86,.26);
    }

    .mini-stat.challenge::before{
      background:linear-gradient(180deg, #FBE8B7 0%, #F7D67F 100%);
    }

    .mini-stat.challenge::after{
      background:linear-gradient(180deg, #F1A61A 0%, #FFCA58 100%);
    }

    .mini-stat.challenge .icon svg{
      stroke:#B87700;
    }

    .mini-stat.submit{
      background:linear-gradient(180deg, rgba(255,255,255,.94) 0%, rgba(241,247,255,.98) 100%);
      border-color:rgba(75,132,255,.20);
    }

    .mini-stat.submit::before{
      background:linear-gradient(180deg, #CFE2FF 0%, #B4D1FF 100%);
    }

    .mini-stat.submit::after{
      background:linear-gradient(180deg, #1B67FF 0%, #5D99FF 100%);
    }

    .mini-stat.submit .icon svg{
      stroke:#1560EC;
    }

    .mini-stat.result{
      background:linear-gradient(180deg, rgba(248,255,248,.95) 0%, rgba(238,248,238,.98) 100%);
      border-color:rgba(94,184,79,.20);
    }

    .mini-stat.result::before{
      background:linear-gradient(180deg, #D6F0CE 0%, #C2E9B8 100%);
    }

    .mini-stat.result::after{
      background:linear-gradient(180deg, #35AF2D 0%, #57CC4A 100%);
    }

    .mini-stat.result .icon svg{
      stroke:#30A429;
    }

    /* =========================
       WHAT HAPPENS HERE
    ========================= */
    .hero-right{
  margin-top:10px;
  width:100%;
  position:relative;
  overflow:hidden;
  border-radius:30px;
  background:
    radial-gradient(circle at 76% 20%, rgba(67,145,255,.18), transparent 20%),
    radial-gradient(circle at 84% 72%, rgba(45,155,255,.15), transparent 22%),
    linear-gradient(135deg, #032C79 0%, #072766 38%, #06235C 100%);
  border:1px solid rgba(72,191,255,.70);
  box-shadow:
    0 22px 44px rgba(10,46,93,.20),
    inset 0 0 0 1px rgba(255,255,255,.04);
  min-height:unset;
  padding:30px 30px 28px;
}

    .hero-right::before{
      content:"";
      position:absolute;
      inset:0;
      background:
        radial-gradient(circle at 74% 40%, rgba(121,196,255,.14), transparent 18%),
        linear-gradient(180deg, rgba(255,255,255,.05), transparent 34%);
      pointer-events:none;
    }

    .hero-right::after{
      content:"";
      position:absolute;
      right:34px;
      top:32px;
      width:92px;
      height:92px;
      border-radius:26px;
      border:1px solid rgba(110,171,255,.28);
      background:rgba(104,158,255,.07);
      transform:rotate(18deg);
      pointer-events:none;
    }

    .hero-right-inner{
      position:relative;
      z-index:2;
      max-width:820px;
    }

    .hero-right-top{
      display:flex;
      align-items:flex-start;
      gap:18px;
      margin-bottom:14px;
    }

    .hero-right-icon{
      width:82px;
      height:82px;
      border-radius:20px;
      display:grid;
      place-items:center;
      background:
        linear-gradient(180deg, rgba(18,79,182,.40) 0%, rgba(5,42,116,.38) 100%);
      border:1px solid rgba(104,194,255,.80);
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.14),
        0 12px 20px rgba(0,0,0,.16);
      flex-shrink:0;
    }

    .hero-right-icon svg{
      width:40px;
      height:40px;
      stroke:#FFFFFF;
      filter:drop-shadow(0 0 8px rgba(163,220,255,.28));
    }

    .hero-right h3{
      margin:4px 0 8px;
      font-family:"Montserrat", sans-serif;
      font-size:28px;
      line-height:1.12;
      font-weight:800;
      letter-spacing:-.7px;
      color:#fff;
    }

    .hero-right p{
      margin:0 0 22px;
      font-size:15px;
      line-height:1.7;
      color:rgba(239,246,255,.92);
      max-width:880px;
    }

    .hero-points{
      display:grid;
      gap:14px;
      max-width:760px;
    }

    .hero-point{
      position:relative;
      display:grid;
      grid-template-columns:88px 1fr 34px;
      align-items:center;
      gap:18px;
      min-height:86px;
      padding:10px 18px 10px 12px;
      border-radius:18px;
      background:linear-gradient(180deg, rgba(10,57,137,.58) 0%, rgba(7,52,126,.64) 100%);
      border:1px solid rgba(101,180,255,.26);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
      transition:transform .22s ease, background .22s ease, border-color .22s ease;
    }

    .hero-point:hover{
      transform:translateY(-2px);
      background:linear-gradient(180deg, rgba(12,62,148,.68) 0%, rgba(9,57,138,.74) 100%);
      border-color:rgba(126,201,255,.40);
    }

    .hero-point-icon{
      width:66px;
      height:66px;
      border-radius:16px;
      display:grid;
      place-items:center;
      background:linear-gradient(180deg, rgba(30,82,176,.92) 0%, rgba(16,60,144,.96) 100%);
      border:1px solid rgba(121,201,255,.22);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.08);
      flex-shrink:0;
      justify-self:start;
    }

    .hero-point-icon svg{
      width:34px;
      height:34px;
      stroke:#91D7FF;
      filter:drop-shadow(0 0 8px rgba(145,215,255,.18));
    }

    .hero-point-text strong{
      display:block;
      color:#fff;
      font-size:18px;
      line-height:1.25;
      font-weight:700;
      margin-bottom:4px;
    }

    .hero-point-text span{
      display:block;
      color:rgba(228,241,255,.94);
      font-size:15px;
      line-height:1.35;
      font-weight:400;
    }

    .hero-point-arrow{
      justify-self:end;
      width:14px;
      height:14px;
      border-top:3px solid #83D9FF;
      border-right:3px solid #83D9FF;
      transform:rotate(45deg);
      opacity:.95;
      margin-right:8px;
    }

    .hero-monitor{
      position:absolute;
      right:42px;
      bottom:26px;
      width:275px;
      height:250px;
      pointer-events:none;
      opacity:.95;
    }

    .hero-monitor svg{
      width:100%;
      height:100%;
      display:block;
      filter:drop-shadow(0 18px 24px rgba(18,122,255,.18));
    }

    /* =========================
       MAIN CONTENT
    ========================= */
    .layout{
      padding:18px 54px 0;
    }

    .card{
      position:relative;
      overflow:hidden;
      background:linear-gradient(180deg, rgba(255,255,255,.95) 0%, rgba(248,251,255,.92) 100%);
      border:1px solid rgba(255,255,255,.72);
      border-radius:30px;
      box-shadow:0 20px 46px rgba(10,46,93,.08);
      padding:20px;
      margin-bottom:24px;
      backdrop-filter:blur(10px);
      -webkit-backdrop-filter:blur(10px);
    }

    .card::before{
      content:"";
      position:absolute;
      top:-52px;
      left:-34px;
      width:140px;
      height:140px;
      border-radius:50%;
      background:rgba(255,194,74,.07);
      pointer-events:none;
    }

    .card::after{
      content:"";
      position:absolute;
      right:-36px;
      bottom:-40px;
      width:130px;
      height:130px;
      border-radius:50%;
      background:rgba(10,46,93,.04);
      pointer-events:none;
    }

    .card h2{
      position:relative;
      z-index:2;
      margin:0 0 18px;
      font-family:"Montserrat", sans-serif;
      font-size:30px;
      font-weight:800;
      letter-spacing:-.6px;
      color:var(--navy);
    }

    .card h3{
      position:relative;
      z-index:2;
      margin:24px 0 12px;
      font-family:"Montserrat", sans-serif;
      font-size:17px;
      font-weight:800;
      color:var(--navy);
    }

    .scenario-wrap{
  display:grid;
  grid-template-columns:minmax(0,1fr) 250px;
  gap:16px;
  align-items:start; /* 👈 مهم جداً */
}
.scenario-side-art{
  position:relative;
  top:-10px;
}

    .scenario-box{
      background:linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
      border:1px solid rgba(10,46,93,.10);
      border-radius:22px;
      padding:20px 22px;
      min-height:190px;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.35), 0 12px 24px rgba(10,46,93,.04);
      color:#0f172a;
      line-height:2;
      white-space:pre-wrap;
    }

    .scenario-side-art{
      border-radius:22px;
      background:linear-gradient(180deg, #ffffff 0%, #eef4ff 100%);
      border:1px solid rgba(10,46,93,.08);
      box-shadow:0 12px 24px rgba(10,46,93,.06);
      padding:16px;
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:190px;
    }

    .meta-grid{
      position:relative;
      z-index:2;
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:14px;
      margin-top:18px;
    }

    .meta-item{
      position:relative;
      overflow:hidden;
      background:#fff;
      border:1px solid rgba(10,46,93,.08);
      border-radius:20px;
      padding:16px;
      box-shadow:0 8px 18px rgba(10,46,93,.05);
      transition:transform .22s ease, box-shadow .22s ease;
    }

    .meta-item::after{
      content:"";
      position:absolute;
      right:-14px;
      top:-14px;
      width:60px;
      height:60px;
      border-radius:18px;
      background:rgba(255,194,74,.11);
      transform:rotate(20deg);
    }

    .meta-item:hover{
      transform:translateY(-2px);
      box-shadow:0 14px 24px rgba(10,46,93,.08);
    }

    .meta-item .k{
      position:relative;
      z-index:2;
      display:block;
      color:#6B7A96;
      font-size:11px;
      margin-bottom:7px;
      text-transform:uppercase;
      letter-spacing:.6px;
      font-weight:700;
    }

    .meta-item .v{
      position:relative;
      z-index:2;
      color:var(--navy);
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:16px;
      word-break:break-word;
    }

    .actions-deliverables-grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:18px;
      margin-top:10px;
      align-items:stretch;
    }

    .actions-deliverables-grid > div{
      display:flex;
      flex-direction:column;
    }

    .actions-deliverables-grid .section-list{
      flex:1;
    }

    .section-list{
      position:relative;
      z-index:2;
      background:#fff;
      border:1px solid rgba(10,46,93,.08);
      border-radius:20px;
      padding:16px 18px;
      box-shadow:0 10px 22px rgba(10,46,93,.04);
    }

    ul{
      margin:0;
      padding-left:20px;
      list-style:disc;
    }

    li{
      color:#0f172a !important;
      font-size:14px;
      line-height:1.9;
      margin-bottom:8px;
      display:list-item !important;
      visibility:visible !important;
    }

    li:last-child{
      margin-bottom:0;
    }

    .empty-note{
      color:#8A8F98 !important;
      font-style:italic;
    }

    .section-divider{
      display:flex;
      align-items:center;
      gap:12px;
      margin:10px 0 18px;
    }

    .section-divider::before,
    .section-divider::after{
      content:"";
      flex:1;
      height:2px;
      border-radius:999px;
      background:linear-gradient(90deg, transparent, rgba(10,46,93,.18));
    }

    .section-divider::after{
      background:linear-gradient(90deg, rgba(10,46,93,.18), transparent);
    }

    .section-divider span{
      padding:7px 15px;
      border-radius:999px;
      background:rgba(10,46,93,.05);
      color:var(--navy);
      font-weight:700;
      font-size:12px;
    }

    label{
      display:block;
      margin:0 0 8px;
      color:var(--navy);
      font-weight:800;
      font-size:14px;
      position:relative;
      z-index:2;
    }

    textarea,
    input[type="url"],
    input[type="text"],
    input[type="file"]{
      width:100%;
      border:1px solid rgba(10,46,93,.12);
      border-radius:18px;
      padding:15px 16px;
      font:inherit;
      background:#fff;
      color:#111;
      outline:none;
      margin-bottom:14px;
      transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease, background .2s ease;
      position:relative;
      z-index:2;
      box-shadow:0 4px 10px rgba(10,46,93,.02);
    }

    textarea::placeholder,
    input[type="url"]::placeholder,
    input[type="text"]::placeholder{
      color:#94a3b8;
    }

    textarea:hover,
    input[type="url"]:hover,
    input[type="text"]:hover,
    input[type="file"]:hover{
      border-color:rgba(10,46,93,.18);
    }

    textarea:focus,
    input[type="url"]:focus,
    input[type="text"]:focus,
    input[type="file"]:focus{
      border-color:rgba(10,46,93,.30);
      box-shadow:0 0 0 5px rgba(10,46,93,.08);
      transform:translateY(-1px);
      background:#fff;
    }

    textarea{
      min-height:220px;
      resize:vertical;
      line-height:1.9;
    }

    input[type="file"]{
      padding:12px 14px;
      background:linear-gradient(180deg, #fff 0%, #fbfdff 100%);
      cursor:pointer;
    }

    .helper{
      position:relative;
      z-index:2;
      color:#677994;
      font-size:13px;
      margin:-3px 0 16px;
      line-height:1.8;
    }

    .submit-top-visual{
      position:relative;
      z-index:2;
      margin-bottom:18px;
      border-radius:22px;
      background:linear-gradient(135deg, #0A2E5D 0%, #1B5FAE 100%);
      padding:20px;
      overflow:hidden;
      color:#fff;
      box-shadow:0 18px 34px rgba(10,46,93,.18);
    }

    .submit-top-visual::before{
      content:"";
      position:absolute;
      width:126px;
      height:126px;
      border-radius:50%;
      background:rgba(255,255,255,.10);
      right:-20px;
      top:-20px;
    }

    .submit-top-visual h4{
      position:relative;
      z-index:2;
      margin:0 0 6px;
      font-family:"Montserrat", sans-serif;
      font-size:18px;
    }

    .submit-top-visual p{
      position:relative;
      z-index:2;
      margin:0;
      color:rgba(255,255,255,.84);
      font-size:13px;
      line-height:1.8;
    }

    .submit-btn{
      width:100%;
      border:none;
      border-radius:18px;
      padding:16px 18px;
      background:linear-gradient(135deg, #0A2E5D 0%, #1B5FAE 100%);
      color:#fff;
      font-family:"Montserrat", sans-serif;
      font-size:14px;
      font-weight:800;
      letter-spacing:.3px;
      cursor:pointer;
      transition:transform .18s ease, box-shadow .18s ease, filter .18s ease, opacity .18s ease;
      box-shadow:0 18px 35px rgba(10,46,93,.22);
      position:relative;
      z-index:2;
    }

    .submit-btn:hover{
      transform:translateY(-3px);
      box-shadow:0 22px 42px rgba(10,46,93,.24);
      filter:brightness(1.03);
    }

    .submit-btn:active{
      transform:translateY(-1px);
    }

    .submit-btn:disabled{
      opacity:.72;
      cursor:not-allowed;
      transform:none;
      box-shadow:none;
      filter:none;
    }

    .result-card{
      position:relative;
      z-index:2;
      background:#fff;
      border:1px solid rgba(10,46,93,.08);
      border-radius:22px;
      padding:18px;
      box-shadow:0 12px 24px rgba(10,46,93,.04);
    }

    .score-banner{
      display:flex;
      align-items:center;
      gap:16px;
      padding:16px;
      border-radius:20px;
      background:linear-gradient(135deg, rgba(10,46,93,.05), rgba(255,194,74,.14));
      border:1px solid rgba(10,46,93,.08);
      margin-bottom:14px;
    }

    .score-orb{
      width:74px;
      height:74px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:linear-gradient(135deg, #0A2E5D 0%, #1B5FAE 100%);
      color:#fff;
      font-family:"Montserrat", sans-serif;
      font-size:20px;
      font-weight:900;
      box-shadow:0 14px 28px rgba(10,46,93,.16);
      flex-shrink:0;
    }

    .score-banner h4{
      margin:0 0 4px;
      font-family:"Montserrat", sans-serif;
      color:var(--navy);
      font-size:18px;
    }

    .score-banner p{
      margin:0;
      color:#677994;
      font-size:13px;
      line-height:1.8;
    }

    .result-row{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      padding:14px 0;
      border-bottom:1px solid rgba(10,46,93,.08);
    }

    .result-row:last-child{
      border-bottom:none;
    }

    .result-label{
      color:#6B7A96;
      font-weight:700;
      font-size:14px;
    }

    .result-value{
      color:var(--navy);
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:17px;
    }

    .feedback-box{
      margin-top:14px;
      background:linear-gradient(180deg, #f9fbff 0%, #ffffff 100%);
      border:1px solid rgba(10,46,93,.08);
      border-radius:18px;
      padding:16px;
      line-height:1.95;
      color:#0f172a;
      white-space:pre-wrap;
      font-size:14px;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.7);
    }

    .hidden{
      display:none !important;
    }

    .loading{
      color:#677994;
    }

    /* =========================
       RESPONSIVE
    ========================= */
    @media (max-width: 1280px){
      .hero-grid{
        grid-template-columns:1fr;
      }

      .hero-right{
        min-height:auto;
      }

      .hero-monitor{
        opacity:.35;
      }
    }

    @media (max-width: 1150px){
      .hero,
      .layout{
        padding-left:22px;
        padding-right:22px;
      }

      .hero-mini-stats,
      .meta-grid,
      .scenario-wrap,
      .actions-deliverables-grid{
        grid-template-columns:1fr;
      }

      .mini-stat{
        padding-left:120px;
      }

      .hero-monitor{
        display:none;
      }
    }

    @media (max-width: 900px){
      .topbar-inner{
        flex-direction:column;
        align-items:flex-start;
        padding:16px 18px;
      }

      .topbar-left,
      .topbar-right{
        width:100%;
        justify-content:space-between;
      }

      .topbar-right{
        flex-wrap:wrap;
      }

      .topbar-nav{
        gap:8px;
        flex-wrap:wrap;
      }

      .topbar-link{
        padding:12px 16px;
        font-size:14px;
      }

      .hero-topbar{
        align-items:flex-start;
      }

      .hero-copy h1{
        font-size:46px;
      }

      .hero-right-top{
        flex-direction:column;
        align-items:flex-start;
      }

      .hero-point{
        grid-template-columns:72px 1fr 24px;
      }

      .hero-point-icon{
        width:56px;
        height:56px;
      }

      .hero-point-icon svg{
        width:28px;
        height:28px;
      }
    }

    @media (max-width: 700px){
      .hero{
        padding-top:22px;
      }

      .hero,
      .layout{
        padding-left:14px;
        padding-right:14px;
      }

      .hero-copy h1{
        font-size:36px;
      }

      .hero-copy p{
        font-size:14px;
      }

      .hero-mini-stats{
        gap:14px;
      }

      .mini-stat{
        min-height:auto;
        padding:18px 18px 18px 106px;
      }

      .mini-stat::before{
        width:72px;
        height:72px;
        left:18px;
      }

      .mini-stat .icon{
        left:35px;
        width:36px;
        height:36px;
      }

      .mini-stat .icon svg{
        width:36px;
        height:36px;
      }

      .hero-right{
        padding:20px 18px 20px;
        border-radius:24px;
      }

      .hero-right-icon{
        width:68px;
        height:68px;
      }

      .hero-right h3{
        font-size:24px;
      }

      .hero-point{
        grid-template-columns:1fr;
        gap:12px;
        padding:14px;
      }

      .hero-point-arrow{
        display:none;
      }

      .hero-point-text strong{
        font-size:17px;
      }

      .card{
        padding:20px;
        border-radius:24px;
      }

      .card h2{
        font-size:25px;
      }

      .score-banner,
      .result-row{
        flex-direction:column;
        align-items:flex-start;
      }

      textarea{
        min-height:180px;
      }
    }
  

/* =========================
   CHALLENGE FLOW (from current design)
   Missing Dependencies + Submit Your Solution
========================= */
.challenge-flow{
  position:relative;
  z-index:1;
}
.challenge-flow .cf-hero{
  position:relative;
  padding:34px 54px 8px;
}
.challenge-flow .cf-hero::before{
  content:"";
  position:absolute;
  right:88px;
  bottom:40px;
  width:350px;
  height:350px;
  border-radius:50%;
  background:rgba(31,88,200,.04);
  z-index:0;
  pointer-events:none;
}
.challenge-flow .cf-hero::after{
  content:"";
  position:absolute;
  right:130px;
  bottom:-10px;
  width:220px;
  height:120px;
  background-image:radial-gradient(rgba(92,142,255,.18) 1.4px, transparent 1.4px);
  background-size:14px 14px;
  opacity:.55;
  z-index:0;
  pointer-events:none;
}
.challenge-flow .cf-hero-grid{
  position:relative;
  z-index:1;
  display:grid;
  grid-template-columns:minmax(0,1fr) 360px;
  gap:34px;
  align-items:start;
}
.challenge-flow .cf-hero-left{min-width:0;position:relative;z-index:2;}
.challenge-flow .cf-hero-topbar{display:flex;flex-direction:column;align-items:flex-end;gap:14px;margin-bottom:10px;}
.challenge-flow .cf-hero-badge{display:inline-flex;align-items:center;gap:12px;min-height:48px;padding:10px 18px 10px 14px;border-radius:999px;background:linear-gradient(180deg, #FFC94E 0%, #F2B734 100%);color:var(--navy);font-family:"Montserrat", sans-serif;font-weight:800;font-size:14px;box-shadow:0 10px 24px rgba(255,194,74,.24), inset 0 1px 0 rgba(255,255,255,.55);border:1px solid rgba(223,157,18,.24);position:relative;}
.challenge-flow .cf-hero-badge::before{content:"";width:12px;height:12px;border-radius:50%;background:#fff9ef;box-shadow:0 0 0 6px rgba(255,255,255,.22);flex-shrink:0;}
.challenge-flow .cf-eyebrow{display:inline-flex;align-items:center;gap:12px;min-height:50px;padding:11px 18px 11px 16px;border-radius:999px;background:linear-gradient(180deg, #143B7A 0%, #0E2F66 100%);color:#fff;font-size:13px;font-weight:700;border:1px solid rgba(255,255,255,.16);box-shadow:0 16px 28px rgba(10,46,93,.14);position:relative;}
.challenge-flow .cf-eyebrow::before{content:"✦";display:grid;place-items:center;width:20px;height:20px;border-radius:50%;background:linear-gradient(180deg, #FFD36C 0%, #F4B83B 100%);color:#fff;font-size:11px;box-shadow:0 6px 12px rgba(255,194,74,.24);flex-shrink:0;}
.challenge-flow .cf-hero-copy h1{margin:0 0 14px;font-family:"Montserrat", sans-serif;font-size:clamp(34px,4.8vw,60px);line-height:1.03;font-weight:900;letter-spacing:-1.7px;color:var(--navy);max-width:920px;}
.challenge-flow .cf-hero-copy h1::after{content:"";display:block;width:48px;height:4px;border-radius:999px;margin-top:18px;background:linear-gradient(90deg, #F2AE18 0%, #FFC857 100%);}
.challenge-flow .cf-hero-copy p{margin:0;max-width:900px;font-size:16px;line-height:1.9;color:#4D6387;font-weight:500;}
.challenge-flow .cf-hero-meta-row{display:grid;grid-template-columns:minmax(0,1fr) 1px minmax(0,1fr);gap:26px;align-items:center;margin:30px 0 18px;max-width:760px;}
.challenge-flow .cf-hero-meta-divider{width:1px;height:58px;background:linear-gradient(180deg, transparent, rgba(10,46,93,.16), transparent);justify-self:center;}
.challenge-flow .cf-hero-meta{display:flex;align-items:center;gap:18px;min-width:0;}
.challenge-flow .cf-hero-meta-icon{width:82px;height:82px;border-radius:50%;display:grid;place-items:center;flex-shrink:0;border:1px solid rgba(10,46,93,.08);box-shadow:0 8px 22px rgba(10,46,93,.05), inset 0 1px 0 rgba(255,255,255,.65);}
.challenge-flow .cf-hero-meta-icon svg{width:38px;height:38px;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.challenge-flow .cf-hero-meta.challenge .cf-hero-meta-icon{background:linear-gradient(180deg, #FFF1C8 0%, #FCE19A 100%);}
.challenge-flow .cf-hero-meta.challenge .cf-hero-meta-icon svg{stroke:#E8A10A;}
.challenge-flow .cf-hero-meta.difficulty .cf-hero-meta-icon{background:linear-gradient(180deg, #E5F0FF 0%, #D4E5FF 100%);}
.challenge-flow .cf-hero-meta.difficulty .cf-hero-meta-icon svg{stroke:#2A78FF;}
.challenge-flow .cf-hero-meta-copy .k{display:block;color:#7B8DAA;font-size:11px;text-transform:uppercase;letter-spacing:.8px;font-weight:800;margin-bottom:4px;}
.challenge-flow .cf-hero-meta-copy .v{display:block;color:var(--navy);font-family:"Montserrat", sans-serif;font-size:18px;line-height:1.2;font-weight:800;word-break:break-word;}
.challenge-flow .cf-hero-side{position:relative;min-height:440px;z-index:2;}
.challenge-flow .cf-hero-side::before{content:"";position:absolute;right:-44px;top:30px;width:210px;height:210px;border-radius:50%;background:rgba(255,194,74,.08);z-index:0;pointer-events:none;}
.challenge-flow .cf-hero-side::after{content:"";position:absolute;right:-10px;top:150px;width:120px;height:200px;background-image:radial-gradient(rgba(241,174,24,.30) 1.4px, transparent 1.4px);background-size:12px 12px;opacity:.5;z-index:0;pointer-events:none;}
.challenge-flow .cf-hero-side-card{position:absolute;top:118px;right:10px;width:290px;height:300px;border-radius:34px;background:linear-gradient(180deg, rgba(255,255,255,.96) 0%, rgba(245,248,253,.92) 100%);border:1px solid rgba(10,46,93,.06);box-shadow:0 18px 40px rgba(10,46,93,.08);display:flex;align-items:center;justify-content:center;z-index:2;}
.challenge-flow .cf-hero-side-visual{width:190px;height:190px;border-radius:28px;background:linear-gradient(180deg, #F8FBFF 0%, #EEF3FB 100%);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
.challenge-flow .cf-hero-side-visual::before{content:"";position:absolute;left:18px;top:26px;width:76px;height:76px;border-radius:22px;background:linear-gradient(135deg, #1E5AB6 0%, #2D76E2 100%);box-shadow:0 10px 20px rgba(45,118,226,.18);}
.challenge-flow .cf-hero-side-visual::after{content:"";position:absolute;right:18px;top:28px;width:20px;height:20px;border-radius:50%;background:#F8B62D;box-shadow:0 6px 12px rgba(248,182,45,.18);}
.challenge-flow .cf-hero-side-lines{position:absolute;inset:0;pointer-events:none;}
.challenge-flow .cf-hero-side-lines span{position:absolute;left:22px;right:22px;height:14px;border-radius:999px;background:#DDE6F2;}
.challenge-flow .cf-hero-side-lines span:nth-child(1){top:32px;left:98px;right:36px;height:16px;}
.challenge-flow .cf-hero-side-lines span:nth-child(2){top:60px;left:98px;right:66px;height:14px;}
.challenge-flow .cf-hero-side-lines span:nth-child(3){top:120px;}
.challenge-flow .cf-hero-side-lines span:nth-child(4){top:158px;right:8px;}
.challenge-flow .cf-hero-side-lines span:nth-child(5){top:196px;left:22px;right:58px;background:linear-gradient(90deg, #F2B328 0%, #F6C64F 100%);}
.challenge-flow .cf-section-head{display:flex;align-items:center;gap:16px;margin:20px 0 16px;}
.challenge-flow .cf-section-head-icon{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;background:rgba(255,255,255,.66);box-shadow:0 8px 20px rgba(10,46,93,.04);border:1px solid rgba(10,46,93,.08);flex-shrink:0;}
.challenge-flow .cf-section-head-icon svg{width:28px;height:28px;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.challenge-flow .cf-section-head.actions .cf-section-head-icon svg{stroke:#F0AA12;}
.challenge-flow .cf-section-head.deliverables .cf-section-head-icon svg{stroke:#377BFF;}
.challenge-flow .cf-section-head h3{margin:0;font-family:"Montserrat", sans-serif;font-size:20px;font-weight:800;color:var(--navy);letter-spacing:-.4px;}
.challenge-flow .cf-section-head h3::after{content:"";display:block;width:32px;height:4px;border-radius:999px;margin-top:10px;}
.challenge-flow .cf-section-head.actions h3::after{background:linear-gradient(90deg, #F1B32D 0%, #FFD45E 100%);}
.challenge-flow .cf-section-head.deliverables h3::after{background:linear-gradient(90deg, #2B72FF 0%, #66A0FF 100%);}
.challenge-flow .cf-actions-deliverables-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-top:8px;align-items:start;max-width:980px;}
.challenge-flow .cf-list-panel{background:transparent;border:none;box-shadow:none;padding:0;margin:0;}
.challenge-flow .cf-section-list{background:transparent;border:none;box-shadow:none;border-radius:0;padding:0;}
.challenge-flow .cf-section-list ul{margin:0;padding:0;list-style:none;}
.challenge-flow .cf-section-list li{position:relative;margin:0 0 14px;padding:0 0 0 48px;color:#44597D !important;font-size:15px;line-height:1.6;font-weight:500;display:block !important;visibility:visible !important;list-style:none;}
.challenge-flow .cf-section-list li::before{content:"";position:absolute;left:0;top:2px;width:28px;height:28px;border-radius:50%;background:#fff;border:1.8px solid rgba(53,114,255,.44);box-shadow:0 6px 12px rgba(10,46,93,.03);}
.challenge-flow .cf-section-list li::after{content:"✓";position:absolute;left:7px;top:1px;color:#3B7FFF;font-size:18px;font-weight:800;line-height:1;}
.challenge-flow .cf-actions-deliverables-grid > div:first-child .cf-section-list li::before{border-color:rgba(241,174,24,.44);}
.challenge-flow .cf-actions-deliverables-grid > div:first-child .cf-section-list li::after{color:#F0AA12;}
.challenge-flow .cf-section-list li.empty-note{padding-left:0;color:#8A8F98 !important;font-style:italic;}
.challenge-flow .cf-section-list li.empty-note::before,.challenge-flow .cf-section-list li.empty-note::after{display:none;}
.challenge-flow .cf-layout{padding:6px 54px 0;position:relative;z-index:1;}
.challenge-flow .cf-card{position:relative;overflow:visible;background:transparent;border:none;border-radius:0;box-shadow:none;padding:0;margin-bottom:34px;}
/* =========================
   SUBMIT YOUR SOLUTION - NEW REDESIGN
========================= */
.challenge-flow #submitBox{
  margin-top:12px;
  padding-top:6px;
  border-top:none;
  position:relative;
}

.challenge-flow #submitBox::before{
  content:"";
  position:absolute;
  left:-48px;
  top:140px;
  width:220px;
  height:220px;
  border-radius:50%;
  background:rgba(10,46,93,.05);
  pointer-events:none;
  z-index:0;
}

.challenge-flow #submitBox::after{
  content:"";
  position:absolute;
  right:-64px;
  top:34px;
  width:180px;
  height:180px;
  border-radius:50%;
  background:rgba(255,194,74,.08);
  pointer-events:none;
  z-index:0;
}

.challenge-flow .cf-section-divider{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;
  margin:0 0 20px;
  position:relative;
  z-index:2;
}

.challenge-flow .cf-section-divider::before,
.challenge-flow .cf-section-divider::after{
  content:"";
  flex:1;
  height:2px;
  border-radius:999px;
  background:linear-gradient(90deg, transparent, rgba(10,46,93,.18));
}

.challenge-flow .cf-section-divider::after{
  background:linear-gradient(90deg, rgba(10,46,93,.18), transparent);
}

.challenge-flow .cf-section-divider span{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 18px;
  border-radius:999px;
  background:#E9EEF6;
  color:var(--navy);
  font-weight:800;
  font-size:13px;
  white-space:nowrap;
  box-shadow:0 4px 10px rgba(10,46,93,.04);
}

.challenge-flow .cf-section-divider span::before{
  content:"";
  width:10px;
  height:10px;
  border-radius:50%;
  background:linear-gradient(180deg, #FFD36C 0%, #F3B93A 100%);
  box-shadow:0 0 0 5px rgba(255,194,74,.16);
}

.challenge-flow #submitBox h2{
  margin:0 0 12px;
  font-family:"Montserrat", sans-serif;
  font-size:clamp(34px,4.3vw,52px);
  font-weight:900;
  letter-spacing:-1px;
  color:var(--navy);
  position:relative;
  z-index:2;
}
.challenge-flow #submitBox h2::after{
  content:"";
  display:block;
  width:48px;
  height:4px;
  border-radius:999px;
  margin-top:14px;
  background:linear-gradient(90deg, #F2AE18 0%, #FFC857 100%);
}



.challenge-flow .cf-submit-top-visual::before{
  content:"";
  position:absolute;
  right:26px;
  top:50%;
  transform:translateY(-50%);
  width:168px;
  height:168px;
  border-radius:50%;
  background:rgba(43,114,255,.07);
  pointer-events:none;
}

.challenge-flow .cf-submit-top-visual::after{
  content:"";
  position:absolute;
  left:-70px;
  top:-70px;
  width:140px;
  height:140px;
  border-radius:50%;
  background:rgba(255,194,74,.07);
  pointer-events:none;
}

.challenge-flow .cf-submit-hero-icon{
  width:94px;
  height:94px;
  border-radius:50%;
  display:grid;
  place-items:center;
  background:linear-gradient(180deg, #F2F6FD 0%, #E8F0FD 100%);
  border:1px solid rgba(170,193,230,.7);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.75);
  position:relative;
  z-index:2;
}

.challenge-flow .cf-submit-hero-icon svg{
  width:56px;
  height:56px;
  display:block;
}

.challenge-flow .cf-submit-hero-copy{
  position:relative;
  z-index:2;
}

.challenge-flow .cf-submit-hero-copy h4{
  margin:0 0 8px;
  font-family:"Montserrat", sans-serif;
  font-size:20px;
  font-weight:800;
  color:var(--navy);
}

.challenge-flow .cf-submit-hero-copy p{
  margin:0;
  color:#4F6487;
  font-size:14px;
  line-height:1.8;
  max-width:560px;
}

.challenge-flow .cf-submit-hero-art{
  position:relative;
  top:0;
  z-index:2;
  display:flex;
  justify-content:flex-end;
  align-items:flex-start;
}

.challenge-flow .cf-submit-hero-art svg{
  width:100%;
  max-width:300px;
  height:auto;
  display:block;
}

.challenge-flow .cf-submit-hero-art svg{
  width:100%;
  max-width:300px;
  height:auto;
  display:block;
}

.challenge-flow .cf-submit-form-shell{
  position:relative;
  z-index:2;
  background:transparent;
  border:none;
  border-radius:0;
  padding:0;
  box-shadow:none;
  margin-top:8px;
}

.challenge-flow .cf-submit-grid{
  display:grid;
  grid-template-columns:1fr;
  gap:16px;
  align-items:start;
}

.challenge-flow .cf-submit-row{
  display:block;
  margin-bottom:18px;
}
.challenge-flow .cf-submit-labelbox{
  display:flex;
  align-items:flex-start;
  gap:16px;
  padding-top:0;
  margin-bottom:10px;
}

.challenge-flow .cf-submit-label-icon{
  width:58px;
  height:58px;
  min-width:58px;
  border-radius:50%;
  display:grid;
  place-items:center;
  background:linear-gradient(180deg, #EEF3FB 0%, #E2EAF8 100%);
  border:1px solid rgba(10,46,93,.06);
}

.challenge-flow .cf-submit-label-icon svg{
  width:28px;
  height:28px;
  stroke:var(--navy);
  fill:none;
  stroke-width:2;
  stroke-linecap:round;
  stroke-linejoin:round;
}

.challenge-flow .cf-submit-labeltext strong{
  display:block;
  color:var(--navy);
  font-family:"Montserrat", sans-serif;
  font-size:17px;
  font-weight:800;
  margin-bottom:6px;
}

.challenge-flow .cf-submit-labeltext span{
  display:block;
  color:#536B8F;
  font-size:14px;
  line-height:1.7;
}
.challenge-flow .cf-input-wrap,
.challenge-flow .cf-file-box{
  width:calc(100% - 74px);
  margin-left:74px;
  max-width:100%;
}

.challenge-flow .cf-file-box{
  margin-top:2px;
}

.challenge-flow textarea{
  min-height:150px;
}

.challenge-flow .cf-tips{
  margin-top:12px;
}

.challenge-flow .cf-submit-btn{
  margin-top:14px;
}

.challenge-flow .cf-submit-labeltext em{
  font-style:normal;
  font-weight:500;
  color:#637A9A;
}

.challenge-flow .cf-input-wrap{
  position:relative;
}

.challenge-flow label{
  display:none;
}

.challenge-flow textarea,
.challenge-flow input[type="url"],
.challenge-flow input[type="text"],
.challenge-flow input[type="file"]{
  width:100%;
  border:1.5px solid rgba(10,46,93,.12);
  border-radius:18px;
  padding:16px 18px;
  font:inherit;
  background:#fff;
  color:#111827;
  outline:none;
  margin-bottom:0;
  transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease, background .2s ease;
  box-shadow:0 4px 10px rgba(10,46,93,.02);
}

.challenge-flow textarea::placeholder,
.challenge-flow input[type="url"]::placeholder,
.challenge-flow input[type="text"]::placeholder{
  color:#A0AEC2;
}

.challenge-flow textarea:hover,
.challenge-flow input[type="url"]:hover,
.challenge-flow input[type="text"]:hover,
.challenge-flow input[type="file"]:hover{
  border-color:rgba(10,46,93,.20);
}

.challenge-flow textarea:focus,
.challenge-flow input[type="url"]:focus,
.challenge-flow input[type="text"]:focus,
.challenge-flow input[type="file"]:focus{
  border-color:rgba(43,114,255,.48);
  box-shadow:0 0 0 5px rgba(43,114,255,.08);
  transform:translateY(-1px);
  background:#fff;
}

.challenge-flow textarea{
  min-height:120px;
  resize:vertical;
  line-height:1.8;
}

.challenge-flow .cf-input-icon{
  position:absolute;
  left:16px;
  top:50%;
  transform:translateY(-50%);
  width:22px;
  height:22px;
  pointer-events:none;
  opacity:.78;
}

.challenge-flow .cf-input-icon svg{
  width:22px;
  height:22px;
  stroke:#7B8DAA;
  fill:none;
  stroke-width:2;
  stroke-linecap:round;
  stroke-linejoin:round;
}

.challenge-flow .cf-input-wrap.has-icon input{
  padding-left:48px;
}

.challenge-flow .cf-file-box{
  border:2px dashed rgba(123,141,170,.42);
  border-radius:18px;
  background:rgba(255,255,255,.85);
  padding:16px 16px 14px;
  transition:border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.challenge-flow .cf-file-box:hover{
  border-color:rgba(43,114,255,.42);
  background:#fff;
  box-shadow:0 10px 20px rgba(10,46,93,.04);
}

.challenge-flow .cf-file-box input[type="file"]{
  border:none;
  background:transparent;
  padding:0;
  box-shadow:none;
  border-radius:0;
}

.challenge-flow .cf-file-meta{
  margin-top:12px;
  color:#7A8CA8;
  font-size:13px;
  line-height:1.7;
}

.challenge-flow .cf-helper{
  display:none;
}

.challenge-flow .cf-tips{
  margin-top:24px;
  border:1px solid rgba(242,174,24,.45);
  background:linear-gradient(180deg, rgba(255,250,238,.96) 0%, rgba(255,248,230,.92) 100%);
  border-radius:18px;
  padding:14px 18px;
  display:grid;
  grid-template-columns:260px repeat(4, minmax(0,1fr));
  gap:14px;
  align-items:center;
}

.challenge-flow .cf-tips-head{
  display:flex;
  align-items:center;
  gap:12px;
}

.challenge-flow .cf-tips-icon{
  width:46px;
  height:46px;
  border-radius:50%;
  display:grid;
  place-items:center;
  background:linear-gradient(180deg, #FFF1C8 0%, #F9DF8B 100%);
  border:1px solid rgba(242,174,24,.24);
}

.challenge-flow .cf-tips-icon svg{
  width:24px;
  height:24px;
  stroke:#D38E08;
  fill:none;
  stroke-width:2;
}

.challenge-flow .cf-tips-head strong{
  display:block;
  color:var(--navy);
  font-family:"Montserrat", sans-serif;
  font-size:16px;
  font-weight:800;
}

.challenge-flow .cf-tip{
  display:flex;
  align-items:center;
  gap:10px;
  color:#1E2F54;
  font-size:14px;
  font-weight:500;
}

.challenge-flow .cf-tip svg{
  width:18px;
  height:18px;
  stroke:#1E2F54;
  fill:none;
  stroke-width:2;
  flex-shrink:0;
}

.challenge-flow .cf-submit-btn{
width:260px;
  max-width:100%;
 margin:0px 0 0 20;
   display:block;  border:none;
  border-radius:20px;
  padding:20px 64px 20px 22px;
  background:linear-gradient(180deg, #153B7D 0%, #0D2E68 100%);
  color:#fff;
  font-family:"Montserrat", sans-serif;
  font-size:18px;
  font-weight:800;
  letter-spacing:-.2px;
  cursor:pointer;
  transition:transform .18s ease, box-shadow .18s ease, filter .18s ease, opacity .18s ease;
  box-shadow:0 18px 35px rgba(10,46,93,.18);
  position:relative;
}

.challenge-flow .cf-submit-btn::after{
  content:"→";
  position:absolute;
  right:24px;
  top:50%;
  transform:translateY(-50%);
  font-size:30px;
  line-height:1;
  color:#FFC24A;
  font-weight:700;
}

.challenge-flow .cf-submit-btn:hover{
  transform:translateY(-3px);
  box-shadow:0 22px 42px rgba(10,46,93,.24);
  filter:brightness(1.03);
}

.challenge-flow .cf-submit-btn:active{
  transform:translateY(-1px);
}

.challenge-flow .cf-submit-btn:disabled{
  opacity:.72;
  cursor:not-allowed;
  transform:none;
  box-shadow:none;
  filter:none;
}

/* =========================
   CENTER DIVIDER (مثل الصورة)
========================= */

.section-divider-center{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:20px;
  margin:40px 0 20px;
  position:relative;
}

/* الخطين */
.section-divider-center::before,
.section-divider-center::after{
  content:"";
  flex:1;
  height:2px;
  border-radius:999px;
  background:linear-gradient(
    90deg,
    transparent,
    rgba(10,46,93,.25)
  );
}

.section-divider-center::after{
  background:linear-gradient(
    90deg,
    rgba(10,46,93,.25),
    transparent
  );
}

/* النجمة بالنص */
.section-divider-center .diamond{
  width:14px;
  height:14px;
  background:linear-gradient(180deg, #FFC24A 0%, #F2AE18 100%);
  transform:rotate(45deg);
  border-radius:3px;
  box-shadow:
    0 4px 10px rgba(255,194,74,.4),
    0 0 0 6px rgba(255,194,74,.15);
  position:relative;
}

/* نقطة صغيرة داخلها (تفصيلة بالصورة) */
.section-divider-center .diamond::after{
  content:"";
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%, -50%) rotate(-45deg);
  width:4px;
  height:4px;
  background:#fff;
  border-radius:50%;
}
/* توسيط Next Step بالكامل */
.challenge-flow .cf-section-divider{
  display:flex;
  align-items:center;
  justify-content:center !important;
  text-align:center;
  width:100%;
  margin:30px auto 30px;
}

/* الخطين يكونوا متساويين */
.challenge-flow .cf-section-divider::before,
.challenge-flow .cf-section-divider::after{
  flex:1;
}

/* الزر نفسه */
.challenge-flow .cf-section-divider span{
  margin:0 auto;
  display:flex;
  align-items:center;
  justify-content:center;
}
@media (max-width: 1250px){
 

  .challenge-flow .cf-submit-hero-art{
    display:flex;
    justify-content:flex-end;
  }

  .challenge-flow .cf-submit-grid{
    grid-template-columns:1fr;
    gap:18px;
  }

  .challenge-flow .cf-submit-row{
    display:block;
  }

  .challenge-flow .cf-submit-labelbox{
    margin-bottom:10px;
  }

  .challenge-flow .cf-input-wrap,
  .challenge-flow .cf-file-box{
    margin-left:74px;
  }

  .challenge-flow .cf-tips{
    grid-template-columns:1fr 1fr;
  }
}

  @media (max-width: 700px){

  /* كودك الحالي */
@media (max-width: 700px){
  .challenge-flow #submitBox h2{
    font-size:34px;
  }


  .challenge-flow .cf-submit-hero-icon{
    width:78px;
    height:78px;
  }

  .challenge-flow .cf-submit-form-shell{
    padding:18px;
    border-radius:20px;
  }

  .challenge-flow .cf-submit-labelbox{
    gap:12px;
  }

  .challenge-flow .cf-submit-label-icon{
    width:50px;
    height:50px;
    min-width:50px;
  }

 .challenge-flow .cf-tips{
  display:none !important;
}

  .challenge-flow .cf-submit-btn{
    font-size:16px;
    padding:18px 54px 18px 18px;
  }


  .challenge-flow .cf-input-wrap,
  .challenge-flow .cf-file-box{
    margin-left:0;
    width:100%;
  }

}
}


.challenge-flow .cf-submit-btn:hover{transform:translateY(-3px);box-shadow:0 22px 42px rgba(10,46,93,.22);filter:brightness(1.03);}
.challenge-flow .cf-submit-btn:active{transform:translateY(-1px);}
.challenge-flow .cf-submit-btn:disabled{opacity:.72;cursor:not-allowed;transform:none;box-shadow:none;filter:none;}
.challenge-flow .cf-result-card{background:rgba(255,255,255,.66);border:1px solid rgba(10,46,93,.08);border-radius:24px;padding:20px;box-shadow:0 8px 22px rgba(10,46,93,.05);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
.challenge-flow .cf-score-banner{display:flex;align-items:center;gap:16px;padding:16px;border-radius:20px;background:linear-gradient(135deg, rgba(10,46,93,.05), rgba(255,194,74,.14));border:1px solid rgba(10,46,93,.08);margin-bottom:14px;}
.challenge-flow .cf-score-orb{width:74px;height:74px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg, #0A2E5D 0%, #1B5FAE 100%);color:#fff;font-family:"Montserrat", sans-serif;font-size:20px;font-weight:900;box-shadow:0 14px 28px rgba(10,46,93,.16);flex-shrink:0;}
.challenge-flow .cf-score-banner h4{margin:0 0 4px;font-family:"Montserrat", sans-serif;color:var(--navy);font-size:18px;}
.challenge-flow .cf-score-banner p{margin:0;color:#677994;font-size:13px;line-height:1.8;}
.challenge-flow .cf-result-row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid rgba(10,46,93,.08);}
.challenge-flow .cf-result-row:last-child{border-bottom:none;}
.challenge-flow .cf-result-label{color:#6B7A96;font-weight:700;font-size:14px;}
.challenge-flow .cf-result-value{color:var(--navy);font-family:"Montserrat", sans-serif;font-weight:800;font-size:17px;}
.challenge-flow .cf-feedback-box{margin-top:14px;background:rgba(255,255,255,.72);border:1px solid rgba(10,46,93,.08);border-radius:18px;padding:16px;line-height:1.95;color:#0f172a;white-space:pre-wrap;font-size:14px;box-shadow:0 10px 20px rgba(10,46,93,.03);}
@media (max-width: 1280px){.challenge-flow .cf-hero-grid{grid-template-columns:1fr;}.challenge-flow .cf-hero-side{min-height:260px;}.challenge-flow .cf-hero-side-card{position:relative;top:auto;right:auto;margin:10px 0 0 auto;}}
@media (max-width: 1150px){.challenge-flow .cf-hero,.challenge-flow .cf-layout{padding-left:22px;padding-right:22px;}.challenge-flow .cf-actions-deliverables-grid,.challenge-flow .cf-hero-meta-row{grid-template-columns:1fr;}.challenge-flow .cf-hero-meta-divider{display:none;}.challenge-flow .cf-hero-side-card{margin-left:auto;margin-right:auto;}}
@media (max-width: 900px){.challenge-flow .cf-hero-topbar{align-items:flex-start;}.challenge-flow .cf-hero-copy h1{font-size:42px;}.challenge-flow .cf-hero-side{min-height:auto;}.challenge-flow .cf-hero-side-card{width:260px;height:270px;}}
@media (max-width: 700px){.challenge-flow .chero{padding-top:18px;}.challenge-flow .cf-hero,.challenge-flow .cf-layout{padding-left:14px;padding-right:14px;}.challenge-flow .cf-hero-copy h1{font-size:34px;}.challenge-flow .cf-hero-copy p{font-size:14px;}.challenge-flow .cf-hero-meta{align-items:flex-start;}.challenge-flow .cf-hero-meta-icon{width:70px;height:70px;}.challenge-flow .cf-hero-side-card{width:100%;max-width:280px;}.challenge-flow .cf-actions-deliverables-grid{gap:24px;}.challenge-flow #submitBox h2,.challenge-flow #resultBox h2{font-size:28px;}.challenge-flow .cf-score-banner,.challenge-flow .cf-result-row{flex-direction:column;align-items:flex-start;}.challenge-flow textarea{min-height:180px;}}


/* FINAL SCREENSHOT-MATCH DESIGN OVERRIDES - design only */
body{padding-top:82px!important;background:#FAFBFD!important;color:#273A5C;}
body::before{top:92px!important;right:-132px!important;width:300px!important;height:300px!important;background:radial-gradient(circle,rgba(255,194,74,.10) 0 48%,transparent 49%)!important;box-shadow:0 0 0 28px rgba(255,194,74,.035),0 0 0 58px rgba(255,194,74,.025)!important;filter:none!important;}
body::after{left:auto!important;right:-170px!important;bottom:-110px!important;width:360px!important;height:360px!important;border:1px solid rgba(10,46,93,.035);background:radial-gradient(circle,rgba(255,194,74,.055) 0 42%,transparent 43%)!important;box-shadow:0 0 0 26px rgba(10,46,93,.025),0 0 0 54px rgba(255,194,74,.018)!important;filter:none!important;}
.page{padding-bottom:40px!important;overflow:hidden}.topbar{height:82px;background:#fff!important;border-bottom:1px solid rgba(10,46,93,.055)!important;box-shadow:0 7px 20px rgba(10,46,93,.045)!important}.topbar-inner{height:82px!important;padding:0 38px!important}.topbar-logo{font-size:28px!important;letter-spacing:-1.5px;color:#0A2E5D!important}.topbar-nav{gap:12px!important}.topbar-link{font-size:13px!important;font-weight:700!important;padding:10px 18px!important;color:#20283A!important}.topbar-link.active{background:#FFC24A!important;color:#fff!important;box-shadow:0 8px 18px rgba(255,194,74,.24)}.topbar-link:not(.back-link):hover{background:#FFC24A!important;color:#fff!important;transform:translateY(-2px)}.topbar-link.back-link:hover{color:#0A2E5D!important;text-decoration:none!important}.logout-btn{height:42px;display:inline-flex;align-items:center;padding:0 20px!important;border:1.8px solid #0A2E5D!important;border-radius:999px!important;font-size:13px!important;font-weight:800!important}.nav-monkey{width:34px!important;height:34px!important}
.hero{padding:24px 52px 0!important;max-width:1280px;margin:0 auto}.hero-grid{display:block!important}.hero-topbar{position:absolute;right:52px;top:26px;display:flex!important;align-items:flex-end!important;gap:12px!important;margin:0!important;z-index:5}.hero-badge{min-height:36px!important;padding:8px 14px 8px 12px!important;font-size:12px!important;box-shadow:0 10px 22px rgba(255,194,74,.22)!important}.hero-badge::before{width:10px!important;height:10px!important;box-shadow:0 0 0 5px rgba(255,255,255,.30)!important}.eyebrow{min-height:36px!important;padding:9px 16px!important;font-size:12px!important;background:linear-gradient(180deg,#213A66 0%,#102B59 100%)!important;box-shadow:0 13px 25px rgba(10,46,93,.16)!important}.eyebrow::before{width:18px!important;height:18px!important;font-size:10px!important}.hero-copy{padding-top:86px!important}.hero-copy h1{font-size:clamp(38px,4vw,50px)!important;letter-spacing:-1.4px!important;margin-bottom:18px!important}.hero-copy h1::after{width:46px!important;height:3px!important;margin-top:18px!important}.hero-copy p{max-width:575px!important;font-size:14px!important;line-height:1.85!important;color:#415778!important}
.hero-mini-stats{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:34px!important;margin:34px 0 34px!important;max-width:920px!important}.mini-stat{min-height:96px!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important;backdrop-filter:none!important;padding:0 22px 0 100px!important;overflow:visible!important}.mini-stat:hover{transform:none!important;box-shadow:none!important}.mini-stat:not(:last-child){border-right:1px solid rgba(10,46,93,.11)!important}.mini-stat::before{left:0!important;top:6px!important;transform:none!important;width:72px!important;height:72px!important;box-shadow:0 13px 25px rgba(10,46,93,.055)!important}.mini-stat::after{left:100px!important;right:auto!important;top:auto!important;bottom:-8px!important;width:40px!important;height:3px!important;border-radius:999px!important}.mini-stat .icon{left:22px!important;top:27px!important;transform:none!important;width:30px!important;height:30px!important}.mini-stat .icon svg{width:30px!important;height:30px!important;stroke-width:2.1!important}.mini-stat strong{font-size:16px!important;margin-bottom:7px!important;color:#0A2E5D!important}.mini-stat span{font-size:13px!important;line-height:1.6!important;color:#415778!important;max-width:240px!important}
.challenge-flow{margin-top:0!important}.challenge-flow .cf-hero{padding:28px 0 0!important}.challenge-flow .cf-hero::before{right:-54px!important;bottom:-155px!important;width:360px!important;height:360px!important;border:1px solid rgba(10,46,93,.03);background:transparent!important;border-radius:50%}.challenge-flow .cf-hero::after{right:0!important;bottom:140px!important;width:120px!important;height:100px!important;background-size:14px 14px!important;opacity:.42!important}.challenge-flow .cf-hero-grid{display:block!important}.challenge-flow .cf-hero-side{display:none!important}.challenge-flow .cf-hero-copy h1{font-size:clamp(28px,3.1vw,36px)!important;line-height:1.08!important;letter-spacing:-.9px!important;max-width:560px!important;margin-bottom:14px!important}.challenge-flow .cf-hero-copy h1::after{width:36px!important;height:3px!important;margin-top:12px!important}.challenge-flow .cf-hero-copy p{max-width:900px!important;font-size:13px!important;line-height:1.85!important;color:#405679!important}.challenge-flow .cf-hero-meta-row{max-width:720px!important;margin:28px 0 34px!important;gap:28px!important;grid-template-columns:minmax(0,1fr) 1px minmax(0,1fr)!important}.challenge-flow .cf-hero-meta-icon{width:72px!important;height:72px!important}.challenge-flow .cf-hero-meta-icon svg{width:32px!important;height:32px!important}.challenge-flow .cf-hero-meta-copy .k{font-size:11px!important;color:#7C8AA3!important}.challenge-flow .cf-hero-meta-copy .v{font-size:15px!important;color:#0A2E5D!important}
.challenge-flow .cf-actions-deliverables-grid{max-width:920px!important;display:grid!important;grid-template-columns:1fr 1fr!important;gap:72px!important;margin-top:0!important}.challenge-flow .cf-section-head{margin:0 0 18px!important;gap:16px!important}.challenge-flow .cf-section-head-icon{width:28px!important;height:28px!important;border-radius:8px!important;background:transparent!important;border:none!important;box-shadow:none!important}.challenge-flow .cf-section-head-icon svg{width:25px!important;height:25px!important}.challenge-flow .cf-section-head h3{font-size:17px!important}.challenge-flow .cf-section-head h3::after{width:28px!important;height:3px!important;margin-top:10px!important}.challenge-flow .cf-section-list li{font-size:13px!important;line-height:1.55!important;margin-bottom:11px!important;padding-left:38px!important;color:#405679!important}.challenge-flow .cf-section-list li::before{width:22px!important;height:22px!important;top:0!important}.challenge-flow .cf-section-list li::after{left:6px!important;top:0!important;font-size:15px!important}
.challenge-flow .cf-layout{padding:14px 0 0!important}.challenge-flow #submitBox{margin-top:20px!important;padding-top:0!important;max-width:1060px}.challenge-flow #submitBox::before{left:-92px!important;top:92px!important;width:180px!important;height:180px!important;background:rgba(42,117,255,.055)!important}.challenge-flow #submitBox::after{display:none!important}.challenge-flow .cf-section-divider{margin:0 0 26px!important;max-width:760px}.challenge-flow .cf-section-divider span{padding:8px 16px!important;font-size:12px!important;background:#F2F5FA!important}.challenge-flow #submitBox h2{font-size:26px!important;letter-spacing:-.5px!important;margin-bottom:24px!important}.challenge-flow #submitBox h2::after{width:38px!important;height:3px!important;margin-top:9px!important}.challenge-flow .cf-submit-form-shell{max-width:980px!important;margin-top:0!important}.challenge-flow .cf-submit-row{margin-bottom:18px!important}.challenge-flow .cf-submit-labelbox{gap:18px!important;margin-bottom:8px!important;align-items:center!important}.challenge-flow .cf-submit-label-icon{width:56px!important;height:56px!important;min-width:56px!important;background:linear-gradient(180deg,#EAF3FF 0%,#D9E9FF 100%)!important;box-shadow:0 9px 18px rgba(10,46,93,.045)!important;border:none!important}.challenge-flow .cf-submit-label-icon svg{width:26px!important;height:26px!important;stroke:#0A2E5D!important}.challenge-flow .cf-submit-labeltext strong{font-size:14px!important;margin-bottom:3px!important}.challenge-flow .cf-submit-labeltext span{font-size:12px!important;line-height:1.55!important;color:#586D8C!important}.challenge-flow .cf-input-wrap,.challenge-flow .cf-file-box{width:calc(100% - 74px)!important;margin-left:74px!important}.challenge-flow textarea,.challenge-flow input[type="url"],.challenge-flow input[type="text"]{border:1px solid rgba(10,46,93,.16)!important;border-radius:15px!important;padding:14px 16px!important;font-size:13px!important;box-shadow:none!important}.challenge-flow textarea{min-height:76px!important;line-height:1.6!important}.challenge-flow .cf-input-wrap.has-icon input{padding-left:16px!important}.challenge-flow .cf-input-icon{display:none!important}.challenge-flow .cf-file-box{border:1.6px dashed rgba(112,128,153,.43)!important;border-radius:15px!important;padding:13px 14px 12px!important;background:#fff!important;box-shadow:none!important}.challenge-flow .cf-file-box input[type="file"]{font-size:13px!important}.challenge-flow .cf-file-meta{margin-top:7px!important;font-size:11px!important;color:#7C8AA3!important}.challenge-flow .cf-submit-btn{width:222px!important;border-radius:17px!important;padding:16px 54px 16px 26px!important;margin:8px 0 0 0!important;background:linear-gradient(180deg,#0B356D 0%,#082F66 100%)!important;font-size:15px!important;box-shadow:0 14px 28px rgba(10,46,93,.18)!important}.challenge-flow .cf-submit-btn::after{right:28px!important;font-size:24px!important;color:#FFC24A!important}.challenge-flow .cf-result-card{background:#fff!important}
@media(max-width:900px){body{padding-top:140px!important}.topbar{height:auto!important}.topbar-inner{height:auto!important;gap:12px!important;align-items:flex-start!important}.hero{padding:18px 18px 0!important}.hero-topbar{position:static!important;align-items:flex-start!important;margin-bottom:22px!important}.hero-copy{padding-top:0!important}.hero-mini-stats,.challenge-flow .cf-actions-deliverables-grid,.challenge-flow .cf-hero-meta-row{grid-template-columns:1fr!important;gap:22px!important}.mini-stat:not(:last-child){border-right:none!important}.challenge-flow .cf-hero-meta-divider{display:none!important}}
@media(max-width:700px){.hero-copy h1{font-size:34px!important}.challenge-flow .cf-hero-copy h1{font-size:28px!important}.challenge-flow .cf-input-wrap,.challenge-flow .cf-file-box{width:100%!important;margin-left:0!important}.challenge-flow .cf-submit-btn{width:100%!important}}
</style>
</head>
<body>
<header class="topbar" id="navbar">
  <div class="topbar-inner">

    <a href="student-dashboard.php#home" class="topbar-logo">QOYN</a>

    <nav class="topbar-nav">
      <a href="student-dashboard.php#home" class="topbar-link active">Home</a>
      <a href="index.php" class="topbar-link back-link">Back to my page</a>
    </nav>

 <div class="nav-right-group">
<a href="#" id="logoutBtn" class="logout-btn">Logout</a>
  <img src="uploads/MONKEY.png" alt="monkey" class="nav-monkey">
</div>

  </div>
</header>

  <div class="page">
    <section class="hero">
      <div class="hero-grid">
        <div class="hero-left">
          <div class="hero-topbar">
            <div class="hero-badge">Project ID: <?php echo (int)$project_id; ?></div>
            <div class="eyebrow">AI Post-Delivery Challenge</div>
          </div>

          <div class="hero-copy">
            <h1>Phase 3 - Level 2</h1>
            <p>
              Complete your post-delivery simulation challenge,<br>
              review the generated scenario, submit your engineering solution,<br>
              and receive a structured evaluation in a more premium QOYN experience.
            </p>
          </div>

          <div class="hero-mini-stats">
            <div class="mini-stat challenge">
              <div class="icon">
                <svg viewBox="0 0 24 24">
                  <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                  <path d="M9 4.5h6"></path>
                  <path d="M9 10h6"></path>
                  <path d="M9 14h6"></path>
                  <path d="M9 18h4"></path>
                </svg>
              </div>
              <strong>Challenge</strong>
              <span>Scenario-driven task with structured required actions and deliverables.</span>
            </div>

            <div class="mini-stat submit">
              <div class="icon">
                <svg viewBox="0 0 24 24">
                  <path d="M12 16V6"></path>
                  <path d="M8 10l4-4 4 4"></path>
                  <path d="M5 16.5v.5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-.5"></path>
                </svg>
              </div>
              <strong>Submit</strong>
              <span>Explain your solution, attach a file, or share a repository link.</span>
            </div>

            <div class="mini-stat result">
              <div class="icon">
                <svg viewBox="0 0 24 24">
                  <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                  <path d="M8 16V9"></path>
                  <path d="M12 16V7"></path>
                  <path d="M16 16v-4"></path>
                </svg>
              </div>
              <strong>Result</strong>
              <span>Get score, decision, rubric breakdown, and readiness feedback.</span>
            </div>
          </div>

          <input type="hidden" id="project_id" value="<?php echo (int)$project_id; ?>">
        </div>

           

        
<div class="section-divider-center">
  <span class="diamond"></span>
</div>
    
    <section class="challenge-flow">
<section class="cf-hero">
      <div class="cf-hero-grid">
        <div class="cf-hero-left">
         

          <div class="cf-hero-copy">
            <h1 id="challengeTitle">جاري تحميل التحدي...</h1>
            <p id="scenarioText">يرجى الانتظار...</p>
          </div>

          <div class="cf-hero-meta-row">
            <div class="cf-hero-meta challenge">
              <div class="cf-hero-meta-icon">
                <svg viewBox="0 0 24 24">
                  <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                  <path d="M9 4.5h6"></path>
                  <path d="M9 10h6"></path>
                  <path d="M9 14h6"></path>
                  <path d="M9 18h4"></path>
                </svg>
              </div>
              <div class="cf-hero-meta-copy">
                <span class="k">Challenge Type</span>
                <span class="v" id="challengeTypeText">-</span>
              </div>
            </div>

            <div class="cf-hero-meta-divider"></div>

            <div class="cf-hero-meta difficulty">
              <div class="cf-hero-meta-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M4 18h16"></path>
                  <path d="M8 18V9"></path>
                  <path d="M12 18V5"></path>
                  <path d="M16 18v-6"></path>
                </svg>
              </div>
              <div class="cf-hero-meta-copy">
                <span class="k">Difficulty</span>
                <span class="v" id="difficultyText">-</span>
              </div>
            </div>
          </div>

          <div class="cf-actions-deliverables-grid">
            <div class="cf-list-panel">
              <div class="cf-section-head actions">
                <div class="cf-section-head-icon">
                  <svg viewBox="0 0 24 24">
                    <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                    <path d="M8 8h8"></path>
                    <path d="M8 12h5"></path>
                    <path d="M8 16h6"></path>
                  </svg>
                </div>
                <h3>Required Actions</h3>
              </div>

              <div class="cf-section-list">
                <ul id="requiredActions">
                  <li class="empty-note">لا يوجد بيانات بعد</li>
                </ul>
              </div>
            </div>

            <div class="cf-list-panel">
              <div class="cf-section-head deliverables">
                <div class="cf-section-head-icon">
                  <svg viewBox="0 0 24 24">
                    <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                    <path d="M9 8h6"></path>
                    <path d="M9 12h6"></path>
                    <path d="M9 16h5"></path>
                  </svg>
                </div>
                <h3>Deliverables</h3>
              </div>

              <div class="cf-section-list">
                <ul id="deliverables">
                  <li class="empty-note">لا يوجد بيانات بعد</li>
                </ul>
              </div>
            </div>
          </div>

        </div>

        <div class="cf-hero-side">
          <div class="cf-hero-side-card" aria-hidden="true">
            <div class="cf-hero-side-visual">
              <div class="cf-hero-side-lines">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="cf-layout">
    <div class="cf-card" id="submitBox">
  <div class="cf-section-divider">
    <span>Next Step</span>
  </div>

  <h2>Submit Your Solution</h2>



   <div class="cf-submit-hero-copy"></div>

    

  <div class="cf-submit-form-shell">
    <form id="submissionForm" enctype="multipart/form-data">
      <input type="hidden" name="project_id" value="<?php echo (int)$project_id; ?>">
      <input type="hidden" name="challenge_id" id="challenge_id" value="">

      <div class="cf-submit-grid">
        <div class="cf-submit-row">
          <div class="cf-submit-labelbox">
            <div class="cf-submit-label-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
              </svg>
            </div>
            <div class="cf-submit-labeltext">
              <strong>Solution Explanation</strong>
              <span>Explain your approach, logic, and how your solution works.</span>
            </div>
          </div>

          <div class="cf-input-wrap">
            <label>Solution Explanation</label>
            <textarea
              name="submission_text"
              rows="8"
              placeholder="Write your explanation here..."
              required
            ></textarea>
          </div>
        </div>

        <div class="cf-submit-row">
          <div class="cf-submit-labelbox">
            <div class="cf-submit-label-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 1 1 7 7L17 13"/>
                <path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 1 1-7-7L7 11"/>
              </svg>
            </div>
            <div class="cf-submit-labeltext">
              <strong>Repository URL <em>(Optional)</em></strong>
              <span>Add the link to your GitHub or GitLab repository.</span>
            </div>
          </div>

          <div class="cf-input-wrap has-icon">
            <span class="cf-input-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 1 1 7 7L17 13"/>
                <path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 1 1-7-7L7 11"/>
              </svg>
            </span>
            <label>Repository URL</label>
            <input
              type="url"
              name="repo_url"
              placeholder="https://github.com/username/repository"
            >
          </div>
        </div>

        <div class="cf-submit-row">
          <div class="cf-submit-labelbox">
            <div class="cf-submit-label-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M12 16V6"/>
                <path d="M8 10l4-4 4 4"/>
                <path d="M5 16.5v.5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-.5"/>
              </svg>
            </div>
            <div class="cf-submit-labeltext">
              <strong>Upload File <em>(Optional)</em></strong>
              <span>Upload your code file or documentation (PDF, ZIP, etc.).</span>
            </div>
          </div>

          <div class="cf-file-box">
            <label>Upload File</label>
            <input type="file" name="solution_file">
            <div class="cf-file-meta">Max file size: 50MB • Allowed: ZIP, PDF, DOCX, PPTX, TXT</div>
          </div>
        </div>
      </div>

     
    

      <div class="cf-helper">ارفع شرح الحل أو ملف أو رابط المستودع إن وجد.</div>

      <button type="submit" class="cf-submit-btn" id="submitBtn">Submit Level 2</button>
    </form>
  </div>
</div>

      <div class="cf-card hidden" id="resultBox">
        <h2>Evaluation Result</h2>

        <div class="cf-result-card">
          <div class="cf-score-banner">
            <div class="cf-score-orb" id="scoreText">-</div>
            <div>
              <h4>Performance Score</h4>
              <p>Your final evaluation score appears here after submission.</p>
            </div>
          </div>

          <div class="cf-result-row">
            <span class="cf-result-label">Decision</span>
            <span class="cf-result-value" id="decisionText">-</span>
          </div>

          <div class="cf-feedback-box" id="feedbackText">لا يوجد تقييم بعد.</div>

          <h3>Rubric Breakdown</h3>
          <div class="cf-section-list">
            <ul id="rubricBox">
              <li class="empty-note">No rubric data yet</li>
            </ul>
          </div>

          <h3>Engineering Readiness</h3>
          <div class="cf-section-list">
            <ul id="readinessBox">
              <li class="empty-note">No readiness data yet</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  
    </section>

  <script>
    const projectId = document.getElementById("project_id").value;

    async function loadChallenge() {
      try {
        let res = await fetch(`api/phase3_level2/get_my_challenge.php?project_id=${projectId}`);
        let data = await res.json();

        if (!data.ok) {
          console.log("get_my_challenge error:", data);
          alert(data.error || "Failed to load challenge");
          return;
        }

        if (!data.challenge) {
          const gen = await fetch("api/phase3_level2/generate.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
              project_id: projectId
            })
          });

          data = await gen.json();

          if (!data.ok) {
            console.log("generate error:", data);
            alert(data.error || "Failed to generate challenge");
            return;
          }
        }

        const c = data.challenge;
        console.log("Challenge loaded:", c);

        document.getElementById("challenge_id").value = c.id || "";
        document.getElementById("challengeTitle").textContent = c.title || "Level 2 Challenge";
        document.getElementById("scenarioText").textContent = c.scenario_text || "";
        document.getElementById("challengeTypeText").textContent = c.challenge_type || "-";
        document.getElementById("difficultyText").textContent = c.difficulty || "-";

        let actions = [];
        let deliverables = [];

        try {
          actions = JSON.parse(c.required_actions_json || "[]");
        } catch (e) {
          console.log("Actions parse error:", e);
        }

        try {
          deliverables = JSON.parse(c.deliverables_json || "[]");
        } catch (e) {
          console.log("Deliverables parse error:", e);
        }

        document.getElementById("requiredActions").innerHTML =
          actions.length
            ? actions.map(a => `<li>${a}</li>`).join("")
            : `<li class="empty-note">لا يوجد actions</li>`;

        document.getElementById("deliverables").innerHTML =
          deliverables.length
            ? deliverables.map(d => `<li>${d}</li>`).join("")
            : `<li class="empty-note">لا يوجد deliverables</li>`;

      } catch (err) {
        console.error("Unexpected loadChallenge error:", err);
        alert("Unexpected error while loading challenge");
      }
    }

    document.getElementById("submissionForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      const btn = document.getElementById("submitBtn");
      btn.disabled = true;
      btn.textContent = "Submitting...";

      try {
        const formData = new FormData(this);

        const res = await fetch("api/phase3_level2/submit.php", {
          method: "POST",
          body: formData
        });

        const data = await res.json();

        if (!data.ok) {
          console.log("submit error:", data);
          alert(data.error || "Submission failed");
          btn.disabled = false;
          btn.textContent = "Submit Level 2";
          return;
        }

        if (data.evaluation) {
          document.getElementById("resultBox").classList.remove("hidden");
          document.getElementById("scoreText").textContent = data.evaluation.score ?? "";
          document.getElementById("decisionText").textContent = data.evaluation.decision ?? "";
          document.getElementById("feedbackText").textContent = data.evaluation.feedback_text ?? "";

          let rubric = {};
          let readiness = {};

          try {
            rubric = JSON.parse(data.evaluation.rubric_scores_json || "{}");
          } catch (e) {
            console.log("rubric parse error:", e);
          }

          try {
            readiness = JSON.parse(data.evaluation.readiness_json || "{}");
          } catch (e) {
            console.log("readiness parse error:", e);
          }

          document.getElementById("rubricBox").innerHTML =
            Object.keys(rubric).length
              ? Object.entries(rubric).map(([k, v]) => `<li>${k}: ${v}</li>`).join("")
              : `<li class="empty-note">No rubric data</li>`;

          document.getElementById("readinessBox").innerHTML =
            Object.keys(readiness).length
              ? Object.entries(readiness).map(([k, v]) => `<li>${k}: ${v}</li>`).join("")
              : `<li class="empty-note">No readiness data</li>`;

          alert("تم إرسال الحل وتقييمه بنجاح");
        } else {
          alert("تم حفظ التسليم بنجاح");
        }

      } catch (err) {
        console.error("Unexpected submit error:", err);
        alert("Unexpected error while submitting");
      } finally {
        btn.disabled = false;
        btn.textContent = "Submit Level 2";
      }
    });

    loadChallenge();
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

