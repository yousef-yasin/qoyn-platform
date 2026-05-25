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
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title data-i18n="phase2_project_page_title">Phase 2 - Project</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <script src="assets/js/i18n.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --navyHover:#144270;
      --yellow:#FFC24A;
      --bg:#F6F7F9;
      --card:#ffffff;
      --text:#0B0B0B;
      --muted:rgba(0,0,0,.62);
      --line:rgba(0,0,0,.08);
      --shadow: 0 10px 30px rgba(0,0,0,.08);
      --r: 18px;
      --container: 1200px;
      --homeLight:#EAF3FF;
    }

    *{box-sizing:border-box}

    html{scroll-behavior:smooth}

    body{
      margin:0;
      background: linear-gradient(135deg, #f4f6f9 0%, #e9edf3 100%);
      color: var(--text);
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      direction: ltr;
      text-align: left;
    }

    .wrap{
      max-width:1200px;
      margin:0 auto;
      padding:18px;
      direction: ltr;
    }

    h1,h2,h3,b{
      font-family:"Montserrat", sans-serif;
    }

    .card{
      background: var(--card);
      border:1px solid var(--line);
      border-radius: 18px;
      padding:14px;
      margin-top:12px;
      box-shadow: var(--shadow);
    }

    #submitBox,
    #resultBox{
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      border-radius: 0 !important;
      padding: 0 !important;
      margin: 0 auto 18px !important;
      max-width: 1100px;
    }

    #submitBox h3,
    #resultBox h3,
    .project-bottom-boxes h3{
      color: var(--navy);
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 30px;
      margin: 0 0 18px 0;
    }

    #submitBox label,
    #resultBox label{
      color: var(--navy) !important;
      font-weight: 700;
    }

    #submitBox .muted,
    #resultBox .muted{
      color: rgba(10,46,93,.82) !important;
    }

    #submitBox input,
    #submitBox textarea{
      border-radius: 16px;
      border: 1px solid rgba(10,46,93,.16);
      background: rgba(255,255,255,.78);
      padding: 14px 16px;
      font-size: 14px;
    }

    #submitBox input:focus,
    #submitBox textarea:focus{
      border-color: rgba(10,46,93,.35);
      box-shadow: 0 0 0 4px rgba(10,46,93,.08);
    }

    #submitBox .btn-primary{
      margin-top: 16px !important;
    }

    @media(max-width:900px){
      .project-bottom-boxes{
        padding: 30px 18px 28px;
      }

      #submitBox h3,
      #resultBox h3,
      .project-bottom-boxes h3{
        font-size: 24px;
      }
    }

    .milestone{
      margin-top:10px;
      padding:12px;
      border-radius:14px;
      border:1px solid var(--line);
      background: rgba(10,46,93,.02);
    }

    textarea,input{
      width:100%;
      border-radius:12px;
      border:1px solid rgba(0,0,0,.12);
      background:#fff;
      color:#111;
      padding:10px;
      outline:none;
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    textarea{min-height:120px}

    textarea:focus,input:focus{
      border-color: rgba(10,46,93,.35);
      box-shadow: 0 0 0 4px rgba(10,46,93,.08);
    }

    .btn{
      cursor:pointer;
      border:none;
      border-radius:14px;
      padding:10px 14px;
      font-weight:800;
      font-family:"Montserrat", sans-serif;
      letter-spacing:.2px;
    }

    .btn-primary{
      background: var(--navy);
      color:#fff;
      box-shadow: 0 14px 28px rgba(0,0,0,.12);
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .btn-primary:hover{
      transform: translateY(-1px);
      background: var(--navyHover);
      box-shadow: 0 20px 42px rgba(0,0,0,.16);
    }

    .muted{
      color: var(--muted);
      opacity:1;
      font-size:.95em;
    }

    .row{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      direction: ltr;
    }

    .pill{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:10px 16px;
      border-radius:999px;
      border:1px solid rgba(10,46,93,.18);
      background: rgba(10,46,93,.06);
      color: var(--navy);
      font-weight:900;
      font-family:"Montserrat", sans-serif;
    }

    .ok{color:#0F9D58}
    .bad{color:#D93025}

    ul{
      margin:8px 0 0 0;
      padding-left:18px;
      padding-right:0;
    }

    .grid2{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:10px;
    }

    .sep{
      height:1px;
      background: var(--line);
      margin:12px 0;
    }

    pre{
      white-space:pre-wrap;
      margin:8px 0 0 0;
    }

    .small{font-size:.9em}

    @media(max-width:800px){
      .grid2{grid-template-columns:1fr}
    }

    .qoyn-topbar{
      position: sticky;
      top: 0;
      z-index: 999;
      width: 100%;
      left: 0;
      right: 0;
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
      border-bottom: 1px solid rgba(0,0,0,.06);
    }

    .qoyn-topbar-inner{
      width: 100%;
      padding:14px 40px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      direction:ltr;
    }

    .qoyn-left{
      display:flex;
      align-items:center;
      gap:18px;
      min-width:0;
    }

    .qoyn-logo{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:28px;
      color:var(--navy);
      letter-spacing:.5px;
      text-decoration:none;
      white-space:nowrap;
    }

    .qoyn-nav{
      display:flex;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .qoyn-link{
      text-decoration:none;
      color:#111;
      font-weight:500;
      font-size:15px;
      padding:10px 14px;
      border-radius:999px;
      transition: color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
      white-space:nowrap;
      border:1px solid rgba(10,46,93,.22);
      background:#fff;
    }

    .qoyn-link:hover{
      color: var(--yellow);
      transform: translateY(-2px);
      font-weight:700;
    }

    .qoyn-link.logout:hover{
      background: var(--navy);
      color:#fff;
      border-color: var(--navy);
    }

    .topbar-logo{
      height:58px;
      width:auto;
      display:block;
      margin-left:auto;
      flex:0 0 auto;
    }

    .qoyn-right{
      display:flex;
      align-items:center;
      gap:40px;
    }

    .back-link{
      border:none !important;
      background:none !important;
      padding:0 !important;
    }

.phase3-nav-btn{
  color: var(--navy) !important;
  font-weight: 700 !important;
}

.phase3-nav-btn:hover{
  color: var(--yellow) !important;
}
.phase3-nav-btn{
  color: var(--navy) !important;
  font-weight: 700 !important;
}

.phase3-nav-btn:hover{
  color: var(--yellow) !important;
}
    .phase2-home{
      min-height:90vh;
      background: var(--homeLight);
      display:flex;
      align-items:center;
    }

    .phase2-home-inner{
      width: 100%;
      max-width: none;
      margin: 0;
      padding: 40px 76px;
      display:grid;
      grid-template-columns: .95fr 1.05fr;
      gap: 54px;
      align-items:center;
      direction:ltr;
    }

    .phase2-home-visual{
      display:flex;
      justify-content:flex-start;
      align-items:center;
      overflow:visible;
    }

    .phase2-visual-wrap{
      position:relative;
      width:min(100%, 560px);
      min-height:560px;
      display:flex;
      align-items:flex-end;
      justify-content:center;
      overflow:visible;
    }

    .phase2-visual-wrap .shape{
      position:absolute;
      border-radius:18px;
      transform: skewX(-24deg) rotate(-6deg);
      box-shadow: var(--shadow);
    }

    .phase2-visual-wrap .shape-yellow{
      width: 220px;
      height: 300px;
      left: 190px;
      top: 180px;
      background: var(--yellow);
      z-index: 1;
    }

    .phase2-visual-wrap .shape-navy{
      width: 270px;
      height: 330px;
      left: 90px;
      top: 110px;
      background: var(--navy);
      z-index: 2;
    }

    .person-frame{
      overflow: visible;
      display:block;
      position: relative;
    }

    .person-young{
      position:absolute;
      left: -200px;
      bottom: 0;
      width: 350px;
      max-width:none;
      height:auto;
      object-fit:contain;
      z-index: 3;
      pointer-events:none;
    }

    .phase2-home-content{
      text-align:left;
    }

    .phase2-home-title{
      margin:0 0 18px 0;
      font-family:"Montserrat", sans-serif;
      font-size:56px;
      line-height:1.08;
      font-weight:800;
      letter-spacing:-.6px;
      color:#111;
    }

    .phase2-home-title .yellow{
      color: var(--yellow);
      display:inline-block;
    }

    .phase2-home-title .navy{
      color: var(--navy);
      display:inline-block;
    }

    .phase2-home-text{
      margin:0;
      max-width:640px;
      font-size:15.5px;
      line-height:1.9;
      color:#111;
    }

    @media(max-width:980px){
      .phase2-home-inner{
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 32px 22px;
      }

      .phase2-home-visual{
        justify-content:center;
        order:2;
        overflow:visible;
      }

      .phase2-home-content{
        order:1;
      }

      .phase2-home-title{
        font-size:40px;
      }

      .phase2-visual-wrap{
        width:min(100%, 470px);
        min-height:470px;
        overflow:visible;
      }

      .phase2-visual-wrap .shape-navy{
        width: 220px;
        height: 285px;
        left: 78px;
        top: 100px;
      }

      .phase2-visual-wrap .shape-yellow{
        width: 180px;
        height: 235px;
        left: 155px;
        top: 205px;
      }

      .person-young{
        width: 300px;
        left: -8px;
        bottom: 0;
      }
    }

    .overlay-image{
      position: absolute;
      top: -30px;
      left: 20px;
      width: 465px;
      height: auto;
      z-index: 5;
      object-fit: contain;
      pointer-events: none;
    }

    .coins-badge{
      position: absolute;
      top: 320px;
      right: 50px;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border-radius: 999px;
      background: linear-gradient(
        90deg,
        rgba(255,255,255,0.45),
        rgba(112, 144, 175, 0.35)
      );
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,.4);
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
      z-index: 6;
    }

    .coin-img{
      width: 35px;
      height: 35px;
      right: -10px;
      object-fit: contain;
      flex: 0 0 42px;
    }

    .coins-badge-text{
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      line-height: 1;
    }

    .coins-value{
      font-family: "Montserrat", sans-serif;
      font-weight: 900;
      font-size: 18px;
      color: #ffffff;
      letter-spacing: -.4px;
      text-shadow: 0 2px 8px rgba(0,0,0,.18);
    }

    .coins-label{
      font-family: "Poppins", sans-serif;
      font-weight: 600;
      font-size: 12px;
      margin-top: 2px;
      color: rgba(255,255,255,.95);
      text-shadow: 0 2px 8px rgba(0,0,0,.14);
    }

    .project-badge{
      position: absolute;
      top: 80px;
      right: 320px;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border-radius: 999px;
      background: linear-gradient(
        90deg,
        rgba(191, 209, 236, 0.45),
        rgba(66, 112, 161, 0.35)
      );
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,.4);
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
      z-index: 6;
    }

    .project-main-icon{
      width: 40px;
      height: auto;
      max-height: 40px;
      object-fit: contain;
      flex: 0 0 40px;
    }

    .project-badge-text{
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      line-height: 1;
    }

    .project-value{
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: "Montserrat", sans-serif;
      font-weight: 900;
      font-size: 16px;
      color: #fff;
      letter-spacing: -.3px;
      text-shadow: 0 2px 8px rgba(0,0,0,.12);
      white-space: nowrap;
    }

    .project-inline-icon{
      width: 28px;
      height: 28px;
      object-fit: contain;
      display: inline-block;
    }

    .phase2-divider{
      width:100%;
      height:8px;
      background: transparent;
    }

    .project-intro{
      position: relative;
      width: 100%;
      margin: 0;
      border-radius: 0;
      background: linear-gradient(135deg, #f8fafc 0%, #eef3f9 100%);
      padding: 58px 80px 50px;
      min-height: 52vh;
      display: flex;
      align-items: center;
      overflow: hidden;
    }

    .project-intro::before{
      content: "";
      position: absolute;
      top: -120px;
      right: -80px;
      width: 320px;
      height: 320px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,194,74,.20), transparent 68%);
      pointer-events: none;
    }

    .project-intro::after{
      content: "";
      position: absolute;
      left: -100px;
      bottom: -120px;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(10,46,93,.10), transparent 70%);
      pointer-events: none;
    }

    .project-intro-layout{
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1380px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1.3fr .75fr;
      gap: 48px;
      align-items: start;
    }

    .project-copy-side{
      text-align: left;
      direction: ltr;
      padding-top: 6px;
    }

    .pageHero{
      text-align:left;
      padding: 0;
      margin: 0 0 14px 0;
    }

    .pageHero h1{
      margin:0;
      font-family:"Montserrat", sans-serif;
      font-weight:900;
      font-size:64px;
      line-height:1.03;
      color:#111;
      letter-spacing:-1px;
    }

    .pageHero h1 .navy{
      color:var(--navy);
    }

    .project-main-title{
      margin: 8px 0 16px 0;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:30px;
      line-height:1.18;
      color: var(--navy);
      letter-spacing:-.4px;
      text-align:left;
      max-width: 760px;
    }

    .project-main-desc{
      margin:0;
      max-width: 760px;
      font-size:18px;
      line-height:1.9;
      color:#243142;
      text-align:left;
    }

    .project-status-inline{
      margin: 14px 0 0 0;
      min-height: 24px;
      font-size: 14px;
      color: rgba(10,46,93,.72);
      text-align:left;
    }

    .project-meta-side{
      display:flex;
      justify-content:flex-end;
      align-items:flex-start;
    }

    #projectBox{
      width:100%;
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    .project-meta-layout{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      align-items:start;
      width: 100%;
    }

    .project-meta-col,
    .project-stack-col{
      display:flex;
      flex-direction:column;
      gap:14px;
      align-items:stretch;
    }

    .project-meta-col .pill,
    .project-stack-col .pill,
    #pStack.stack-pills-vertical .pill{
      min-width: 100%;
    }

    .pill{
      display:flex;
      align-items:center;
      justify-content:center;
      min-height: 48px;
      padding: 14px 18px;
      border-radius: 18px;
      border: 1px solid rgba(10,46,93,.12);
      background: rgba(255,255,255,.72);
      backdrop-filter: blur(8px);
      color: var(--navy);
      font-weight:800;
      font-family:"Montserrat", sans-serif;
      font-size: 16px;
      text-align:center;
      box-shadow: 0 10px 24px rgba(10,46,93,.06);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .pill:hover{
      transform: translateY(-2px);
      box-shadow: 0 16px 30px rgba(10,46,93,.10);
      border-color: rgba(10,46,93,.22);
    }

    .stack-title-pill{
      background: var(--navy) !important;
      color: #fff !important;
      border-color: var(--navy) !important;
      box-shadow: 0 14px 30px rgba(10,46,93,.18);
    }

    #pStack.stack-pills-vertical{
      display:flex;
      flex-direction:column;
      gap:14px;
      align-items:stretch;
    }

    @media(max-width:1100px){
      .project-intro-layout{
        grid-template-columns: 1fr;
        gap: 28px;
      }

      .project-meta-side{
        justify-content:flex-start;
      }

      .pageHero h1{
        font-size:48px;
      }

      .project-main-title{
        font-size:25px;
      }
    }

    @media(max-width:700px){
      .project-intro{
        padding: 36px 20px 34px;
      }

      .pageHero h1{
        font-size:36px;
      }

      .project-main-title{
        font-size:22px;
      }

      .project-main-desc{
        font-size:16px;
        line-height:1.8;
      }

      .project-meta-layout{
        grid-template-columns: 1fr;
      }
    }

    .project-shell{
      width:100%;
      max-width:none;
      margin: 0;
      padding: 8px 0 0;
      background: transparent;
    }

    .project-meta .pill,
    .project-stack-row .pill{
      min-height: 44px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding: 10px 16px;
      border-radius:999px;
      border:1px solid rgba(10,46,93,.18);
      background: rgba(10,46,93,.06);
      color: var(--navy);
      font-weight:900;
      font-family:"Montserrat", sans-serif;
      box-shadow: none;
    }

    .stack-title-pill{
      background: rgba(10,46,93,.14) !important;
      color: var(--navy) !important;
    }

    .project-feature-grid{
      margin-top: 34px;
      width:100%;
      max-width:none;
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
      padding: 0 24px;
      background: transparent;
    }

    .project-feature-box{
      position: relative;
      padding: 28px 24px 24px;
      min-height: 260px;
      background: #ffffff;
      color: #111;
      text-align: left;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      border-radius: 22px;
      border: 1px solid rgba(10,46,93,.08);
      box-shadow: 0 12px 30px rgba(10,46,93,.08);
      overflow: hidden;
      transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .project-feature-box:hover{
      transform: translateY(-6px);
      box-shadow: 0 18px 40px rgba(10,46,93,.14);
      border-color: rgba(10,46,93,.16);
    }

    .project-feature-box::before{
      content:"";
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:8px;
      border-radius: 22px 22px 0 0;
    }

    .project-feature-box.navy:first-child::before{
      background: linear-gradient(90deg, #0A2E5D, #1E4E8C);
    }

    .project-feature-box.black::before{
      background: linear-gradient(90deg, #B8C7DB, #D9E3F0);
    }

    .project-feature-box.navy:last-child::before{
      background: linear-gradient(90deg, #F2C66D, #FFE2A8);
    }

    .project-feature-title{
      margin: 10px 0 16px 0;
      font-family:"Montserrat", sans-serif;
      font-weight: 800;
      font-size: 24px;
      line-height: 1.2;
      color: #0A2E5D !important;
      letter-spacing: -.2px;
      text-align: left;
    }

    .project-feature-content,
    .project-feature-content .muted,
    .project-feature-content .small,
    .project-feature-box.black .project-feature-content,
    .project-feature-box.black .muted,
    .project-feature-box.black .small{
      color: #1A1A1A !important;
      font-family:"Poppins", sans-serif;
      font-size: 15px;
      line-height: 1.75;
      text-align: left;
    }

    .project-feature-content ul{
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .project-feature-content li{
      position: relative;
      padding-left: 18px;
      margin: 8px 0;
      line-height: 1.6;
    }

    .project-feature-content li::before{
      content: "•";
      position: absolute;
      left: 0;
      top: 0;
      color: #0A2E5D;
      font-size: 18px;
      line-height: 1.2;
    }

    .project-feature-box::after{
      content:"";
      position:absolute;
      width:140px;
      height:140px;
      right:-35px;
      bottom:-35px;
      border-radius:50%;
      background: rgba(10,46,93,.04);
      pointer-events:none;
    }

    .project-feature-box.black::after{
      background: rgba(185,199,219,.20);
    }

    .project-feature-box.navy:last-child::after{
      background: rgba(242,198,109,.16);
    }

    @media(max-width:980px){
      .project-feature-grid{
        grid-template-columns: 1fr;
        gap: 18px;
        padding: 0 18px;
      }

      .project-feature-box{
        min-height: auto;
      }

      .project-feature-title{
        font-size: 22px;
      }
    }

    .milestones-section{
      background: transparent;
      margin-top: 34px;
      padding: 8px 18px 6px;
    }

    .milestones-head{
      text-align:center;
      margin-bottom: 22px;
    }

    .milestones-head h3{
      margin:0 0 8px 0;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 34px;
      color: var(--navy);
      letter-spacing:-.4px;
    }

    .milestones-head .muted{
      font-size:14px;
    }

    #milestones{
      width:100%;
      max-width:none;
      margin:0;
      padding:0 40px;
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:26px;
      align-items:stretch;
    }

    .milestone{
      margin-top: 0;
      padding:18px;
      border-radius:18px;
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, #ffffff 0%, #f1f4f9 100%);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(0,0,0,0.08);
      box-shadow: 0 10px 30px rgba(0,0,0,.06);
      min-height: 220px;
    }

    .milestone::before{
      content: "";
      position: absolute;
      inset: 0;
      border-radius:18px;
      background: radial-gradient(circle at top right, rgba(255,200,100,0.15), transparent 60%);
      pointer-events: none;
    }

    @media(max-width:1000px){
      #milestones{
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media(max-width:600px){
      #milestones{
        grid-template-columns: 1fr;
      }
    }

    .milestone .row{
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .milestone ul{
      margin: 8px 0 0 0;
      padding-left: 18px;
    }

    .milestone b{
      line-height: 1.4;
    }

    .project-bottom-boxes{
      max-width: none;
      width: 100%;
      margin: 34px 0 0 0;
      padding: 42px 24px 36px;
      background: #E3E7EC;
    }

    @media(max-width:980px){
      .pageHero h1{ font-size:40px; }
      .project-main-title{ font-size:24px; }

      .project-feature-grid{
        grid-template-columns: 1fr;
        gap: 0;
      }

      .project-feature-box{
        min-height:auto;
      }
    }

    @media(max-width:700px){
      .pageHero h1{ font-size:34px; }
      .project-main-title{ font-size:22px; }
    }

    .heroCard{
      background: var(--navy) !important;
      border:1px solid rgba(255,255,255,.12) !important;
      box-shadow: 0 18px 50px rgba(0,0,0,.12) !important;
    }

    .heroText{
      color:#fff;
      text-align: center;
    }

    .heroStrong{
      font-family:"Montserrat", sans-serif;
      font-weight:900;
      font-size:18px;
      line-height:1.6;
      text-align:center;
    }

    .heroSub{
      margin-top:8px;
      font-family:"Poppins", sans-serif;
      font-weight:500;
      font-size:14.5px;
      opacity:.92;
      line-height:1.7;
    }

    .heroStatus{
      color: rgba(255,255,255,.92);
      font-size:14px;
      text-align:center;
    }

    .milestone b,
    .milestone .title,
    .milestone h4,
    .milestone h3{
      color: var(--navy) !important;
    }

    #submitBox h3::after{
      content:"";
      display:block;
      width:72px;
      height:5px;
      margin-top:12px;
      border-radius:999px;
      background: linear-gradient(90deg, var(--navy), var(--yellow));
    }

    .no-border{
      border: none !important;
      background: transparent !important;
      border-radius: 0 !important;
      box-shadow: none !important;
      padding: 0 !important;
    }

    .no-border:hover{
      color: var(--yellow);
      background: transparent !important;
      transform: translateY(-2px);
    }

/* ===== QOYN Phase 2 exact visual redesign override ===== */
:root{--qoyn-navy:#082f63;--qoyn-yellow:#ffc247;--qoyn-bg:#f4f7fc;--qoyn-text:#111827;}
html,body{width:100%;overflow-x:hidden;background:var(--qoyn-bg)!important;color:var(--qoyn-text)!important;}
body{font-family:"Poppins",Arial,sans-serif!important;} h1,h2,h3,b,.qoyn-logo{font-family:"Montserrat",Arial,sans-serif!important;}
.qoyn-topbar{position:relative!important;z-index:30!important;width:100%!important;background:#fff!important;border-bottom:1px solid rgba(10,46,93,.08)!important;box-shadow:0 8px 24px rgba(10,46,93,.06)!important;border-radius:0!important;margin:0!important;}
.qoyn-topbar-inner{max-width:none!important;width:100%!important;height:76px!important;margin:0!important;padding:0 45px!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:24px!important;}
.qoyn-logo{font-size:31px!important;line-height:1!important;letter-spacing:.5px!important;color:var(--qoyn-navy)!important;text-decoration:none!important;font-weight:900!important;}
.qoyn-right{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:34px!important;margin:0!important;}
.qoyn-link{border:0!important;background:transparent!important;box-shadow:none!important;color:#111827!important;border-radius:0!important;padding:7px 0!important;font-size:14px!important;font-weight:500!important;text-decoration:none!important;line-height:1!important;}
.qoyn-link:hover{color:var(--qoyn-navy)!important;transform:translateY(-1px)!important;}.phase3-nav-btn{font-weight:800!important;color:var(--qoyn-navy)!important;}
.qoyn-link.logout{border:1px solid rgba(10,46,93,.18)!important;border-radius:999px!important;background:#fff!important;padding:13px 25px!important;color:#111827!important;font-weight:500!important;}
.qoyn-link.logout:hover{background:var(--qoyn-navy)!important;color:#fff!important;border-color:var(--qoyn-navy)!important;}.back-link{border:0!important;background:transparent!important;padding:7px 0!important;}.topbar-logo{height:54px!important;width:auto!important;margin-left:2px!important;display:block!important;}
.phase2-home{position:relative!important;min-height:430px!important;height:430px!important;background:#edf4ff!important;display:block!important;overflow:hidden!important;border-bottom:1px solid rgba(10,46,93,.08)!important;}
.phase2-home:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 8% 45%, rgba(255,194,74,.95) 0 10px, transparent 11px),radial-gradient(circle at 9% 18%, rgba(10,46,93,.08) 0 13px, transparent 14px),radial-gradient(circle at 95% 50%, rgba(255,194,74,.9) 0 10px, transparent 11px),linear-gradient(135deg, rgba(255,255,255,.78), rgba(234,242,255,.88));pointer-events:none;}
.phase2-home:after{content:"";position:absolute;right:-76px;top:-105px;width:280px;height:280px;border:1.5px solid rgba(10,46,93,.22);border-radius:50%;opacity:.85;}
.phase2-home-inner{position:relative!important;z-index:2!important;width:100%!important;height:100%!important;margin:0!important;padding:0 64px!important;display:grid!important;grid-template-columns:48% 52%!important;gap:0!important;align-items:center!important;direction:ltr!important;}
.phase2-home-visual{height:100%!important;display:flex!important;align-items:flex-end!important;justify-content:center!important;overflow:visible!important;order:1!important;}.phase2-visual-wrap{width:570px!important;height:430px!important;min-height:430px!important;position:relative!important;display:block!important;overflow:visible!important;}
.phase2-visual-wrap:before{content:"";position:absolute;left:-130px;top:-105px;width:545px;height:545px;border-radius:0 0 56% 0;background:linear-gradient(135deg, rgba(255,255,255,.28), rgba(255,255,255,0));z-index:0;}
.phase2-visual-wrap:after{content:"";position:absolute;left:-72px;top:250px;width:128px;height:120px;background-image:radial-gradient(rgba(10,46,93,.13) 1.3px, transparent 1.3px);background-size:18px 18px;z-index:0;}
.phase2-visual-wrap .shape{position:absolute!important;border-radius:22px!important;box-shadow:none!important;}.phase2-visual-wrap .shape-yellow{width:160px!important;height:270px!important;left:320px!important;top:125px!important;background:var(--qoyn-yellow)!important;transform:skewX(-15deg) rotate(-7deg)!important;z-index:1!important;}.phase2-visual-wrap .shape-navy{width:310px!important;height:340px!important;left:120px!important;top:88px!important;background:linear-gradient(145deg,#082f63,#113f78)!important;transform:skewX(-16deg) rotate(-7deg)!important;z-index:2!important;overflow:visible!important;}.person-frame{display:block!important;}
.person-young{position:absolute!important;left:-98px!important;bottom:-18px!important;width:395px!important;max-width:none!important;height:auto!important;z-index:4!important;transform:skewX(16deg) rotate(7deg)!important;object-fit:contain!important;filter:drop-shadow(0 16px 24px rgba(10,46,93,.20));}
.overlay-image{position:absolute!important;left:56px!important;top:3px!important;width:430px!important;height:auto!important;z-index:5!important;object-fit:contain!important;pointer-events:none!important;}.coins-badge{top:278px!important;right:42px!important;z-index:9!important;background:#fff!important;border:1px solid rgba(10,46,93,.14)!important;border-radius:999px!important;padding:10px 18px 10px 10px!important;box-shadow:0 14px 32px rgba(10,46,93,.18)!important;gap:10px!important;backdrop-filter:none!important;}.coin-img{width:38px!important;height:38px!important;flex:0 0 38px!important;object-fit:contain!important;}.coins-value{font-size:18px!important;color:var(--qoyn-navy)!important;text-shadow:none!important;line-height:1!important;}.coins-label{font-size:11px!important;color:#111827!important;text-shadow:none!important;font-weight:700!important;line-height:1.2!important;}.project-badge{top:64px!important;right:300px!important;z-index:10!important;background:linear-gradient(135deg, rgba(10,46,93,.96), rgba(10,46,93,.86))!important;color:#fff!important;border-radius:999px!important;border:1px solid rgba(255,255,255,.35)!important;padding:10px 18px 10px 10px!important;gap:10px!important;box-shadow:0 16px 30px rgba(10,46,93,.22)!important;}.project-main-icon{width:42px!important;max-height:42px!important;object-fit:contain!important;}.project-value{font-size:16px!important;color:#fff!important;text-shadow:none!important;font-weight:900!important;}
.phase2-home-content{order:2!important;text-align:left!important;padding-left:24px!important;align-self:center!important;}.phase2-home-title{margin:0 0 26px!important;font-size:50px!important;line-height:1.12!important;letter-spacing:-1px!important;font-weight:900!important;color:#111!important;}.phase2-home-title .navy{color:var(--qoyn-navy)!important;}.phase2-home-title:after{content:"";display:block;width:54px;height:3px;background:linear-gradient(90deg,var(--qoyn-yellow) 0 72%,var(--qoyn-navy) 72%);margin-top:24px;border-radius:10px;}.phase2-home-text{font-size:15px!important;line-height:1.72!important;color:#1f2937!important;max-width:555px!important;margin:0!important;}
.phase2-divider{height:0!important;display:none!important;}.project-intro{position:relative!important;background:#f5f7fb!important;border-radius:0!important;box-shadow:none!important;border:0!important;margin:0!important;padding:30px 70px 26px!important;min-height:238px!important;overflow:hidden!important;}.project-intro:before{content:"";position:absolute;left:-58px;top:-12px;width:150px;height:170px;background:#082f63;border-radius:0 0 90px 0;z-index:0;}.project-intro:after{content:"";position:absolute;right:-70px;bottom:-95px;width:230px;height:230px;border-radius:50%;background:rgba(10,46,93,.06);z-index:0;}.project-intro-layout{position:relative!important;z-index:2!important;max-width:none!important;width:100%!important;margin:0!important;display:grid!important;grid-template-columns:48% 52%!important;gap:22px!important;align-items:center!important;}.project-copy-side{padding-left:72px!important;text-align:left!important;}.pageHero{margin:0 0 4px!important;}.pageHero h1{font-size:38px!important;line-height:1.12!important;letter-spacing:-.6px!important;font-weight:900!important;color:#050505!important;margin:0!important;}.pageHero h1 .navy{color:var(--qoyn-navy)!important;}.project-main-title{font-size:23px!important;margin:12px 0 12px!important;color:var(--qoyn-navy)!important;font-weight:800!important;}.project-main-title:after{content:"";display:block;width:42px;height:3px;background:var(--qoyn-yellow);border-radius:10px;margin-top:10px;}.project-main-desc{font-size:14px!important;line-height:1.65!important;color:#1f2937!important;max-width:500px!important;margin:0!important;}.project-status-inline{display:none!important;}.project-meta-side{justify-content:stretch!important;align-items:center!important;}#projectBox{width:100%!important;display:block;}.project-meta-layout{display:grid!important;grid-template-columns:1fr .82fr!important;gap:30px!important;align-items:center!important;position:relative!important;}.project-meta-layout:before{content:"";position:absolute;height:170px;width:1px;border-left:1px dashed rgba(10,46,93,.35);left:48.5%;top:18px;}.project-meta-col{display:flex!important;flex-direction:column!important;gap:12px!important;align-items:center!important;}.project-stack-col{display:flex!important;flex-direction:column!important;gap:10px!important;align-items:center!important;}.project-meta-col .pill,.project-stack-col .pill,#pStack.stack-pills-vertical .pill{width:260px!important;min-width:0!important;min-height:54px!important;border-radius:13px!important;background:#fff!important;border:0!important;box-shadow:0 10px 24px rgba(10,46,93,.07)!important;color:#10254a!important;font-size:14px!important;font-weight:800!important;justify-content:center!important;padding:13px 18px!important;}.project-meta-col .pill:nth-child(1):before{content:"🪙";font-size:24px;margin-right:14px;}.project-meta-col .pill:nth-child(2):before{content:"📋";font-size:22px;margin-right:14px;}.project-meta-col .pill:nth-child(3):before{content:"🏅";font-size:22px;margin-right:14px;}.stack-title-pill{background:var(--qoyn-navy)!important;color:#fff!important;border-radius:11px 11px 0 0!important;box-shadow:0 12px 26px rgba(10,46,93,.18)!important;min-height:40px!important;}#pStack.stack-pills-vertical{display:flex!important;flex-direction:column!important;gap:10px!important;align-items:center!important;}#pStack.stack-pills-vertical .pill{background:#fff!important;color:#10254a!important;text-transform:none!important;}#pStack.stack-pills-vertical .pill:nth-child(1):before{content:"🐍";font-size:23px;margin-right:14px;}#pStack.stack-pills-vertical .pill:nth-child(2):before{content:"🔶";font-size:23px;margin-right:14px;}#pStack.stack-pills-vertical .pill:nth-child(3):before{content:"🐳";font-size:23px;margin-right:14px;}
.project-shell{background:#f5f7fb!important;padding:18px 33px 24px!important;margin:0!important;}.project-feature-grid{margin:0!important;padding:0!important;display:grid!important;grid-template-columns:repeat(3,1fr)!important;gap:22px!important;}.project-feature-box{min-height:315px!important;background:#fff!important;border:1px solid rgba(10,46,93,.07)!important;border-radius:17px!important;padding:28px 28px 24px 28px!important;box-shadow:0 12px 30px rgba(10,46,93,.07)!important;overflow:hidden!important;position:relative!important;color:#111!important;}.project-feature-box:hover{transform:translateY(-4px)!important;box-shadow:0 18px 38px rgba(10,46,93,.11)!important;}.project-feature-box:before{content:""!important;position:absolute!important;left:0!important;right:0!important;top:0!important;height:8px!important;border-radius:17px 17px 0 0!important;background:var(--qoyn-navy)!important;}.project-feature-box.black:before{background:var(--qoyn-navy)!important;}.project-feature-box.navy:last-child:before{background:var(--qoyn-yellow)!important;}.project-feature-box:after{content:""!important;position:absolute!important;right:12px!important;bottom:0!important;width:112px!important;height:112px!important;border-radius:0!important;background:rgba(10,46,93,.035)!important;clip-path:polygon(30% 0,100% 0,100% 100%,0 100%,0 45%);}.project-feature-box.navy:last-child:after{background:rgba(255,194,74,.11)!important;}.project-feature-title{font-size:22px!important;margin:9px 0 24px 66px!important;color:#082f63!important;font-weight:900!important;position:relative!important;}.project-feature-title:before{content:"";position:absolute;left:-62px;top:-12px;width:46px;height:46px;border-radius:50%;background:#082f63;box-shadow:0 6px 16px rgba(10,46,93,.15);}.project-feature-box.black .project-feature-title:before{content:"★";display:grid;place-items:center;color:#fff;font-size:23px;line-height:46px;text-align:center;}.project-feature-box.navy:first-child .project-feature-title:before{content:"▣";display:grid;place-items:center;color:#fff;font-size:22px;line-height:46px;text-align:center;}.project-feature-box.navy:last-child .project-feature-title:before{content:"◇";display:grid;place-items:center;background:var(--qoyn-yellow);color:#fff;font-size:26px;line-height:46px;text-align:center;}.project-feature-title:after{content:"";display:block;width:34px;height:3px;background:var(--qoyn-yellow);border-radius:99px;margin-top:8px;}.project-feature-content,.project-feature-content .muted,.project-feature-content .small{font-size:13.5px!important;line-height:1.65!important;color:#111!important;}.project-feature-content li{padding-left:20px!important;margin:7px 0!important;}.project-feature-content li:before{color:#0a4d92!important;font-size:17px!important;}.project-feature-box.navy:last-child .project-feature-content li:before{color:var(--qoyn-yellow)!important;}.milestones-section,.project-bottom-boxes{max-width:1200px!important;margin:28px auto 0!important;}
@media(max-width:1100px){.qoyn-topbar-inner{padding:0 24px!important}.qoyn-right{gap:16px!important}.phase2-home{height:auto!important;min-height:auto!important}.phase2-home-inner{grid-template-columns:1fr!important;padding:32px 22px!important}.phase2-home-visual{order:1!important}.phase2-home-content{order:2!important;padding:0!important}.phase2-home-title{font-size:38px!important}.project-intro{padding:34px 22px!important}.project-intro:before{display:none}.project-intro-layout,.project-meta-layout{grid-template-columns:1fr!important}.project-copy-side{padding-left:0!important}.project-feature-grid{grid-template-columns:1fr!important}.project-meta-layout:before{display:none}.project-meta-col .pill,.project-stack-col .pill,#pStack.stack-pills-vertical .pill{width:100%!important}.project-shell{padding:18px!important}}

/* ===== Coins Distribution page exact redesign (only milestones section) ===== */
.milestones-section{position:relative!important;max-width:none!important;width:100%!important;margin:0!important;padding:42px 38px 52px!important;background:linear-gradient(135deg,#f8fbff 0%,#fff 44%,#f3f7ff 100%)!important;border:0!important;border-radius:0!important;box-shadow:none!important;overflow:hidden!important;isolation:isolate!important;}
.milestones-section::before{content:""!important;position:absolute!important;left:-92px!important;top:-135px!important;width:280px!important;height:280px!important;background:#062e63!important;border-radius:0 0 70% 0!important;z-index:-1!important;}
.milestones-section::after{content:""!important;position:absolute!important;right:-70px!important;top:30px!important;width:190px!important;height:230px!important;background-image:radial-gradient(rgba(35,101,190,.18) 1.7px, transparent 1.7px)!important;background-size:16px 16px!important;z-index:-1!important;}
.milestones-head{text-align:center!important;display:block!important;margin:0 auto 34px!important;padding:0!important;}
.milestones-head h3{display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:14px!important;margin:0!important;font-size:42px!important;line-height:1.06!important;letter-spacing:-1.2px!important;color:#082f63!important;font-weight:900!important;position:relative!important;}
.milestones-head h3::before{content:"🪙"!important;width:58px!important;height:58px!important;display:inline-grid!important;place-items:center!important;border-radius:22px!important;background:#fff7df!important;font-size:38px!important;filter:drop-shadow(0 10px 18px rgba(255,194,74,.22))!important;}
.milestones-head h3::after{content:""!important;position:absolute!important;left:50%!important;transform:translateX(-50%)!important;bottom:-30px!important;width:38px!important;height:4px!important;border-radius:99px!important;background:#ffc24a!important;box-shadow:18px 0 0 rgba(255,194,74,.45)!important;}
.milestones-head .muted{margin-top:12px!important;color:#5f6f85!important;font-size:16px!important;line-height:1.6!important;font-weight:500!important;}
#milestones{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:22px!important;max-width:1180px!important;margin:0 auto!important;padding:0!important;}
#milestones .milestone{position:relative!important;min-height:150px!important;margin:0!important;padding:28px 24px 22px 96px!important;background:#fff!important;border:1px solid rgba(10,46,93,.075)!important;border-radius:16px!important;box-shadow:0 14px 35px rgba(10,46,93,.055)!important;overflow:visible!important;transition:.25s ease!important;}
#milestones .milestone:hover{transform:translateY(-4px)!important;box-shadow:0 18px 44px rgba(10,46,93,.09)!important;}
#milestones .milestone::before{content:attr(data-icon)!important;position:absolute!important;left:25px!important;top:28px!important;width:50px!important;height:50px!important;border-radius:15px!important;display:grid!important;place-items:center!important;font-size:27px!important;background:#eef4ff!important;color:#1e6bdd!important;box-shadow:none!important;}
#milestones .milestone:nth-child(1)::before{background:#eef4ff!important;color:#2d6cdf!important;}#milestones .milestone:nth-child(2)::before{background:#f2edff!important;color:#7458ff!important;}#milestones .milestone:nth-child(3)::before{background:#e9fbf2!important;color:#22b883!important;}#milestones .milestone:nth-child(4)::before{background:#fff4dd!important;color:#f4a61b!important;}#milestones .milestone:nth-child(5)::before{background:#ffeceb!important;color:#ff5d5d!important;}#milestones .milestone:nth-child(6)::before{background:#e8fbf8!important;color:#21b9ad!important;}#milestones .milestone:nth-child(7)::before{background:#f1edff!important;color:#8064ff!important;}#milestones .milestone:nth-child(8)::before{background:#e9f7ff!important;color:#1982d9!important;}
#milestones .milestone .row{display:block!important;margin:0!important;padding:0!important;}#milestones .milestone b.ms-title,#milestones .milestone .row>b{display:block!important;color:#082f63!important;font-size:15.5px!important;line-height:1.38!important;font-weight:900!important;margin:0 0 9px!important;letter-spacing:-.15px!important;}
#milestones .milestone .pill,#milestones .milestone .ms-coins{display:inline-flex!important;align-items:center!important;gap:6px!important;height:27px!important;width:auto!important;padding:0 14px!important;border-radius:999px!important;background:#fff8e7!important;border:1px solid #f7e6bd!important;color:#082f63!important;font-size:13px!important;font-weight:800!important;box-shadow:none!important;}
#milestones .milestone .pill::before,#milestones .milestone .ms-coins::before{content:"🪙"!important;font-size:14px!important;}
#milestones .milestone .muted{color:#3d4b60!important;font-size:12.8px!important;line-height:1.55!important;margin-top:13px!important;}#milestones .milestone .muted b,#milestones .milestone .ms-label{color:#0a4d92!important;font-weight:900!important;}
#milestones .milestone .acceptance-title{display:block!important;margin-top:11px!important;color:#0a4d92!important;font-size:12.8px!important;font-weight:900!important;}#milestones .milestone ul{margin:7px 0 0!important;padding-left:17px!important;color:#273349!important;font-size:12.6px!important;line-height:1.55!important;}#milestones .milestone li{margin:3px 0!important;padding-left:0!important;}#milestones .milestone li::before{display:none!important;content:none!important;}
@media(max-width:980px){#milestones{grid-template-columns:1fr 1fr!important}.milestones-section{padding:34px 22px!important}.milestones-head h3{font-size:34px!important}}
@media(max-width:640px){#milestones{grid-template-columns:1fr!important}#milestones .milestone{padding-left:88px!important}}
  </style>
</head>
<body>

  <div class="qoyn-topbar">
    <div class="qoyn-topbar-inner">
      <a href="index.php" class="qoyn-logo">QOYN</a>

      <div class="qoyn-right">
  <a href="student-dashboard.php#home" class="qoyn-link no-border">Home</a>
  <a href="#project-intro" class="qoyn-link no-border">Your Project</a>

  <a id="goPhase3TopBtn" href="my_capstone.php" class="qoyn-link no-border phase3-nav-btn" style="display:none;">
    Go to Phase3
  </a>

  <a href="index.php" class="qoyn-link back-link" data-i18n="back">Back</a>
<a href="#" id="logoutBtn" class="qoyn-link logout" data-i18n="logout">Logout</a>
  <img src="uploads/MONKEY.png" class="topbar-logo" alt="Logo">
</div>
    </div>
  </div>

 

  <section class="phase2-home">
    <div class="phase2-home-inner">
      <div class="phase2-home-visual">
        <div class="phase2-visual-wrap">
          <div class="shape shape-yellow"></div>

          <div class="shape shape-navy person-frame">
            <img class="person-young" src="uploads/.png" alt="">
          </div>

          <img class="overlay-image" src="uploads/qQ.png" alt="">

          <div class="coins-badge">
            <img src="uploads/qoinn.png" class="coin-img" alt="">
            <div class="coins-badge-text">
              <span class="coins-value">+10,000</span>
              <span class="coins-label">coins</span>
            </div>

            <div class="project-badge">
              <img src="uploads/proo.png" class="project-main-icon" alt="">
              <div class="project-badge-text">
                <span class="project-value">
                  first project
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="phase2-home-content">
        <h1 class="phase2-home-title">
          <span data-i18n="congratulations">Congratulations!</span><br>
          <span class="navy" data-i18n="moved_to_phase2">You’ve moved to Phase 2.</span>
        </h1>
        <p class="phase2-home-text" data-i18n="phase2_intro_text">
          During this phase, you will focus on completing your individual project.
          Successfully finish your tasks and collect 20,000 coins to unlock the next stage
          and join the major project.
        </p>
      </div>
    </div>
  </section>

  <div class="phase2-divider"></div>

  <section id="project-intro" class="project-intro">
    <div class="project-intro-layout">
      <div class="project-copy-side">
        <div class="pageHero">
          <h1><span data-i18n="phase2">Phase2</span> - <span class="navy" data-i18n="your_project">Your Project</span></h1>
        </div>

        <h2 id="pTitle" class="project-main-title">ML Engine Project</h2>

        <p id="pDesc" class="project-main-desc">
          Develop an end-to-end machine learning pipeline using Python and various libraries, including data preprocessing, feature engineering, model training, and deployment.
        </p>

        <div id="status" class="project-status-inline" data-i18n="loading_project">Loading project...</div>
      </div>

      <div class="project-meta-side">
        <div id="projectBox" style="display:none">
          <div class="project-meta-layout">
            <div class="project-meta-col">
              <span class="pill"><span data-i18n="project_coins">Project Coins</span>: <b id="pCoins"></b></span>
              <span class="pill"><span data-i18n="project_id">Project ID</span>: <b id="pId"></b></span>
              <span class="pill"><span data-i18n="pass_score">Pass Score</span>: <b id="pPass"></b></span>
            </div>

            <div class="project-stack-col">
              <span class="pill stack-title-pill" data-i18n="suggested_stack">Suggested Stack</span>
              <div id="pStack" class="stack-pills-vertical"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="project-shell">
    <div class="project-feature-grid">
      <div class="project-feature-box navy">
        <h3 class="project-feature-title" data-i18n="deliverables">Deliverables</h3>
        <div id="pDeliverables" class="project-feature-content"></div>
      </div>

      <div class="project-feature-box black">
        <h3 class="project-feature-title" data-i18n="must_have">Must-have</h3>
        <div id="pMust" class="project-feature-content"></div>
      </div>

      <div class="project-feature-box navy">
        <h3 class="project-feature-title" data-i18n="nice_to_have">Nice-to-have</h3>
        <div id="pNice" class="project-feature-content"></div>
      </div>
    </div>

    <div class="milestones-section">
      <div class="milestones-head">
        <h3 data-i18n="coins_distribution">Coins Distribution</h3>
        <div class="muted" data-i18n="milestones_note">Each milestone has a clear deliverable + an acceptance checklist.</div>
      </div>

      <div id="milestones"></div>
    </div>
  </div>

  <div class="project-bottom-boxes">
    <div id="submitBox" class="card" style="display:none">
      <h3 data-i18n="submit_project">Submit the Project</h3>

      <label class="muted" data-i18n="repo_link_optional">GitHub/GitLab Link (optional if you uploaded a ZIP)</label>
      <input id="repoUrl" data-i18n-placeholder="repo_placeholder" placeholder="https://github.com/username/repo" />

      <label class="muted" style="display:block;margin-top:10px" data-i18n="upload_zip_optional">Upload ZIP (optional if you provided a repo)</label>
      <input id="zipFile" type="file" accept=".zip" />

      <label class="muted" style="display:block;margin-top:10px" data-i18n="notes_demo_run">Notes + Demo Video Link + Run Instructions</label>
      <textarea id="notes" data-i18n-placeholder="notes_placeholder" placeholder="How do I run the project? Demo link? Any notes?"></textarea>

      <button class="btn btn-primary" id="submitBtn" style="margin-top:12px" data-i18n="submit_project_for_review">Submit the Project for Review</button>
      <div class="muted small" style="margin-top:8px" data-i18n="submit_note">Note: You must upload a ZIP file or provide a repository link.</div>
    </div>

    <div id="resultBox" class="card" style="display:none">
      <h3 data-i18n="review_result">Review Result</h3>
      <div id="resMeta" class="muted"></div>
      <p id="resFeedback"></p>
      <div id="resFixes"></div>
    </div>
  </div>

<script>
  let REVIEW_MODE = "ai";

  const API_BASE = "../utbn-backend/api";
  let PROJECT_ID = null;
  let BASE_COINS = 2000;
  let MILESTONES = [];
  const PHASE3_REQUIRED_COINS = 20000;

  function el(id){ return document.getElementById(id); }
  function esc(s){ return (s ?? "").toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  function listToHtml(arr){
    if(!Array.isArray(arr) || arr.length===0) return '<span class="muted">' + t("dash") + '</span>';
    return '<ul>' + arr.map(x=>`<li>${esc(x)}</li>`).join('') + '</ul>';
  }

  function setPhase3CtaVisible(total){
  const btn = el("goPhase3TopBtn");
  if(!btn) return;
  btn.style.display = Number(total) >= PHASE3_REQUIRED_COINS ? "inline-flex" : "none";
}

  async function loadPhase3Cta(){
    try{
      const coins = await fetch(`${API_BASE}/coins.php?_t=${Date.now()}`, {credentials:"include"});
      const j = await coins.json();
      const total = parseInt(j.coins_total || "0", 10);
      setPhase3CtaVisible(total);
    }catch(e){
      console.error("phase3 cta error", e);
    }
  }

  function renderProject(payload){
    const j = payload || {};
    const p = j.project || {};

    PROJECT_ID = j.project_id ?? PROJECT_ID;
    BASE_COINS = j.base_coins ?? BASE_COINS;

    el("projectBox").style.display = "";
    el("submitBox").style.display = "";

    el("status").textContent = "";
    el("pId").textContent = PROJECT_ID ?? "-";
    el("pCoins").textContent = BASE_COINS ?? 2000;
    el("pPass").textContent = p.pass_score ?? 70;
    el("pTitle").textContent = p.title || t("project");
    el("pDesc").textContent = p.description || "";

    const stack = Array.isArray(p.stack) ? p.stack : [];
    el("pStack").innerHTML = stack.length ? stack.map(x=>`<span class="pill">${esc(x)}</span>`).join('') : '<span class="muted">' + t("dash") + '</span>';
    el("pStack").className = "stack-pills-vertical";

    el("pDeliverables").innerHTML = listToHtml(p.deliverables);
    const scope = p.scope || {};
    el("pMust").innerHTML = listToHtml(scope.must_have);
    el("pNice").innerHTML = listToHtml(scope.nice_to_have);

    MILESTONES = Array.isArray(p.milestones) ? p.milestones : (Array.isArray(p.tasks) ? p.tasks : []);

    const box = el("milestones");
    box.innerHTML = "";
    MILESTONES.forEach(m=>{
      const title = m.title || m.question || `${t("milestone")} #${m.id ?? ""}`;
      const coins = m.coins ?? "";
      const deliverable = m.deliverable || m.expected || "";
      const acceptance = Array.isArray(m.acceptance) ? m.acceptance : [];

      const div = document.createElement("div");
      div.className = "milestone";
      const icons = ["▤","♟","☁","▥","◎","▣","▶","▤"];
      div.setAttribute("data-icon", icons[box.children.length % icons.length]);
      div.innerHTML = `
        <div class="row">
          <b class="ms-title">${esc(title)}</b>
          <span class="ms-coins">${esc(coins)} ${t("coins")}</span>
        </div>
        ${deliverable ? `<div class="muted"><span class="ms-label">${t("deliverable")}:</span> ${esc(deliverable)}</div>` : ""}
        ${acceptance.length ? `
          <div>
            <span class="acceptance-title">${t("acceptance_checklist")}</span>
            <ul>${acceptance.map(a=>`<li>${esc(a)}</li>`).join("")}</ul>
          </div>` : ""}
      `;
      box.appendChild(div);
    });
  }

  async function loadProject(){
    try{
      const cached = localStorage.getItem("phase2_cached_project");
      if(cached){
        const payload = JSON.parse(cached);
        renderProject(payload);
      } else {
        el("status").textContent = t("loading_or_generating_project");
      }
    }catch(e){
      el("status").textContent = t("loading_or_generating_project");
    }

    let r;
    try{
      r = await fetch(`${API_BASE}/phase2_get_or_create_project.php`, {credentials:"include"});
    }catch(err){
      el("status").textContent = t("api_fetch_failed");
      console.error(err);
      return;
    }

    let j;
    try{
      j = await r.json();
    }catch(e){
      el("status").textContent = t("api_non_json");
      return;
    }

    if(!j.ok){
      el("status").textContent = "";
      return;
    }

    localStorage.setItem("phase2_cached_project", JSON.stringify(j));
    renderProject(j);
  }

  async function loadLastSubmission(){
    try{
      if(!PROJECT_ID) return;
      const r = await fetch(`${API_BASE}/phase2_status.php?project_id=${PROJECT_ID}&_t=${Date.now()}`, {credentials:"include"});
      const j = await r.json();
      if(!j.ok || !j.has) return;

      el("resultBox").style.display = "";
      const s = j.submission || {};

      if (s.status === "awaiting_company") {
        el("resultBox").style.display = "";
        el("resMeta").innerHTML = `
          <div style="margin-bottom:10px">${t("sent_to_company_waiting")}</div>
          <span class="pill">${t("status")}: <b>awaiting_company</b></span>
          <span class="pill">${t("submission_id")}: <b>${s.id}</b></span>
        `;
        el("resFeedback").textContent = t("waiting_company_review");
        el("resFixes").innerHTML = "";
        return;
      }

      const decision = s.decision || "";
      const cls = (decision === "PASS") ? "ok" : (decision === "FAIL" ? "bad" : "");

      const awarded = Number(s.coins_awarded ?? 0);
      const total   = Number(s.coins_total ?? 0);

      setPhase3CtaVisible(total);

      let coinMsg = "";
      if (decision === "PASS") {
        coinMsg = awarded > 0
          ? `${t("last_submit_added")} <b>${awarded}</b> ${t("coins")}.`
          : `${t("last_submit_passed_no_coins")}`;
      } else {
        coinMsg = `${t("last_submit_not_successful")}`;
      }

      el("resMeta").innerHTML = `
        <div style="margin-bottom:10px">${coinMsg}</div>
        <span class="pill">${t("score")}: <b>${s.score}</b></span>
        <span class="pill">${t("decision")}: <b class="${cls}">${decision}</b></span>
        <span class="pill">${t("coins_added")}: <b>${awarded}</b></span>
        <span class="pill">${t("coins_total")}: <b>${total}</b></span>
        <span class="pill">${t("submission_id")}: <b>${s.id}</b></span>
      `;
      el("resFeedback").textContent = s.feedback || "";
      const fixes = Array.isArray(s.fixes) ? s.fixes : [];
      el("resFixes").innerHTML = fixes.length
        ? "<h4>" + t("suggested_fixes") + "</h4><ul>" + fixes.map(x=>`<li>${esc(x)}</li>`).join("") + "</ul>"
        : "";

    }catch(e){
      console.error(e);
    }
  }

  async function submitProject(){
    if(!PROJECT_ID){ alert(t("no_project")); return; }

    const repo_url = (el("repoUrl").value || "").trim();
    const notes = (el("notes").value || "").trim();
    const f = el("zipFile").files[0];

    if(!repo_url && !f){ alert(t("must_upload_zip_or_repo")); return; }

    const fd = new FormData();
    fd.append("project_id", String(PROJECT_ID));
    fd.append("repo_url", repo_url);
    fd.append("notes", notes);
    fd.append("review_mode", REVIEW_MODE);
    if(f) fd.append("artifact", f);
    fd.append("review_mode", REVIEW_MODE);

    el("submitBtn").disabled = true;
    el("submitBtn").textContent = (REVIEW_MODE === "company") ? t("sending_to_company") : t("reviewing");

    try{
      const r = await fetch(`${API_BASE}/phase2_submit_project.php`, {
        method:"POST",
        credentials:"include",
        body: fd
      });
      const j = await r.json();
      if(!j.ok){ alert(t("error") + ": " + (j.error || "UNKNOWN")); return; }

      if (j.mode === "company" || j.status === "awaiting_company") {
        el("resultBox").style.display = "";
        el("resMeta").innerHTML = `
          <div style="margin-bottom:10px">${t("sent_project_to_company")}</div>
          <span class="pill">${t("status")}: <b>awaiting_company</b></span>
          <span class="pill">${t("submission_id")}: <b>${j.submission_id}</b></span>
        `;
        el("resFeedback").textContent = j.message || t("waiting_company_review");
        el("resFixes").innerHTML = "";
        return;
      }

      el("resultBox").style.display = "";
      const decision = j.decision || "";
      const cls = (decision === "PASS") ? "ok" : (decision === "FAIL" ? "bad" : "");

      const awarded = Number(j.coins_awarded ?? 0);
      const total   = Number(j.coins_total ?? 0);
      const prev    = Number(j.prev_best_total ?? 0);
      const bestNow = Number(j.new_best_total ?? Math.max(prev, total));

      setPhase3CtaVisible(bestNow);

      let coinMsg = "";
      if (decision === "PASS") {
        if (awarded > 0) {
          coinMsg = `${t("congrats_added")} <b>${awarded}</b> ${t("coins")}.`;
        } else {
          coinMsg = `${t("passed_no_extra_coins")} <b>${prev}</b> ${t("coins_or_more")}.`;
        }
      } else {
        coinMsg = `${t("coins_only_on_pass")}`;
      }

      el("resMeta").innerHTML = `
        <div style="margin-bottom:10px">${coinMsg}</div>
        <span class="pill">${t("score")}: <b>${j.score}</b></span>
        <span class="pill">${t("decision")}: <b class="${cls}">${decision}</b></span>
        <span class="pill">${t("coins_added_now")}: <b>${awarded}</b></span>
        <span class="pill">${t("eligible_this_submit")}: <b>${total}</b></span>
        <span class="pill">${t("prev_best")}: <b>${prev}</b></span>
        <span class="pill">${t("best_now")}: <b>${bestNow}</b></span>
        <span class="pill">${t("submission_id")}: <b>${j.submission_id}</b></span>
      `;
      el("resFeedback").textContent = j.feedback || "";
      const fixes = Array.isArray(j.fixes) ? j.fixes : [];
      el("resFixes").innerHTML = fixes.length
        ? "<h4>" + t("suggested_fixes") + "</h4><ul>" + fixes.map(x=>`<li>${esc(x)}</li>`).join("") + "</ul>"
        : "";
    } finally {
      el("submitBtn").disabled = false;
      el("submitBtn").textContent = t("submit_project_for_review");
    }
  }

  el("submitBtn").addEventListener("click", submitProject);

  (async ()=>{
    await loadPhase3Cta();
    await loadProject();
    await loadLastSubmission();
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
