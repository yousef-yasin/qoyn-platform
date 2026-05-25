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
  <title>Phase 1</title>
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
      --card:#FFFFFF;
      --shadow:0 18px 45px rgba(8,43,95,.08);
      --radius:18px;
      --yellow:#FFC24A;
      --danger:#E84C5A;
    }
    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{margin:0;background:var(--bg);color:var(--text);font-family:"Poppins",Arial,sans-serif;overflow-x:hidden;display:block!important}
    .phase-page{width:100%;min-height:100vh;background:linear-gradient(180deg,#FBFCFF 0%,#F5F7FC 100%)}
    .phase-shell{width:min(1260px,calc(100% - 36px));margin:0 auto;padding:18px 0 42px}
    .topbar{height:74px;background:rgba(255,255,255,.96);border-bottom:1px solid var(--line);box-shadow:0 10px 32px rgba(8,43,95,.04);position:sticky;top:0;z-index:50}
    .topbar-inner{width:min(1260px,calc(100% - 36px));height:100%;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:18px}
    .logo{font-family:"Montserrat",sans-serif;font-weight:900;font-size:26px;letter-spacing:1px;color:var(--navy);text-decoration:none;line-height:1}
    .back-btn{display:inline-flex;align-items:center;gap:8px;background:var(--navy);color:#fff!important;border:0!important;border-radius:10px;padding:10px 18px;text-decoration:none;font-family:"Montserrat",sans-serif;font-weight:800;font-size:13px;box-shadow:0 10px 22px rgba(8,43,95,.18);transition:.2s ease}
    .back-btn:hover{transform:translateY(-2px);background:var(--navy2)}
    .hero-head{text-align:center;padding:8px 0 12px}
    .hero-head h1{margin:0 0 10px;font-family:"Montserrat",sans-serif;font-size:44px;line-height:1;font-weight:900;color:var(--text);letter-spacing:-.8px}
    .steps-line{color:var(--muted);font-size:13px;font-weight:500}.steps-line:after{content:"";display:block;width:22px;height:3px;background:var(--yellow);border-radius:30px;margin:10px auto 0}
    .section-card{background:transparent;border:0;border-radius:0;box-shadow:none;padding:22px 0 30px;margin:0;position:relative;overflow:visible;border-bottom:1px solid rgba(8,43,95,.08)}
    .section-card.tall,.section-card.medium{min-height:auto}.section-card.center{min-height:260px;display:flex;align-items:center;justify-content:center;text-align:center;border-bottom:0}
    .section-title{display:flex;align-items:flex-start;gap:13px;margin-bottom:18px}.step-num{width:32px;height:32px;min-width:32px;border-radius:50%;display:grid;place-items:center;background:var(--navy);color:#fff;font-family:"Montserrat",sans-serif;font-weight:900;font-size:14px;box-shadow:0 10px 20px rgba(8,43,95,.12)}
    h2{margin:0 0 6px;font-family:"Montserrat",sans-serif;font-size:22px;line-height:1.25;font-weight:900;color:var(--text);letter-spacing:-.2px}.muted{color:var(--muted);font-size:13px;line-height:1.75;margin:0}.section-subtitle{margin-top:2px;color:var(--muted);font-size:12.5px}
    .form-panel{background:transparent;border:0;border-radius:0;padding:0;box-shadow:none}
    .row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;align-items:start}.row.two{grid-template-columns:repeat(2,minmax(0,1fr))}.row.auto{display:flex;flex-wrap:wrap;gap:12px;align-items:center}.row.auto>*{flex:1;min-width:170px}
    .input,input.input,select.input,textarea.input,input[type="file"]{width:100%;height:42px;background:#fff;border:1px solid #DDE5F0;border-radius:10px;padding:0 14px;color:var(--text);font-family:"Poppins",Arial,sans-serif;font-size:13px;outline:0;box-shadow:inset 0 1px 0 rgba(255,255,255,.7);transition:.18s ease}textarea.input{height:auto;min-height:104px;padding:13px 14px;resize:vertical}input[type="file"]{padding:9px;background:#fff}.input:focus{border-color:#9DB7DF;box-shadow:0 0 0 4px rgba(44,116,229,.08)}
    .btn,.miniBtn,button,a.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;height:38px;border-radius:8px;border:1px solid rgba(8,43,95,.12)!important;background:var(--navy)!important;color:#fff!important;text-decoration:none;cursor:pointer;padding:0 18px;font-family:"Montserrat",sans-serif;font-size:12px;font-weight:900;white-space:nowrap;box-shadow:0 10px 22px rgba(8,43,95,.16);transition:.18s ease}.btn:hover,.miniBtn:hover,button:hover,a.btn:hover{background:var(--navy2)!important;transform:translateY(-1px);box-shadow:0 14px 28px rgba(8,43,95,.22)}.btn.danger{background:#fff!important;color:var(--danger)!important;border-color:#F3D7DB!important;box-shadow:0 8px 18px rgba(232,76,90,.06)}.btn.danger:hover{background:#FFF4F5!important;color:var(--danger)!important}
    .btnRow{display:flex;gap:12px;flex-wrap:wrap;align-items:center}.file-actions{display:grid;grid-template-columns:1fr 1.05fr 1fr 1fr;gap:14px;margin-top:20px}.wide-btn{width:100%;margin-top:12px}.label{display:block;margin:12px 0 7px;font-size:12px;font-weight:800;color:var(--text)}
    hr{border:0;border-top:1px dashed #DDE5F0;margin:22px 0}.quizItem{background:transparent!important;border:0!important;box-shadow:none!important;padding:0!important;margin-top:10px}.list{margin:10px 0 0 0;padding-left:20px;color:var(--muted);font-size:13px;line-height:1.8}.msg{min-height:20px;margin-top:8px}.code-grid{display:grid;grid-template-columns:1.4fr .45fr .45fr;gap:12px}.textarea-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:12px}.submit-final{text-align:center}.submit-final .icon-card{width:100px;height:100px;margin:0 auto 18px;border-radius:28px;background:linear-gradient(135deg,#EEF4FF,#fff);display:grid;place-items:center;font-size:56px;box-shadow:0 18px 34px rgba(44,116,229,.10)}.submit-final h3{margin:0 0 8px;font-family:"Montserrat",sans-serif;font-size:22px;font-weight:900}.submit-final .btn{height:52px;min-width:230px;border-radius:10px;margin-top:18px}.footer-submit{display:none}.ps-block{margin-top:0}.cover-block{margin-top:18px}.cover-row{display:grid;grid-template-columns:1fr auto;gap:12px;align-items:center}.phase-grid{display:grid;grid-template-columns:1fr;gap:0;align-items:start}.phase-grid .section-card{margin:0}.full{grid-column:1 / -1}
    @media(max-width:980px){.phase-grid{grid-template-columns:1fr}.row,.row.two,.code-grid,.textarea-grid,.file-actions,.cover-row{grid-template-columns:1fr}.hero-head h1{font-size:36px}.phase-shell,.topbar-inner{width:min(100% - 24px,1260px)}}
  </style>
</head>
<body>
<div class="phase-page">
<header class="topbar"><div class="topbar-inner"><a class="logo" href="company.php">QOYN</a><a class="back-btn" href="company.php">← Back</a></div></header>
<main class="phase-shell">
  <section class="hero-head"><h1>Phase 1</h1><div class="steps-line">File/Playlist → Title → Upload Video → Questions → Submit</div></section>
  <div class="phase-grid">
    <section class="section-card tall">
      <div class="section-title"><span class="step-num">1</span><div><h2>File / Playlist</h2><p class="section-subtitle">Select a path then one of your cloned playlists, or use the main selector below.</p></div></div>
      <div class="form-panel">
        <div class="row two" style="margin-bottom:12px">
          <select id="pathPicker" class="input"><option value="">Select Path...</option></select>
          <select id="playlistPicker" class="input" disabled><option value="">Select Playlist...</option></select>
        </div>
        <button id="openPicked" class="btn" type="button" disabled>▣ Open</button>
        <div class="row auto" style="margin-top:18px">
          <select class="input" id="playlistSelect"><option value="">... Loading files</option></select>
          <button class="btn" type="button" id="btnNewPlaylist">＋ Add File</button>
          <button class="btn" type="button" id="btnPublishPlaylist">✈ Publish</button>
          <button class="btn danger" type="button" id="btnDeletePlaylist">🗑 Delete</button>
        </div>
      </div>
      <div class="muted msg" id="plMsg"></div>
      <div class="cover-block">
        <label class="label">صورة البلاي ليست (اختياري)</label>
        <div class="cover-row"><input type="file" id="playlistCover" accept="image/*"><button class="btn" type="button" id="btnSavePlaylistCover">حفظ صورة البلاي ليست</button></div>
        <div class="muted msg" id="plCoverMsg"></div>
      </div>
      <div id="plVideosMsg" class="muted msg"></div><ul id="plVideosList" class="list"></ul>
    </section>
    <section class="section-card medium">
      <div class="section-title"><span class="step-num">2</span><div><h2>Video Title</h2><p class="section-subtitle">You cannot upload a video without a title.</p></div></div>
      <input class="input" id="videoTitle" placeholder="Write a clear video title..."/>
      <div style="margin-top:14px"><button class="btn" type="button" id="btnSaveVideoTitle">▣ Save Video Title</button></div>
      <div class="muted msg" id="videoTitleMsg"></div>
    </section>
    <section class="section-card medium">
      <div class="section-title"><span class="step-num">3</span><div><h2>Upload Video</h2><p class="section-subtitle">After upload, a Video ID will appear and the video will be linked to the selected file.</p></div></div>
      <label class="label">صورة الغلاف (اختياري)</label>
      <div class="cover-row"><input type="file" id="coverImage" accept="image/*"><button class="btn" type="button" id="btnSaveVideoCover">حفظ غلاف الفيديو</button></div>
      <div class="muted msg" id="coverMsg"></div>
      <div class="muted" style="margin:8px 0 12px">يفضل 1280x720 أو أي صورة واضحة.</div>
      <div class="row two"><input class="input" type="file" id="videoFile" accept="video/*"/><button class="btn" id="btnUploadVideo" type="button">↥ Upload Video</button></div>
      <div class="muted msg" id="uploadMsg"></div><input type="hidden" id="currentVideoId" value=""/>
    </section>
    <section class="section-card medium">
      <div class="section-title"><span class="step-num">4</span><div><h2>Video Questions + Answers</h2><p class="section-subtitle">Each question: 4 choices + correct answer + explanation (optional).</p></div></div>
      <div class="btnRow"><button class="btn" type="button" id="btnAddQuestion">＋ Add Question</button><button class="btn" type="button" id="btnSaveQuiz">▣ Save Questions</button></div>
      <div class="muted msg" id="quizMsg"></div><div id="quizList"></div>
    </section>
    <section class="section-card medium">
      <div class="section-title"><span class="step-num">5</span><div><h2>Problem Solving (Code Writing)</h2><p class="section-subtitle">If you enable this section, students will see a box to write code and submit it.<br>Evaluation is done by AI by comparing it to your solution (it does not have to match your code 100%).</p></div></div>
      <div class="ps-block"><div class="code-grid"><input class="input" id="codeTitle" placeholder="Problem title (e.g., Sum of Two Numbers)"/><select class="input" id="codeLang"><option value="python">Python</option><option value="javascript">JavaScript</option><option value="cpp">C++</option><option value="java">Java</option></select><input class="input" id="codeMaxCoin" type="number" min="1" max="1000" value="50" placeholder="Max coin"/></div><textarea class="input" id="codePrompt" style="margin-top:12px" placeholder="Problem statement + requirements + input/output + examples"></textarea><div class="textarea-grid"><textarea class="input" id="codeStarter" placeholder="Starter code (optional)"></textarea><textarea class="input" id="codeSolution" placeholder="Instructor solution (required)"></textarea></div><div class="btnRow" style="margin-top:14px"><button class="btn" type="button" id="btnSaveCodeProblem">▣ Save Coding Problem</button><button class="btn danger" type="button" id="btnDeleteCodeProblem">🗑 Delete Coding Problem</button></div><div class="muted msg" id="codeMsg"></div></div>
    </section>
    <section class="section-card center">
      <div class="submit-final"><div class="icon-card">📋</div><h3>You're almost done!</h3><p class="muted">Review your content and submit Phase 1 when ready.</p><button class="btn" type="button" id="btnSubmitPhase1">Submit Phase 1 ✈</button><div class="muted msg" id="submitMsg"></div></div>
    </section>
  </div>
</main>
</div>
<script src="assets/js/company.js"></script>

<script>
  // =========================================================
  // UI Text Fixes (without touching company.js)
  // =========================================================
  const placeholderMap = new Map([
    ["اكتب السوال هنا", "Write the question here"],
    ["اكتب السؤال هنا", "Write the question here"],
    ["اختيار A", "Choice A"],
    ["اختيار B", "Choice B"],
    ["اختيار C", "Choice C"],
    ["اختيار D", "Choice D"],
    ["الإجابة الصحيحة", "Correct answer"],
    ["اكتب الشرح هنا", "Write the explanation here"],
  ]);

  function translatePlaceholders(root=document){
    root.querySelectorAll("input[placeholder], textarea[placeholder]").forEach(el => {
      const ph = el.getAttribute("placeholder");
      if(ph && placeholderMap.has(ph)) el.setAttribute("placeholder", placeholderMap.get(ph));
    });
  }

  function removeVideosHint(){
    const msg = document.getElementById("plVideosMsg");
    if(!msg) return;
    if(msg.textContent.includes("اختر ملف لعرض الفيديوهات")) msg.textContent = "";
    if(msg.textContent.toLowerCase().includes("videos in this file")) msg.textContent = "";
  }

  function translatePlaylistLoading(){
    const sel = document.getElementById("playlistSelect");
    if(!sel) return;
    const opt = sel.querySelector("option");
    if(opt && opt.textContent.includes("تعذر تحميل الملفات")) opt.textContent = "Failed to load files";
  }

  function fixArabicUI(){
    translatePlaceholders(document);
    removeVideosHint();
    translatePlaylistLoading();
  }

  // Initial run
  fixArabicUI();

  // Watch dynamic changes from company.js
  const obs = new MutationObserver(() => fixArabicUI());
  obs.observe(document.body, { childList:true, subtree:true });
  async function deletePlaylist(){
  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  if (!playlistId) { setMsg("plMsg","اختر ملف أولاً"); return; }
  if (!confirm("متأكد بدك تحذف هذا الملف وكل فيديوهاته؟")) return;

  const r = await apiPostJson("partner_playlist_delete.php", { playlist_id: playlistId });
  if (!r.ok) { setMsg("plMsg", r.json?.error ? `خطأ: ${r.json.error}` : "فشل الحذف"); return; }

  setMsg("plMsg","تم حذف الملف ✅", true);
  await loadPlaylists("");
  document.getElementById("plVideosList").innerHTML = "";
  document.getElementById("plVideosMsg").textContent = "";
}

document.getElementById("btnDeletePlaylist")?.addEventListener("click", deletePlaylist);

</script>
<script>
async function saveVideoCover(){
  const vid = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);
  const file = document.getElementById("coverImage")?.files?.[0];
  const msgEl = document.getElementById("coverMsg");

  if (!vid) { msgEl.textContent = "لازم تختار فيديو (اضغط Edit على الفيديو أولاً)"; return; }
  if (!file) { msgEl.textContent = "اختَر صورة غلاف أولاً"; return; }

  msgEl.textContent = "جاري حفظ الغلاف...";

  const fd = new FormData();
  fd.append("video_id", String(vid));
  fd.append("cover", file);

  const r = await fetch("/utbn-backend/api/partner_video_cover_update.php", {
    method: "POST",
    credentials: "include",
    body: fd
  });

  const j = await r.json().catch(()=>({}));
  if (!r.ok || !j.ok){
    msgEl.textContent = "فشل: " + (j.error || "ERROR");
    return;
  }

  msgEl.textContent = "تم حفظ غلاف الفيديو ✅";
  // إذا بدك: reload للقائمة حتى تشوفه (حسب company.js كيف بعرض القائمة)
  // location.reload();
}

document.getElementById("btnSaveVideoCover")?.addEventListener("click", saveVideoCover);
</script>
<script>
(function(){
  const qs = new URLSearchParams(location.search);
  const pid = parseInt(qs.get("playlist_id") || "0", 10);
  if(!pid) return;

  const sel = document.getElementById("playlistSelect");
  if(!sel) return;

  // انتظر لحد ما company.js يحمّل الخيارات
  const t = setInterval(()=>{
    const opt = sel.querySelector(`option[value="${pid}"]`);
    if(opt){
      sel.value = String(pid);
      sel.dispatchEvent(new Event("change"));
      clearInterval(t);
    }
  }, 200);

  setTimeout(()=> clearInterval(t), 8000);
})();
</script>


<script>
const API = "/utbn-backend/api";

async function getJson(url){
  const r = await fetch(url, { credentials:"include" });
  const j = await r.json().catch(()=> ({}));
  if(!r.ok || !j.ok) throw j;
  return j;
}

(async function initPathPlaylistPicker(){
  const pathPicker = document.getElementById("pathPicker");
  const playlistPicker = document.getElementById("playlistPicker");
  const openBtn = document.getElementById("openPicked");

  if(!pathPicker || !playlistPicker || !openBtn) return;

  // حمّل المسارات المتاحة للشركة
  try{
    const paths = await getJson(`${API}/company_available_paths.php`);
    (paths.items || []).forEach(p=>{
      const opt = document.createElement("option");
      opt.value = p.id;
      opt.textContent = `${p.title} (ID: ${p.id})`;
      pathPicker.appendChild(opt);
    });
  }catch(e){
    console.error(e);
  }

  async function loadMyPlaylists(pathId){
    playlistPicker.innerHTML = `<option value="">اختر Playlist...</option>`;
    playlistPicker.disabled = true;
    openBtn.disabled = true;

    if(!pathId) return;

    try{
      const pls = await getJson(`${API}/company_my_playlists.php?path_id=${encodeURIComponent(pathId)}`);
      (pls.items || []).forEach(pl=>{
        const opt = document.createElement("option");
        opt.value = pl.id;
        opt.textContent = `${pl.name} (ID: ${pl.id})`;
        playlistPicker.appendChild(opt);
      });
      playlistPicker.disabled = false;
    }catch(e){
      console.error(e);
    }
  }

  pathPicker.onchange = ()=> loadMyPlaylists(pathPicker.value);

  playlistPicker.onchange = ()=>{
    openBtn.disabled = !playlistPicker.value;
  };

  openBtn.onclick = ()=>{
    const pid = playlistPicker.value;
    if(!pid) return;
    window.location.href = `company_phase1.php?playlist_id=${encodeURIComponent(pid)}`;
  };
})();
</script>
</body>
</html>



