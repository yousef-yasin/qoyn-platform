<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}

$pid = $_GET["list"] ?? "";
$pid = preg_replace("/[^a-zA-Z0-9_\-]/", "", $pid);
if ($pid === "") die("Playlist غير صالحة");

// ✅ اسم المادة (اختياري) عشان الأسئلة تصير أقوى
$course = trim($_GET["course"] ?? "");
$course_safe = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>مشاهدة الدورة</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- ✅ Fonts (QOYN theme) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --navyHover:#144270; /* أفتح ~4 درجات */
      --yellow:#FFC24A;
      --bg:#F6F7F9;
      --card:#ffffff;
      --text:#0B0B0B;
      --muted:rgba(0,0,0,.62);
      --line:rgba(0,0,0,.08);
      --shadow: 0 10px 30px rgba(0,0,0,.08);
      --container: 1200px;
      --radius: 24px;
      --radiusSm: 14px;
    }

    *{box-sizing:border-box}

    body{
      margin:0;
      background:var(--bg);
      color:var(--text);
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Kufi Arabic", "Cairo", sans-serif;
    }

    .container{
      max-width:var(--container);
      margin:0 auto;
      padding:26px 18px 60px;
    }

    /* ✅ Topbar re-theme (keep same markup) */
    .topbar{
      max-width:var(--container);
      margin:18px auto 0;
      padding:16px 18px;
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
      flex-wrap:wrap;

      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      border:1px solid var(--line);
      box-shadow: var(--shadow);
      border-radius: var(--radius);
    }

    .topbar h1{
      margin:0;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      letter-spacing:-.2px;
      font-size:28px;
      color:#111;
    }

    .muted{ color: var(--muted) !important; }

    /* ✅ Buttons (override existing) */
    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:10px 14px;
      border-radius: var(--radiusSm);
      text-decoration:none;
      cursor:pointer;
      user-select:none;
      white-space:nowrap;

      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size:13px;
      letter-spacing:.2px;

      background: var(--navy) !important;
      color:#fff !important;
      border:1px solid rgba(0,0,0,.08) !important;
      box-shadow: 0 14px 28px rgba(0,0,0,.12) !important;
      transition: transform .12s ease, background .12s ease, box-shadow .12s ease;
    }
    .btn:hover{
      transform: translateY(-1px);
      background: var(--navyHover) !important;
      box-shadow: 0 20px 42px rgba(0,0,0,.16) !important;
    }
    .btn.ghost{ background: var(--navy) !important; }

    /* ✅ Cards */
    .card{
      background: var(--card) !important;
      border: 1px solid var(--line) !important;
      border-radius: var(--radius) !important;
      box-shadow: var(--shadow) !important;
      padding: 18px !important;
    }

    /* ===== Player ===== */
    .playerWrap { margin-top:16px }
    .playerBox {
      width:100%;
      aspect-ratio: 16 / 9;
      border-radius: 18px;
      overflow:hidden;
      background:#000;
      border:1px solid rgba(0,0,0,.10);
    }
    #ytPlayer { width:100%; height:100%; }

    textarea{
      width:100%;
      padding:12px 14px;
      border-radius:14px;
      border:1px solid rgba(0,0,0,.12);
      background:#fff;
      color:#111;
      outline:none;
      resize: vertical;
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      font-size:14px;
      box-shadow:none;
    }
    textarea:focus{
      border-color: rgba(10,46,93,.35);
      box-shadow: 0 0 0 4px rgba(10,46,93,.08);
    }

    .hintBox{
      padding:10px 12px;
      border:1px solid rgba(0,0,0,.10);
      border-radius:14px;
      background: rgba(10,46,93,.03);
      color: rgba(0,0,0,.75);
      line-height:1.8;
      font-size:13px;
    }

    /* radio labels nicer (no layout changes) */
    input[type="radio"]{
      accent-color: var(--navy);
      margin-top:2px;
    }
  </style>
</head>
<body>

<div class="topbar">
  <div>
    <h1>مشاهدة الدورة</h1>
    <div class="muted">المشاهدة داخل موقعك + اختبار تلقائي سريع + اختبار متقدم تلقائي (مرة واحدة لكل فيديو) + حفظ الحل</div>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="courses.php" class="btn ghost">رجوع للدورات</a>
    <a href="index.php" class="btn ghost">الرئيسية</a>
  </div>
</div>

<div class="container">

  <!-- ===== Player ===== -->
  <div class="card">
    <b style="font-family:Montserrat, sans-serif;font-weight:800;color:#111">الدورة الكاملة</b>
    <div class="muted" style="margin-top:8px">
      اختر أي فيديو من البلاي ليست — سيتم التعرف عليه تلقائيًا ثم توليد اختبار سريع ثم متقدم (مرة واحدة لكل فيديو).
    </div>

    <div class="playerWrap">
      <div class="playerBox">
        <div id="ytPlayer"></div>
      </div>
    </div>

    <div class="muted" id="nowPlaying" style="margin-top:10px">الفيديو الحالي: ...</div>
    <?php if ($course_safe !== ""): ?>
      <div class="muted" style="margin-top:6px">المادة: <?php echo $course_safe; ?></div>
    <?php endif; ?>
  </div>

  <!-- ===== Quiz Under SAME page ===== -->
  <div class="card" style="margin-top:14px">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
      <div>
        <b style="font-family:Montserrat, sans-serif;font-weight:800;color:#111">اختبار على الفيديو الحالي</b>
        <div class="muted" id="quizTitle" style="margin-top:6px">
          سيتم تحديث الاختبار تلقائيًا عند تغيير الفيديو
        </div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn ghost" id="genQuizBtn">تحديث الاختبار السريع</button>
        <button class="btn" id="deepQuizBtn">اختبار متقدم</button>
      </div>
    </div>

    <div class="muted" id="quizHint" style="margin-top:10px"></div>
    <div id="quizBox" style="margin-top:12px"></div>
  </div>

</div>

<script>
  
  // ✅ اسم المادة لو موجود
  window.COURSE_NAME = "<?php echo $course_safe; ?>";
  window.API_BASE = window.API_BASE || "/utbn-backend/api";

  function esc(s){
    return String(s ?? "").replace(/[&<>"']/g, m => ({
      "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
    }[m]));
  }

  function showHint(html){
    document.getElementById("quizHint").innerHTML = `<div class="hintBox">${html}</div>`;
  }

  // ====== Render Quiz UI ======
  function renderQuizUI(data){
    const box = document.getElementById("quizBox");

    const mcq = (data.quiz?.mcq || []).map((x, i) => `
      <div class="card" style="margin-top:10px">
        <b>(${i+1}) ${esc(x.q)}</b>
        <div style="margin-top:10px;display:grid;gap:8px">
          ${(x.choices||[]).map((c, idx) => `
            <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer">
              <input type="radio" name="mcq_${i}" value="${idx}">
              <span>${esc(c)}</span>
            </label>
          `).join("")}
        </div>
        <div class="muted" data-explain style="margin-top:10px;display:none"></div>
      </div>
    `).join("");

    const tf = (data.quiz?.trueFalse || []).map((x, i) => `
      <div class="card" style="margin-top:10px">
        <b>(صح/خطأ ${i+1}) ${esc(x.q)}</b>
        <div style="margin-top:10px;display:flex;gap:12px;flex-wrap:wrap">
          <label style="cursor:pointer"><input type="radio" name="tf_${i}" value="true"> صح</label>
          <label style="cursor:pointer"><input type="radio" name="tf_${i}" value="false"> خطأ</label>
        </div>
        <div class="muted" data-explain style="margin-top:10px;display:none"></div>
      </div>
    `).join("");

    const shortQ = (data.quiz?.short || []).map((x, i) => `
      <div class="card" style="margin-top:10px">
        <b>(سؤال قصير ${i+1}) ${esc(x.q)}</b>
        <textarea data-short="${i}" style="margin-top:10px;min-height:90px" placeholder="اكتب إجابتك هنا..."></textarea>
        <div class="muted" data-explain style="margin-top:10px;display:none"></div>
      </div>
    `).join("");

    const app = (data.quiz?.application || []).map((x, i) => `
      <div class="card" style="margin-top:10px">
        <b>(تطبيق/تحليل ${i+1}) ${esc(x.q)}</b>
        <textarea data-app="${i}" style="margin-top:10px;min-height:110px" placeholder="اكتب الحل/التحليل هنا..."></textarea>
        <div class="muted" data-explain style="margin-top:10px;display:none"></div>
      </div>
    `).join("");

    box.innerHTML = `
      ${mcq}
      ${tf}
      ${shortQ}
      ${app}
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;align-items:center">
        <button class="btn" id="checkBtn">تصحيح</button>
        <button class="btn ghost" id="showBtn">إظهار الحل والشرح</button>
        <button class="btn" id="saveBtn">حفظ الحل</button>
        <div class="muted" id="scoreBox" style="margin-top:6px"></div>
      </div>
    `;

    document.getElementById("checkBtn").onclick = () => {
      let score = 0, total = 0;

      (data.quiz?.mcq || []).forEach((x, i) => {
        total++;
        const chosen = document.querySelector(`input[name="mcq_${i}"]:checked`);
        if (chosen && Number(chosen.value) === Number(x.answerIndex)) score++;
      });

      (data.quiz?.trueFalse || []).forEach((x, i) => {
        total++;
        const chosen = document.querySelector(`input[name="tf_${i}"]:checked`);
        if (chosen && (chosen.value === String(!!x.answer))) score++;
      });

      document.getElementById("scoreBox").textContent = `نتيجتك: ${score} / ${total}`;
    };

    document.getElementById("showBtn").onclick = () => {
      const cards = box.querySelectorAll(".card");
      let idx = 0;

      (data.quiz?.mcq || []).forEach((x) => {
        const el = cards[idx++].querySelector("[data-explain]");
        el.style.display = "block";
        const correct = (x.choices||[])[x.answerIndex] ?? "";
        el.innerHTML = `✅ الإجابة: <b>${esc(correct)}</b><br>${esc(x.explain||"")}`;
      });

      (data.quiz?.trueFalse || []).forEach((x) => {
        const el = cards[idx++].querySelector("[data-explain]");
        el.style.display = "block";
        el.innerHTML = `✅ الإجابة: <b>${x.answer ? "صح" : "خطأ"}</b><br>${esc(x.explain||"")}`;
      });

      (data.quiz?.short || []).forEach((x) => {
        const el = cards[idx++].querySelector("[data-explain]");
        el.style.display = "block";
        el.innerHTML = `✅ نموذج إجابة: <b>${esc(x.answer||"")}</b><br><span class="muted">${esc(x.rubric||"")}</span>`;
      });

      (data.quiz?.application || []).forEach((x) => {
        const el = cards[idx++].querySelector("[data-explain]");
        el.style.display = "block";
        el.innerHTML = `✅ حل/شرح: <b>${esc(x.answer||"")}</b><br><span class="muted">${esc(x.rubric||"")}</span>`;
      });
    };

    document.getElementById("saveBtn").onclick = () => saveAttempt();
  }

  function collectAnswers(data){
    const out = { mcq: [], trueFalse: [], short: [], application: [] };

    (data.quiz?.mcq || []).forEach((x, i) => {
      const chosen = document.querySelector(`input[name="mcq_${i}"]:checked`);
      out.mcq[i] = chosen ? Number(chosen.value) : null;
    });

    (data.quiz?.trueFalse || []).forEach((x, i) => {
      const chosen = document.querySelector(`input[name="tf_${i}"]:checked`);
      out.trueFalse[i] = chosen ? (chosen.value === "true") : null;
    });

    (data.quiz?.short || []).forEach((x, i) => {
      const ta = document.querySelector(`textarea[data-short="${i}"]`);
      out.short[i] = ta ? ta.value : "";
    });

    (data.quiz?.application || []).forEach((x, i) => {
      const ta = document.querySelector(`textarea[data-app="${i}"]`);
      out.application[i] = ta ? ta.value : "";
    });

    return out;
  }

  async function saveAttempt(){
    if (!currentQuizData || !currentVideoId) return;
    console.log("CLAIM currentVideoId =", currentVideoId);

    const answers = collectAnswers(currentQuizData);
    const base = (window.API_BASE || "/utbn-backend/api").replace(/\/+$/,"");
    const url = base + "/video_quiz_submit.php";

    showHint("💾 جاري حفظ الحل...");

    const r = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({
        videoId: currentVideoId,
        type: currentQuizData.type || "quick",
        quiz: currentQuizData.quiz,
        answers
      })
    });

    const ct = (r.headers.get("content-type") || "").toLowerCase();
    const txt = await r.text();
    if (!r.ok || !ct.includes("application/json")) {
      showHint("⚠️ فشل الحفظ: " + esc(txt.slice(0, 250)));
      return;
    }

    let j;
    try { j = JSON.parse(txt); }
    catch(e){
      showHint("⚠️ JSON غير صالح: " + esc(txt.slice(0, 250)));
      return;
    }

    if (!j.ok) { showHint("⚠️ فشل الحفظ."); return; }

    showHint(`✅ تم حفظ الحل لهذا الفيديو. نتيجتك (MCQ/صح-خطأ): ${j.score} / ${j.total}`);

    // ✅ Claim coins (التعديل الوحيد هنا)
    try {
      const r2 = await fetch("/utbn-backend/api/video_reward_claim.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          videoId: currentVideoId,
          quiz_correct: j.score,
          quiz_total: j.total
        })
      });

      const rrTxt = await r2.text();
      let rr = {};
      try { rr = JSON.parse(rrTxt); } catch(e){ rr = { ok:false, error:"BAD_JSON" }; }

      if (rr.ok && !rr.already_rewarded) {
        showHint(`✅ تم حفظ الحل. نتيجتك: ${j.score}/${j.total}<br>🎉 حصلت على <b>${rr.total_coin}</b> Coins`);
      } else if (rr.ok && rr.already_rewarded) {
        showHint(`✅ تم حفظ الحل. نتيجتك: ${j.score}/${j.total}<br>ℹ️ المكافأة مأخوذة سابقاً لهذا الفيديو`);
      } else {
        showHint(`✅ تم حفظ الحل. نتيجتك: ${j.score}/${j.total}<br>⚠️ لم يتم احتساب Coins: ${esc(rr.error || rrTxt.slice(0,150) || "UNKNOWN")}`);
      }
    } catch(e) {
      showHint(`✅ تم حفظ الحل. نتيجتك: ${j.score}/${j.total}<br>⚠️ فشل طلب المكافأة`);
    }
  }

  // ====== Quiz Logic ======
  let currentVideoId = null;
  let lastGeneratedFor = null;
  let genTimer = null;
  let quizReqId = 0;
  let currentQuizData = null; // آخر quiz رجع من السيرفر
  let autoDeepDoneFor = null; // ✅ حتى ما نعيد deep كثير

  async function fetchWithTimeout(url, opts={}, ms=25000){
    const ctrl = new AbortController();
    const t = setTimeout(() => ctrl.abort(), ms);
    try{
      const r = await fetch(url, { ...opts, signal: ctrl.signal });
      clearTimeout(t);
      return r;
    } catch(e){
      clearTimeout(t);
      throw e;
    }
  }

  function buildQuizUrl(type){
    const course = window.COURSE_NAME || "";
    const base = (window.API_BASE || "/utbn-backend/api").replace(/\/+$/,"");
    return base + "/video_quiz.php?videoId=" + encodeURIComponent(currentVideoId) +
      "&course=" + encodeURIComponent(course) +
      "&type=" + encodeURIComponent(type);
  }

  // ✅ QUICK (Auto + زر تحديث)
  async function generateQuizQuick(auto=false){
    if (!currentVideoId) return;
    if (auto && lastGeneratedFor === currentVideoId) return;

    const myReq = ++quizReqId;
    const box  = document.getElementById("quizBox");
    showHint("⏳ جاري إنشاء اختبار سريع...");
    box.innerHTML = "";

    const url = buildQuizUrl("quick");

    try{
      const r = await fetchWithTimeout(url, { credentials:"include" }, 25000);
      if (myReq !== quizReqId) return;

      const ct = (r.headers.get("content-type") || "").toLowerCase();
      const txt = await r.text();

      if (!r.ok) {
        showHint(`⚠️ خطأ HTTP: ${r.status}<br>${esc(txt.slice(0, 250))}`);
        return;
      }
      if (!ct.includes("application/json")) {
        showHint(`⚠️ السيرفر رجّع HTML بدل JSON (Redirect أو PHP Error).<br>${esc(txt.slice(0, 250))}`);
        return;
      }

      let j;
      try { j = JSON.parse(txt); }
      catch(e){
        showHint(`⚠️ JSON غير صالح.<br>${esc(txt.slice(0, 250))}`);
        return;
      }

      if (!j.ok){
        showHint("⚠️ ما قدرنا نولّد اختبار سريع. جرّب تحديث أو فيديو ثاني.");
        return;
      }

      lastGeneratedFor = currentVideoId;
      currentQuizData = j;
      document.getElementById("quizHint").textContent = "";
      document.getElementById("quizTitle").textContent = (j.videoTitle || "اختبار الفيديو") + " (سريع)";
      renderQuizUI(j);

      // ✅ توليد متقدم تلقائي مرة واحدة لكل فيديو
      if (autoDeepDoneFor !== currentVideoId) {
        autoDeepDoneFor = currentVideoId;
        setTimeout(() => generateQuizDeep(true), 500);
      }

    } catch(e){
      if (myReq !== quizReqId) return;
      const msg = (e && e.name === "AbortError") ? "انتهى وقت الطلب (Timeout)" : (e.message || String(e));
      showHint(`⌛ فشل الطلب: ${esc(msg)}. جرّب مرة ثانية.`);
    }
  }

  // ✅ DEEP (manual + autoOnce)
  async function generateQuizDeep(auto=false){
    if (!currentVideoId) return;

    const myReq = ++quizReqId;
    const box  = document.getElementById("quizBox");
    showHint(auto ? "🔥 جاري إنشاء اختبار متقدم تلقائيًا..." : "🔥 جاري إنشاء اختبار متقدم (قد يأخذ وقتًا أطول)...");
    box.innerHTML = "";

    const url = buildQuizUrl("deep");

    try{
      const r = await fetchWithTimeout(url, { credentials:"include" }, 45000);
      if (myReq !== quizReqId) return;

      const ct = (r.headers.get("content-type") || "").toLowerCase();
      const txt = await r.text();

      if (!r.ok) {
        showHint(`⚠️ خطأ HTTP: ${r.status}<br>${esc(txt.slice(0, 250))}`);
        return;
      }
      if (!ct.includes("application/json")) {
        showHint(`⚠️ السيرفر رجّع HTML بدل JSON.<br>${esc(txt.slice(0, 250))}`);
        return;
      }

      const j = JSON.parse(txt);
      if (!j.ok){
        showHint("⚠️ فشل إنشاء الاختبار المتقدم. جرّب مرة ثانية.");
        return;
      }

      currentQuizData = j;
      document.getElementById("quizHint").textContent = "";
      document.getElementById("quizTitle").textContent = (j.videoTitle || "اختبار الفيديو") + " (متقدم)";
      renderQuizUI(j);

    } catch(e){
      if (myReq !== quizReqId) return;
      const msg = (e && e.name === "AbortError") ? "انتهى وقت الطلب (Timeout)" : (e.message || String(e));
      showHint(`⌛ الاختبار المتقدم فشل: ${esc(msg)}. جرّب مرة ثانية.`);
    }
  }

  document.getElementById("genQuizBtn").addEventListener("click", () => generateQuizQuick(false));
  document.getElementById("deepQuizBtn").addEventListener("click", () => generateQuizDeep(false));

  // ====== YouTube Iframe API ======
  const PLAYLIST_ID = "<?php echo htmlspecialchars($pid, ENT_QUOTES, 'UTF-8'); ?>";
  let player;
  function getCurrentYoutubeId(){
    if (!player) return null;

    const data = (player.getVideoData && player.getVideoData()) ? player.getVideoData() : null;
    let vid = data && data.video_id ? data.video_id : null;

    if (!vid && player.getVideoUrl) {
      const url = player.getVideoUrl() || "";
      const m = url.match(/[?&]v=([^&]+)/);
      if (m && m[1]) vid = m[1];
      if (vid) vid = vid.replace(/[^a-zA-Z0-9_\-]/g, "");
    }

    vid = (vid || "").trim();
    if (!vid || vid === "undefined" || vid === "null") return null;

    return vid;
  }

  function onVideoMaybeChanged(){
    if (!player) return;

    const vid = getCurrentYoutubeId();
    if (!vid) return;

    if (vid !== currentVideoId){
      currentVideoId = vid;

      const vtitle = (player.getVideoData && player.getVideoData().title) ? player.getVideoData().title : "";
      document.getElementById("nowPlaying").textContent = "الفيديو الحالي: " + (vtitle ? vtitle : currentVideoId);

      lastGeneratedFor = null;
      autoDeepDoneFor = null;
      currentQuizData = null;

      clearTimeout(genTimer);
      genTimer = setTimeout(() => generateQuizQuick(true), 600);
    }
  }

  (function(){
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(tag);
  })();

  let watchPing = null;

  function startWatchPing(){
    if (watchPing) clearInterval(watchPing);

    watchPing = setInterval(() => {
      if(!player) return;

      const st = player.getPlayerState?.();
      if (st !== YT.PlayerState.PLAYING) return;

      const vid = getCurrentYoutubeId();
      if(!vid) return;

      const dur = player.getDuration?.() || 0;
      const cur = player.getCurrentTime?.() || 0;
      if (dur <= 0) return;

      fetch("/utbn-backend/api/video_watch.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          videoId: vid,
          watched_seconds: Math.floor(cur),
          duration_seconds: Math.floor(dur)
        })
      })
      .then(r => r.text())
      .then(t => console.log("watch_progress:", t.slice(0,120)))
      .catch(e => console.log("watch_progress err:", e));

    }, 5000);
  }

  function stopWatchPing(){
    if (watchPing) clearInterval(watchPing);
    watchPing = null;
  }

  window.onYouTubeIframeAPIReady = function(){
    player = new YT.Player("ytPlayer", {
      height: "100%",
      width: "100%",
      host: "https://www.youtube-nocookie.com",
      playerVars: {
        listType: "playlist",
        list: PLAYLIST_ID,
        rel: 0,
        modestbranding: 1,
        playsinline: 1
      },
      events: {
        onReady: function(){
          setTimeout(onVideoMaybeChanged, 400);
        },
        onStateChange: function(e){
          onVideoMaybeChanged();

          if (e.data === YT.PlayerState.PLAYING) startWatchPing();
          if (e.data === YT.PlayerState.PAUSED || e.data === YT.PlayerState.ENDED) stopWatchPing();
        }
      }
    });

    setInterval(onVideoMaybeChanged, 900);
  };

</script>

</body>
</html>

