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
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title data-i18n="phase3_results_title">Phase 3 Results</title>

  <link rel="stylesheet" href="assets/css/style.css"/>
  <script src="assets/js/i18n.js"></script>

 <style>
  :root{
    --navy:#0A2E5D;
    --navyHover:#144270;
    --navySoft:#1d4f85;
    --yellow:#FFC24A;
    --yellowSoft:#ffd778;
    --bg:#F6F7F9;
    --bg-2:#eef3f9;
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --line:#e5e7eb;
    --line-2:rgba(10,46,93,.10);
    --shadow:0 18px 45px rgba(10,46,93,.08);
    --shadow-sm:0 10px 24px rgba(15,23,42,.06);
    --shadow-lg:0 24px 55px rgba(10,46,93,.14);
    --r:18px;
    --r-lg:28px;
    --r-md:22px;
    --green:#10b981;
    --red:#ef4444;
    --orange:#f59e0b;
  }

  *{box-sizing:border-box}

 body{
  margin:0;
  color:var(--text);
  font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  background:
    radial-gradient(circle at 10% 10%, rgba(255,194,74,.14), transparent 22%),
    radial-gradient(circle at 90% 12%, rgba(10,46,93,.07), transparent 24%),
    linear-gradient(180deg, #f8fbff 0%, #f3f6fb 48%, #eef3f8 100%);
}

  .wrap{
  width:100%;
  margin:0;
  padding:0 0 42px;
}

  .topbar{
  width:100%;
  display:flex;
  gap:16px;
  align-items:center;
  justify-content:space-between;
  margin:0 0 18px;
  padding:26px 32px;
  border:none;
  border-radius:0;
  background:linear-gradient(90deg, rgba(255,255,255,.92) 0%, rgba(244,248,253,.96) 100%);
  box-shadow:none;
  flex-wrap:wrap;
  position:relative;
  overflow:hidden;
  min-height:120px;
}


  .topbar::before{
    content:"";
    position:absolute;
    top:-45px;
    right:-45px;
    width:140px;
    height:140px;
    border-radius:50%;
    background:rgba(255,194,74,.15);
    pointer-events:none;
  }

  .topbar::after{
    content:"";
    position:absolute;
    bottom:-50px;
    left:-40px;
    width:130px;
    height:130px;
    border-radius:50%;
    background:rgba(10,46,93,.06);
    pointer-events:none;
  }

  .topbar-left,
  .topbar-right{
    position:relative;
    z-index:2;
  }

  .topbar-left{
    display:flex;
    flex-direction:column;
    gap:5px;
  }

  .title{
    font-size:28px;
    font-weight:900;
    color:var(--navy);
    margin:0;
    letter-spacing:-.6px;
    line-height:1.15;
  }

  .subtitle{
    color:var(--muted);
    font-size:13px;
    margin:0;
    line-height:1.8;
  }

  .topbar-right{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  .actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
  }

  .btn{
    background:linear-gradient(135deg, var(--navy) 0%, var(--navySoft) 100%);
    color:#fff;
    border:0;
    padding:11px 15px;
    border-radius:14px;
    cursor:pointer;
    font-weight:800;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    box-shadow:0 14px 28px rgba(10,46,93,.16);
    font-size:13px;
  }

  .btn:hover{
    transform:translateY(-2px);
    background:linear-gradient(135deg, var(--navyHover) 0%, var(--navy) 100%);
    box-shadow:0 20px 42px rgba(10,46,93,.22);
  }

  .btn.secondary{
    background:#fff;
    color:var(--navy);
    border:1px solid rgba(10,46,93,.12);
    box-shadow:0 8px 18px rgba(15,23,42,.04);
  }

  .btn.secondary:hover{
    border-color:rgba(10,46,93,.20);
    background:#fdfefe;
  }

  .grid{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }

  .card{
    background:rgba(255,255,255,.92);
    border:1px solid rgba(255,255,255,.75);
    backdrop-filter:blur(8px);
    -webkit-backdrop-filter:blur(8px);
    border-radius:26px;
    padding:18px;
    box-shadow:var(--shadow-sm);
    position:relative;
    overflow:hidden;
  }

  .card::before{
    content:"";
    position:absolute;
    top:-35px;
    right:-30px;
    width:90px;
    height:90px;
    border-radius:22px;
    background:rgba(255,194,74,.08);
    transform:rotate(18deg);
    pointer-events:none;
  }

  .k{
    color:var(--muted);
    font-size:12px;
    margin-bottom:8px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.4px;
    position:relative;
    z-index:2;
  }

  .v{
    font-weight:900;
    font-size:28px;
    color:var(--navy);
    line-height:1;
    position:relative;
    z-index:2;
  }

  .sectionHead{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    margin-bottom:14px;
    position:relative;
    z-index:2;
  }

  .sectionTitle{
    font-weight:900;
    color:var(--navy);
    font-size:20px;
    margin:0;
    letter-spacing:-.3px;
  }

  .muted{
    color:var(--muted);
    font-size:13px;
    line-height:1.8;
  }

  .tableWrap{
    overflow:auto;
    border:1px solid var(--line);
    border-radius:20px;
    background:#fff;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.7);
  }

  .table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    min-width:1100px;
  }

  .table th,
  .table td{
    padding:14px 12px;
    border-bottom:1px solid var(--line);
    font-size:14px;
    vertical-align:top;
    text-align:start;
  }

  .table th{
    position:sticky;
    top:0;
    background:linear-gradient(180deg, #f8fbff 0%, #f2f7fd 100%);
    color:var(--navy);
    font-weight:800;
    white-space:nowrap;
    z-index:1;
  }

  .table tbody tr{
    transition:background .18s ease;
  }

  .table tbody tr:hover{
    background:rgba(10,46,93,.02);
  }

  .table tbody tr:last-child td{
    border-bottom:0;
  }

  .badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 11px;
    border-radius:999px;
    font-weight:800;
    font-size:12px;
    border:1px solid var(--line);
    background:#fff;
    white-space:nowrap;
    min-width:84px;
  }

  .b-pass{
    color:#065f46;
    background:rgba(16,185,129,.08);
    border-color:rgba(16,185,129,.22);
  }

  .b-fail{
    color:#991b1b;
    background:rgba(239,68,68,.08);
    border-color:rgba(239,68,68,.22);
  }

  .b-pend{
    color:#92400e;
    background:rgba(245,158,11,.10);
    border-color:rgba(245,158,11,.28);
  }

  .link{
    color:var(--navy);
    font-weight:800;
    text-decoration:none;
    word-break:break-word;
  }

  .link:hover{
    text-decoration:underline;
  }

  .details{
    margin-top:12px;
    border-top:1px dashed rgba(10,46,93,.16);
    padding-top:12px;
    display:none;
  }

  .details.open{
    display:block;
    animation:fadeSlide .2s ease;
  }

  @keyframes fadeSlide{
    from{
      opacity:0;
      transform:translateY(-4px);
    }
    to{
      opacity:1;
      transform:translateY(0);
    }
  }

  .detailsCard{
    background:linear-gradient(180deg, #fbfdff 0%, #f7faff 100%);
    border:1px solid rgba(10,46,93,.08);
    border-radius:16px;
    padding:12px;
    margin-bottom:10px;
    box-shadow:0 6px 14px rgba(15,23,42,.03);
  }

  .detailsTitle{
    font-weight:900;
    color:var(--navy);
    margin-bottom:8px;
    font-size:14px;
  }

  .toggleBtn{
    border:1px solid rgba(10,46,93,.15);
    background:#fff;
    color:var(--navy);
    border-radius:12px;
    padding:9px 12px;
    cursor:pointer;
    font-weight:800;
    transition:all .18s ease;
    box-shadow:0 6px 14px rgba(15,23,42,.04);
  }

  .toggleBtn:hover{
    background:rgba(10,46,93,.05);
    border-color:rgba(10,46,93,.24);
    transform:translateY(-1px);
  }

  .emptyState,
  .loadingState{
    text-align:center;
    padding:36px 20px;
    color:var(--muted);
    font-size:14px;
    line-height:1.9;
    background:linear-gradient(180deg, #fbfdff 0%, #f8fbff 100%);
    border:1px dashed rgba(10,46,93,.14);
    border-radius:18px;
  }

  ul.clean{
    margin:8px 0 0;
    padding-inline-start:18px;
  }

  ul.clean li{
    margin-bottom:6px;
    line-height:1.8;
  }

  .lang-dropdown{
    position:relative;
  }

  .lang-trigger{
    min-width:128px;
    justify-content:space-between;
    gap:8px;
  }

  .lang-menu{
    position:absolute;
    top:calc(100% + 8px);
    inset-inline-start:0;
    min-width:100%;
    background:#fff;
    border:1px solid rgba(10,46,93,.10);
    border-radius:14px;
    box-shadow:0 18px 36px rgba(15,23,42,.10);
    padding:6px;
    display:none;
    z-index:50;
  }

  .lang-dropdown.open .lang-menu{
    display:block;
    animation:fadeSlide .18s ease;
  }

  .lang-option{
    width:100%;
    border:0;
    background:transparent;
    padding:10px 12px;
    border-radius:10px;
    text-align:start;
    cursor:pointer;
    font-weight:600;
    transition:background .18s ease, color .18s ease;
  }

  .lang-option:hover,
  .lang-option.active{
    background:rgba(10,46,93,.08);
    color:#0A2E5D;
  }

  @media (max-width:980px){
    .grid{
      grid-template-columns:repeat(2, minmax(0,1fr));
    }
  }

  @media (max-width:640px){
    .wrap{
      padding:16px;
    }

   

    .title{
      font-size:23px;
    }

    .grid{
      grid-template-columns:1fr;
    }

    

   

    .card{
      padding:16px;
      border-radius:22px;
    }

    .sectionTitle{
      font-size:18px;
    }

    .v{
      font-size:24px;
    }
  }
  /* ===== NEW HEADER ONLY ===== */

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
  filter:drop-shadow(0 8px 16px rgba(0,0,0,.14));
}

.logo{
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:26px;
  color:#0b2f6b;
  text-decoration:none;
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

/* ===== PAGE SIZE SAME AS OTHER PAGES ===== */

.page-title-box,
.grid,
.wrap > .card{
  width:calc(100% - 130px);
  max-width:1360px;
  margin-left:auto;
  margin-right:auto;
}

.page-title-box{
  margin-top:50px;
  margin-bottom:22px;
}

.grid{
  margin-bottom:18px;
}

.wrap > .card{
  margin-top:0;
}

/* يخلي كروت الإحصائيات صح */
.grid .card{
  width:auto;
  max-width:none;
  margin:0;
}

/* يخفي تأثير التوب بار القديم لأنه انحذف */
.topbar{
  display:none;
}

@media(max-width:900px){
  .nav{
    flex-direction:column;
    align-items:flex-start;
  }

  .nav-links{
    width:100%;
    flex-wrap:wrap;
    gap:10px;
  }

  .page-title-box,
  .grid,
  .wrap > .card{
    width:calc(100% - 36px);
  }
}
</style>
</head>
<body>

<div class="wrap">

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
      <li><a href="#" onclick="history.back();return false;">Back</a></li>
      <li><a href="my_phase3_results.php" class="active">Phase 3 Results</a></li>
    </ul>
  </nav>
</header>

<section class="page-title-box">
  <h1 class="title" data-i18n="phase3_results_header">Phase 3 — Results</h1>
  <p class="subtitle" data-i18n="phase3_results_subtitle">
    View your interview simulations, scores, verdicts, and feedback
  </p>
</section>

  <div class="grid">
    <div class="card">
      <div class="k" data-i18n="total">Total</div>
      <div class="v" id="statTotal">0</div>
    </div>

    <div class="card">
      <div class="k" data-i18n="passed">Passed</div>
      <div class="v" id="statPassed">0</div>
    </div>

    <div class="card">
      <div class="k" data-i18n="failed">Failed</div>
      <div class="v" id="statFailed">0</div>
    </div>

    <div class="card">
      <div class="k" data-i18n="pending">PENDING</div>
      <div class="v" id="statPending">0</div>
    </div>
  </div>

  <div class="card">
    <div class="sectionHead">
      <h2 class="sectionTitle" data-i18n="phase3_results_list">Phase 3 Results</h2>
      <span class="muted" data-i18n="phase3_results_description">Here you can see all your submissions and results with company evaluation.</span>
    </div>

    <div id="resultsBox" class="loadingState" data-i18n="loading">Loading...</div>
  </div>

</div>

<script>
const API_BASE = "../utbn-backend/api";
let PHASE3_RESULTS = [];

function esc(s){
  return (s ?? "").toString()
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#39;");
}

function safeJsonParse(s){
  try { return JSON.parse(s); } catch { return null; }
}

function getLang(){
  if (typeof getCurrentLang === "function") {
    return getCurrentLang();
  }
  return localStorage.getItem("lang") || "en";
}

function decisionType(decision){
  const d = (decision || "").toString().trim().toUpperCase();

  if (!d) return "pending";
  if (d.includes("PASS") || d.includes("READY") || d.includes("ACCEPT")) return "pass";
  if (d.includes("FAIL") || d.includes("NOT READY") || d.includes("REJECT")) return "fail";
  return "pending";
}

function badgeClass(decision){
  const type = decisionType(decision);
  if (type === "pass") return "badge b-pass";
  if (type === "fail") return "badge b-fail";
  return "badge b-pend";
}

function updateStats(results){
  const total = results.length;
  const passed = results.filter(r => decisionType(r.decision || (safeJsonParse(r.grade_json)?.grade?.decision) || (safeJsonParse(r.grade_json)?.decision)) === "pass").length;
  const failed = results.filter(r => decisionType(r.decision || (safeJsonParse(r.grade_json)?.grade?.decision) || (safeJsonParse(r.grade_json)?.decision)) === "fail").length;
  const pending = total - passed - failed;

  document.getElementById("statTotal").textContent = total;
  document.getElementById("statPassed").textContent = passed;
  document.getElementById("statFailed").textContent = failed;
  document.getElementById("statPending").textContent = pending;
}

function renderResults(results){
  const box = document.getElementById("resultsBox");
  updateStats(results);

  if (!results.length) {
    box.className = "emptyState";
    box.textContent = t("no_phase3_results");
    return;
  }

  box.className = "";
  box.innerHTML = `
    <div class="tableWrap">
      <table class="table">
        <thead>
          <tr>
            <th>${t("task")}</th>
            <th>${t("company")}</th>
            <th>${t("role")}</th>
            <th>${t("decision")}</th>
            <th>${t("score")}</th>
            <th>${t("submitted")}</th>
            <th>${t("details")}</th>
          </tr>
        </thead>
        <tbody>
          ${results.map((r, i) => {
            const parsed = safeJsonParse(r.grade_json) || {};
            const g = parsed.grade || parsed || {};
            const feedback = g.feedback || "";
            const fixes = Array.isArray(g.fixes) ? g.fixes : [];
            const decision = (r.decision || g.decision || t("pending")).toString();
            const zipLink = r.zip_path ? `../utbn-backend/${r.zip_path}` : "";
            const rowId = `detailsRow${i}`;

            return `
              <tr>
                <td>
                  <strong>${esc(r.capstone_title || "—")}</strong>
                  <div class="muted" style="margin-top:4px">${esc(r.task_code || "—")}</div>
                </td>

                <td>${esc(r.company_name || "—")}</td>
                <td>${esc(r.role_name || "—")}</td>

                <td>
                  <span class="${badgeClass(decision)}">${esc(decision)}</span>
                </td>

                <td>${esc(r.score ?? "—")}</td>
                <td>${esc(r.submitted_at || "—")}</td>

                <td>
                  <button class="toggleBtn" type="button" onclick="toggleDetails('${rowId}')">
                    ${t("view")}
                  </button>

                  <div class="details" id="${rowId}">
                    <div class="detailsCard">
                      <div class="detailsTitle">${t("ai_result")}</div>

                      ${
                        feedback
                          ? `<div><strong>${t("feedback")}:</strong> ${esc(feedback)}</div>`
                          : `<div class="muted">${t("no_ai_feedback")}</div>`
                      }

                      ${
                        fixes.length
                          ? `<div style="margin-top:8px">
                              <strong>${t("fixes")}:</strong>
                              <ul class="clean">
                                ${fixes.map(x => `<li>${esc(x)}</li>`).join("")}
                              </ul>
                            </div>`
                          : ``
                      }
                    </div>

                    <div class="detailsCard">
                      <div class="detailsTitle">${t("company_review")}</div>
                      <div><strong>${t("rating")}:</strong> ${esc(r.partner_rating ?? "—")}</div>
                      <div style="margin-top:6px"><strong>${t("comment")}:</strong> ${esc(r.partner_comment ?? "—")}</div>
                      <div style="margin-top:6px"><strong>${t("reviewed_at")}:</strong> ${esc(r.partner_reviewed_at ?? "—")}</div>
                    </div>

                    ${
                      r.notes
                        ? `<div class="detailsCard">
                            <div class="detailsTitle">${t("your_notes")}</div>
                            <div>${esc(r.notes)}</div>
                          </div>`
                        : ``
                    }

                    ${
                      r.repo_url || zipLink
                        ? `<div class="detailsCard">
                            <div class="detailsTitle">${t("details")}</div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                              ${r.repo_url ? `<a class="btn secondary" target="_blank" href="${esc(r.repo_url)}">${t("open_repo")}</a>` : ``}
                              ${zipLink ? `<a class="btn secondary" target="_blank" href="${zipLink}">${t("download_zip")}</a>` : ``}
                            </div>
                          </div>`
                        : ``
                    }
                  </div>
                </td>
              </tr>
            `;
          }).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function toggleDetails(id){
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle("open");
}

async function loadResults(){
  const box = document.getElementById("resultsBox");
  box.className = "loadingState";
  box.textContent = t("loading");

  try {
    const res = await fetch(`${API_BASE}/phase3/my_results.php`, {
      credentials: "include"
    });

    const j = await res.json().catch(() => ({}));

    if (!res.ok || !j.ok) {
      box.className = "emptyState";
      box.textContent = t("failed") + ": " + (j.error || res.status || "Error");
      return;
    }

    PHASE3_RESULTS = Array.isArray(j.results) ? j.results : [];
    renderResults(PHASE3_RESULTS);
  } catch (e) {
    box.className = "emptyState";
    box.textContent = t("failed") + ": " + (e.message || "Error");
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  await loadResults();
});

document.addEventListener("languageChanged", () => {
  renderResults(PHASE3_RESULTS);

  const currentLangText = document.getElementById("currentLangText");
  if (currentLangText) {
    currentLangText.textContent = getLang() === "ar" ? "العربية" : "English";
  }
});
</script>

</body>
</html>