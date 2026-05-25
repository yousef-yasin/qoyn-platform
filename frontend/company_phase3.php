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
  <title>Phase 3</title>
  <link rel="stylesheet" href="assets/css/style.css"/>

  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{--navy:#0A2E5D;--navyHover:#123F76;--yellow:#FFC24A;--bg:#F6F8FC;--text:#092A55;--muted:#42526E;--line:#DDE7F5;--shadow:0 18px 45px rgba(10,46,93,.08);--container:1180px;--radius:18px;}
    *{box-sizing:border-box} html{scroll-behavior:smooth}
    body{margin:0;min-height:100vh;font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 5% 15%,rgba(255,194,74,.10),transparent 28%),radial-gradient(circle at 96% 8%,rgba(10,46,93,.08),transparent 30%),var(--bg);overflow-x:hidden;display:block}
    body::before{content:"";position:fixed;inset:78px 0 auto 0;height:1px;background:rgba(10,46,93,.08);pointer-events:none;z-index:1}
    .container{max-width:var(--container);margin:0 auto;padding:0 28px 70px;width:100%}
    .phase-navbar{position:fixed;top:0;left:0;width:100%;z-index:9999;background:rgba(255,255,255,.96);backdrop-filter:blur(16px);border-bottom:1px solid rgba(10,46,93,.08);box-shadow:0 10px 28px rgba(10,46,93,.05);padding:15px 34px}
    .phase-navbar-inner{max-width:var(--container);margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px}
    main{padding-top:112px;width:100%}
    .logo{font-family:"Montserrat",sans-serif;font-weight:800;font-size:28px;letter-spacing:.8px;color:var(--navy);text-decoration:none;user-select:none}
    .pageTitleWrap{text-align:center;margin:0 0 36px;position:relative}
    .pageTitleWrap h1{margin:0 0 10px;font-family:"Montserrat",sans-serif;font-weight:800;font-size:50px;letter-spacing:-.8px;line-height:1.05;color:var(--navy)}
    .pageTitleWrap::after{content:"";display:block;width:42px;height:4px;margin:14px auto 0;border-radius:999px;background:var(--yellow)}
    .muted{color:var(--muted);font-size:13.5px;line-height:1.75}
    .pageTitleWrap .muted{font-weight:500;color:#415373}
    .card{width:100%;background:transparent!important;border:0!important;box-shadow:none!important;padding:0!important;margin:0!important}
    .card>div:not(.btnRow):not(.statusBox){width:100%;margin-top:26px!important}
    label.muted{display:block;margin:0 0 10px;font-family:"Montserrat",sans-serif;font-weight:700;font-size:15px;color:var(--navy)}
    .input,input.input,textarea.input{width:100%;background:rgba(255,255,255,.98);border:1px solid var(--line);border-radius:15px;padding:14px 16px;color:#0B1F3F;font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;font-size:14px;outline:none;box-shadow:0 10px 26px rgba(10,46,93,.04);transition:border-color .18s ease,box-shadow .18s ease,transform .18s ease}
    textarea.input{min-height:210px;resize:vertical;line-height:1.75}.input::placeholder{color:#8A98AD}
    .input:focus{border-color:rgba(10,46,93,.42);box-shadow:0 0 0 4px rgba(10,46,93,.08),0 14px 35px rgba(10,46,93,.07);transform:translateY(-1px)}
    .btnRow{width:100%;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:28px!important;align-items:stretch}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border-radius:12px;text-decoration:none;white-space:nowrap;cursor:pointer;user-select:none;font-family:"Montserrat",sans-serif;font-weight:800;font-size:13px;letter-spacing:.1px;border:1px solid rgba(10,46,93,.10);background:var(--navy);color:#fff;box-shadow:0 16px 28px rgba(10,46,93,.18);transition:transform .18s ease,box-shadow .18s ease,background .18s ease,border-color .18s ease}
    .phase-navbar .btn{min-width:110px;background:#fff;color:var(--navy);border-color:#D6E0EE;box-shadow:none}.phase-navbar .btn::before{content:"←";margin-right:8px;font-weight:900}
    .btn:hover{transform:translateY(-2px);background:var(--navyHover);color:#fff;border-color:var(--navyHover);box-shadow:0 22px 44px rgba(10,46,93,.23)}
    .btn:disabled{opacity:.65;cursor:not-allowed;transform:none;box-shadow:none}
    .statusBox{margin-top:30px;padding:18px 20px;border-radius:18px;background:rgba(255,255,255,.72);border:1px solid var(--line);box-shadow:var(--shadow)}
    .statusOk{color:#16A36B;font-weight:800}.statusErr{color:#D93025;font-weight:800}.smallMeta{margin-top:8px;font-size:13px;color:#52617A}.smallMeta b{color:var(--navy)}
    @media (max-width:980px){.container{padding:0 18px 50px}.phase-navbar{padding:14px 18px}main{padding-top:104px}.pageTitleWrap h1{font-size:38px}.btnRow{grid-template-columns:1fr 1fr}}
    @media (max-width:620px){.phase-navbar-inner{gap:10px}.logo{font-size:24px}.phase-navbar .btn{min-width:auto;padding:10px 14px}.pageTitleWrap h1{font-size:32px}.btnRow{grid-template-columns:1fr}}
  </style>
</head>

<body style="display:block">
<div class="container">


  <header class="phase-navbar">
    <div class="phase-navbar-inner">
      <a class="logo" href="company.php">QOYN</a>
      <a class="btn" href="company.php">Back</a>
    </div>
  </header>


  <main>

    <div class="pageTitleWrap">
      <h1>Phase 3</h1>
      <div class="muted">Big Project (Capstone)</div>
    </div>

    <div class="card">
      <div style="margin-top:10px">
        <label class="muted" for="capTitle">Capstone project title</label>
        <input class="input" id="capTitle" placeholder="Example: Smart Campus System"/>
      </div>

      <div style="margin-top:10px">
        <label class="muted" for="capDesc">Capstone project description</label>
        <textarea class="input" id="capDesc" rows="10" placeholder="Write a full description + requirements + submission + tools..."></textarea>
      </div>

      <div class="btnRow" style="margin-top:10px">
        <button class="btn" type="button" id="btnSavePhase3">Save Phase 3</button>
        <button class="btn" type="button" id="btnAnalyzePhase3">Analyze with AI</button>
        <button class="btn" type="button" id="btnMatchPhase3">Auto Assign Students</button>
        <button class="btn" type="button" id="btnFinalizePhase3">Finalize & Publish</button>
      </div>

      <div class="statusBox">
        <div id="phase3Msg" class="muted">جاهز.</div>
        <div class="smallMeta">
          Current Project ID:
          <b id="currentProjectIdLabel">0</b>
        </div>
      </div>
    </div>
  </main>

</div>

<script src="assets/js/company.js"></script>

<script>
const API_BASE_PHASE3 = "../utbn-backend/api";


let LAST_PROJECT_ID = parseInt(localStorage.getItem("phase3_last_project_id") || "0", 10) || 0;

function setProjectId(id){
  LAST_PROJECT_ID = parseInt(id || "0", 10) || 0;
  localStorage.setItem("phase3_last_project_id", String(LAST_PROJECT_ID));
  document.getElementById("currentProjectIdLabel").textContent = String(LAST_PROJECT_ID || 0);
}

function clearProjectId(){
  LAST_PROJECT_ID = 0;
  localStorage.removeItem("phase3_last_project_id");
  document.getElementById("currentProjectIdLabel").textContent = "0";
}

function msg(t, ok=false){
  const el = document.getElementById("phase3Msg");
  if(!el) return;
  el.textContent = t || "";
  el.className = ok ? "statusOk" : "statusErr";
}

function msgNeutral(t){
  const el = document.getElementById("phase3Msg");
  if(!el) return;
  el.textContent = t || "";
  el.className = "muted";
}

function ensureProjectIdFromStorage(){
  if(!LAST_PROJECT_ID){
    LAST_PROJECT_ID = parseInt(localStorage.getItem("phase3_last_project_id") || "0", 10) || 0;
  }
  document.getElementById("currentProjectIdLabel").textContent = String(LAST_PROJECT_ID || 0);
  return LAST_PROJECT_ID > 0;
}

async function postJson(path, data){
  const res = await fetch(`${API_BASE_PHASE3}/${path}`, {
    method: "POST",
    headers: { "Content-Type":"application/json" },
    credentials: "include",
    body: JSON.stringify(data || {})
  });

  const text = await res.text();
  let j = {};
  try { j = JSON.parse(text); } catch { j = { ok:false, error:"BAD_JSON", _raw:text }; }

  return {
    ok: res.ok && !!j.ok,
    status: res.status,
    json: j
  };
}

function fillFromSavedDraft(){
  ensureProjectIdFromStorage();
}

document.getElementById("btnSavePhase3").addEventListener("click", async ()=>{
  const title = document.getElementById("capTitle").value.trim();
  const desc  = document.getElementById("capDesc").value.trim();

  if(!title || !desc){
    msg("Please fill title + description");
    return;
  }

  msgNeutral("Saving...");

  const payload = {
    project_id: LAST_PROJECT_ID || 0,
    title: title,
    description: desc
  };

  const r = await postJson("partner_phase3_save.php", payload);

  if(!r.ok){
    msg("Save failed: " + (r.json.error || r.status));
    console.log("SAVE_RESPONSE_ERROR", r.json);
    return;
  }

  const newId = parseInt(r.json.project_id || r.json.capstone_id || r.json.id || "0", 10) || 0;
  if(!newId){
    msg("Saved but missing project id in response!");
    console.log("SAVE_RESPONSE", r.json);
    return;
  }

  setProjectId(newId);
  msg("Saved ✔ project_id=" + LAST_PROJECT_ID, true);
});


document.getElementById("btnAnalyzePhase3").addEventListener("click", async ()=>{
  if(!ensureProjectIdFromStorage()){
    msg("Save first to get project_id");
    return;
  }

  msgNeutral("Analyzing with AI architect...");

  const r = await postJson("phase3/architect.php", {
    project_id: LAST_PROJECT_ID
  });

  if(!r.ok){
    msg("Architect failed: " + (r.json.error || r.status));
    console.log("ARCHITECT_ERROR", r.json);
    return;
  }

  if (r.json.project_id) setProjectId(r.json.project_id);

  msg("Architect done ✔ tasks=" + (r.json.tasks_inserted || r.json.tasks_count || 0), true);
});


document.getElementById("btnMatchPhase3").addEventListener("click", async ()=>{
  if(!ensureProjectIdFromStorage()){
    msg("Save first");
    return;
  }

  msgNeutral("Auto matching students...");

  const r = await postJson("phase3/match.php", {
    project_id: LAST_PROJECT_ID
  });

  if(!r.ok){
    msg("Match failed: " + (r.json.error || r.status));
    console.log("MATCH_ERROR", r.json);
    return;
  }

  if (r.json.project_id) setProjectId(r.json.project_id);

  const assignments =
    (r.json.match && Array.isArray(r.json.match.assignments)) ? r.json.match.assignments :
    (Array.isArray(r.json.assignments) ? r.json.assignments : []);

  const hasZeroTaskIds = assignments.some(x => Number(x.task_id || 0) <= 0);
  if (hasZeroTaskIds) {
    msg("Match done لكن يوجد task_id=0. تأكد أن match.php يربط task_code مع task_id.", false);
    console.log("MATCH_RESPONSE_WITH_ZERO_TASK_IDS", r.json);
    return;
  }

  msg("Match done ✔ assignments=" + assignments.length, true);
});


document.getElementById("btnFinalizePhase3").addEventListener("click", async ()=>{
  if(!ensureProjectIdFromStorage()){
    msg("Save first");
    return;
  }

  msgNeutral("Generating final report / publishing project...");

  const r = await postJson("phase3/finalize.php", {
    project_id: LAST_PROJECT_ID
  });

  if(!r.ok){
    msg("Finalize failed: " + (r.json.error || r.status));
    console.log("FINALIZE_ERROR", r.json);
    return;
  }

  if (r.json.project_id) setProjectId(r.json.project_id);

  msg("Finalize success ✔ status=" + (r.json.status || "PUBLISHED"), true);
  console.log("FINAL_REPORT", r.json.final_report || r.json);
});

fillFromSavedDraft();
</script>

<script>
function fixPhase3ArabicUI(){
  const saveBtn = document.getElementById("btnSavePhase3");
  if(saveBtn && saveBtn.textContent.trim() === "حفظ Phase3"){
    saveBtn.textContent = "Save Phase 3";
  }
}
fixPhase3ArabicUI();
const obs = new MutationObserver(() => fixPhase3ArabicUI());
obs.observe(document.body, { childList:true, subtree:true });
</script>

</body>
</html>

