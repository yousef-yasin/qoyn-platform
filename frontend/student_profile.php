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
  <title>QOYN - My Profile Phase 2</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="assets/js/i18n.js"></script>

<style>
:root{
  --navy:#0A2E5D;
  --blue:#2f6fb5;
  --yellow:#FFC24A;
  --bg:#f7f9fc;
  --card:#ffffff;
  --text:#0f2344;
  --muted:#718096;
  --line:#e8edf5;
  --success:#10b981;
  --danger:#ef4444;
  --pending:#f59e0b;
  --shadow:0 8px 24px rgba(15,35,75,.055);
}

*{box-sizing:border-box}

body{
  margin:0;
  min-height:100vh;
   background:#ffffff;
  color:var(--text);
  font-family:"Poppins",sans-serif;
}

a,button,input,select{
  font-family:inherit;
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
  line-height:1;
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

.nav-links a,
.nav-links button{
  text-decoration:none;
  color:#111;
  background:transparent;
  border:0;
  font-weight:700;
  font-size:14px;
  padding:10px 14px;
  border-radius:999px;
  transition:.2s ease;
  white-space:nowrap;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  gap:8px;
}

.nav-links a:hover,
.nav-links button:hover{
  color:#ffb31f;
  transform:translateY(-1px);
}

.nav-links a.active{
  background:#ffb31f;
  color:#fff;
}

.nav-logout{
  border:1.5px solid #0b2f6b !important;
  color:#0b2f6b !important;
}

.nav-logout:hover{
  background:#0b2f6b !important;
  color:#fff !important;
}
.page{
  width:min(96%,1540px);
  margin:22px auto 34px;
  padding:0;
  background:#ffffff;
}

.profile-head{
  padding:0 0 10px;
}

.breadcrumb{
  display:flex;
  align-items:center;
  gap:10px;
  color:#718096;
  font-size:13px;
  font-weight:600;
}

.hero-badge{
  width:42px;
  height:32px;
  border-radius:12px;
  background:#e9f2ff;
  color:#2f6fb5;
  display:inline-grid;
  place-items:center;
  font-weight:900;
  margin-right:8px;
}

.topbar-left{
  display:flex;
  align-items:flex-start;
}

.title{
  margin:16px 0 6px;
  font-family:"Montserrat",sans-serif;
  color:var(--navy);
  font-size:31px;
  font-weight:900;
}

.subtitle{
  margin:0;
  color:#718096;
  font-size:14px;
}

.hero-glance{
  display:flex;
  gap:28px;
  margin-top:22px;
  border-bottom:1px solid #e8edf5;
}

.glance-chip{
  padding:0 0 12px;
  font-size:13px;
  color:#718096;
  font-weight:700;
}

.glance-chip:first-child{
  color:var(--navy);
  border-bottom:3px solid #2f6fb5;
}

.stats{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:16px;
  margin:16px 0;
}

.stat-card{
  background:#fff;
  border:1px solid var(--line);
  border-radius:14px;
  box-shadow:var(--shadow);
  padding:20px;
  min-height:118px;
  display:grid;
  grid-template-columns:54px 1fr 90px;
  gap:16px;
  align-items:center;
}

.stat-card::after{
  content:"";
  width:80px;
  height:38px;
  opacity:.9;
  background:
    linear-gradient(140deg, transparent 0 35%, rgba(47,111,181,.35) 36% 39%, transparent 40% 52%, rgba(47,111,181,.3) 53% 56%, transparent 57%),
    radial-gradient(circle at 20% 65%, rgba(47,111,181,.22) 0 3px, transparent 4px),
    radial-gradient(circle at 70% 30%, rgba(255,194,74,.28) 0 3px, transparent 4px);
}

.stat-top{display:contents}

.stat-icon{
  width:52px;
  height:52px;
  border-radius:20px;
  background:#f1f6ff;
  display:grid;
  place-items:center;
  font-size:24px;
}

.stat-card:nth-child(2) .stat-icon{background:#fff7e7}
.stat-card:nth-child(3) .stat-icon{background:#eafaf3}

.stat-label{
  margin:0 0 8px;
  color:#718096;
  font-size:13px;
  font-weight:700;
}

.stat-value{
  margin:0;
  color:var(--navy);
  font-family:"Montserrat",sans-serif;
  font-weight:900;
  font-size:28px;
}

.stat-foot{
  grid-column:2/3;
  color:#718096;
  font-size:12px;
  line-height:1.6;
}

.filters{
  background:#fff;
  border:1px solid var(--line);
  border-radius:14px;
  box-shadow:var(--shadow);
  display:grid;
  grid-template-columns:1.1fr .8fr .8fr .8fr 1.5fr 54px;
  margin:14px 0;
  overflow:hidden;
}

.filter-box{
  min-height:54px;
  padding:9px 16px;
  border-right:1px solid var(--line);
  display:flex;
  flex-direction:column;
  justify-content:center;
  gap:3px;
}

.filter-box label{
  font-size:11px;
  color:#718096;
  font-weight:700;
}

.filter-box select,
.filter-box input{
  border:0;
  outline:0;
  background:transparent;
  color:#24364c;
  font-weight:700;
  width:100%;
}

.filter-icon{
  border:0;
  background:#fff;
  color:var(--navy);
  font-size:18px;
}

.sectionHead{
  padding:4px 0 8px;
}

.sectionTitle{
  margin:0;
  font-family:"Montserrat",sans-serif;
  color:var(--navy);
  font-size:22px;
  font-weight:900;
}

.sectionSub{
  margin:6px 0 0;
  color:#718096;
  font-size:13px;
}

.panel{
  background:#fff;
  border:1px solid var(--line);
  border-radius:14px;
  box-shadow:var(--shadow);
  overflow:hidden;
}

.tableWrap{overflow:auto}

.table{
  width:100%;
  min-width:1050px;
  border-collapse:collapse;
}

.table th{
  text-align:start;
  background:#fff;
  color:#526173;
  font-size:11px;
  text-transform:uppercase;
  padding:12px 16px;
  border-bottom:1px solid var(--line);
}

.table td{
  padding:14px 16px;
  border-bottom:1px solid var(--line);
  font-size:13px;
  vertical-align:middle;
}

.table tbody tr:hover{background:#fbfdff}

.project-cell{
  display:flex;
  align-items:center;
  gap:14px;
}

.project-avatar{
  width:46px;
  height:46px;
  border-radius:18px;
  display:grid;
  place-items:center;
  background:#f1f6ff;
  font-size:20px;
  flex:0 0 46px;
}

.project-name{
  font-weight:900;
  color:#172b4d;
  line-height:1.4;
}

.meta-row{
  display:flex;
  gap:7px;
  flex-wrap:wrap;
  color:#718096;
  font-size:11px;
  margin-top:5px;
}

.badge{
  display:inline-flex;
  padding:5px 9px;
  border-radius:999px;
  font-size:10px;
  font-weight:900;
  background:#eef2f7;
  color:#526173;
}

.b-pass{background:#dff8ed;color:#059669}
.b-fail{background:#fee2e2;color:#dc2626}
.b-pend{background:#fff2d6;color:#d97706}

.soft-text{
  color:#718096;
  font-size:12px;
  margin-top:5px;
}

.score-pill{
  color:var(--navy);
  font-weight:900;
}

.score-pill .dim{
  color:#718096;
  font-size:11px;
  margin-left:3px;
}

.coin-line .score-pill::before{
  content:"●";
  color:#f5b82e;
  margin-right:6px;
}

.score-stack,
.status-stack,
.coin-stack{
  display:flex;
  flex-direction:column;
  gap:6px;
}

.details-toggle{
  background:transparent;
  color:#2f6fb5;
  border:none;
  padding:0;
  font-weight:800;
  cursor:pointer;
}

.details-toggle::after{content:" →"}

.details{
  margin-top:10px;
  display:none;
}

.details.open{display:block}

.detailsCard{
  background:#f8fafc;
  border:1px solid #edf1f6;
  border-radius:14px;
  padding:12px;
  margin-top:8px;
}

.detailsTitle{
  color:var(--navy);
  font-weight:900;
  margin-bottom:8px;
}

.detailsLine{
  font-size:12px;
  color:#334155;
  line-height:1.7;
}

.details pre{
  background:#0f172a;
  color:#e2e8f0;
  padding:12px;
  border-radius:12px;
  overflow:auto;
  font-size:11px;
}

.note{
  margin-top:14px;
  background:#fff;
  border:1px solid var(--line);
  border-radius:14px;
  padding:14px;
  color:#718096;
  font-size:12px;
}

@media(max-width:1100px){
  .stats{grid-template-columns:1fr}
  .stat-card{grid-template-columns:54px 1fr}
  .stat-card::after{display:none}
  .filters{grid-template-columns:1fr}
  .filter-box{border-right:0;border-bottom:1px solid var(--line)}
}

@media(max-width:700px){
  .page{padding:18px 16px 30px}
  .nav{
  flex-direction:column;
  align-items:flex-start;
}

.nav-links{
  width:100%;
  flex-wrap:wrap;
  gap:10px;
}
  .topbar-left{flex-direction:column}
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
      <li><a href="my_courses.php">My courses</a></li>
      <li><a href="courses.php">All courses</a></li>
      <li><a href="my_phase3_results.php" class="active">Phase 3 Results</a></li>
      <li><button type="button" onclick="loadData()">⟳ Refresh</button></li>
    </ul>
  </nav>
</header>

<div class="page">

  <section class="profile-head">
    <div class="topbar-left">
      <div class="hero-badge">P2</div>

      <div>
        <div class="breadcrumb">
          <span>Student Center</span>
          <span>›</span>
          <span>Phase 2 Overview</span>
        </div>

        <h1 class="title">My Profile - Phase 2</h1>
        <p class="subtitle">Shows AI + Company + Final + Coins for each project</p>

        <div class="hero-glance">
          <span class="glance-chip">AI Review</span>
          <span class="glance-chip">Company Review</span>
          <span class="glance-chip">Final Score</span>
          <span class="glance-chip">Coins Tracking</span>
        </div>
      </div>
    </div>
  </section>

  <section class="stats">
    <div class="stat-card">
      <div class="stat-top">
        <p class="stat-label">Submissions Count</p>
        <div class="stat-icon">📄</div>
      </div>
      <p class="stat-value" id="stat_subs">—</p>
      <div class="stat-foot">Track how many phase 2 submissions are already recorded on your profile.</div>
    </div>

    <div class="stat-card">
      <div class="stat-top">
        <p class="stat-label">Best Final Score</p>
        <div class="stat-icon">🏆</div>
      </div>
      <p class="stat-value" id="stat_best">—</p>
      <div class="stat-foot">Shows your top visible result based on the final review or the best available score.</div>
    </div>

    <div class="stat-card">
      <div class="stat-top">
        <p class="stat-label">Total Coins Earned</p>
        <div class="stat-icon">🎯</div>
      </div>
      <p class="stat-value" id="stat_coins">—</p>
      <div class="stat-foot">A quick summary of all earned coins collected across your displayed projects.</div>
    </div>
  </section>

  <section class="filters">
    <div class="filter-box">
      <label>All Projects</label>
      <select id="projectFilter">
        <option value="">All projects</option>
      </select>
    </div>

    <div class="filter-box">
      <label>Status</label>
      <select id="statusFilter">
        <option value="">All</option>
        <option value="SUBMITTED">Submitted</option>
        <option value="REVIEWED">Reviewed</option>
        <option value="PASS">Pass</option>
        <option value="PENDING">Pending</option>
      </select>
    </div>

    <div class="filter-box">
      <label>AI Model</label>
      <select id="aiFilter">
        <option value="">All</option>
      </select>
    </div>

    <div class="filter-box">
      <label>Company</label>
      <select id="companyFilter">
        <option value="">All</option>
      </select>
    </div>

    <div class="filter-box">
      <label>Search</label>
      <input id="searchInput" type="search" placeholder="Search projects...">
    </div>

    <button class="filter-icon" type="button" onclick="resetFilters()">☷</button>
  </section>

  <section class="sectionHead">
    <h2 class="sectionTitle">My Submissions</h2>
    <p class="sectionSub">Professional summary of your project submissions, review stages, and detailed feedback.</p>
  </section>

  <section class="panel">
    <div class="tableWrap">
      <table class="table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Status</th>
            <th>AI</th>
            <th>Company</th>
            <th>Final</th>
            <th>Coins</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody id="rows">
          <tr>
            <td colspan="7">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <div class="note">
    Note: If the Company Review has not happened yet, Company/Final will appear empty.
  </div>
</div>

<script>
const API_URL = "../utbn-backend/api/student_profile_phase2.php";
let PROFILE_ITEMS = [];

if (typeof window.t !== "function") {
  window.t = function(key){ return key.replaceAll("_", " "); };
}

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, (c) => ({
    "&":"&amp;",
    "<":"&lt;",
    ">":"&gt;",
    '"':"&quot;",
    "'":"&#39;"
  }[c]));
}

function badge(dec){
  const d = String(dec || "").toUpperCase();

  if (d === "PASS") return `<span class="badge b-pass">PASS</span>`;
  if (d === "FAIL") return `<span class="badge b-fail">FAIL</span>`;
  if (d === "PENDING") return `<span class="badge b-pend">PENDING</span>`;
  if (d === "NEEDS_FIX") return `<span class="badge b-pend">NEEDS_FIX</span>`;
  if (d === "SUBMITTED") return `<span class="badge b-pass">SUBMITTED</span>`;
  if (d === "REVIEWED") return `<span class="badge">REVIEWED</span>`;

  return `<span class="badge">${esc(d || "-")}</span>`;
}

function fmtScore(x){
  if (x === null || x === undefined || x === "") {
    return `<span class="soft-text">—</span>`;
  }

  return `<span class="score-pill"><span>${esc(x)}</span><span class="dim">/100</span></span>`;
}

function projectIcon(index){
  const icons = ["🤖","✂️","🏗️","🌐","📄","🧠","⚙️"];
  return icons[index % icons.length];
}

function renderStats(items){
  document.getElementById("stat_subs").textContent = items.length;

  let best = null;
  let coinsSum = 0;

  for (const it of items){
    const finalScore = it.final?.score;
    const aiScore = it.ai?.score;
    const scorePick = (finalScore !== null && finalScore !== undefined) ? finalScore : aiScore;

    if (scorePick !== null && scorePick !== undefined && scorePick !== "") {
      if (best === null || Number(scorePick) > Number(best)) {
        best = scorePick;
      }
    }

    coinsSum += Number(it.coins?.awarded || 0);
  }

  document.getElementById("stat_best").textContent = best === null ? "—" : `${best}/100`;
  document.getElementById("stat_coins").textContent = coinsSum.toLocaleString();
}

function getFilteredItems(){
  const search = String(document.getElementById("searchInput")?.value || "").toLowerCase().trim();
  const status = String(document.getElementById("statusFilter")?.value || "").toUpperCase();
  const ai = String(document.getElementById("aiFilter")?.value || "").toLowerCase();
  const project = String(document.getElementById("projectFilter")?.value || "").toLowerCase();

  return PROFILE_ITEMS.filter(it => {
    const title = String(it.project_title || "").toLowerCase();
    const st = String(it.status || "").toUpperCase();
    const model = String(it.ai?.ai_model || "").toLowerCase();

    if (search && !title.includes(search)) return false;
    if (project && title !== project) return false;
    if (status && !st.includes(status) && String(it.ai?.decision || "").toUpperCase() !== status) return false;
    if (ai && model !== ai) return false;

    return true;
  });
}

function renderRows(items){
  const tbody = document.getElementById("rows");

  if (!items.length){
    tbody.innerHTML = `<tr><td colspan="7">No submissions found.</td></tr>`;
    return;
  }

  tbody.innerHTML = items.map((it, index) => {
    const sid = it.submission_id;
    const proj = esc(it.project_title || ("Project #" + it.project_id));
    const status = esc(it.status || "-");

    const aiS = it.ai?.score ?? null;
    const aiD = it.ai?.decision ?? "";
    const coS = it.company?.score ?? null;
    const coD = it.company?.decision ?? "";
    const fiS = it.final?.score ?? null;
    const fiD = it.final?.decision ?? "";

    const coinsA = Number(it.coins?.awarded || 0);
    const coinsT = Number(it.coins?.total || 0);

    const aiFeedback = it.ai?.feedback || "—";
    const coFeedback = it.company?.feedback || "—";
    const fiFeedback = it.final?.feedback || "—";

    const aiFixes = it.ai?.fixes || [];
    const coFixes = it.company?.fixes || [];
    const fiFixes = it.final?.fixes || [];

    return `
      <tr>
        <td>
          <div class="project-cell">
            <div class="project-avatar">${projectIcon(index)}</div>
            <div>
              <div class="project-name">${proj}</div>
              <div class="meta-row">
                <span>Submission #${esc(sid)}</span>
                <span>•</span>
                <span>${esc(it.created_at || "")}</span>
              </div>
              <div class="soft-text">AI Model: ${esc(it.ai?.ai_model || "-")}</div>
            </div>
          </div>
        </td>

        <td>
          <div class="status-stack">
            <div>${badge(status)}</div>
            <div class="soft-text">Mode: ${esc(it.review_mode || "-")}</div>
          </div>
        </td>

        <td>
          <div class="score-stack">
            <div>${fmtScore(aiS)}</div>
            <div>${badge(aiD)}</div>
          </div>
        </td>

        <td>
          <div class="score-stack">
            <div>${fmtScore(coS)}</div>
            <div>${badge(coD)}</div>
          </div>
        </td>

        <td>
          <div class="score-stack">
            <div>${fmtScore(fiS)}</div>
            <div>${badge(fiD)}</div>
          </div>
        </td>

        <td>
          <div class="coin-stack">
            <div class="coin-line">
              <span class="score-pill"><span>${coinsA.toLocaleString()}</span></span>
            </div>
            <div class="soft-text">earned</div>
            <div class="soft-text">eligibility: ${coinsT.toLocaleString()}</div>
          </div>
        </td>

        <td style="min-width:220px">
          <button class="details-toggle" type="button" onclick="toggleDetails(${sid})">
            View Details
          </button>

          <div class="details" id="det_${sid}">
            <div class="detailsCard">
              <div class="detailsTitle">Feedback</div>
              <div class="detailsLine"><b>AI:</b> ${esc(aiFeedback)}</div>
              <div class="detailsLine"><b>Company:</b> ${esc(coFeedback)}</div>
              <div class="detailsLine"><b>Final:</b> ${esc(fiFeedback)}</div>
            </div>

            <div class="detailsCard">
              <div class="detailsTitle">Fixes</div>
              <pre>${esc(JSON.stringify({ aiFixes, coFixes, fiFixes }, null, 2))}</pre>
            </div>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

function toggleDetails(id){
  const el = document.getElementById("det_" + id);
  if (!el) return;
  el.classList.toggle("open");
}

function renderFiltered(){
  renderRows(getFilteredItems());
}

function populateFilters(items){
  const projectFilter = document.getElementById("projectFilter");
  const aiFilter = document.getElementById("aiFilter");

  const projects = [...new Set(items.map(x => String(x.project_title || "").trim()).filter(Boolean))];
  const models = [...new Set(items.map(x => String(x.ai?.ai_model || "").trim()).filter(Boolean))];

  projectFilter.innerHTML = `<option value="">All projects</option>` + projects.map(p => {
    return `<option value="${esc(p.toLowerCase())}">${esc(p)}</option>`;
  }).join("");

  aiFilter.innerHTML = `<option value="">All</option>` + models.map(m => {
    return `<option value="${esc(m.toLowerCase())}">${esc(m)}</option>`;
  }).join("");
}

function resetFilters(){
  document.getElementById("projectFilter").value = "";
  document.getElementById("statusFilter").value = "";
  document.getElementById("aiFilter").value = "";
  document.getElementById("companyFilter").value = "";
  document.getElementById("searchInput").value = "";
  renderFiltered();
}

async function loadData(){
  const tbody = document.getElementById("rows");
  tbody.innerHTML = `<tr><td colspan="7">Loading...</td></tr>`;

  try{
    const r = await fetch(API_URL, { credentials: "include" });
    const data = await r.json();

    if (!r.ok || !data.ok) {
      throw new Error(data?.error || "API_FAILED");
    }

    PROFILE_ITEMS = Array.isArray(data.items) ? data.items : [];
    renderStats(PROFILE_ITEMS);
    populateFilters(PROFILE_ITEMS);
    renderRows(PROFILE_ITEMS);

  }catch(e){
    tbody.innerHTML = `
      <tr>
        <td colspan="7" style="color:#b91c1c;font-weight:800">
          Failed loading data: ${esc(e.message)}
        </td>
      </tr>
    `;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const langDropdown = document.getElementById("langDropdown");
  const langTrigger = document.getElementById("langTrigger");

  langTrigger?.addEventListener("click", () => {
    langDropdown?.classList.toggle("open");
  });

  document.querySelectorAll(".lang-option").forEach(btn => {
    btn.addEventListener("click", () => {
      const lang = btn.dataset.lang;
      localStorage.setItem("lang", lang);

      if (typeof setLanguage === "function") {
        setLanguage(lang);
      }

      document.getElementById("currentLangText").textContent = lang === "ar" ? "العربية" : "English";
      langDropdown?.classList.remove("open");
    });
  });

  ["searchInput","projectFilter","statusFilter","aiFilter","companyFilter"].forEach(id => {
    document.getElementById(id)?.addEventListener("input", renderFiltered);
    document.getElementById(id)?.addEventListener("change", renderFiltered);
  });

  loadData();
});

document.addEventListener("languageChanged", () => {
  renderStats(PROFILE_ITEMS);
  renderRows(getFilteredItems());
});
</script>
</body>
</html>