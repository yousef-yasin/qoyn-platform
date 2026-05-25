<?php
$secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");

if (session_status() === PHP_SESSION_NONE) {
  if (defined("PHP_VERSION_ID") && PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      "lifetime" => 0,
      "path"     => "/",
      "secure"   => $secure,
      "httponly" => true,
      "samesite" => "Lax",
    ]);
  } else {
    session_set_cookie_params(0, "/");
  }
  session_start();
}

if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}

if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") {
  header("Location: index.php");
  exit;
}
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title data-i18n="company_reviews_title">QOYN | Company Reviews</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>

    :root{
      --navy:#092c5c;
      --navy-2:#183f78;
      --blue:#2f80ed;
      --gold:#ffc64c;
      --purple:#7b61ff;
      --bg:#f7f9fc;
      --white:#fff;
      --text:#0b254f;
      --muted:#66758f;
      --line:#e7edf5;
      --soft:#f4f7fb;
      --success:#16b78a;
      --shadow:0 18px 45px rgba(9,44,92,.07);
      --shadow-sm:0 10px 26px rgba(9,44,92,.055);
      --radius:22px;
      --container:1450px;
    }
    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{
      margin:0;
      font-family:"Poppins",sans-serif;
      color:var(--text);
      background:linear-gradient(180deg,#fbfcff 0%,#f6f8fc 100%);
    }
    h1,h2,h3,h4,h5,h6,strong,b{font-family:"Montserrat",sans-serif}
    a{text-decoration:none;color:inherit}
    .topbar{
      position:sticky;top:0;z-index:1000;
      height:80px;background:rgba(255,255,255,.96);
      backdrop-filter:blur(16px);
      border-bottom:1px solid #e9eef6;
      box-shadow:0 5px 24px rgba(9,44,92,.035);
    }
    .topbar-inner{
      width:100%;height:100%;padding:0 30px;
      display:grid;grid-template-columns:220px 1fr auto;align-items:center;gap:24px;
    }
    .brand{display:flex;align-items:center;gap:11px;min-width:0}
    .brand-logo{font:900 29px/1 "Montserrat",sans-serif;letter-spacing:.6px;color:var(--navy)}
    .brand-mark{width:14px;height:14px;border-radius:50%;background:var(--gold);box-shadow:0 0 0 9px rgba(255,198,76,.18)}
    .nav-links{display:flex;justify-content:center;align-items:center;gap:48px;color:#16233a;font-weight:500;font-size:14px;white-space:nowrap}
    .nav-links a{transition:.2s ease}.nav-links a:hover{color:var(--blue)}
    .topbar-actions{display:flex;align-items:center;gap:24px;justify-content:flex-end}
    .lang-switch{display:flex;align-items:center;padding:0;border:1px solid #e2e8f0;border-radius:26px;background:#fff;overflow:hidden;height:44px;box-shadow:none}
    .lang-btn{height:44px;min-width:54px;border:none;background:transparent;color:#667085;font-weight:800;font-family:"Poppins",sans-serif;cursor:pointer;border-radius:24px;transition:.2s ease}
    .lang-btn.active,.lang-btn:hover{background:var(--navy);color:#fff}
    .back-btn{height:44px;min-width:106px;display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:0 22px;border-radius:14px;background:#fff;color:#0d2447;border:1px solid #e2e8f0;font-weight:800;box-shadow:none;transition:.2s ease}
    .back-btn::before{content:"←";font-size:22px;line-height:1}.back-btn:hover{background:var(--navy);color:#fff;border-color:var(--navy);transform:translateY(-1px)}
    .avatar-badge{width:46px;height:54px;border:0;background:transparent;box-shadow:none;border-radius:0;display:grid;place-items:center;padding:0;overflow:visible}.avatar-badge img{width:42px;height:52px;object-fit:contain}
    .wrap{width:min(94vw,var(--container));margin:0 auto;padding:26px 0 34px}
    .hero{position:relative;margin-bottom:18px;padding:14px 6px 0;background:transparent;border-radius:0;color:var(--text);box-shadow:none;overflow:visible;min-height:160px}
    .hero::before,.hero::after{content:"";position:absolute;pointer-events:none}.hero::before{right:36px;top:18px;width:300px;height:126px;border-radius:26px;background:linear-gradient(135deg,rgba(127,97,255,.13),rgba(47,128,237,.06));transform:skewX(-10deg);opacity:.45}.hero::after{right:64px;top:22px;width:268px;height:120px;background:repeating-linear-gradient(90deg,transparent 0 39px,rgba(127,97,255,.18) 40px 61px,transparent 62px 80px);border-radius:24px;transform:rotate(-6deg);opacity:.33}
    .hero-grid{position:relative;z-index:1;display:grid;grid-template-columns:1fr 690px;gap:34px;align-items:start}
    .hero-badge{display:inline-flex;align-items:center;gap:10px;padding:8px 13px;margin:0 0 18px;border-radius:9px;background:#fff;border:1px solid #e8edf5;color:var(--text);font-weight:800;font-size:13px;box-shadow:0 8px 18px rgba(9,44,92,.035)}
    .hero-badge::before{content:"";width:10px;height:10px;border-radius:50%;background:var(--gold);display:inline-block}
    .hero h1{margin:0 0 12px;font-size:36px;line-height:1.22;font-weight:900;letter-spacing:-1.1px;color:var(--navy)}
    .hero p{margin:0;max-width:680px;color:#596987;line-height:1.75;font-size:15px}
    .hero-visual{display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:stretch;padding-top:16px}
    .visual-card{width:auto;min-height:130px;border-radius:18px;background:#fff;border:1px solid #e8edf5;box-shadow:var(--shadow-sm);padding:24px 28px;display:flex;align-items:center;gap:18px;overflow:hidden;position:relative}.visual-card::before{display:none}.visual-main{display:none}.visual-chip{position:static;box-shadow:none;border:0;background:transparent;border-radius:0;padding:0;color:var(--text);font-size:0;font-weight:400;display:block}.visual-chip::before{content:"";width:58px;height:58px;border-radius:18px;display:grid;place-items:center;background:#f1f6ff;color:#2f80ed;flex:0 0 auto}.chip-1::before{content:"☆";font-size:30px}.chip-2::before{content:"▥";font-size:30px;color:#f0b429;background:#fff8e9}.visual-card .chip-1,.visual-card .chip-2{display:flex;align-items:center;gap:18px}.visual-card .chip-1::after{content:"Quality Review\A Ensure top standards in\A every submission.";white-space:pre-line;font-size:14px;line-height:1.85;color:#66758f;font-weight:400}.visual-card .chip-2::after{content:"Professional Evaluation\A Evaluate fairly. Empower\A future leaders.";white-space:pre-line;font-size:14px;line-height:1.85;color:#66758f;font-weight:400}.visual-card .chip-1::after,.visual-card .chip-2::after{font-family:"Poppins",sans-serif}.visual-card .chip-1::first-line,.visual-card .chip-2::first-line{color:var(--text);font-weight:800;font-family:"Montserrat",sans-serif}
    .main-grid{display:grid;grid-template-columns:1.05fr 1fr;gap:28px;align-items:start}.stack{display:grid;gap:10px}.card{background:#fff;border:1px solid #e8edf5;border-radius:22px;padding:18px;box-shadow:var(--shadow);overflow:hidden;position:relative}.card::before{display:none}.section-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:18px}.section-head>div:first-child{display:grid;grid-template-columns:54px 1fr;column-gap:16px;align-items:center}.section-head>div:first-child::before{content:"";width:54px;height:54px;border-radius:18px;background:#f1f6ff;display:block;grid-row:1/3;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='26' height='26' viewBox='0 0 24 24' fill='none' stroke='%232f80ed' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 5v14M5 12h14'/%3E%3Cpath d='M7 7h10v10H7z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:center}.section-title{margin:0;font-size:16px;font-weight:900;color:var(--text);letter-spacing:-.1px}.section-sub{margin:5px 0 0;color:#66758f;font-size:12.5px;line-height:1.7}.mini-icon{display:none}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:10px}.toolbar-left{display:flex;align-items:center;gap:14px;flex:1}.btn{cursor:pointer;border:none;border-radius:11px;padding:12px 18px;font-weight:900;font-family:"Montserrat",sans-serif;transition:.2s ease}.btn:hover{transform:translateY(-1px)}.btn-primary{color:#fff;background:var(--navy);box-shadow:0 12px 26px rgba(9,44,92,.16)}.btn-ghost{background:#fff;color:var(--blue);border:1px solid #e2e8f0}.count-pill{min-width:112px;height:43px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #e8edf5;border-radius:10px;background:#fff;color:var(--text);font-weight:800;font-size:13px}.count-pill::before{content:"▯";color:#2f80ed;margin-right:9px}.status-filter-box{flex:1;display:grid;grid-template-columns:140px 1fr;align-items:center;gap:12px;padding:9px 12px;border:1px solid #e8edf5;border-radius:12px;background:#fff;box-shadow:0 8px 20px rgba(9,44,92,.03)}.status-filter-label{font-size:12px;font-weight:800;text-transform:uppercase;color:#1e2f4d}.status-select-wrap{position:relative}.status-select-wrap::after{content:"⌄";position:absolute;right:14px;top:50%;transform:translateY(-50%);pointer-events:none;color:#556987;font-size:20px}.status-select-wrap select{height:44px;margin:0;padding:0 40px 0 14px;border-radius:10px;background:#fff;font-weight:800}.list-shell{padding:0 0 6px;background:#fff;border:1px solid #edf1f7;border-radius:14px;min-height:322px;overflow:hidden}.listItem{margin:0;border:0;border-bottom:1px solid #edf1f7;border-radius:0;box-shadow:none;background:#fff;padding:17px 16px;transition:.18s ease}.listItem:first-child{background:#f5f7ff;border-left:4px solid #2f80ed}.listItem:hover{transform:none;background:#f8fbff;box-shadow:none}.listItem button{min-width:42px;height:42px;padding:0;border-radius:50%;font-size:0;background:#2f80ed}.listItem button::after{content:"✓";font-size:18px}.muted{color:#66758f;font-size:12px}.selected-banner{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:-4px -2px 14px;padding:0;background:transparent;border:0}.selected-banner>div:first-child{display:grid;grid-template-columns:54px 1fr;column-gap:16px;align-items:center}.selected-banner>div:first-child::before{content:"";width:54px;height:54px;border-radius:18px;background:#fff8e9;display:block;grid-row:1/3;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='27' height='27' viewBox='0 0 24 24' fill='none' stroke='%23f0b429' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7h5l2 3h11v9H3z'/%3E%3Cpath d='M3 7v12'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:center}.selected-banner h3{margin:0;font-size:16px;color:var(--text);font-weight:900}.selected-banner p{margin:5px 0 0;color:#66758f;font-size:12.5px}.selected-art{width:54px;height:54px;border-radius:12px;border:1px solid #eef2f7;background:#fff;padding:8px}.selected-art img{width:100%;height:100%;object-fit:contain}.details-hidden{display:none}.pill-row,.teamBox{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px}.info-pill,.teamPill{display:block;padding:10px 12px;border-radius:10px;background:#fffdf8;border:1px solid #f6ead2;color:var(--text);font-size:13px}.link-row{display:flex;gap:10px;margin:14px 0}.form-card{min-height:692px;background:#fff}.form-card .section-head>div:first-child::before{background-color:#f1f6ff;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='%232f80ed' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m22 2-7 20-4-9-9-4Z'/%3E%3Cpath d='M22 2 11 13'/%3E%3C/svg%3E")}.review-badge{display:none}.action-note{display:flex;align-items:center;gap:11px;padding:13px 16px;margin:8px 0 28px;border-radius:10px;background:#f7f9ff;border:1px solid #e8edf9;color:#66758f;font-size:13px}.action-note::before{content:"ⓘ";font-size:20px;color:#2f80ed;background:transparent;width:auto;height:auto;margin:0}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:24px}label{display:block;font-weight:800;color:var(--text);font-size:14px}input,select,textarea{width:100%;margin-top:9px;border:1px solid #e2e8f0;border-radius:10px;background:#fff;color:var(--text);padding:13px 14px;outline:none;font-family:"Poppins",sans-serif;font-size:15px;transition:.2s ease;box-shadow:none}input:focus,select:focus,textarea:focus{border-color:#2f80ed;box-shadow:0 0 0 4px rgba(47,128,237,.08)}#decision{height:48px}textarea{min-height:168px;resize:vertical}.submit-wrap{margin-top:28px}#send{width:100%;height:58px;border-radius:12px;font-size:16px;background:linear-gradient(135deg,#203f73,#17386d)}#send::after{content:"  ✈";font-size:22px;vertical-align:-2px}.ok{color:#16a36f}.bad{color:#d92d20}.warn{color:#b7791f}.empty-state{min-height:160px;display:grid;place-items:center;text-align:center;color:#66758f;font-weight:600;padding:22px}pre{white-space:pre-wrap;margin:8px 0 0;padding:12px;border-radius:10px;background:#fbfcfe;border:1px solid #e8edf5;color:#344054;max-height:190px;overflow:auto;font-family:"Poppins",sans-serif;line-height:1.7}.rtl{direction:rtl}.ltr{direction:ltr}body.rtl .topbar-inner,body.rtl .hero-grid,body.rtl .main-grid{direction:ltr}body.rtl [data-i18n],body.rtl p,body.rtl .section-sub,body.rtl label,body.rtl .listItem,body.rtl input,body.rtl textarea,body.rtl select{direction:rtl}body.rtl .status-select-wrap::after{right:auto;left:14px}body.rtl .status-select-wrap select{padding-left:40px;padding-right:14px}
    @media(max-width:1180px){.hero-grid,.main-grid{grid-template-columns:1fr}.hero-visual{grid-template-columns:1fr 1fr}.nav-links{gap:20px}.topbar-inner{grid-template-columns:160px 1fr auto}}
    @media(max-width:800px){.topbar{height:auto}.topbar-inner{display:flex;flex-wrap:wrap;padding:14px}.nav-links{order:3;width:100%;overflow:auto;justify-content:flex-start}.hero h1{font-size:30px}.hero-visual,.form-grid,.pill-row,.teamBox{grid-template-columns:1fr}.toolbar,.toolbar-left{flex-direction:column;align-items:stretch}.status-filter-box{grid-template-columns:1fr}.wrap{width:min(94vw,100%);padding-top:18px}}

  </style>

</head>
<body>
  <header class="topbar">
    <div class="topbar-inner">
      <div class="brand">
        <span class="brand-mark"></span>
        <a href="company.php" class="brand-logo">QOYN</a>
      </div>
      <nav class="nav-links" aria-label="Main navigation">
        <a href="student_projects.php">Student Projects</a>
        <a href="our_chat.php">Our Chat</a>
        <a href="profile.php">Profile</a>
        <a href="about.php">About</a>
        <a href="paths.php">Paths</a>
        <a href="phases.php">Phases</a>
      </nav>
      <div class="topbar-actions">
        
        <div class="avatar-badge"><img src="uploads/MONKEY.png" alt="QOYN"></div>
      </div>
    </div>
  </header>

  <main class="wrap">
    <section class="hero">
      <div class="hero-grid">
        <div>
          <div class="hero-badge" data-i18n="company_reviews_badge">Phase 3 Company Review Panel</div>
          <h1 data-i18n="company_reviews_heading">Reviewing student submissions — Phase 3</h1>
          <p data-i18n="company_reviews_subtitle">Here the company reviews deliverables, gives a score out of 100, adds feedback, and tracks deliverables by project, team, and student.</p>
        </div>
        <div class="hero-visual">
          <div class="visual-card"><div class="visual-chip chip-1" data-i18n="chip_quality">Quality Review</div></div>
          <div class="visual-card"><div class="visual-chip chip-2" data-i18n="chip_evaluation">Professional Evaluation</div></div>
        </div>
      </div>
    </section>

    <section class="main-grid">
      <div class="stack">
        <div class="card">
          <div class="section-head">
            <div>
              <h3 class="section-title" data-i18n="queue_title">Submission Queue</h3>
              <p class="section-sub" data-i18n="queue_subtitle">Browse all submissions and choose one to review.</p>
            </div>
            <div class="mini-icon"><img src="uploads/company.png" alt="Company"></div>
          </div>
          <div class="toolbar">
            <div class="toolbar-left">
              <button class="btn btn-primary" id="reload" data-i18n="reload_btn">Reload</button>
              <div class="status-filter-box">
                <div class="status-filter-label" data-i18n="task_status">Task Status</div>
                <div class="status-select-wrap">
                  <select id="status">
                    <option value="SUBMITTED" selected data-i18n="submitted">SUBMITTED</option>
                    <option value="REVIEWED" data-i18n="reviewed">REVIEWED</option>
                    <option value="ALL" data-i18n="all">ALL</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="count-pill" id="count">0 item(s)</div>
          </div>
          <div id="list" class="list-shell"><div class="empty-state" data-i18n="loading_text">Loading...</div></div>
        </div>

        <div class="card selected-card">
          <div class="selected-banner">
            <div>
              <h3 data-i18n="selected_submission_title">Selected Submission</h3>
              <p id="selMsg" data-i18n="choose_submission_msg">Choose any submission from the list.</p>
            </div>
            <div class="selected-art"><img src="uploads/company.png" alt="Selected"></div>
          </div>
          <input type="hidden" id="project_id"/><input type="hidden" id="task_id"/><input type="hidden" id="student_id"/><input type="hidden" id="submission_id"/><input type="hidden" id="team_id"/>
          <div id="details" class="details-hidden">
            <div class="pill-row"><span class="info-pill"><span data-i18n="submission_label">Submission</span>: <b id="d_id"></b></span><span class="info-pill"><span data-i18n="student_label">Student</span>: <b id="d_student"></b></span><span class="info-pill"><span data-i18n="project_label">Project</span>: <b id="d_project"></b></span></div>
            <div class="pill-row"><span class="info-pill"><span data-i18n="task_label">Task</span>: <b id="d_task"></b></span><span class="info-pill"><span data-i18n="role_label">Role</span>: <b id="d_role"></b></span><span class="info-pill"><span data-i18n="status_label">Status</span>: <b id="d_status"></b></span></div>
            <div class="pill-row"><span class="info-pill"><span data-i18n="decision_label">Decision</span>: <b id="d_selection"></b></span><span class="info-pill"><span data-i18n="submitted_at_label">Submitted At</span>: <b id="d_submitted_at"></b></span></div>
            <div class="teamBox"><span class="teamPill"><span data-i18n="team_number_label">Team #</span> <b id="d_team_no" style="margin-inline-start:4px"></b></span><span class="teamPill"><span data-i18n="team_id_label">Team ID</span>: <b id="d_team_id" style="margin-inline-start:4px"></b></span></div>
            <div class="link-row"><a class="btn btn-ghost" id="zipLink" href="#" target="_blank" style="display:none" data-i18n="download_zip">Download ZIP</a><a class="btn btn-ghost" id="repoLink" href="#" target="_blank" style="display:none" data-i18n="open_repo">Open Repo</a></div>
            <div class="section-sub" style="margin-top:10px;font-weight:700" data-i18n="repo_url_label">Repo URL</div><div id="d_repo" style="word-break:break-all;margin-top:6px"></div>
            <div class="section-sub" style="margin-top:14px;font-weight:700" data-i18n="student_notes_label">Student Notes</div><pre id="d_notes"></pre>
          </div>
        </div>
      </div>

      <div class="card form-card">
        <div class="section-head">
          <div>
            <h3 class="section-title" data-i18n="review_form_title">Send Company Evaluation</h3>
            <p class="section-sub" data-i18n="review_form_subtitle">Enter the score, decision, and feedback, then submit the final review.</p>
          </div>
          <div class="review-badge" data-i18n="review_badge">Company Evaluation</div>
        </div>
        <div class="action-note" data-i18n="review_note">Fill in the following fields clearly, then submit the final evaluation for the student.</div>
        <div class="form-grid">
          <label><span data-i18n="score_label">Score (0-100)</span><input type="number" id="score" min="0" max="100" value="80"></label>
          <label><span data-i18n="rating_label">Rating (0-5)</span><input type="number" id="rating" min="0" max="5" value="5"></label>
        </div>
        <div style="margin-top:14px"><label><span data-i18n="decision_label">Decision</span><select id="decision"><option value="PASS" data-i18n="pass">PASS</option><option value="NEEDS_FIX" data-i18n="needs_fix">NEEDS_FIX</option><option value="FAIL" data-i18n="fail">FAIL</option></select></label></div>
        <div style="margin-top:28px"><label><span data-i18n="feedback_label">Feedback</span><textarea id="feedback" data-i18n-placeholder="feedback_placeholder" placeholder="Write feedback to the student..."></textarea></label></div>
        <div class="submit-wrap"><button class="btn btn-primary" id="send" data-i18n="submit_review_btn">Submit Review</button><div id="out"></div></div>
      </div>
    </section>
  </main>

  <script src="i18n.js"></script>


  <script>
    const API_BASE = "../utbn-backend/api";
    const BACKEND_BASE = "../utbn-backend";

    let current = null;

    const fallbackTranslations = {
      en: {
        company_reviews_title: "QOYN | Company Reviews",
        back_btn: "Back",
        company_reviews_badge: "Phase 3 Company Review Panel",
        company_reviews_heading: "Reviewing student submissions — Phase 3",
        company_reviews_subtitle: "Here the company reviews deliverables, gives a score out of 100, adds feedback, and tracks deliverables by project, team, and student.",
        chip_quality: "Quality Review",
        chip_evaluation: "Professional Evaluation",
        queue_title: "Submission Queue",
        queue_subtitle: "Browse all submissions and choose one to review.",
        reload_btn: "Reload",
        task_status: "Task Status",
        submitted: "SUBMITTED",
        reviewed: "REVIEWED",
        all: "ALL",
        selected_submission_title: "Selected Submission",
        choose_submission_msg: "Choose any submission from the list.",
        submission_label: "Submission",
        student_label: "Student",
        project_label: "Project",
        task_label: "Task",
        role_label: "Role",
        status_label: "Status",
        decision_label: "Decision",
        submitted_at_label: "Submitted At",
        team_number_label: "Team #",
        team_id_label: "Team ID",
        download_zip: "Download ZIP",
        open_repo: "Open Repo",
        repo_url_label: "Repo URL",
        student_notes_label: "Student Notes",
        review_form_title: "Send Company Evaluation",
        review_form_subtitle: "Enter the score, decision, and feedback, then submit the final review.",
        review_badge: "Company Evaluation",
        review_note: "Fill in the following fields clearly, then submit the final evaluation for the student.",
        score_label: "Score (0-100)",
        rating_label: "Rating (0-5)",
        feedback_label: "Feedback",
        feedback_placeholder: "Write feedback to the student...",
        submit_review_btn: "Submit Review",
        pass: "PASS",
        needs_fix: "NEEDS_FIX",
        fail: "FAIL",
        loading_text: "Loading...",
        no_submissions_found: "No submissions found for this status.",
        item_count: "item(s)",
        select_btn: "Select",
        loading_submit: "Submitting...",
        select_submission_first: "Select a submission first.",
        review_saved: "Review saved.",
        failed_text: "Failed"
      },
      ar: {
        company_reviews_title: "QOYN | تقييمات الشركة",
        back_btn: "رجوع",
        company_reviews_badge: "لوحة تقييم الشركة - المرحلة 3",
        company_reviews_heading: "مراجعة تسليمات الطلاب — المرحلة 3",
        company_reviews_subtitle: "هنا تقوم الشركة بمراجعة التسليمات، وإعطاء علامة من 100، وإضافة ملاحظات، وتتبع التسليمات حسب المشروع والفريق والطالب.",
        chip_quality: "مراجعة الجودة",
        chip_evaluation: "تقييم احترافي",
        queue_title: "قائمة التسليمات",
        queue_subtitle: "تصفح جميع التسليمات واختر واحدًا للمراجعة.",
        reload_btn: "إعادة تحميل",
        task_status: "حالة المهمة",
        submitted: "تم التسليم",
        reviewed: "تمت المراجعة",
        all: "الكل",
        selected_submission_title: "التسليم المختار",
        choose_submission_msg: "اختر أي submission من القائمة.",
        submission_label: "التسليم",
        student_label: "الطالب",
        project_label: "المشروع",
        task_label: "المهمة",
        role_label: "الدور",
        status_label: "الحالة",
        decision_label: "القرار",
        submitted_at_label: "تاريخ التسليم",
        team_number_label: "رقم الفريق",
        team_id_label: "معرّف الفريق",
        download_zip: "تحميل ZIP",
        open_repo: "فتح المستودع",
        repo_url_label: "رابط المستودع",
        student_notes_label: "ملاحظات الطالب",
        review_form_title: "إرسال تقييم الشركة",
        review_form_subtitle: "أدخل العلامة والقرار والملاحظات ثم أرسل التقييم النهائي.",
        review_badge: "تقييم الشركة",
        review_note: "املأ الحقول التالية بشكل واضح ثم أرسل التقييم النهائي للطالب.",
        score_label: "العلامة (0-100)",
        rating_label: "التقييم (0-5)",
        feedback_label: "الملاحظات",
        feedback_placeholder: "اكتب ملاحظاتك للطالب...",
        submit_review_btn: "إرسال التقييم",
        pass: "ناجح",
        needs_fix: "يحتاج تعديل",
        fail: "راسب",
        loading_text: "جاري التحميل...",
        no_submissions_found: "لا توجد تسليمات لهذه الحالة.",
        item_count: "عنصر",
        select_btn: "اختيار",
        loading_submit: "جاري الإرسال...",
        select_submission_first: "اختر تسليمًا أولاً.",
        review_saved: "تم حفظ التقييم.",
        failed_text: "فشل"
      }
    };

    function t(key){
      const lang = localStorage.getItem("lang") || document.documentElement.lang || "en";
      return (fallbackTranslations[lang] && fallbackTranslations[lang][key]) ||
             (fallbackTranslations.en[key]) ||
             key;
    }

    function updateDir(lang){
      const html = document.documentElement;
      const isArabic = lang === "ar";
      html.lang = isArabic ? "ar" : "en";
      html.dir = isArabic ? "rtl" : "ltr";
      document.body.classList.toggle("rtl", isArabic);
      document.body.classList.toggle("ltr", !isArabic);

      document.querySelectorAll("[data-set-lang]").forEach(btn=>{
        btn.classList.toggle("active", btn.getAttribute("data-set-lang") === lang);
      });
    }

    async function applyPageTranslations(lang){
      updateDir(lang);

      if (typeof window.setLanguage === "function") {
        try {
          await window.setLanguage(lang);
        } catch (e) {}
      } else if (typeof window.applyTranslations === "function") {
        try {
          await window.applyTranslations(lang);
        } catch (e) {}
      } else {
        document.querySelectorAll("[data-i18n]").forEach(el=>{
          const key = el.getAttribute("data-i18n");
          el.textContent = t(key);
        });

        document.querySelectorAll("[data-i18n-placeholder]").forEach(el=>{
          const key = el.getAttribute("data-i18n-placeholder");
          el.setAttribute("placeholder", t(key));
        });

        document.title = t("company_reviews_title");
      }

      refreshStaticOptions();
      refreshCountLabel();
    }

    function refreshStaticOptions(){
      const status = document.getElementById("status");
      const decision = document.getElementById("decision");
      if (status) {
        if (status.options[0]) status.options[0].text = t("submitted");
        if (status.options[1]) status.options[1].text = t("reviewed");
        if (status.options[2]) status.options[2].text = t("all");
      }
      if (decision) {
        if (decision.options[0]) decision.options[0].text = t("pass");
        if (decision.options[1]) decision.options[1].text = t("needs_fix");
        if (decision.options[2]) decision.options[2].text = t("fail");
      }
    }

    function esc(s){
      return (s ?? "").toString().replace(/[&<>"']/g, m => ({
        '&':'&amp;',
        '<':'&lt;',
        '>':'&gt;',
        '"':'&quot;',
        "'":'&#39;'
      }[m]));
    }

    function toUrl(p){
      if (!p) return "";
      const norm = String(p).replaceAll("\\", "/");
      return `${BACKEND_BASE}${norm.startsWith("/") ? "" : "/"}${norm}`;
    }

    function statusClass(v){
      const x = String(v || "").toUpperCase();
      if (x === "PASS" || x === "REVIEWED" || x === "SELECTED") return "ok";
      if (x === "FAIL" || x === "NOT_SELECTED") return "bad";
      return "warn";
    }

    function normalizeItems(payload){
      if (Array.isArray(payload.items)) return payload.items;
      if (Array.isArray(payload.submissions)) return payload.submissions;
      if (Array.isArray(payload.data)) return payload.data;
      return [];
    }

    async function apiGet(url){
      const r = await fetch(`${API_BASE}/${url}`, { credentials:"include" });
      const text = await r.text();
      let j = {};
      try { j = JSON.parse(text); } catch { j = { ok:false, error:"BAD_JSON", _raw:text }; }
      return j;
    }

    async function apiPost(url, body){
      const fd = new FormData();
      Object.keys(body || {}).forEach(k => fd.append(k, body[k]));
      const r = await fetch(`${API_BASE}/${url}`, {
        method:"POST",
        body: fd,
        credentials:"include"
      });
      const text = await r.text();
      let j = {};
      try { j = JSON.parse(text); } catch { j = { ok:false, error:"BAD_JSON", _raw:text }; }
      return j;
    }

    function refreshCountLabel(count = null){
      const countEl = document.getElementById("count");
      if (!countEl) return;

      if (count === null) {
        const raw = countEl.getAttribute("data-count");
        count = Number(raw || 0);
      }

      countEl.setAttribute("data-count", String(count));
      countEl.textContent = `${count} ${t("item_count")}`;
    }

    async function loadQueue(){
      const list = document.getElementById("list");
      const status = document.getElementById("status").value;

      list.innerHTML = `<div class="empty-state">${esc(t("loading_text"))}</div>`;
      refreshCountLabel(0);

      const r = await apiGet(`phase3/partner_queue.php?status=${encodeURIComponent(status)}`);

      if (!r.ok) {
        list.innerHTML = `<div class="empty-state">❌ ${esc(t("failed_text"))}: ${esc(r.error || "UNKNOWN")}</div>`;
        return;
      }

      const items = normalizeItems(r);
      window.__QUEUE = items;
      refreshCountLabel(items.length);

      if (!items.length) {
        list.innerHTML = `<div class="empty-state">${esc(t("no_submissions_found"))}</div>`;
        return;
      }

      list.innerHTML = items.map(it => {
        const badge = it.partner_final_decision || it.decision || it.task_status || "PENDING";
        const teamNo = it.team_no ?? "—";
        const rating = it.partner_rating ?? "—";
        const score = it.score ?? "—";

        return `
          <div class="listItem">
            <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap">
              <div>
                <div style="font-weight:900;font-size:17px;color:var(--navy)">
                  ${esc(it.student_name || "Student")}
                </div>
                <div style="margin-top:4px;color:#344054;font-weight:600">
                  ${esc(it.project_title || it.project_name || "Project")}
                </div>
                <div class="muted" style="margin-top:6px">
                  ${esc(t("team_number_label"))}: <b>${esc(teamNo)}</b>
                  • ${esc(t("task_label"))}: <b>${esc(it.task_code || "—")}</b>
                  • ${esc(t("role_label"))}: <b>${esc(it.role_name || "—")}</b>
                </div>
                <div class="muted" style="margin-top:4px">
                  ${esc(it.submitted_at || "")}
                  • ${esc(t("score_label"))}: <b>${esc(score)}</b>
                  • ${esc(t("rating_label"))}: <b>${esc(rating)}</b>
                  • <b class="${statusClass(badge)}">${esc(badge)}</b>
                </div>
              </div>
              <button class="btn btn-primary" onclick="selectSubmission(${Number(it.submission_id || it.id || 0)})">${esc(t("select_btn"))}</button>
            </div>
          </div>
        `;
      }).join("");
    }

    window.selectSubmission = function(submission_id){
      const items = window.__QUEUE || [];
      const it = items.find(x => Number(x.submission_id || x.id) === Number(submission_id));
      if (!it) return;

      current = it;

      document.getElementById("submission_id").value = it.submission_id || it.id || "";
      document.getElementById("project_id").value = it.project_id || "";
      document.getElementById("task_id").value = it.task_id || "";
      document.getElementById("student_id").value = it.student_id || "";
      document.getElementById("team_id").value = it.team_id || "";

      document.getElementById("selMsg").textContent = `Selected submission_id = ${it.submission_id || it.id}`;
      document.getElementById("details").style.display = "";

      document.getElementById("d_id").textContent = it.submission_id || it.id || "—";
      document.getElementById("d_student").textContent = it.student_name || "—";
      document.getElementById("d_project").textContent = it.project_title || it.project_name || "—";
      document.getElementById("d_task").textContent = it.task_code || "—";
      document.getElementById("d_role").textContent = it.role_name || "—";
      document.getElementById("d_status").textContent = it.task_status || it.decision || "—";
      document.getElementById("d_selection").textContent = it.partner_final_decision || it.decision || "PENDING";
      document.getElementById("d_submitted_at").textContent = it.submitted_at || "—";
      document.getElementById("d_team_no").textContent = it.team_no || "—";
      document.getElementById("d_team_id").textContent = it.team_id || "—";

      document.getElementById("d_repo").textContent = it.repo_url || "—";
      document.getElementById("d_notes").textContent = it.notes || "";

      const zipLink = document.getElementById("zipLink");
      const zipUrl = toUrl(it.zip_path);
      if (zipUrl) {
        zipLink.href = zipUrl;
        zipLink.style.display = "";
      } else {
        zipLink.style.display = "none";
      }

      const repoLink = document.getElementById("repoLink");
      if (it.repo_url) {
        repoLink.href = it.repo_url;
        repoLink.style.display = "";
      } else {
        repoLink.style.display = "none";
      }

      if (it.score !== null && it.score !== undefined && it.score !== "") {
        document.getElementById("score").value = it.score;
      } else {
        document.getElementById("score").value = 80;
      }

      if (it.partner_rating !== null && it.partner_rating !== undefined && it.partner_rating !== "") {
        document.getElementById("rating").value = it.partner_rating;
      } else {
        document.getElementById("rating").value = 5;
      }

      const dec = it.partner_final_decision || it.decision;
      if (dec && ["PASS", "NEEDS_FIX", "FAIL"].includes(String(dec).toUpperCase())) {
        document.getElementById("decision").value = String(dec).toUpperCase();
      } else {
        document.getElementById("decision").value = "PASS";
      }

      document.getElementById("feedback").value = it.partner_comment || it.comment || "";
    };

    document.getElementById("reload").onclick = loadQueue;
    document.getElementById("status").onchange = loadQueue;

    document.getElementById("send").onclick = async () => {
      const out = document.getElementById("out");

      const project_id = Number(document.getElementById("project_id").value || 0);
      const task_id = Number(document.getElementById("task_id").value || 0);
      const student_id = Number(document.getElementById("student_id").value || 0);
      const submission_id = Number(document.getElementById("submission_id").value || 0);
      const rating = Number(document.getElementById("rating").value || 0);

      if (!project_id || !task_id || !student_id || !submission_id) {
        out.textContent = t("select_submission_first");
        return;
      }

      const score = Number(document.getElementById("score").value || 0);
      const final_decision = document.getElementById("decision").value;
      const comment = document.getElementById("feedback").value.trim();

      out.textContent = t("loading_submit");

      const r = await apiPost("phase3/partner_review_save.php", {
        project_id,
        task_id,
        student_id,
        score,
        rating,
        final_decision,
        comment
      });

      if (!r.ok) {
        out.textContent = `❌ ${r.error || t("failed_text")}`;
        return;
      }

      out.textContent = `✅ ${t("review_saved")}`;

      current = null;
      document.getElementById("details").style.display = "none";
      document.getElementById("selMsg").textContent = t("choose_submission_msg");

      await loadQueue();
    };

    document.querySelectorAll("[data-set-lang]").forEach(btn=>{
      btn.addEventListener("click", async () => {
        const lang = btn.getAttribute("data-set-lang");
        localStorage.setItem("lang", lang);
        await applyPageTranslations(lang);
        await loadQueue();
      });
    });

    (async function initPage(){
      const savedLang = localStorage.getItem("lang") || "en";
      await applyPageTranslations(savedLang);
      await loadQueue();
    })();
  </script>
</body>


</html>