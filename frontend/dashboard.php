<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
if (($_SESSION["role"] ?? "") !== "admin") { header("Location: company.php"); exit; }
?>
<!doctype html>
<html lang="en" dir="ltr">
  <head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
<title data-i18n="admin_dashboard_title">Admin Dashboard</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
<script src="assets/js/i18n.js"></script>
  <!-- QOYN Fonts -->
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
      --line:rgba(10,46,93,.10);
      --shadow:0 10px 30px rgba(0,0,0,.08);
      --radius:999px;
      --radius-card:24px;
      --container:1200px;
      --danger:#c0392b;
      --dangerHover:#a93226;
      --success:#1f8f5f;
    }

    *{box-sizing:border-box}

    html,body{
      margin:0;
      padding:0;
      background:var(--bg);
      color:var(--text);
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      direction:ltr;
      text-align:left;
    }

    body{
      display:block !important;
      min-height:100vh;
    }

    h1,h2,h3,h4,h5,h6,b,strong,.btn,.badge{
      font-family:"Montserrat", sans-serif;
    }

    .container{
      width:min(92vw, var(--container));
      margin:0 auto;
      padding:28px 0 40px;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:18px;
      padding:18px 22px;
      background:rgba(255,255,255,.92);
      border:1px solid rgba(10,46,93,.08);
      border-radius:24px;
      box-shadow:var(--shadow);
      backdrop-filter:blur(10px);
      flex-wrap:wrap;
    }

    .topbar h1{
      margin:0 0 6px 0;
      color:var(--navy);
      font-size:38px;
      font-weight:800;
      line-height:1.08;
      letter-spacing:-.6px;
    }

    .topbar .muted,
    .topbar div[style*="opacity"]{
      color:#46546a !important;
      opacity:1 !important;
      font-size:14px;
      line-height:1.8;
    }

    .row{
      display:flex;
      gap:18px;
      flex-wrap:wrap;
    }

    .col{
      flex:1 1 0;
      min-width:0;
    }

    .card{
      background:var(--card) !important;
      border:1px solid rgba(10,46,93,.08);
      border-radius:var(--radius-card);
      box-shadow:var(--shadow);
      padding:20px !important;
      overflow:hidden;
    }

    .card h2{
      margin:0 0 10px 0;
      color:var(--navy);
      font-size:30px;
      font-weight:800;
      line-height:1.12;
      letter-spacing:-.4px;
    }

    .muted{
      color:#5f6b7a !important;
      opacity:1 !important;
      line-height:1.8;
    }

    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:12px 18px;
      border-radius:14px;
      border:1px solid rgba(10,46,93,.16);
      background:#fff;
      color:var(--navy);
      text-decoration:none;
      cursor:pointer;
      font-size:14px;
      font-weight:800;
      transition:transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
      box-shadow:none;
    }

    .btn:hover{
      transform:translateY(-2px);
      box-shadow:0 12px 24px rgba(0,0,0,.08);
    }

    .btn.primary{
      background:var(--navy);
      color:#fff;
      border-color:var(--navy);
    }

    .btn.primary:hover{
      background:var(--navyHover);
      border-color:var(--navyHover);
      color:#fff;
    }

    .btn.ghost{
      background:#fff;
      color:var(--navy);
      border-color:rgba(10,46,93,.18);
    }

    .btn.ghost:hover{
      background:var(--yellow);
      color:#111;
      border-color:var(--yellow);
    }

    .btn.danger{
      background:#fff;
      color:var(--danger);
      border-color:rgba(192,57,43,.22);
    }

    .btn.danger:hover{
      background:var(--danger);
      color:#fff;
      border-color:var(--danger);
    }

    .input,
    select.input,
    textarea,
    input[type="text"],
    input[type="email"],
    input[type="number"]{
      width:100%;
      min-height:48px;
      padding:12px 14px;
      border:1px solid rgba(10,46,93,.14);
      border-radius:14px;
      background:#fff;
      color:var(--text);
      font-family:"Poppins", sans-serif;
      font-size:14px;
      transition:border-color .18s ease, box-shadow .18s ease;
      outline:none;
    }

    .input:focus,
    select.input:focus,
    textarea:focus,
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus{
      border-color:rgba(10,46,93,.35);
      box-shadow:0 0 0 4px rgba(10,46,93,.08);
    }

    .list{
      display:flex;
      flex-direction:column;
      gap:12px !important;
    }

    .item{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
      flex-wrap:wrap;
      background:#fff;
      border:1px solid rgba(10,46,93,.08);
      border-radius:20px;
      padding:16px 18px;
      box-shadow:0 8px 24px rgba(0,0,0,.04);
    }

    .item b{
      color:#111;
      font-weight:800;
    }

    .badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-height:34px;
      padding:7px 12px;
      border-radius:999px;
      background:rgba(10,46,93,.06);
      border:1px solid rgba(10,46,93,.12);
      color:var(--navy);
      font-size:12px;
      font-weight:800;
      white-space:nowrap;
    }

    .badge.ok{
      background:rgba(255,194,74,.18);
      border-color:rgba(255,194,74,.48);
      color:#7c5600;
    }

    hr{
      border:none;
      border-top:1px solid rgba(10,46,93,.12);
      margin:18px 0 !important;
      opacity:1 !important;
    }

    #aiMsg{
      font-size:14px;
      line-height:1.8;
      color:#5f6b7a;
    }

    #roleSelect{
      background:#fff;
    }

    label{
      font-family:"Poppins", sans-serif;
      color:#4b5a6f;
    }

    input[type="checkbox"]{
      accent-color:var(--navy);
      width:16px;
      height:16px;
    }

    @media (max-width: 900px){
      .container{
        width:min(94vw, var(--container));
        padding:20px 0 34px;
      }

      .topbar{
        padding:16px;
      }

      .topbar h1{
        font-size:30px;
      }

      .card h2{
        font-size:24px;
      }

      .row{
        gap:14px;
      }

      .item{
        padding:14px;
      }
    }

    @media (max-width: 640px){
      .topbar{
        align-items:flex-start;
      }

      .btn{
        width:100%;
      }

      .row[style*="align-items:center"] .btn,
      .row[style*="align-items:center"] .input,
      .row[style*="align-items:center"] select{
        width:100%;
        max-width:none !important;
      }
    }
    .lang-dropdown{
  position: relative;
  display: inline-block;
}

.lang-trigger{
  border: none;
  background: transparent;
  color: #111;
  font-weight: 500;
  font-size: 15px;
  font-family: "Poppins", sans-serif;
  padding: 10px 14px;
  border-radius: 999px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
}

.lang-trigger:hover{
  color: var(--yellow);
  transform: translateY(-2px);
  font-weight: 700;
}

.lang-arrow{
  font-size: 11px;
  transition: transform .2s ease;
}

.lang-dropdown.open .lang-arrow{
  transform: rotate(180deg);
}
 .lang-menu{
      position: absolute;
      top: calc(100% + 10px);
      left: 0;
      display: none;
      min-width: 170px;
      padding: 8px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0,0,0,.10);
      z-index: 9999;
    }
.topbar{
  position: relative;
  z-index: 10000;
  overflow: visible;

  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:18px;
  padding:18px 22px;
  background:rgba(255,255,255,.92);
  border:1px solid rgba(10,46,93,.08);
  border-radius:24px;
  box-shadow:var(--shadow);
  backdrop-filter:blur(10px);
  flex-wrap:wrap;
}
.lang-dropdown.open .lang-menu{
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.lang-option{
  border: none;
  background: transparent;
  color: #111;
  font-weight: 500;
  font-size: 15px;
  font-family: "Poppins", sans-serif;
  padding: 10px 14px;
  border-radius: 999px;
  text-align: left;
  cursor: pointer;
  transition: color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
}

.lang-option:hover{
  color: #fff;
  background: var(--yellow);
  transform: translateY(-2px);
  font-weight: 800;
}

.lang-option.active{
  color: #fff;
  background: var(--yellow);
  font-weight: 800;
}
  </style>
</head>
<body style="display:block">
  <div class="container">
    <div class="topbar">
  <div>
    <h1 data-i18n="admin_dashboard_heading">Admin Dashboard</h1>
    <div style="opacity:.8">
      <span data-i18n="welcome_admin">Welcome</span>
      <?php echo htmlspecialchars($_SESSION["name"] ?? ""); ?>
    </div>
  </div>

  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
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

    <a class="btn" href="/utbn-backend/api/logout.php" data-i18n="logout">Logout</a>
  </div>
</div>

    <div class="row" style="margin-top:14px">
      <div class="col">
        <div class="card" style="padding:14px">
          <h2 data-i18n="platform_summary">Platform Summary</h2>
<div class="muted" id="statsLine" data-i18n="loading">Loading...</div>

<div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px">
  <button class="btn ghost" id="tabPartners" data-i18n="companies">Companies</button>
  <button class="btn ghost" id="tabStudents" data-i18n="students">Students</button>
  <button class="btn ghost" id="tabOffers" data-i18n="company_paths">Company Paths</button>
</div>
        </div>
      </div>
    </div>

    <hr style="opacity:.25;margin:14px 0">

    <div class="row" style="gap:10px;align-items:center">
      <select class="input" id="roleSelect" style="max-width:260px">
        <option value="ml_engineer">Machine Learning Engineer</option>
        <option value="algorithm_engineer">Algorithm Engineer</option>
        <option value="fullstack">Full Stack Developer</option>
        <option value="pentester">Penetration Tester</option>
      </select>

      <button class="btn primary" id="btnPreviewAi" data-i18n="preview_ai">Preview AI</button>
<button class="btn primary" id="btnCreateAiPath" data-i18n="create_path">Create Path</button>
    </div>

    <div class="muted" id="aiMsg" style="min-height:22px;margin-top:10px"></div>
    <div id="aiPreviewBox" class="list" style="margin-top:10px;gap:10px"></div>

    <!-- ===== الشركات ===== -->
    <div class="row" style="margin-top:14px" id="partnersBox" hidden>
      <div class="col">
        <div class="card" style="padding:14px">
          <h2 data-i18n="companies">Companies</h2>
<div class="muted" data-i18n="click_company_show_playlists">Click a company to view playlists and then videos</div>
          <div id="partnersList" class="list" style="margin-top:12px; gap:10px"></div>
        </div>
      </div>
    </div>

    <!-- ===== الطلاب ===== -->
    <div class="row" style="margin-top:14px" id="studentsBox" hidden>
      <div class="col">
        <div class="card" style="padding:14px">
          <h2 data-i18n="students">Students</h2>
<div class="muted" data-i18n="students_major_coins">All students + major + coins</div>
          <div id="studentsList" class="list" style="margin-top:12px; gap:10px"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- ===== المسارات للشركات ===== -->
  <div class="row" style="margin-top:14px">
    <div class="container" id="offersBox" hidden style="padding-top:0">
      <div class="col">
        <div class="card" style="padding:14px">
          <h2 data-i18n="offer_paths_to_companies">Offer Paths to Companies</h2>
<div class="muted" data-i18n="open_company_toggle_paths">Open a company, then enable/disable the paths you want for it</div>
          <div id="offersCompaniesList" class="list" style="margin-top:12px; gap:10px"></div>
        </div>
      </div>
    </div>
  </div>

<script>
const API = "/utbn-backend/api";

function el(tag, cls){
  const x = document.createElement(tag);
  if (cls) x.className = cls;
  return x;
}

async function getJson(url){
  const r = await fetch(url, { credentials:"include" });

  // اقرأ النص أولاً عشان لو رجع HTML نعرف
  const text = await r.text();
  let j = {};
  try { j = JSON.parse(text); } catch(e) {
    console.error("Non-JSON response from:", url, "status:", r.status, "body:", text.slice(0,200));
    throw { ok:false, error:"NON_JSON", status:r.status, url };
  }

  if(!r.ok || !j.ok){
    console.error("API error:", url, "status:", r.status, "json:", j);
    throw j;
  }
  return j;
}

/* ✅✅ FIX: لازم تكون global عشان onclick يشتغل */
window.showAiStats = async function(studentId, name){
  try{
    const j = await getJson(`${API}/admin_ai_predict_level.php?student_id=${studentId}&n=20`);

    if(!j || !j.ok){
      alert("تعذر جلب AI stats");
      return;
    }
    if(j.reason === "NOT_ENOUGH_DATA"){
      alert(`AI Stats (${name})\nبدنا بيانات أكثر: ${j.have} من ${j.need}`);
      return;
    }

    const f = j.features || {};
    const lvl = j.level || "unknown";
    const ready = !!j.phase_ready;

    const lvlAr = (lvl==="beginner")?"مبتدئ"
               : (lvl==="intermediate")?"متوسط"
               : (lvl==="advanced")?"متقدم"
               : lvl;

    const msg =
`AI Stats (${name})
Model: ${j.model_version || "-"}

Level: ${lvlAr}
Ready: ${ready ? "جاهز ✅" : "غير جاهز ❌"}

Attempts: ${Math.round(Number(f.n||0))}
Avg Score: ${Math.round(Number(f.avg_score||0))}%
Avg Watch: ${Math.round(Number(f.avg_watch||0)*100)}%
Avg Time: ${Math.round(Number(f.avg_time||0))}s
Avg Difficulty: ${Math.round(Number(f.avg_difficulty||0)*100)/100}
Hard Avg Score: ${Math.round(Number(f.hard_avg_score||0))}%`;

    alert(msg);
  }catch(e){
    alert("تعذر جلب AI stats");
  }
};

async function loadStats(){
  const s = await getJson(`${API}/admin_stats.php`);
  statsLine.textContent = `طلاب: ${s.total_students} | شركات: ${s.total_partners} | Playlists: ${s.total_playlists} | Videos: ${s.total_videos}`;
}

async function loadPartners(){
  partnersList.innerHTML = "جارٍ التحميل...";
  const r = await getJson(`${API}/admin_partners.php`);
  partnersList.innerHTML = "";

  for(const p of r.items){
    const row = el("div","item");
    row.style.flexDirection="column";
    row.style.alignItems="stretch";

    row.innerHTML = `
      <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center">
        <div>
          <div><b>#${p.partner_user_id} - ${p.company_name}</b></div>
          <div class="muted">${p.email} ${p.partner_type ? " | " + p.partner_type : ""}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
          <span class="badge ok">Playlists: ${p.playlists_count}</span>
          <span class="badge">Videos: ${p.videos_count}</span>
          <button class="btn primary">عرض القوائم</button>
        </div>
      </div>
      <div class="list" style="margin-top:10px;display:none;gap:10px"></div>
    `;

    const btn = row.querySelector("button");
    const box = row.querySelector(".list");

    btn.onclick = async ()=>{
      if(box.style.display === "block"){
        box.style.display="none";
        btn.textContent="عرض القوائم";
        return;
      }
      btn.textContent="جارٍ التحميل...";
      const pls = await getJson(`${API}/admin_partner_playlists.php?partner_user_id=${p.partner_user_id}`);
      box.innerHTML = "";
      box.style.display="block";
      btn.textContent="إخفاء القوائم";

      for(const pl of pls.items){
        const plRow = el("div","item");
        plRow.style.flexDirection="column";
        plRow.style.alignItems="stretch";
        plRow.innerHTML = `
          <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center">
            <div>
              <div><b>Playlist #${pl.id} - ${pl.name}</b></div>
              <div class="muted">${pl.created_at}</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
              <span class="badge ok">Videos: ${pl.videos_count}</span>
              <span class="badge">Viewers: ${pl.viewers_count}</span>
              <button class="btn ghost">عرض الفيديوهات</button>
            </div>
          </div>
          <div class="list" style="margin-top:10px;display:none;gap:10px"></div>
        `;

        const vb = plRow.querySelector(".list");
        const vbtn = plRow.querySelector("button");

        vbtn.onclick = async ()=>{
          if(vb.style.display==="block"){
            vb.style.display="none";
            vbtn.textContent="عرض الفيديوهات";
            return;
          }
          vbtn.textContent="جارٍ التحميل...";
          const vids = await getJson(`${API}/admin_playlist_videos.php?playlist_id=${pl.id}`);
          vb.innerHTML = "";
          vb.style.display="block";
          vbtn.textContent="إخفاء الفيديوهات";

          for(const v of vids.items){
            const vRow = el("div","item");
            vRow.innerHTML = `
              <div style="flex:1">
                <div><b>Video #${v.id} - ${v.title}</b></div>
                <div class="muted">${v.created_at} | ${v.duration_seconds}s</div>
              </div>
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <span class="badge ok">Viewers: ${v.viewers_count}</span>
                <span class="badge">Completed: ${v.completed_sum}</span>
              </div>
            `;
            vb.appendChild(vRow);
          }
        };

        box.appendChild(plRow);
      }
    };

    partnersList.appendChild(row);
  }
}

async function loadStudents(){
  studentsList.innerHTML = "جارٍ التحميل...";
  const r = await getJson(`${API}/admin_students.php`);
  studentsList.innerHTML = "";

  for(const s of r.items){
    const safeName = (s.full_name || "").replace(/'/g, "\\'");
    const row = el("div","item");
    row.innerHTML = `
      <div style="flex:1">
        <div><b>#${s.id} - ${s.full_name}</b></div>
        <div class="muted">${s.email}</div>
        <div class="muted">التخصص: <b>${s.major_text || "-"}</b></div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <span class="badge ok">${s.coins_total} coin</span>
        <button class="btn ghost" style="margin-right:8px" onclick="showAiStats(${s.id}, '${safeName}')">AI Stats</button>
      </div>
    `;
    studentsList.appendChild(row);
  }
}

async function loadOffersCompanies(){
  offersCompaniesList.innerHTML = "جارٍ التحميل...";
  const r = await getJson(`${API}/admin_partners.php`);
  offersCompaniesList.innerHTML = "";

  for(const p of r.items){
    const row = el("div","item");
    row.style.flexDirection="column";
    row.style.alignItems="stretch";

    row.innerHTML = `
      <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center">
        <div>
          <div><b>#${p.partner_user_id} - ${p.company_name}</b></div>
          <div class="muted">${p.email}</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
          <button class="btn primary">إدارة المسارات</button>
        </div>
      </div>
      <div class="list" style="margin-top:10px;display:none;gap:10px"></div>
    `;

    const btn = row.querySelector("button");
    const box = row.querySelector(".list");

    btn.onclick = async ()=>{
      if(box.style.display === "block"){
        box.style.display="none";
        btn.textContent="إدارة المسارات";
        return;
      }

      btn.textContent="جارٍ التحميل...";
      box.innerHTML = "";
      box.style.display="block";

      const pathsRes = await getJson(`${API}/admin_learning_paths.php`);
      const offersRes = await getJson(`${API}/admin_company_offers_get.php?company_id=${p.partner_id || p.partner_user_id}`);
      const activeSet = new Set((offersRes.active_path_ids || []).map(x => Number(x)));

      btn.textContent="إخفاء المسارات";

      for(const path of (pathsRes.items || [])){
        const item = el("div","item");
        item.style.alignItems="center";
        item.style.justifyContent="space-between";
        item.style.gap="10px";

        const isActive = activeSet.has(Number(path.id));
        const pub = Number(path.is_published) === 1;

        item.innerHTML = `
          <div style="flex:1">
            <div><b>#${path.id} - ${path.title}</b></div>
            <div class="muted">role_key: <b>${path.role_key}</b> ${path.role_name ? " | " + path.role_name : ""}</div>
          </div>

          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            <label class="muted" style="display:flex;align-items:center;gap:6px;user-select:none">
              <input type="checkbox" ${isActive ? "checked":""}/>
              تفعيل للشركة
            </label>

            <button class="btn ghost">${pub ? "Unpublish" : "Publish"}</button>

            <button class="btn danger">Delete</button>
          </div>
        `;

        const cb = item.querySelector("input[type=checkbox]");
        const pubBtn = item.querySelectorAll("button")[0];
        const delBtn = item.querySelectorAll("button")[1];

        cb.onchange = async ()=>{
          cb.disabled = true;
          try{
            const form = new URLSearchParams();
            form.set("company_id", p.partner_id);
            form.set("path_id", path.id);
            form.set("is_active", cb.checked ? "1" : "0");

            const rr = await fetch(`${API}/admin_company_offer_toggle.php`, {
              method:"POST",
              credentials:"include",
              headers: { "Content-Type":"application/x-www-form-urlencoded" },
              body: form.toString()
            });
            const jj = await rr.json().catch(()=> ({}));
            if(!rr.ok || !jj.ok) throw jj;
          }catch(e){
            alert("فشل حفظ التفعيل للشركة");
            cb.checked = !cb.checked;
          }finally{
            cb.disabled = false;
          }
        };

        pubBtn.onclick = async ()=>{
          pubBtn.disabled = true;
          const newVal = pub ? 0 : 1;
          try{
            const form = new URLSearchParams();
            form.set("path_id", path.id);
            form.set("is_published", String(newVal));

            const rr = await fetch(`${API}/admin_path_publish.php`, {
              method:"POST",
              credentials:"include",
              headers: { "Content-Type":"application/x-www-form-urlencoded" },
              body: form.toString()
            });
            const jj = await rr.json().catch(()=> ({}));
            if(!rr.ok || !jj.ok) throw jj;

            path.is_published = newVal;
            pubBtn.textContent = newVal ? "Unpublish" : "Publish";
          }catch(e){
            alert("فشل تغيير حالة النشر");
          }finally{
            pubBtn.disabled = false;
          }
        };

        delBtn.onclick = async ()=>{
          if(!confirm(`متأكد بدك تحذف المسار #${path.id} ؟`)) return;

          delBtn.disabled = true;
          try{
            const form = new URLSearchParams();
            form.set("path_id", path.id);

            const rr = await fetch(`${API}/admin_path_delete.php`, {
              method:"POST",
              credentials:"include",
              headers: { "Content-Type":"application/x-www-form-urlencoded" },
              body: form.toString()
            });

            const jj = await rr.json().catch(()=> ({}));
            if(!rr.ok || !jj.ok) throw jj;

            item.remove();
          }catch(e){
            alert("فشل حذف المسار");
            delBtn.disabled = false;
          }
        };

        box.appendChild(item);
      }
    };

    offersCompaniesList.appendChild(row);
  }
}

function showTab(tab){
  partnersBox.hidden = tab !== "partners";
  studentsBox.hidden = tab !== "students";
  offersBox.hidden   = tab !== "offers";
}

tabPartners.onclick = async ()=>{
  showTab("partners");
  await loadPartners();
};

tabStudents.onclick = async ()=>{
  showTab("students");
  await loadStudents();
};

tabOffers.onclick = async ()=>{
  showTab("offers");
  await loadOffersCompanies();
};

function setAiMsg(t, ok=false){
  aiMsg.textContent = t;
  aiMsg.style.color = ok ? "#1f8f5f" : "";
}

async function previewAi(){
  aiPreviewBox.innerHTML = "";
  const role_key = roleSelect.value;

  try{
    setAiMsg("جارٍ تحليل المسار بواسطة AI ...");
    const j = await getJson(`${API}/admin_generate_ai_path.php?role_key=${encodeURIComponent(role_key)}`);

    const items = j.recommended_playlists || [];
    if(items.length === 0){
      setAiMsg("ما في توصيات (تأكد من templates و mapping)", false);
      return;
    }

    setAiMsg(`تم ✅ | Skills: ${j.skills_count} | Playlists: ${items.length}`, true);

    for(const pl of items){
      const row = el("div","item");
      row.innerHTML = `
        <div style="flex:1">
          <div><b>${pl.name}</b></div>
          <div class="muted">Subject: <b>${pl.subject}</b> | Template ID: ${pl.id}</div>
        </div>
      `;
      aiPreviewBox.appendChild(row);
    }
  }catch(e){
    setAiMsg("فشل preview (شوف Console)", false);
    console.error(e);
  }
}

async function createAiPath(){
  aiPreviewBox.innerHTML = "";
  const role_key = roleSelect.value;

  try{
    setAiMsg("جارٍ إنشاء المسار...");
    const form = new URLSearchParams();
    form.set("role_key", role_key);

    const r = await fetch(`${API}/admin_create_path_from_ai.php`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: form.toString()
    });

    const j = await r.json().catch(()=> ({}));
    if(!r.ok || !j.ok){
      console.error(j);
      setAiMsg("فشل إنشاء المسار: " + (j.error || "ERROR"), false);
      return;
    }

    setAiMsg(`تم إنشاء المسار ✅ Path ID: ${j.path_id}`, true);
    await previewAi();

  }catch(e){
    setAiMsg("فشل إنشاء المسار (شوف Console)", false);
    console.error(e);
  }
}

btnPreviewAi.onclick = previewAi;
btnCreateAiPath.onclick = createAiPath;

(async ()=>{
  await loadStats();
  showTab("partners");
  await loadPartners();
})();
</script>

</body>
</html>

