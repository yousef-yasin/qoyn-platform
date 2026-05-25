<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") { header("Location: index.php"); exit; }
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Phase 2</title>
  <link rel="stylesheet" href="assets/css/style.css"/>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#082B5F;
      --navy2:#0B3675;
      --bg:#F6F8FC;
      --line:#E7ECF5;
      --text:#081D42;
      --muted:#66758E;
      --yellow:#FFC24A;
      --shadow:0 18px 45px rgba(8,43,95,.08);
      --radius:18px;
    }

    *{box-sizing:border-box}
    html{scroll-behavior:smooth}

    body{
      margin:0;
      background:var(--bg);
      color:var(--text);
      font-family:"Poppins",Arial,sans-serif;
      overflow-x:hidden;
      display:block!important;
    }

    .phase-page{
      width:100%;
      min-height:100vh;
      background:linear-gradient(180deg,#FBFCFF 0%,#F5F7FC 100%);
    }

    .topbar{
      height:74px;
      background:rgba(255,255,255,.96);
      border-bottom:1px solid var(--line);
      box-shadow:0 10px 32px rgba(8,43,95,.04);
      position:sticky;
      top:0;
      z-index:50;
    }

    .topbar-inner{
      width:min(1260px,calc(100% - 36px));
      height:100%;
      margin:0 auto;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
    }

    .logo{
      font-family:"Montserrat",sans-serif;
      font-weight:900;
      font-size:26px;
      letter-spacing:1px;
      color:var(--navy);
      text-decoration:none;
      line-height:1;
      user-select:none;
    }

    .back-btn,
    .btn,
    button.btn,
    a.btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      height:38px;
      border-radius:8px;
      border:1px solid rgba(8,43,95,.12)!important;
      background:var(--navy)!important;
      color:#fff!important;
      text-decoration:none;
      cursor:pointer;
      padding:0 18px;
      font-family:"Montserrat",sans-serif;
      font-size:12px;
      font-weight:900;
      white-space:nowrap;
      box-shadow:0 10px 22px rgba(8,43,95,.16);
      transition:.18s ease;
    }

    .back-btn{
      height:38px;
      padding:0 18px;
      font-size:13px;
      border-radius:10px;
    }

    .back-btn:hover,
    .btn:hover,
    button.btn:hover,
    a.btn:hover{
      background:var(--navy2)!important;
      transform:translateY(-1px);
      box-shadow:0 14px 28px rgba(8,43,95,.22);
    }

    .phase-shell{
      width:min(1260px,calc(100% - 36px));
      margin:0 auto;
      padding:18px 0 42px;
    }

    .hero-head{
      text-align:center;
      padding:8px 0 12px;
    }

    .hero-head h1{
      margin:0 0 10px;
      font-family:"Montserrat",sans-serif;
      font-size:44px;
      line-height:1;
      font-weight:900;
      color:var(--text);
      letter-spacing:-.8px;
    }

    .steps-line{
      color:var(--muted);
      font-size:13px;
      font-weight:500;
    }

    .steps-line:after{
      content:"";
      display:block;
      width:22px;
      height:3px;
      background:var(--yellow);
      border-radius:30px;
      margin:10px auto 0;
    }

    .phase-grid{
      display:grid;
      grid-template-columns:1fr;
      gap:0;
      align-items:start;
    }

    .section-card,
    .card{
      background:transparent!important;
      border:0!important;
      border-radius:0!important;
      box-shadow:none!important;
      padding:22px 0 30px!important;
      margin:0!important;
      position:relative;
      overflow:visible;
      border-bottom:1px solid rgba(8,43,95,.08)!important;
      max-width:100%!important;
    }

    .section-title{
      display:flex;
      align-items:flex-start;
      gap:13px;
      margin-bottom:18px;
    }

    .step-num{
      width:32px;
      height:32px;
      min-width:32px;
      border-radius:50%;
      display:grid;
      place-items:center;
      background:var(--navy);
      color:#fff;
      font-family:"Montserrat",sans-serif;
      font-weight:900;
      font-size:14px;
      box-shadow:0 10px 20px rgba(8,43,95,.12);
    }

    h2{
      margin:0 0 6px;
      font-family:"Montserrat",sans-serif;
      font-size:22px;
      line-height:1.25;
      font-weight:900;
      color:var(--text);
      letter-spacing:-.2px;
    }

    .muted{
      color:var(--muted);
      font-size:13px;
      line-height:1.75;
      margin:0;
    }

    label.muted{
      display:block;
      margin:12px 0 7px;
      font-size:12px;
      font-weight:800;
      color:var(--text);
      line-height:1.4;
    }

    .form-panel{
      background:transparent;
      border:0;
      border-radius:0;
      padding:0;
      box-shadow:none;
    }

    .input,
    input.input,
    select.input,
    textarea.input{
      width:100%;
      height:42px;
      background:#fff;
      border:1px solid #DDE5F0;
      border-radius:10px;
      padding:0 14px;
      color:var(--text);
      font-family:"Poppins",Arial,sans-serif;
      font-size:13px;
      outline:0;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.7);
      transition:.18s ease;
    }

    textarea.input{
      height:auto;
      min-height:150px;
      padding:13px 14px;
      resize:vertical;
    }

    .input:focus{
      border-color:#9DB7DF;
      box-shadow:0 0 0 4px rgba(44,116,229,.08);
    }

    .form-stack{
      display:grid;
      grid-template-columns:1fr;
      gap:14px;
      max-width:100%;
    }

    .btnRow{
      display:flex;
      gap:12px;
      flex-wrap:wrap;
      align-items:center;
      margin-top:14px;
    }

    #phase2Msg{
      min-height:22px;
      margin-top:8px;
      color:var(--muted);
      font-size:13px;
    }

    @media(max-width:980px){
      .phase-shell,.topbar-inner{width:min(100% - 24px,1260px)}
      .hero-head h1{font-size:36px}
      .topbar{height:70px}
    }
  </style>
</head>

<body>
<div class="phase-page">
  <header class="topbar">
    <div class="topbar-inner">
      <a class="logo" href="company.php">QOYN</a>
      <a class="back-btn" href="company.php">← Back</a>
    </div>
  </header>

  <main class="phase-shell">
    <section class="hero-head">
      <h1>Phase 2</h1>
      <div class="steps-line">Course Project</div>
    </section>

    <div class="phase-grid">
      <section class="section-card">
        <div class="section-title">
          <span class="step-num">2</span>
          <div>
            <h2>Course Project</h2>
            <p class="muted">Select a course, then write a clear project title and description.</p>
          </div>
        </div>

        <div class="form-panel">
          <div class="form-stack">
            <div>
              <label class="muted" for="courseSelect">Select a course (from database)</label>
              <select class="input" id="courseSelect">
                <option value="">... Loading</option>
              </select>
            </div>

            <div>
              <label class="muted" for="courseManual">Or type the course name manually</label>
              <input class="input" id="courseManual" placeholder="Example: Data Structures"/>
            </div>

            <div>
              <label class="muted" for="projectTitle">Project title</label>
              <input class="input" id="projectTitle" placeholder="Write a clear project title"/>
            </div>

            <div>
              <label class="muted" for="projectDesc">Project description</label>
              <textarea class="input" id="projectDesc" rows="8" placeholder="Write a complete project description + requirements + submission details..."></textarea>
            </div>
          </div>

          <div class="btnRow">
            <button class="btn" type="button" id="btnSavePhase2">▣ Save Phase 2</button>
          </div>

          <div class="muted" id="phase2Msg"></div>
        </div>
      </section>
    </div>
  </main>
</div>

<script src="assets/js/company.js"></script>

<script>
  // =========================================================
  // UI Text Fixes (in case company.js injects Arabic strings)
  // =========================================================
  function fixPhase2ArabicUI(){
    // Translate loading option if it becomes Arabic
    const sel = document.getElementById("courseSelect");
    if(sel){
      const opt = sel.querySelector("option");
      if(opt && opt.textContent.includes("جاري التحميل")){
        opt.textContent = "... Loading";
      }
      if(opt && opt.textContent.includes("تعذر")){
        opt.textContent = "Failed to load";
      }
    }

    // Translate save button if it becomes Arabic
    const saveBtn = document.getElementById("btnSavePhase2");
    if(saveBtn && saveBtn.textContent.trim() === "حفظ Phase2"){
      saveBtn.textContent = "▣ Save Phase 2";
    }
  }

  fixPhase2ArabicUI();
  const obs = new MutationObserver(() => fixPhase2ArabicUI());
  obs.observe(document.body, { childList:true, subtree:true });
</script>

</body>
</html>

