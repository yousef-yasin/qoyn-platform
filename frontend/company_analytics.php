<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") { header("Location: index.php"); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Company Analytics - Phase 3</title>
  <link rel="stylesheet" href="assets/css/style.css"/>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#082d5f;
      --navy-2:#123d73;
      --blue:#2578ff;
      --bg:#f6f8fc;
      --text:#0b2856;
      --muted:#64748b;
      --line:#e8edf5;
      --card:#ffffff;
      --shadow:0 18px 45px rgba(11,40,86,.08);
      --soft-shadow:0 10px 30px rgba(11,40,86,.06);
      --container:1400px;
      --radius:18px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--text);
      background:
        radial-gradient(circle at 92% 18%, rgba(213,225,255,.48) 0 160px, transparent 360px),
        linear-gradient(180deg,#fbfcff 0%,#f7f9fd 100%);
      direction:ltr;
      overflow-x:hidden;
      min-height:100vh;
    }

    .container{
      width:100%;
      max-width:none;
      margin:0;
      padding:0;
    }

    .phase-navbar{
      position:sticky;
      top:0;
      left:0;
      width:100%;
      z-index:9999;
      height:78px;
      background:#fff;
      backdrop-filter:none;
      border-bottom:1px solid #e6ebf3;
      box-shadow:none;
      padding:0 34px;
    }

    .phase-navbar-inner{
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      direction:ltr;
    }

    main{
      width:min(var(--container), calc(100% - 88px));
      margin:0 auto;
      padding:40px 0 64px;
      direction:ltr;
    }

    .logo{
      font-family:"Montserrat",sans-serif;
      font-weight:800;
      font-size:34px;
      letter-spacing:1.2px;
      color:var(--navy);
      text-decoration:none;
      line-height:1;
    }

    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:13px 20px;
      border-radius:14px;
      text-decoration:none;
      cursor:pointer;
      user-select:none;
      font-family:"Montserrat",sans-serif;
      font-weight:800;
      font-size:13px;
      border:1px solid transparent;
      background:var(--navy);
      color:#fff;
      box-shadow:0 14px 28px rgba(8,45,95,.18);
      transition:transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    }

    .btn:hover{transform:translateY(-2px);background:var(--navy-2);box-shadow:0 18px 38px rgba(8,45,95,.22)}
    .btn.secondary{background:#fff;color:var(--navy);border:1px solid #dbe5f4;box-shadow:0 8px 22px rgba(8,45,95,.04)}
    .btn.secondary:hover{background:#f7faff;border-color:#bfd2ee}
    .btn.small{padding:10px 16px;font-size:13px;border-radius:12px}

    .back-btn{min-width:118px;height:52px;border-radius:999px;font-size:14px;justify-content:center}
    .back-btn:before{content:"←";font-size:18px;line-height:1;margin-top:-1px}

    .hero-head{
      position:relative;
      min-height:126px;
      display:flex;
      align-items:center;
      justify-content:flex-start;
      gap:28px;
      margin-bottom:30px;
      overflow:hidden;
      direction:ltr;
      text-align:left;
    }

    .hero-title-wrap{display:flex;align-items:center;gap:20px;direction:ltr;text-align:left;margin:0;position:relative;z-index:2;flex:0 0 auto}
    .hero-icon{
      width:60px;height:60px;border-radius:16px;
      display:grid;place-items:center;flex:0 0 auto;
      background:linear-gradient(135deg,#7b61ff 0%,#8e8cff 100%);
      box-shadow:0 16px 32px rgba(123,97,255,.25);
      color:#fff;
    }
    .hero-icon svg{width:30px;height:30px;stroke-width:2.2}

    .hero-copy{direction:ltr}
    .hero-copy h1{
      margin:0 0 10px;
      font-family:"Montserrat",sans-serif;
      font-weight:800;
      font-size:38px;
      letter-spacing:-1.4px;
      color:var(--navy);
      line-height:1.12;
    }
    .hero-copy .muted{font-size:14px;color:#64748b;direction:rtl;text-align:left}

    .hero-art{
      position:absolute;
      right:0;
      top:0;
      width:280px;
      height:132px;
      opacity:.36;
      pointer-events:none;
      background:
        linear-gradient(145deg, rgba(255,255,255,.75), rgba(234,240,255,.85));
      border-radius:28px;
      transform:rotate(-8deg) translate(26px,-8px);
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.8), 0 24px 50px rgba(88,115,190,.18);
    }
    .hero-art:before,
    .hero-art:after{content:"";position:absolute;bottom:22px;width:22px;border-radius:10px;background:linear-gradient(180deg,#a6bbff,#dfe7ff)}
    .hero-art:before{right:44px;height:82px;box-shadow:-46px 10px 0 #cbd8ff,-92px 24px 0 #dbe4ff,-138px 34px 0 #e8eeff}
    .hero-art:after{right:42px;bottom:118px;width:8px;height:8px;border-radius:50%;background:#9db6ff;box-shadow:-46px 12px 0 #b3c5ff,-92px 31px 0 #c9d7ff,-138px 45px 0 #d9e3ff}

    .stats-grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:18px;
      margin-bottom:20px;
      direction:ltr;
    }
    .stat-card{
      min-height:108px;
      background:rgba(255,255,255,.92);
      border:1px solid var(--line);
      border-radius:14px;
      box-shadow:var(--soft-shadow);
      padding:22px;
      display:flex;
      align-items:center;
      gap:20px;
      direction:ltr;
    }
    .stat-icon{width:58px;height:58px;border-radius:15px;display:grid;place-items:center;flex:0 0 auto}
    .stat-icon svg{width:28px;height:28px;stroke-width:2.2}
    .stat-icon.purple{background:#f0eafe;color:#755cff}.stat-icon.gold{background:#fff5e5;color:#e09a00}.stat-icon.blue{background:#eaf4ff;color:#277bdc}.stat-icon.green{background:#e7fbf3;color:#25ad86}
    .stat-label{font-size:14px;color:#42526e;margin-bottom:5px}
    .stat-number{font-family:"Montserrat",sans-serif;font-weight:800;font-size:30px;line-height:1;color:var(--navy);display:inline-block;margin-right:8px}
    .stat-unit{font-size:14px;color:#64748b}

    .layout{display:grid;grid-template-columns:1fr;gap:18px;align-items:start}

    .panel{
      width:100%;
      background:rgba(255,255,255,.96);
      border:1px solid var(--line);
      border-radius:18px;
      box-shadow:var(--shadow);
      padding:16px 18px 22px;
    }

    .panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px;direction:ltr}
    .panel-title{display:flex;align-items:center;gap:10px;font-family:"Montserrat",sans-serif;font-weight:800;color:var(--navy)}
    .panel-title svg{width:20px;height:20px;color:#6aa5ff;stroke-width:2}

    .muted{color:#64748b;font-size:13.5px;line-height:1.75}
    .empty{padding:18px 0;color:#64748b}

    .table-wrap{overflow-x:auto;border-radius:12px;border:1px solid #edf1f7;margin-top:8px}
    table{width:100%;border-collapse:collapse;background:#fff;margin:0;direction:rtl}
    th,td{padding:13px 14px;border-bottom:1px solid #edf1f7;font-size:13px;text-align:right;vertical-align:middle;color:#102a55}
    thead th{background:#fafbfd;font-family:"Montserrat",sans-serif;font-weight:800;font-size:12.5px;color:#354763;white-space:nowrap}
    tbody tr{transition:background .16s ease, transform .16s ease}
    tbody tr:hover{background:#f4f8ff}
    tbody tr:nth-child(3){background:#f4f7fd}
    tbody tr:last-child td{border-bottom:0}

    .project-name{font-family:"Montserrat",sans-serif;font-weight:800;color:#102a55;line-height:1.45}
    .row-meta{font-size:12.5px;color:#71809a;margin-top:3px;direction:ltr;text-align:right}
    .actions{white-space:nowrap;color:#9aa6b6;direction:ltr;text-align:left}
    .link{color:#1f7ad9;text-decoration:none;font-weight:800;cursor:pointer;transition:color .16s ease}
    .link:hover{color:var(--navy);text-decoration:underline}

    .status-badge{display:inline-flex;align-items:center;justify-content:center;min-width:78px;padding:7px 12px;border-radius:999px;font-family:"Montserrat",sans-serif;font-size:12px;font-weight:800;text-transform:uppercase}
    .status-badge.published,.status-badge.pass,.status-badge.selected,.status-badge.reviewed,.status-badge.final,.status-badge.active{background:#fff5df;color:#d28a00}
    .status-badge.draft{background:#eaf4ff;color:#2a7fd8}
    .status-badge.matched{background:#e8fbf4;color:#24a77f}
    .status-badge.new{background:#f4eaff;color:#8a57df}
    .status-badge.default{background:#f1f5f9;color:#64748b}

    .pill{display:inline-flex;align-items:center;padding:7px 11px;border-radius:999px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;font-weight:700;margin:4px 6px 0 0;color:#334155}
    .ok{color:#0F9D58;font-weight:900}.bad{color:#D93025;font-weight:900}.warn{color:#B26A00;font-weight:900}

    .topBox{margin-top:12px;padding:14px 16px;border-radius:16px;background:#f5f9ff;border:1px solid #dbe8fb;color:#102a55}
    .errorBox{margin-top:10px;padding:12px 14px;border-radius:14px;background:#fff4f4;border:1px solid #f2caca;color:#8a1f1f;font-size:13px;line-height:1.8;white-space:pre-wrap;overflow:auto;max-height:220px}

    .reportModal{position:fixed;inset:0;background:rgba(8,45,95,.42);display:none;align-items:center;justify-content:center;z-index:10000;padding:20px;backdrop-filter:blur(4px)}
    .reportCard{width:min(980px, 100%);max-height:88vh;overflow:auto;background:#fff;border-radius:24px;box-shadow:0 22px 70px rgba(8,45,95,.25);padding:24px;border:1px solid #e6ebf3}
    .reportHead{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px;direction:ltr}.reportHead h3{margin:0;font-family:"Montserrat",sans-serif;font-size:24px;color:var(--navy)}
    .reportSection{margin-top:18px;padding:16px;border:1px solid #e6ebf3;border-radius:16px;background:#fbfcff}.reportSection h4{margin:0 0 10px;font-family:"Montserrat",sans-serif;font-size:16px;color:var(--navy)}
    .reportList{margin:0;padding-right:18px}.reportList li{margin:6px 0;line-height:1.8}.reportBreakdown{width:100%;border-collapse:collapse;margin-top:8px}.reportBreakdown th,.reportBreakdown td{border-bottom:1px solid #edf1f7;padding:10px 8px;text-align:right;vertical-align:top}


    .hero-head > *{flex-shrink:0}
    .hero-copy,.hero-copy h1,.hero-copy .muted{direction:ltr;text-align:left}
    .hero-copy .muted{max-width:780px}
    .layout,.panel,.stats-grid{direction:ltr}
    .panel{margin-top:0}

    @media(max-width:1050px){main{width:calc(100% - 36px);padding-top:28px}.stats-grid{grid-template-columns:repeat(2,1fr)}.hero-art{opacity:.18}.hero-copy h1{font-size:32px}}
    @media(max-width:650px){.phase-navbar{padding:0 18px}.logo{font-size:28px}.back-btn{min-width:auto}.stats-grid{grid-template-columns:1fr}.hero-title-wrap{align-items:flex-start}.hero-copy h1{font-size:28px}.hero-icon{width:52px;height:52px}.panel{padding:14px}.stat-card{padding:18px}}
  </style>
</head>
<body>
  <div class="container">

    <header class="phase-navbar">
      <div class="phase-navbar-inner">
        <a class="btn secondary back-btn" href="company.php">Back</a>
        <a class="logo" href="company.php">QOYN</a>
      </div>
    </header>

    <main>
      <section class="hero-head">
        <div class="hero-title-wrap">
          <div class="hero-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19h16"/><path d="M7 16V9"/><path d="M12 16V5"/><path d="M17 16v-8"/></svg>
          </div>
          <div class="hero-copy">
            <h1>Phase 3 Analytics</h1>
            <div class="muted">المشاريع ← التيمات ← أعضاء التيم. ومن خلاله تستكشف متوسط كل تقييم، الأعضاء، الملفات، والتقييم النهائي.</div>
          </div>
        </div>
        <div class="hero-art" aria-hidden="true"></div>
      </section>

      <section class="stats-grid" aria-label="Project statistics">
        <div class="stat-card">
          <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 6V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1"/><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 12h18"/><path d="M9 12v2h6v-2"/></svg></div>
          <div><div class="stat-label">Total Projects</div><div><span class="stat-number" id="statTotal">0</span><span class="stat-unit">مشاريع</span></div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 15c-1.5 1.2-2 3-2 6 3 0 4.8-.5 6-2"/><path d="M9 15 15 9"/><path d="M15 9c1-4 4-6 6-6 0 2-2 5-6 6Z"/><circle cx="14" cy="10" r="2"/></svg></div>
          <div><div class="stat-label">Published</div><div><span class="stat-number" id="statPublished">0</span><span class="stat-unit">منشورة</span></div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div>
          <div><div class="stat-label">Draft</div><div><span class="stat-number" id="statDraft">0</span><span class="stat-unit">مسودة</span></div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M17 11l2 2 4-4"/></svg></div>
          <div><div class="stat-label">Matched</div><div><span class="stat-number" id="statMatched">0</span><span class="stat-unit">متطابقة</span></div></div>
        </div>
      </section>

      <div class="layout">
        <div class="panel">
          <div class="panel-head">
            <button class="btn small" id="reloadProjects" type="button">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 0 1-15.5 6.3"/><path d="M3 12A9 9 0 0 1 18.5 5.7"/><path d="M3 17v5h5"/><path d="M21 7V2h-5"/></svg>
              Refresh
            </button>
            <div class="panel-title">
              <span>مشاريعي</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/></svg>
            </div>
          </div>
          <div id="projectsBox" class="muted" style="margin-top:8px">Loading...</div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <span class="muted" id="projectTitle">اختر مشروع</span>
            <div class="panel-title"><span>تيمات المشروع</span></div>
          </div>
          <div id="projectInfo" class="muted" style="margin-top:10px">—</div>
          <div id="teamsBox" class="muted" style="margin-top:12px">—</div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <span class="muted" id="teamTitle">اختر Team</span>
            <div class="panel-title"><span>أعضاء التيم</span></div>
          </div>
          <div id="topTeamBox" style="display:none" class="topBox"></div>
          <div id="membersBox" class="muted" style="margin-top:12px">—</div>
        </div>
      </div>
    </main>
  </div>

  <div class="reportModal" id="reportModal">
    <div class="reportCard">
      <div class="reportHead">
        <h3 id="reportModalTitle">AI Report</h3>
        <div style="display:flex;gap:8px">
          <button class="btn small secondary" type="button" onclick="closeReportModal()">Close</button>
        </div>
      </div>
      <div id="reportBody" class="muted">Loading...</div>
    </div>
  </div>

<script>
const API = "/utbn-backend/api";

let currentProject = null;
let currentTeam = null;

function esc(s){
  return String(s ?? "").replace(/[&<>"']/g, m => ({
    "&":"&amp;",
    "<":"&lt;",
    ">":"&gt;",
    '"':"&quot;",
    "'":"&#039;"
  }[m]));
}

function qs(k){
  const u = new URL(location.href);
  return u.searchParams.get(k);
}

function setQs(projectId = null, teamId = null){
  const u = new URL(location.href);
  if (projectId) u.searchParams.set("project_id", String(projectId));
  else u.searchParams.delete("project_id");

  if (teamId) u.searchParams.set("team_id", String(teamId));
  else u.searchParams.delete("team_id");

  history.replaceState({}, "", u.toString());
}

function debugErrorHtml(obj){
  return `
    <div class="errorBox">
      <div><b>Error:</b> ${esc(obj.error || "FAILED")}</div>
      ${obj._url ? `<div><b>URL:</b> ${esc(obj._url)}</div>` : ``}
      ${obj.mysql_error ? `<div><b>MySQL:</b> ${esc(obj.mysql_error)}</div>` : ``}
      ${obj._raw ? `<div><b>Raw Response:</b>\n${esc(obj._raw)}</div>` : ``}
    </div>
  `;
}

async function getJson(path){
  const url = `${API}/${path}`;
  console.log("GET:", url);

  try {
    const r = await fetch(url, {
      credentials: "include",
      cache: "no-store"
    });

    const text = await r.text();
    console.log("RAW RESPONSE:", url, text);

    let j = {};
    try {
      j = JSON.parse(text);
    } catch {
      j = {
        ok: false,
        error: "BAD_JSON",
        _raw: text,
        _url: url
      };
    }

    if (!r.ok && typeof j === "object" && j) {
      j.ok = false;
      if (!j.error) j.error = `HTTP_${r.status}`;
      j._url = url;
    }

    return j;
  } catch (e) {
    return {
      ok: false,
      error: "FETCH_FAILED",
      _url: url,
      _raw: String(e && e.message ? e.message : e)
    };
  }
}

async function postForm(path, body){
  const url = `${API}/${path}`;
  console.log("POST:", url, body);

  const fd = new FormData();
  Object.keys(body || {}).forEach(k => fd.append(k, body[k]));

  try {
    const r = await fetch(url, {
      method: "POST",
      credentials: "include",
      body: fd,
      cache: "no-store"
    });

    const text = await r.text();
    console.log("RAW RESPONSE:", url, text);

    let j = {};
    try {
      j = JSON.parse(text);
    } catch {
      j = {
        ok: false,
        error: "BAD_JSON",
        _raw: text,
        _url: url
      };
    }

    if (!r.ok && typeof j === "object" && j) {
      j.ok = false;
      if (!j.error) j.error = `HTTP_${r.status}`;
      j._url = url;
    }

    return j;
  } catch (e) {
    return {
      ok: false,
      error: "FETCH_FAILED",
      _url: url,
      _raw: String(e && e.message ? e.message : e)
    };
  }
}

function statusClass(v){
  const x = String(v || "").toUpperCase();
  if (x === "PASS" || x === "SELECTED" || x === "REVIEWED" || x === "FINAL" || x === "ACTIVE") return "ok";
  if (x === "FAIL" || x === "NOT_SELECTED") return "bad";
  return "warn";
}

function jsString(v){
  return JSON.stringify(String(v ?? ""));
}

function setText(id, value){
  const el = document.getElementById(id);
  if (el) el.textContent = String(value ?? 0);
}

function normalizeStatus(v){
  return String(v || "").trim().toUpperCase();
}

function statusBadge(v){
  const label = String(v || "—").trim() || "—";
  const x = normalizeStatus(label);
  let cls = "default";
  if (["PUBLISHED","PASS","SELECTED","REVIEWED","FINAL","ACTIVE"].includes(x)) cls = x === "PUBLISHED" ? "published" : x.toLowerCase();
  else if (x === "DRAFT") cls = "draft";
  else if (x === "MATCHED") cls = "matched";
  else if (x === "NEW") cls = "new";
  return `<span class="status-badge ${cls}">${esc(label)}</span>`;
}

async function loadProjects(){
  const box = document.getElementById("projectsBox");
  box.innerHTML = "Loading...";

  const r = await getJson("phase3/partner_projects.php");
  if (!r.ok) {
    box.innerHTML = debugErrorHtml(r);
    return;
  }

  const items = Array.isArray(r.projects) ? r.projects : [];
  setText("statTotal", items.length);
  setText("statPublished", items.filter(p => normalizeStatus(p.status) === "PUBLISHED").length);
  setText("statDraft", items.filter(p => normalizeStatus(p.status) === "DRAFT").length);
  setText("statMatched", items.filter(p => normalizeStatus(p.status) === "MATCHED").length);
  if (!items.length) {
    box.innerHTML = `<div class="empty">No Phase3 projects yet.</div>`;
    return;
  }

  box.innerHTML = `
    <div class="table-wrap"><table>
      <thead>
        <tr>
          <th>Project</th>
          <th>Status</th>
          <th>Tasks</th>
          <th>Subs</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        ${items.map(p => {
          const projectId = Number(p.project_id || 0);
          const projectTitle = jsString(p.capstone_title || `Project #${projectId}`);
          const hasReport = Number(p.has_report || 0) > 0;

          return `
            <tr>
              <td>
                <div class="project-name">${esc(p.capstone_title)}</div>
                <div class="row-meta">${esc(p.created_at || "")} • #${esc(p.project_id)}</div>
              </td>
              <td>${statusBadge(p.status || "")}</td>
              <td>${esc(p.tasks_count || 0)}</td>
              <td>${esc(p.submissions_count || 0)}</td>
              <td class="actions">
                <span class="link" onclick="openProject(${projectId})">Open</span>
                &nbsp; | &nbsp;
                <span class="link" onclick='analyzeProject(${projectId}, ${projectTitle})'>تحليل</span>
                ${hasReport ? `&nbsp; | &nbsp;<span class="link" onclick='viewReport(${projectId}, ${projectTitle})'>عرض التقرير</span>` : ``}
              </td>
            </tr>
          `;
        }).join("")}
      </tbody>
    </table></div>
  `;

  const pid = Number(qs("project_id") || 0);
  const tid = Number(qs("team_id") || 0);

  if (pid) {
    await openProject(pid, tid);
  }
}

window.openProject = async function(project_id, autoTeamId = 0){
  currentProject = project_id;
  currentTeam = null;
  setQs(project_id, null);

  document.getElementById("projectTitle").textContent = `Loading project #${project_id}...`;
  document.getElementById("projectInfo").innerHTML = "Loading...";
  document.getElementById("teamsBox").innerHTML = "Loading...";
  document.getElementById("teamTitle").textContent = "اختر Team";
  document.getElementById("membersBox").innerHTML = "—";
  document.getElementById("topTeamBox").style.display = "none";
  document.getElementById("topTeamBox").innerHTML = "";

  const r = await getJson(`phase3/project_teams.php?project_id=${encodeURIComponent(project_id)}`);
  if (!r.ok) {
    document.getElementById("projectTitle").textContent = "Error";
    document.getElementById("projectInfo").innerHTML = debugErrorHtml(r);
    document.getElementById("teamsBox").innerHTML = "";
    return;
  }

  const p = r.project || {};
  const teams = Array.isArray(r.teams) ? r.teams : [];

  document.getElementById("projectTitle").textContent = p.capstone_title || `Project #${project_id}`;
  document.getElementById("projectInfo").innerHTML = `
    <div><b>الوصف:</b> ${esc(p.capstone_description || "—")}</div>
    <div style="margin-top:8px">
      <span class="pill">Status: ${esc(p.status || "—")}</span>
      <span class="pill">Teams: ${esc(teams.length)}</span>
    </div>
  `;

  if (!teams.length) {
    document.getElementById("teamsBox").innerHTML = `<div class="empty">No teams found for this project.</div>`;
    return;
  }

  document.getElementById("teamsBox").innerHTML = `
    <div class="table-wrap"><table>
      <thead>
        <tr>
          <th>Team</th>
          <th>Members</th>
          <th>Average</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        ${teams.map(t => `
          <tr>
            <td>
              <b>${esc(t.team_name || ('Team #' + (t.team_no || t.team_id || '')))}</b>
              <div class="muted">#${esc(t.team_id || '')} • Team No: ${esc(t.team_no || '—')}</div>
            </td>
            <td>${esc(t.actual_members || 0)} / ${esc(t.required_members || 0)}</td>
            <td>
              <b>${esc(t.final_score ?? t.avg_score ?? "—")}</b>
              <div class="muted">AI: ${esc(t.avg_score ?? "—")} • Rating: ${esc(t.avg_partner_rating ?? "—")}</div>
            </td>
            <td>${statusBadge(t.final_decision || t.status || "—")}</td>
            <td><span class="link" onclick="openTeam(${Number(t.team_id)})">Open</span></td>
          </tr>
        `).join("")}
      </tbody>
    </table></div>
  `;

  if (autoTeamId) {
    await openTeam(autoTeamId);
  }
};

window.openTeam = async function(team_id){
  currentTeam = team_id;
  setQs(currentProject, team_id);

  document.getElementById("teamTitle").textContent = `Loading team #${team_id}...`;
  document.getElementById("membersBox").innerHTML = "Loading...";
  document.getElementById("topTeamBox").style.display = "none";
  document.getElementById("topTeamBox").innerHTML = "";

  const r = await getJson(`phase3/team_members.php?team_id=${encodeURIComponent(team_id)}`);
  if (!r.ok) {
    document.getElementById("teamTitle").textContent = "Error";
    document.getElementById("membersBox").innerHTML = debugErrorHtml(r);
    return;
  }

  const items = Array.isArray(r.items) ? r.items : [];
  const team = r.team || {};

  document.getElementById("teamTitle").textContent =
    team.team_name
      ? `${team.team_name} — Team #${team.team_no || team.team_id || ''}`
      : `Team #${team_id}`;

  document.getElementById("topTeamBox").style.display = "";
  document.getElementById("topTeamBox").innerHTML = `
    <div style="font-weight:800;font-family:'Montserrat',sans-serif">ملخص التيم</div>
    <div style="margin-top:6px">
      <b>${esc(team.team_name || ("Team #" + (team.team_no || team_id)))}</b>
    </div>
    <div class="muted">
      Members: <b>${esc(team.actual_members ?? "—")}</b> / <b>${esc(team.required_members ?? "—")}</b>
      • Avg Score: <b>${esc(team.avg_score ?? "—")}</b>
      • Avg Rating: <b>${esc(team.avg_partner_rating ?? "—")}</b>
      • Final Score: <b>${esc(team.final_score ?? "—")}</b>
      • Decision: <b class="${statusClass(team.final_decision || team.status)}">${esc(team.final_decision || team.status || "—")}</b>
    </div>
  `;

  if (!items.length) {
    document.getElementById("membersBox").innerHTML = `<div class="empty">No team members found yet.</div>`;
    return;
  }

  document.getElementById("membersBox").innerHTML = `
    <div class="table-wrap"><table>
      <thead>
        <tr>
          <th>Student</th>
          <th>Role</th>
          <th>Task</th>
          <th>Score</th>
          <th>Rating</th>
          <th>Decision</th>
          <th>Submitted</th>
          <th>Files</th>
        </tr>
      </thead>
      <tbody>
        ${items.map(m => {
          const dec = m.decision || "—";
          const repo = m.repo_url ? `<a class="link" href="${esc(m.repo_url)}" target="_blank">Repo</a>` : `—`;
          const zip = m.zip_path ? `<a class="link" href="/utbn-backend/${String(m.zip_path).replace(/^\/+/, '')}" target="_blank">ZIP</a>` : `—`;

          return `
            <tr>
              <td>
                <b>${esc(m.full_name || "—")}</b>
                <div class="muted">#${esc(m.student_id || "—")}</div>
              </td>
              <td>${esc(m.role_name || "—")}</td>
              <td>${esc(m.task_code || "—")}</td>
              <td>${esc(m.score ?? "—")}</td>
              <td>${esc(m.partner_rating ?? "—")}</td>
              <td>${statusBadge(dec)}</td>
              <td>${esc(m.submitted_at || "—")}</td>
              <td>${repo} ${repo !== '—' && zip !== '—' ? '&nbsp;|&nbsp;' : ''} ${zip}</td>
            </tr>
          `;
        }).join("")}
      </tbody>
    </table></div>
  `;
};

document.getElementById("reloadProjects").onclick = loadProjects;

function closeReportModal(){
  document.getElementById("reportModal").style.display = "none";
}

function openReportModal(title, html){
  document.getElementById("reportModalTitle").textContent = title || "AI Report";
  document.getElementById("reportBody").innerHTML = html || "";
  document.getElementById("reportModal").style.display = "flex";
}

function renderReport(report){
  if (!report || typeof report !== "object") {
    return `<div class="empty">لا يوجد تقرير بعد.</div>`;
  }

  const highlights = Array.isArray(report.highlights) ? report.highlights : [];
  const risks = Array.isArray(report.risks) ? report.risks : [];
  const missing = Array.isArray(report.missing_parts) ? report.missing_parts : [];
  const nextSteps = Array.isArray(report.next_steps) ? report.next_steps : [];
  const breakdown = Array.isArray(report.task_breakdown) ? report.task_breakdown : [];

  return `
    <div class="reportSection">
      <h4>Executive Summary</h4>
      <div>${esc(report.executive_summary || "—")}</div>
      <div style="margin-top:10px">
        <span class="pill">Overall Readiness: ${esc(report.overall_readiness || "—")}</span>
      </div>
    </div>

    <div class="reportSection">
      <h4>Highlights</h4>
      <ul class="reportList">
        ${highlights.length ? highlights.map(x => `<li>${esc(x)}</li>`).join("") : `<li>—</li>`}
      </ul>
    </div>

    <div class="reportSection">
      <h4>Risks</h4>
      <ul class="reportList">
        ${risks.length ? risks.map(x => `<li>${esc(x)}</li>`).join("") : `<li>—</li>`}
      </ul>
    </div>

    <div class="reportSection">
      <h4>Missing Parts</h4>
      <ul class="reportList">
        ${missing.length ? missing.map(x => `<li>${esc(x)}</li>`).join("") : `<li>—</li>`}
      </ul>
    </div>

    <div class="reportSection">
      <h4>Task Breakdown</h4>
      <table class="reportBreakdown">
        <thead>
          <tr>
            <th>Task</th>
            <th>Role</th>
            <th>Status</th>
            <th>Score</th>
            <th>Key Issues</th>
            <th>Recommended Actions</th>
          </tr>
        </thead>
        <tbody>
          ${
            breakdown.length ? breakdown.map(t => `
              <tr>
                <td>${esc(t.task_code || "—")}</td>
                <td>${esc(t.role_name || "—")}</td>
                <td><span class="${statusClass(t.status)}">${esc(t.status || "—")}</span></td>
                <td>${esc(t.score ?? "—")}</td>
                <td>${Array.isArray(t.key_issues) && t.key_issues.length ? t.key_issues.map(esc).join("<br>") : "—"}</td>
                <td>${Array.isArray(t.recommended_actions) && t.recommended_actions.length ? t.recommended_actions.map(esc).join("<br>") : "—"}</td>
              </tr>
            `).join("") : `<tr><td colspan="6">—</td></tr>`
          }
        </tbody>
      </table>
    </div>

    <div class="reportSection">
      <h4>Next Steps</h4>
      <ul class="reportList">
        ${nextSteps.length ? nextSteps.map(x => `<li>${esc(x)}</li>`).join("") : `<li>—</li>`}
      </ul>
    </div>
  `;
}

function unwrapReport(payload){
  if (!payload || typeof payload !== "object") return {};
  if (payload.report && typeof payload.report === "object") return payload.report;
  return payload;
}

window.analyzeProject = async function(project_id, title){
  console.log("analyzeProject clicked", project_id, title);
  openReportModal(`AI Report — ${title}`, `<div class="muted">جاري توليد التقرير من الـ AI...</div>`);

  const r = await postForm("phase3/generate_report.php", { project_id });

  if (!r.ok) {
    document.getElementById("reportBody").innerHTML = `
      <div class="empty">فشل توليد التقرير</div>
      ${debugErrorHtml(r)}
    `;
    return;
  }

  const finalReport = unwrapReport(r.report);
  document.getElementById("reportBody").innerHTML = renderReport(finalReport);
  await loadProjects();
};

window.viewReport = async function(project_id, title){
  console.log("viewReport clicked", project_id, title);
  openReportModal(`AI Report — ${title}`, `<div class="muted">Loading...</div>`);

  const r = await getJson(`phase3/get_report.php?project_id=${encodeURIComponent(project_id)}`);

  if (!r.ok) {
    document.getElementById("reportBody").innerHTML = `
      <div class="empty">فشل تحميل التقرير</div>
      ${debugErrorHtml(r)}
    `;
    return;
  }

  const finalReport = unwrapReport(r.report);
  document.getElementById("reportBody").innerHTML = renderReport(finalReport);
};

document.getElementById("reportModal").addEventListener("click", function(e){
  if (e.target.id === "reportModal") closeReportModal();
});

loadProjects();
</script>

</body>
</html>


