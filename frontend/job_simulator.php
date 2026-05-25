<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title data-i18n="job_simulator_page_title">QOYN | AI Job Simulator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="assets/js/i18n.js"></script>
  <script defer src="assets/js/job_simulator.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --navy-2:#082754;
      --yellow:#FFC24A;
      --yellow-2:#FFD875;
      --bg:#F6F7F9;
      --card:#ffffff;
      --text:#0A1938;
      --muted:#6D7890;
      --line:#E8ECF3;
      --shadow:0 20px 45px rgba(10,46,93,.10);
      --shadow-soft:0 10px 24px rgba(10,46,93,.08);
      --container:1440px;
    }

    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
   body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #F6F7F9;
    position: relative;
    overflow-x: hidden;
}

/* الطبقة الأساسية */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    /* تدرج ناعم مثل الصورة */
    background: 
        radial-gradient(circle at 20% 30%, rgba(10, 46, 93, 0.06), transparent 40%),
        radial-gradient(circle at 80% 70%, rgba(10, 46, 93, 0.05), transparent 40%),
        linear-gradient(180deg, #F6F7F9 0%, #FFFFFF 100%);

    z-index: -3;
}

/* الموجة الكبيرة اليمين */
body::after {
    content: "";
    position: fixed;
    right: -200px;
    top: 0;
    width: 600px;
    height: 100%;
    background: radial-gradient(circle, rgba(10, 46, 93, 0.08), transparent 70%);
    border-radius: 50%;
    z-index: -2;
}

/* نقاط (dots) مثل الصورة */
.bg-dots {
    position: fixed;
    width: 120px;
    height: 120px;
    background-image: radial-gradient(#0A2E5D 1px, transparent 1px);
    background-size: 10px 10px;
    opacity: 0.2;
    z-index: -1;
}

/* نقطة يسار */
.bg-dots.left {
    left: 50px;
    top: 300px;
}

/* نقطة يمين */
.bg-dots.right {
    right: 80px;
    top: 200px;
}


    .nav-wrap{
  position:sticky;
  top:0;
  z-index:999;
  padding:14px 0;
  background:#fff;
  box-shadow:0 8px 28px rgba(0,0,0,.05);
  border-bottom:1px solid rgba(10,46,93,.06);
}

.nav{
  display:flex;
  align-items:center;
  width:calc(100% - 80px);
  max-width:none;
  margin:0 auto;
  gap:20px;
  direction:ltr;
  overflow:hidden;
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
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.14));
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
  gap:10px;
  list-style:none;
  margin:0;
  padding:0;
  flex-wrap:nowrap;
}

.nav-links a{
  text-decoration:none;
  color:#111;
  font-weight:700;
  font-size:13px;
  padding:8px 12px;
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
    .page{padding:28px 0 80px}.content-shell{max-width:var(--container);margin:0 auto;padding:0 48px;position:relative}
    .hero{display:grid;grid-template-columns:1.02fr .98fr;align-items:center;gap:20px;min-height:420px;margin-bottom:18px;position:relative}
    .hero-main{padding:18px 0 0}.hero-actions-top{display:none}.eyebrow{
      display:inline-flex;align-items:center;gap:11px;width:max-content;margin-bottom:23px;
      padding:9px 16px;border-radius:999px;background:#fff;border:1px solid rgba(10,46,93,.12);
      color:var(--navy);font-weight:700;font-size:14px;box-shadow:0 8px 18px rgba(10,46,93,.05)
    }
    .eyebrow::before{content:"✦";display:grid;place-items:center;width:21px;height:21px;border-radius:50%;color:var(--yellow);background:rgba(255,194,74,.14)}
    .hero-copy h1{margin:0 0 17px;font-family:"Montserrat",sans-serif;font-size:56px;line-height:1.05;letter-spacing:-1.6px;color:var(--navy);max-width:580px;font-weight:900}
    .hero-copy h1::first-line{color:var(--navy)}
    .hero-copy h1{background:linear-gradient(180deg,var(--navy) 0 52%,var(--yellow) 52% 100%);-webkit-background-clip:text;background-clip:text;color:transparent}
    .hero-copy p{margin:0 0 26px;font-size:15.5px;line-height:1.9;color:var(--muted);max-width:560px;font-weight:500}
    .hero-cta-row{display:flex;align-items:center;gap:22px;flex-wrap:wrap;margin-bottom:27px}.hero-primary-btn{
      display:inline-flex;align-items:center;justify-content:center;gap:18px;text-decoration:none;min-width:196px;
      padding:16px 27px;border-radius:22px;background:var(--navy);color:#fff;font-family:"Montserrat",sans-serif;font-size:15px;font-weight:800;box-shadow:0 17px 32px rgba(10,46,93,.22);transition:.25s ease
    }
    .hero-primary-btn::after{content:"→";font-size:22px;font-weight:500}.hero-primary-btn:hover{transform:translateY(-4px);background:#082754;box-shadow:0 22px 38px rgba(10,46,93,.28)}
    .hero-watch{display:inline-flex;align-items:center;gap:16px;text-decoration:none;color:var(--navy);font-weight:800;font-size:14px;transition:.25s ease}.hero-watch:hover{transform:translateY(-3px);color:#082754}
    .hero-watch-icon{width:52px;height:52px;border-radius:50%;display:grid;place-items:center;background:#fff;border:1px solid var(--line);box-shadow:0 12px 24px rgba(10,46,93,.09);color:var(--navy);font-size:19px}
    .hero-brand-strip{display:flex;align-items:center;gap:29px;flex-wrap:wrap;color:#0E254A;font-size:13.5px;font-weight:600}.hero-brand-item{display:flex;align-items:center;gap:9px}.hero-brand-dot{width:16px;height:16px;border-radius:50%;background:var(--navy);position:relative}.hero-brand-dot::after{content:"✓";position:absolute;inset:0;display:grid;place-items:center;color:#fff;font-size:10px;font-weight:900}

    .hero-side{min-height:420px;display:flex;align-items:center;justify-content:center;position:relative}.hero-visual-scene{position:relative;width:620px;max-width:100%;height:410px}.hero-orbit{
      position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:272px;height:272px;border-radius:50%;background:var(--navy);box-shadow:0 30px 55px rgba(10,46,93,.24);z-index:2;overflow:hidden
    }
    .hero-orbit::before{content:"";position:absolute;inset:18px;border-radius:50%;background:repeating-radial-gradient(circle at 50% 50%,rgba(255,255,255,.15) 0 1px,transparent 2px 10px);opacity:.55}.hero-orbit::after{content:"✦";position:absolute;inset:0;display:grid;place-items:center;color:white;font-size:86px;text-shadow:0 0 28px rgba(255,255,255,.35)}
    .hero-visual-scene::before,.hero-visual-scene::after{content:"";position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);border-radius:50%;border:1.5px solid rgba(10,46,93,.17);width:385px;height:385px}.hero-visual-scene::after{width:315px;height:315px;border-color:rgba(255,194,74,.42)}
        .hero-bubble{position:absolute;display:grid;place-items:center;border-radius:50%;background:#fff;border:1px solid rgba(10,46,93,.10);box-shadow:0 18px 34px rgba(10,46,93,.12);z-index:4;color:var(--navy);transition:.25s ease}
    .hero-bubble svg{width:50px;height:50px}.hero-bubble svg *{stroke:var(--navy)!important}.hero-bubble svg rect,.hero-bubble svg circle{fill:none!important}.hero-bubble.medium{width:92px;height:92px}.hero-bubble.large{width:102px;height:102px}
    .bubble-mail{left:172px;top:54px}.bubble-folder{right:118px;top:70px}.bubble-code{left:158px;top:242px}.bubble-design{right:108px;top:260px}
    .bubble-coin{left:50%;top:22px;transform:translateX(-50%);width:40px!important;height:40px!important;background:var(--yellow);border:0;box-shadow:0 14px 22px rgba(255,194,74,.30)}.bubble-coin svg{display:none}.bubble-coin::before{content:"+";font-size:24px;color:var(--navy);font-weight:800;line-height:1}
    .bubble-tag{position:absolute;z-index:5;color:var(--navy);font-weight:800;font-size:14px;background:transparent;box-shadow:none;padding:0}.bubble-tag::after{display:block;color:#7B8498;font-size:12px;font-weight:500;margin-top:5px}.tag-code{left:92px;top:260px}.tag-code::after{content:"Analyze"}.tag-design{right:20px;top:280px}.tag-design::after{content:"Enhance"}
    .hero-mini-pill{position:absolute;z-index:5;color:var(--navy);font-weight:800;font-size:14px}.hero-mini-pill::after{display:block;color:#7B8498;font-size:12px;font-weight:500;margin-top:5px}.pill-left{left:100px;top:80px}.pill-left::after{content:"Evaluate"}.pill-right{right:0;top:128px}.pill-right::after{content:"Showcase"}
    .hero-floating-dot{position:absolute;border-radius:50%;background:var(--navy);z-index:3}.dot-1{width:13px;height:13px;right:198px;bottom:32px}.dot-2{width:8px;height:8px;left:178px;top:150px;background:var(--yellow)}.dot-3{display:none}.dot-4{width:10px;height:10px;right:158px;top:38px}.dot-5{width:8px;height:8px;right:84px;top:254px;background:var(--yellow)}.dot-6{width:7px;height:7px;left:72px;bottom:46px;background:#C8D3E8}


    .pro-strip{display:grid;grid-template-columns:repeat(3,1fr);gap:0;background:rgba(255,255,255,.78);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow-soft);margin:0 0 12px;overflow:hidden}.pro-box{display:flex;align-items:center;gap:20px;padding:20px 28px;min-height:106px;transition:.25s ease}.pro-box:not(:last-child){border-inline-end:1px solid var(--line)}.pro-box:hover{background:#fff;transform:translateY(-2px)}.pro-icon{width:60px;height:60px;border-radius:50%;display:grid;place-items:center;background:var(--navy);color:#fff;box-shadow:0 12px 20px rgba(10,46,93,.18);flex-shrink:0}.pro-icon svg{width:31px;height:31px}.pro-box h4{margin:0 0 6px;font-family:"Montserrat",sans-serif;color:var(--navy);font-size:17px;font-weight:800}.pro-box p{margin:0;color:#20304C;font-size:13px;line-height:1.6;font-weight:500}

    .simulation-area{display:grid;grid-template-columns:1.9fr 1fr;gap:42px;align-items:stretch;margin-top:12px}.form-card,.tips-card,.feature-card{background:rgba(255,255,255,.88);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow-soft)}.form-card{padding:28px 40px 20px;position:relative;overflow:hidden}.section-title{margin:0 0 6px;font-family:"Montserrat",sans-serif;font-size:27px;color:var(--navy);font-weight:900;letter-spacing:-.5px}.section-title::after,.tips-copy h3::after{content:"";display:block;width:36px;height:3px;background:var(--yellow);border-radius:999px;margin-top:7px}.section-subtitle{margin:0 0 18px;color:var(--muted);font-size:12.5px;line-height:1.55;max-width:720px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px 28px}.field{display:grid;grid-template-columns:46px 1fr;gap:10px 14px;align-items:start}.field::before{content:"";grid-row:1 / span 3;width:46px;height:46px;border-radius:50%;background:#F1F4FA;display:block}.field:nth-child(1)::before{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%230A2E5D' stroke-width='2'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='12' cy='7' r='4'/%3E%3C/svg%3E") center/22px no-repeat}.field:nth-child(2)::before{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='23' height='23' viewBox='0 0 24 24' fill='none' stroke='%230A2E5D' stroke-width='2'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='17 8 12 3 7 8'/%3E%3Cline x1='12' y1='3' x2='12' y2='15'/%3E%3C/svg%3E") center/23px no-repeat}.field:nth-child(3)::before{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%230A2E5D' stroke-width='2'%3E%3Cpath d='M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4.93'/%3E%3Cpath d='M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 19.07'/%3E%3C/svg%3E") center/22px no-repeat}.field.full{grid-column:1 / -1}label{font-size:13px;font-weight:800;color:#0A1938}input,select{width:100%;padding:12px 14px;border:1px solid #DDE3ED;border-radius:8px;outline:none;background:#fff;font-family:inherit;font-size:13.5px;color:#0A1938;transition:.25s ease}input:focus,select:focus{border-color:var(--navy);box-shadow:0 0 0 4px rgba(10,46,93,.08)}.hint{font-size:11.5px;color:#7B8498}.action-row{display:flex;align-items:center;gap:18px;flex-wrap:wrap;margin-top:20px}button{border:0;cursor:pointer;padding:15px 31px;border-radius:14px;background:var(--navy);color:#fff;font-family:"Montserrat",sans-serif;font-weight:800;font-size:13.5px;box-shadow:0 14px 27px rgba(10,46,93,.23);transition:.25s ease}button::after{content:"→";margin-inline-start:22px;font-size:18px;font-weight:500}button:hover{transform:translateY(-3px);background:#082754;box-shadow:0 20px 32px rgba(10,46,93,.27)}.back-link{text-decoration:none;color:var(--navy);font-weight:800;padding:13px 24px;border-radius:14px;border:1px solid #DDE3ED;background:#fff;transition:.25s ease}.back-link::before{content:"←";margin-inline-end:12px}.back-link:hover{transform:translateY(-3px);box-shadow:0 12px 24px rgba(10,46,93,.09)}#result{display:none;margin-top:22px;padding:16px 18px;border-radius:14px;background:#F7FAFF;border:1px solid var(--line);color:var(--navy);font-weight:700;line-height:1.8}

    .tips-card{margin:0;padding:30px 36px;display:flex;flex-direction:column;justify-content:center;gap:18px;position:relative}.tips-card::before{content:"🎯";position:absolute;right:36px;top:26px;width:82px;height:82px;border-radius:50%;display:grid;place-items:center;font-size:52px}.tips-copy h3{max-width:260px;margin:0 0 22px;font-family:"Montserrat",sans-serif;font-size:22px;line-height:1.22;color:var(--navy);font-weight:900}.tips-copy p{display:none}.tips-points{display:grid;gap:10px}.tip{display:grid;grid-template-columns:48px 1fr;align-items:center;gap:16px;background:#fff;border-radius:13px;padding:13px 16px;box-shadow:0 10px 22px rgba(10,46,93,.06);font-size:12.8px;line-height:1.55;color:#10244A;font-weight:600}.tip-dot{width:48px;height:48px;margin:0;border-radius:50%;background:#F1F4FA;box-shadow:none}.tip:nth-child(1) .tip-dot{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%230A2E5D' stroke-width='2'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'/%3E%3Cpolyline points='14 2 14 8 20 8'/%3E%3Cline x1='16' y1='13' x2='8' y2='13'/%3E%3Cline x1='16' y1='17' x2='8' y2='17'/%3E%3C/svg%3E") center/22px no-repeat}.tip:nth-child(2) .tip-dot{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%230A2E5D'%3E%3Cpath d='M12 .5A11.5 11.5 0 0 0 8.36 22.9c.58.1.8-.25.8-.56v-2c-3.25.7-3.94-1.39-3.94-1.39-.53-1.35-1.3-1.71-1.3-1.71-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.73 1.27 3.4.97.1-.75.41-1.27.74-1.56-2.6-.3-5.33-1.3-5.33-5.78 0-1.28.46-2.32 1.2-3.14-.12-.3-.52-1.5.12-3.1 0 0 .98-.31 3.2 1.2a11.1 11.1 0 0 1 5.82 0c2.22-1.51 3.2-1.2 3.2-1.2.64 1.6.24 2.8.12 3.1.75.82 1.2 1.86 1.2 3.14 0 4.49-2.74 5.48-5.35 5.77.42.36.8 1.08.8 2.18v3.23c0 .31.21.67.81.56A11.5 11.5 0 0 0 12 .5Z'/%3E%3C/svg%3E") center/23px no-repeat}.tip:nth-child(3) .tip-dot{background:#F1F4FA url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='23' height='23' viewBox='0 0 24 24' fill='none' stroke='%230A2E5D' stroke-width='2'%3E%3Crect x='2' y='7' width='20' height='14' rx='2'/%3E%3Cpath d='M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16'/%3E%3C/svg%3E") center/23px no-repeat}
    
      
    .feature-icon{
      width:36px;
    height:36px;
    border-radius:18px;
    display:grid;
    place-items:center;background:#F1F4FA;margin-bottom:14px;position:relative;z-index:2}.feature-card h4{margin:0 0 9px;font-family:"Montserrat",sans-serif;color:var(--navy);font-size:19px}.feature-card p{margin:0;color:var(--muted);font-size:13px;line-height:1.75;position:relative;z-index:2}

    @media(max-width:680px){.nav{height:auto;min-height:72px;align-items:flex-start;flex-direction:column;padding:14px 18px}.nav-links{gap:8px}.page{padding-top:18px}.content-shell{padding:0 14px}.hero-copy h1{font-size:34px}.hero-side{min-height:360px}.hero-visual-scene{height:360px}.hero-visual-scene::before{width:300px;height:300px}.hero-visual-scene::after{width:245px;height:245px}.hero-orbit{width:220px;height:220px}.hero-brand-strip{gap:12px}.form-card,.tips-card{padding:22px 18px}.form-grid{grid-template-columns:1fr}.field{grid-template-columns:42px 1fr}.field::before{width:42px;height:42px}.bubble-tag,.hero-mini-pill{display:none}}
  </style>
</head>
<body>

<div class="bg-dots left"></div>
<div class="bg-dots right"></div>


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
      <li><a href="my_courses.php">My courses</a></li>
      <li><a href="courses.php">All courses</a></li>
      <li><a href="job_simulator.php" class="active">AI Job Simulator</a></li>
      <li><a href="login.html" class="nav-logout">Logout</a></li>
    </ul>
  </nav>
</header>

  <div class="page">
    <div class="content-shell">

      <section class="hero">
        <div class="hero-main">
          <div>
            <div class="hero-actions-top">
              
            </div>

            <div class="eyebrow" data-i18n="job_sim_eyebrow">AI Powered Career Check</div>

            <div class="hero-top">
              <div class="hero-copy">
                <h1 data-i18n="job_sim_hero_title">AI Job Readiness Simulator</h1>
                <p data-i18n="job_sim_hero_desc">
                  Upload your CV, add your GitHub or project link, choose your target role,
                  and get a more premium, visual, and exciting readiness experience that feels
                  like a real modern AI product.
                </p>

                <div class="hero-cta-row">
                  <a href="#start-simulation" class="hero-primary-btn" data-i18n="job_sim_start_btn">Start Analysis</a>

                  <a href="#start-simulation" class="hero-watch">
                    <span class="hero-watch-icon">▶</span>
                    <span data-i18n="job_sim_side_title">What it checks</span>
                  </a>
                </div>

                <div class="hero-brand-strip">
                  <div class="hero-brand-item"><span class="hero-brand-dot"></span> GitHub</div>
                  <div class="hero-brand-item"><span class="hero-brand-dot"></span> CV Scan</div>
                  <div class="hero-brand-item"><span class="hero-brand-dot"></span> AI Match</div>
                  <div class="hero-brand-item"><span class="hero-brand-dot"></span> Career Fit</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-side">
                    <div class="hero-visual-scene">
            <div class="hero-orbit"></div>

            <div class="hero-bubble medium bubble-mail" aria-hidden="true">
              <svg viewBox="0 0 64 64" width="50" height="50" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 22h36a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H14a4 4 0 0 1-4-4V26a4 4 0 0 1 4-4Z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                <path d="M12 27l20 14 20-14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M24 22v-4a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
            </div>

            <div class="hero-bubble medium bubble-folder" aria-hidden="true">
              <svg viewBox="0 0 64 64" width="50" height="50" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 20a5 5 0 0 1 5-5h14l6 6h18a5 5 0 0 1 5 5v25a5 5 0 0 1-5 5H13a5 5 0 0 1-5-5V20Z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                <path d="M8 27h48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
            </div>

            <div class="hero-bubble medium bubble-code" aria-hidden="true">
              <svg viewBox="0 0 64 64" width="50" height="50" xmlns="http://www.w3.org/2000/svg">
                <path d="M25 20 13 32l12 12" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M39 20l12 12-12 12" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M35 16 29 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
              </svg>
            </div>

            <div class="hero-bubble medium bubble-design" aria-hidden="true">
              <svg viewBox="0 0 64 64" width="50" height="50" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 48l5-15 24-24a6 6 0 0 1 8 8L29 41 16 48Z" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                <path d="M40 14l10 10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M21 33l10 10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
            </div>

            <div class="hero-bubble bubble-coin" aria-hidden="true"></div>

            <div class="bubble-tag tag-code">Code</div>
            <div class="bubble-tag tag-design">Design</div>

            <div class="hero-mini-pill pill-left">Skills</div>
            <div class="hero-mini-pill pill-right">Portfolio</div>

            <div class="hero-floating-dot dot-1"></div>
            <div class="hero-floating-dot dot-2"></div>
            <div class="hero-floating-dot dot-3"></div>
            <div class="hero-floating-dot dot-4"></div>
            <div class="hero-floating-dot dot-5"></div>
            <div class="hero-floating-dot dot-6"></div>
          </div>

        </div>
      </section>

      <section class="pro-strip">
        <div class="pro-box">
          <div class="pro-icon">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M12 3l7 4v10l-7 4-7-4V7l7-4z" stroke="currentColor" stroke-width="2"/>
              <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div>
            <h4 data-i18n="job_sim_box1_title">Professional Experience</h4>
            <p data-i18n="job_sim_box1_desc">Cleaner layout, stronger visual hierarchy, and a more polished AI product feel.</p>
          </div>
        </div>

        <div class="pro-box">
          <div class="pro-icon">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M4 19h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M7 15V9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M12 15V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M17 15v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <div>
            <h4 data-i18n="job_sim_box2_title">Smarter Presentation</h4>
            <p data-i18n="job_sim_box2_desc">Your simulator now looks more like a premium dashboard, not just a plain upload form.</p>
          </div>
        </div>

        <div class="pro-box">
          <div class="pro-icon">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M12 8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <div>
            <h4 data-i18n="job_sim_box3_title">Fast Navigation</h4>
            <p data-i18n="job_sim_box3_desc">Added a clear return button so the user can jump back to the main page instantly.</p>
          </div>
        </div>
      </section>

      <div class="simulation-area">
      <section class="form-card" id="start-simulation">
        <h2 class="section-title" data-i18n="job_sim_form_title">Start your simulation</h2>
        <p class="section-subtitle" data-i18n="job_sim_form_desc">
          Fill in the fields below and launch your AI job simulation. The design now feels more premium,
          more visual, and much closer to a modern startup dashboard.
        </p>

        <div class="form-grid">
          <div class="field">
            <label for="role_key" data-i18n="job_sim_choose_role">Choose Target Role</label>
            <select id="role_key">
              <option value="ml_engineer" data-i18n="job_role_ml_engineer">Machine Learning Engineer</option>
              <option value="fullstack" data-i18n="job_role_fullstack">Full Stack Developer</option>
              <option value="pentester" data-i18n="job_role_pentester">Pentester</option>
              <option value="algorithm_engineer" data-i18n="job_role_algorithm_engineer">Algorithm Engineer</option>
            </select>
          </div>

          <div class="field">
            <label for="cv_file" data-i18n="job_sim_upload_cv">Upload CV</label>
            <input type="file" id="cv_file" accept=".pdf,.doc,.docx">
            <div class="hint" data-i18n="job_sim_cv_hint">Accepted: PDF, DOC, DOCX</div>
          </div>

          <div class="field full">
            <label for="github_url" data-i18n="job_sim_github_label">GitHub / Project Link</label>
            <input type="text" id="github_url" placeholder="https://github.com/username/project" data-i18n-placeholder="job_sim_github_placeholder">
            <div class="hint" data-i18n="job_sim_github_hint">Add your public GitHub repository or project link</div>
          </div>
        </div>

        <div class="action-row">
          <button id="startBtn" data-i18n="job_sim_start_btn">Start Analysis</button>
        </div>

        <div id="result"></div>
      </section>

      <section class="tips-card">
        <div class="tips-copy">
          <h3 data-i18n="job_sim_tips_title">How to look stronger in the simulation</h3>
          <p data-i18n="job_sim_tips_desc">
            Small changes in your submission can make your profile look much better. Keep your CV clean, your GitHub public,
            and your target role aligned with the skills you actually show.
          </p>
        </div>

        <div class="tips-points">
          <div class="tip">
            <div class="tip-dot"></div>
            <span data-i18n="job_sim_tip1">Use a CV that clearly shows tools, projects, and measurable outcomes.</span>
          </div>
          <div class="tip">
            <div class="tip-dot"></div>
            <span data-i18n="job_sim_tip2">Add a public GitHub repo with a solid README and visible commits.</span>
          </div>
          <div class="tip">
            <div class="tip-dot"></div>
            <span data-i18n="job_sim_tip3">Pick a target role that actually matches your strongest technical skills.</span>
          </div>
        </div>
      </section>
      </div>



      


    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const currentLangText = document.getElementById("currentLangText");
      const langOptions = document.querySelectorAll(".lang-option");

      function updateLangButton() {
        const lang = (typeof getCurrentLang === "function") ? getCurrentLang() : (localStorage.getItem("lang") || "en");
        if (currentLangText) {
          currentLangText.textContent = lang === "ar" ? "العربية" : "English";
        }
        langOptions.forEach(btn => {
          btn.classList.toggle("active", btn.dataset.lang === lang);
        });
      }

      updateLangButton();
      document.addEventListener("languageChanged", updateLangButton);
    });
  </script>

</body>
</html>