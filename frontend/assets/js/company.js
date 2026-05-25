// utbn-web/assets/js/company.js
const API_BASE = "/utbn-backend/api";

/* ---------------- helpers ---------------- */
async function apiGet(path) {
  const res = await fetch(`${API_BASE}/${path}`, { credentials: "include" });
  const json = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, json };
}

async function apiPostJson(path, data) {
  const res = await fetch(`${API_BASE}/${path}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(data),
  });
  const json = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, json };
}

// robust (لو رجع HTML error)
async function apiPostForm(path, formData) {
  const res = await fetch(`${API_BASE}/${path}`, {
    method: "POST",
    credentials: "include",
    body: formData,
  });

  const text = await res.text();
  let json = {};
  try { json = JSON.parse(text); }
  catch { json = { _raw: text }; }

  return { ok: res.ok, status: res.status, json };
}

function setMsg(id, text, ok = false) {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent = text || "";
  el.style.color = ok ? "#16a34a" : "#b91c1c";
}

function escapeHtml(s) {
  return (s || "").replace(/[&<>"']/g, (m) => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[m]));
}

function formatSecs(total) {
  total = parseInt(total || "0", 10);
  const h = Math.floor(total / 3600);
  const m = Math.floor((total % 3600) / 60);
  const s = total % 60;
  if (h > 0) return `${h}h ${m}m`;
  if (m > 0) return `${m}m ${s}s`;
  return `${s}s`;
}

/* ---------------- Dashboard ---------------- */
async function loadMeIntoName() {
  const el = document.getElementById("companyName");
  if (!el) return;
  const r = await apiGet("partner_me.php");
  el.textContent = (r.ok && r.json.company_name) ? r.json.company_name : "Partner";
}

/* ---------------- Playlists (Phase 1) ---------------- */

function normalizeItems(json) {
  if (!json) return [];
  if (Array.isArray(json)) return json;
  if (Array.isArray(json.items)) return json.items;
  if (Array.isArray(json.data)) return json.data;
  return [];
}

function makeSlug(s) {
  return String(s || "")
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

async function loadPlaylists(selectValueToKeep = "") {
  const sel = document.getElementById("playlistSelect");
  if (!sel) return;

  const keep = selectValueToKeep || sel.value || "";

  sel.innerHTML = `<option value="">اختر ملف...</option>`;
  const r = await apiGet("partner_playlists_list.php");

  if (!r.ok) {
    sel.innerHTML = `<option value="">تعذر تحميل الملفات</option>`;
    return;
  }

  const items = normalizeItems(r.json);

  if (!items.length) {
    sel.innerHTML = `<option value="">لا يوجد ملفات بعد</option>`;
    return;
  }

  for (const p of items) {
    const opt = document.createElement("option");
    opt.value = String(p.id);

    const secs = p.total_seconds ?? p.totalSeconds ?? 0;
    opt.textContent = `${p.name}${(secs ? `  (${formatSecs(secs)})` : "")}`;

    sel.appendChild(opt);
  }

  if (keep) sel.value = keep;
}

async function createPlaylist() {
  setMsg("plMsg", "");
  const name = prompt("اكتب اسم الملف/Playlist:");
  if (!name) return;

  const wantedSlug = makeSlug(name);

  // ✅ NEW required fields
  const description = prompt("اكتب وصف كامل للـ Playlist (إجباري):") || "";
  if (!description.trim()) { setMsg("plMsg", "الوصف إجباري"); return; }

  const expectedStr = prompt("كم محاضرة متوقعة؟ (رقم > 0):") || "";
  const expected_lectures = parseInt(expectedStr, 10);
  if (!Number.isFinite(expected_lectures) || expected_lectures <= 0) {
    setMsg("plMsg", "عدد المحاضرات لازم يكون رقم أكبر من 0");
    return;
  }

  const diffStr = prompt("صعوبة البلاي ليست من 0 إلى 100:") || "0";
  const difficulty = parseInt(diffStr, 10);
  if (!Number.isFinite(difficulty) || difficulty < 0 || difficulty > 100) {
    setMsg("plMsg", "الصعوبة لازم تكون رقم بين 0 و 100");
    return;
  }

  const major_text = prompt("اكتب اسم التخصص (Major) لهذه البلاي ليست:") || "";
  if (!major_text.trim()) { setMsg("plMsg", "التخصص إجباري"); return; }

  const course_name = prompt("اكتب اسم الكورس (للتنظيم داخل التخصص):") || "";
  if (!course_name.trim()) { setMsg("plMsg", "اسم الكورس إجباري"); return; }

  const r = await apiPostJson("partner_playlist_create.php", {
    name,
    description,
    expected_lectures,
    difficulty,
    major_text,
    course_name,
  });

  if (!r.ok) {
    if (r.status === 409) {
      setMsg("plMsg", "⚠️ هذا الملف موجود مسبقًا");

      await loadPlaylists();

      const lr = await apiGet("partner_playlists_list.php");
      const items = normalizeItems(lr.json);

      const found = items.find(p =>
        (p.slug && String(p.slug) === wantedSlug) ||
        (p.name && String(p.name).trim().toLowerCase() === String(name).trim().toLowerCase())
      );

      const sel = document.getElementById("playlistSelect");
      if (sel && found) {
        sel.value = String(found.id);
        loadPlaylistVideos(parseInt(sel.value || "0", 10));
      }
      return;
    }

    setMsg("plMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل إنشاء الملف");
    return;
  }

  if (!r.json.playlist_id && !r.json.id) {
    setMsg("plMsg", "تم الإنشاء لكن لم يرجع playlist_id/id (راجع API)", false);
    return;
  }

  const newId = r.json.playlist_id ?? r.json.id;

  setMsg("plMsg", "تم إنشاء الملف ✅", true);

  await loadPlaylists(String(newId));

  const sel = document.getElementById("playlistSelect");
  if (sel) {
    sel.value = String(newId);
    loadPlaylistVideos(parseInt(sel.value || "0", 10));
  }
}

/* ✅ NEW: تحميل فيديوهات الملف المختار */
async function loadPlaylistVideos(playlistId) {
  const list = document.getElementById("plVideosList");
  const msg  = document.getElementById("plVideosMsg");
  if (!list || !msg) return;

  list.innerHTML = "";
  msg.textContent = "";

  if (!playlistId) {
    msg.textContent = "اختر ملف لعرض الفيديوهات.";
    return;
  }

  const r = await apiGet(`partner_playlist_videos.php?playlist_id=${encodeURIComponent(playlistId)}`);

  if (!r.ok) {
    msg.textContent = r.json?.error ? `خطأ: ${r.json.error}` : "تعذر تحميل الفيديوهات";
    return;
  }

  const items = r.json.items || [];
  if (!items.length) {
    msg.textContent = "لا يوجد فيديوهات داخل هذا الملف بعد.";
    return;
  }

  msg.textContent = `عدد الفيديوهات: ${items.length}`;

  for (const v of items) {
    const li = document.createElement("li");

    li.innerHTML = `
      <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
        <div>
          <b>${escapeHtml(v.title)}</b>
          <span class="muted"> — مدة: ${formatSecs(v.duration_seconds)} — ID: ${v.id}</span>
        </div>
        <div style="display:flex;gap:8px">
          <button class="miniBtn" type="button" data-edit-video="${v.id}" data-edit-title="${escapeHtml(v.title)}">Edit</button>
          <button class="miniBtn" type="button" data-del-video="${v.id}">Delete</button>
        </div>
      </div>
    `;

    list.appendChild(li);
  }

  // Edit
  list.querySelectorAll("[data-edit-video]").forEach(btn => {
    btn.addEventListener("click", () => {
      const vid = parseInt(btn.getAttribute("data-edit-video") || "0", 10);
      const title = btn.getAttribute("data-edit-title") || "";

      if (!vid) return;

      const hv = document.getElementById("currentVideoId");
      if (hv) hv.value = String(vid);

      const titleEl = document.getElementById("videoTitle");
      if (titleEl) titleEl.value = title;

      window.__quizSaved = false;
      window.__codeSaved = false;

      setMsg("uploadMsg", `تم اختيار فيديو للتعديل ✅ (Video ID: ${vid})`, true);
      document.getElementById("quizList")?.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });

  // Delete
  list.querySelectorAll("[data-del-video]").forEach(btn => {
    btn.addEventListener("click", async () => {
      const vid = parseInt(btn.getAttribute("data-del-video") || "0", 10);
      if (!vid) return;

      if (!confirm("هل أنت متأكد من حذف هذا الفيديو؟")) return;

      const rr = await apiPostJson("partner_video_delete.php", {
        video_id: vid
      });

      if (!rr.ok || rr.json.ok === false) {
        alert(rr.json?.error || "فشل حذف الفيديو");
        return;
      }

      const currentVideoIdEl = document.getElementById("currentVideoId");
      if (currentVideoIdEl && parseInt(currentVideoIdEl.value || "0", 10) === vid) {
        currentVideoIdEl.value = "";
        const titleEl = document.getElementById("videoTitle");
        if (titleEl) titleEl.value = "";
      }

      alert("تم حذف الفيديو ✅");
      await loadPlaylistVideos(playlistId);
    });
  });
}

/* ✅ NEW: حفظ تعديل عنوان الفيديو */
async function saveVideoTitle() {
  setMsg("videoTitleMsg", "");

  const videoId = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);
  const title = document.getElementById("videoTitle")?.value?.trim() || "";

  if (!videoId) {
    setMsg("videoTitleMsg", "اختَر فيديو من زر Edit أولاً");
    return;
  }

  if (!title) {
    setMsg("videoTitleMsg", "اكتب عنوان الفيديو أولاً");
    return;
  }

  setMsg("videoTitleMsg", "جاري حفظ التعديل...");

  const r = await apiPostJson("partner_video_update.php", {
    video_id: videoId,
    title: title
  });

  if (!r.ok || r.json.ok === false) {
    setMsg("videoTitleMsg", r.json?.error ? `خطأ: ${r.json.error}` : "فشل حفظ التعديل");
    return;
  }

  setMsg("videoTitleMsg", "تم تحديث عنوان الفيديو ✅", true);

  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  if (playlistId) {
    await loadPlaylistVideos(playlistId);
  }
}

async function savePlaylistCover() {
  setMsg("plCoverMsg", "");

  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  if (!playlistId) { setMsg("plCoverMsg", "اختر ملف/Playlist أولاً"); return; }

  const f = document.getElementById("playlistCover")?.files?.[0];
  if (!f) { setMsg("plCoverMsg", "اختار صورة أولاً"); return; }

  const fd = new FormData();
  fd.append("playlist_id", String(playlistId));
  fd.append("cover_image", f);

  setMsg("plCoverMsg", "جاري رفع الصورة...");
  const r = await apiPostForm("partner_playlist_cover_upload.php", fd);

  if (!r.ok || r.json.ok === false) {
    setMsg("plCoverMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل رفع الصورة");
    return;
  }

  setMsg("plCoverMsg", "تم حفظ صورة البلاي ليست ✅", true);
  await loadPlaylists(String(playlistId));
}

// استخراج مدة الفيديو قبل الرفع
async function getVideoDurationSeconds(file) {
  return new Promise((resolve) => {
    const v = document.createElement("video");
    v.preload = "metadata";
    v.onloadedmetadata = () => {
      const d = Math.floor(v.duration || 0);
      URL.revokeObjectURL(v.src);
      resolve(Number.isFinite(d) ? d : 0);
    };
    v.onerror = () => resolve(0);
    v.src = URL.createObjectURL(file);
  });
}

/* ---------------- Phase 1 ---------------- */
async function uploadVideo() {
  setMsg("uploadMsg", "");

  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  if (!playlistId) { setMsg("uploadMsg", "اختر ملف/Playlist أولاً"); return; }

  const title = document.getElementById("videoTitle")?.value?.trim() || "";
  if (!title) { setMsg("uploadMsg", "لازم تكتب عنوان للفيديو أولاً"); return; }

  const fileEl = document.getElementById("videoFile");
  const file = fileEl?.files?.[0];
  if (!file) { setMsg("uploadMsg", "اختار فيديو أولاً"); return; }

  const duration_seconds = await getVideoDurationSeconds(file);

  const fd = new FormData();
  fd.append("playlist_id", String(playlistId));
  fd.append("title", title);
  fd.append("duration_seconds", String(duration_seconds));
  fd.append("video", file);

  // ✅ cover image (optional)
  const coverEl = document.getElementById("coverImage");
  const coverFile = coverEl?.files?.[0];
  if (coverFile) fd.append("cover_image", coverFile);

  setMsg("uploadMsg", "جاري رفع الفيديو...");
  const r = await apiPostForm("partner_video_upload.php", fd);

  // ✅ التوافق مع أكثر من شكل للـ API
  const uploadedId =
    r.json.video_id ??
    r.json.videoId ??
    (r.json.video ? (r.json.video.id ?? r.json.video.video_id) : null);

  if (!r.ok || r.json.ok === false || !uploadedId) {
    setMsg("uploadMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل رفع الفيديو");
    if (r.json._raw) console.error("RAW:", r.json._raw);
    return;
  }

  document.getElementById("currentVideoId").value = uploadedId;
  window.__quizSaved = false;
  window.__codeSaved = false;

  setMsg("uploadMsg", `تم رفع الفيديو ✅ (Video ID: ${uploadedId}) مدة: ${formatSecs(duration_seconds)}`, true);

  // ✅ حدّث قائمة فيديوهات الملف بعد الرفع
  loadPlaylistVideos(playlistId);
}

function makeQuizItem() {
  return { question: "", options: ["", "", "", ""], correct: "A", explanation: "" };
}

function renderQuiz() {
  const wrap = document.getElementById("quizList");
  if (!wrap) return;
  const items = window.__quiz || [];
  wrap.innerHTML = "";

  items.forEach((q, idx) => {
    const box = document.createElement("div");
    box.className = "quizItem";
    box.innerHTML = `
      <div class="row">
        <div style="flex:2;min-width:240px">
          <label class="muted">السؤال #${idx + 1}</label>
          <input class="input" value="${escapeHtml(q.question)}" data-k="question" data-i="${idx}" placeholder="اكتب السؤال هنا"/>
        </div>
        <div style="flex:1;min-width:160px">
          <label class="muted">الإجابة الصحيحة</label>
          <select class="input" data-k="correct" data-i="${idx}">
            ${["A","B","C","D"].map(v => `<option value="${v}" ${q.correct===v?"selected":""}>${v}</option>`).join("")}
          </select>
        </div>
      </div>

      <div style="margin-top:10px" class="row">
        ${q.options.map((opt, j) => `
          <div>
            <label class="muted">Option ${["A","B","C","D"][j]}</label>
            <input class="input" value="${escapeHtml(opt)}" data-k="opt${j}" data-i="${idx}" placeholder="اختيار ${["A","B","C","D"][j]}"/>
          </div>
        `).join("")}
      </div>

      <div style="margin-top:10px">
        <label class="muted">شرح (اختياري)</label>
        <input class="input" value="${escapeHtml(q.explanation)}" data-k="explanation" data-i="${idx}" placeholder="شرح قصير للإجابة"/>
      </div>

      <div class="btnRow" style="margin-top:10px">
        <button class="btn ghost" type="button" data-del="${idx}">حذف السؤال</button>
      </div>
    `;
    wrap.appendChild(box);
  });

  wrap.querySelectorAll("[data-k]").forEach(el => {
    el.addEventListener("input", () => {
      const i = parseInt(el.dataset.i, 10);
      const k = el.dataset.k;
      const items = window.__quiz || [];
      if (!items[i]) return;

      if (k === "question") items[i].question = el.value;
      else if (k === "correct") items[i].correct = el.value;
      else if (k === "explanation") items[i].explanation = el.value;
      else if (k.startsWith("opt")) {
        const j = parseInt(k.replace("opt", ""), 10);
        items[i].options[j] = el.value;
      }
      window.__quiz = items;
      window.__quizSaved = false;
    });
  });

  wrap.querySelectorAll("[data-del]").forEach(btn => {
    btn.addEventListener("click", () => {
      const i = parseInt(btn.dataset.del, 10);
      window.__quiz.splice(i, 1);
      window.__quizSaved = false;
      renderQuiz();
    });
  });
}

function addQuestion() {
  window.__quiz = window.__quiz || [];
  window.__quiz.push(makeQuizItem());
  window.__quizSaved = false;
  renderQuiz();
}

/* ✅ save quiz (supports delete when empty) */
async function saveQuiz() {
  setMsg("quizMsg", "");
  const videoId = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);
  if (!videoId) { setMsg("quizMsg", "لازم ترفع الفيديو أولاً (Step 2)"); return; }

  const quiz = window.__quiz || [];

  // ✅ delete on server if empty
  if (!quiz.length) {
    setMsg("quizMsg", "جاري حذف الأسئلة...");
    const rr = await apiPostJson("partner_video_quiz_save.php", { video_id: videoId, quiz: [] });
    if (!rr.ok) { setMsg("quizMsg", rr.json.error ? `خطأ: ${rr.json.error}` : "فشل حذف الأسئلة"); return; }
    window.__quizSaved = true;
    setMsg("quizMsg", "تم حذف الأسئلة ✅", true);
    return;
  }

  for (let i = 0; i < quiz.length; i++) {
    const q = quiz[i];
    if (!q.question.trim()) { setMsg("quizMsg", `السؤال #${i + 1} فاضي`); return; }
    if (!q.options || q.options.length !== 4) { setMsg("quizMsg", `خيارات السؤال #${i + 1} غير كاملة`); return; }
    if (q.options.some(x => !String(x || "").trim())) { setMsg("quizMsg", `في خيار فاضي بالسؤال #${i + 1}`); return; }
    if (!["A", "B", "C", "D"].includes(String(q.correct || "").toUpperCase())) { setMsg("quizMsg", `الإجابة الصحيحة بالسؤال #${i + 1} غير صحيحة`); return; }
  }

  setMsg("quizMsg", "جاري حفظ الأسئلة...");
  const r = await apiPostJson("partner_video_quiz_save.php", { video_id: videoId, quiz });
  if (!r.ok || r.json.ok === false) { setMsg("quizMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل حفظ الأسئلة"); return; }

  window.__quizSaved = true;
  setMsg("quizMsg", "تم حفظ الأسئلة ✅", true);
}

/* ---------------- Code Problem (Phase 1) ---------------- */
async function saveCodeProblem() {
  setMsg("codeMsg", "");
  const videoId = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);
  if (!videoId) { setMsg("codeMsg", "لازم ترفع الفيديو أولاً (Step 2)"); return; }

  const title = document.getElementById("codeTitle")?.value?.trim() || "";
  const language = document.getElementById("codeLang")?.value || "python";
  const max_coin = parseInt(document.getElementById("codeMaxCoin")?.value || "50", 10);
  const prompt = document.getElementById("codePrompt")?.value || "";
  const starter_code = document.getElementById("codeStarter")?.value || "";
  const solution_code = document.getElementById("codeSolution")?.value || "";

  if (!title || !prompt.trim() || !solution_code.trim()) {
    setMsg("codeMsg", "اكتب عنوان + نص المسألة + حل الأستاذ (مطلوب)");
    return;
  }

  setMsg("codeMsg", "جاري حفظ مسألة الكود...");
  const r = await apiPostJson("partner_video_code_problem_save.php", {
    video_id: videoId,
    title,
    prompt,
    language,
    starter_code,
    solution_code,
    max_coin: Number.isFinite(max_coin) ? max_coin : 50
  });

  if (!r.ok || r.json.ok === false) {
    setMsg("codeMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل حفظ مسألة الكود");
    return;
  }

  window.__codeSaved = true;
  setMsg("codeMsg", "تم حفظ مسألة الكود ✅", true);
}

/* ✅ delete code problem */
async function deleteCodeProblem() {
  setMsg("codeMsg", "");
  const videoId = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);
  if (!videoId) { setMsg("codeMsg", "لازم ترفع الفيديو أولاً (Step 2)"); return; }
  if (!confirm("متأكد بدك تحذف مسألة الكود لهذا الفيديو؟")) return;

  setMsg("codeMsg", "جاري حذف مسألة الكود...");
  const r = await apiPostJson("partner_video_code_problem_save.php", { video_id: videoId, delete: 1 });
  if (!r.ok || r.json.ok === false) {
    setMsg("codeMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل حذف مسألة الكود");
    return;
  }

  ["codeTitle", "codePrompt", "codeStarter", "codeSolution"].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });

  window.__codeSaved = false;
  setMsg("codeMsg", "تم حذف مسألة الكود ✅", true);
}

/* ✅✅ Phase 1 submit (مرن: مش إجباري حفظ كلشي) */
async function submitPhase1() {
  setMsg("submitMsg", "");

  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  const title = document.getElementById("videoTitle")?.value?.trim() || "";
  const videoId = parseInt(document.getElementById("currentVideoId")?.value || "0", 10);

  if (!playlistId) { setMsg("submitMsg", "اختر ملف/Playlist"); return; }
  if (!title) { setMsg("submitMsg", "Step 1: اكتب عنوان الفيديو"); return; }
  if (!videoId) { setMsg("submitMsg", "Step 2: لازم ترفع الفيديو أولاً"); return; }

  // ✅ مسألة الكود: إذا بدأ يكتب لازم يكمل، وإذا كاملة ولم تُحفظ نحفظها تلقائيًا
  const codeTitle = (document.getElementById("codeTitle")?.value || "").trim();
  const codePrompt = (document.getElementById("codePrompt")?.value || "").trim();
  const codeSolution = (document.getElementById("codeSolution")?.value || "").trim();

  const startedCode = !!(codeTitle || codePrompt || codeSolution);

  if (startedCode && (!codeTitle || !codePrompt || !codeSolution)) {
    setMsg("submitMsg", "مسألة الكود ناقصة: إذا بدك تسلم بدونها امسحها كلها، أو كمّل (عنوان + نص + حل الأستاذ)");
    return;
  }

  if (startedCode && !window.__codeSaved) {
    await saveCodeProblem();
    if (!window.__codeSaved) { setMsg("submitMsg", "تعذر حفظ مسألة الكود"); return; }
  }

  // ✅ الأسئلة: اختيارية. إذا موجودة لكن غير محفوظة، بننبه فقط (ما بنمنع التسليم)
  if ((window.__quiz || []).length > 0 && !window.__quizSaved) {
    setMsg("submitMsg", "✅ تم تسليم Phase 1 (ملاحظة: الأسئلة غير محفوظة)", true);
    return;
  }

  setMsg("submitMsg", "✅ تم تسليم Phase 1 بنجاح", true);
}

/* ✅ publish playlist */
async function publishPlaylist() {
  setMsg("plMsg", "");
  const playlistId = parseInt(document.getElementById("playlistSelect")?.value || "0", 10);
  if (!playlistId) { setMsg("plMsg", "اختر ملف/Playlist أولاً"); return; }
  if (!confirm("نشر البلاي ليست للطلاب؟ سيتم التحقق من الأسئلة وحساب coin_pool.")) return;

  setMsg("plMsg", "جاري النشر...");
  const r = await apiPostJson("partner_playlist_publish.php", { playlist_id: playlistId });

  if (!r.ok || r.json.ok === false) {
    if (r.json.error === "VIDEO_MISSING_QUESTIONS" && Array.isArray(r.json.video_ids)) {
      setMsg("plMsg", `فيديوهات ناقصة أسئلة: ${r.json.video_ids.join(", ")}`);
      return;
    }
    if (r.json.error === "NOT_ENOUGH_VIDEOS") {
      setMsg("plMsg", `عدد الفيديوهات (${r.json.videos_count}) أقل من المتوقع (${r.json.expected_lectures})`);
      return;
    }
    setMsg("plMsg", r.json.error ? `خطأ: ${r.json.error}` : "فشل النشر");
    return;
  }

  setMsg("plMsg", `تم النشر ✅ coin_pool: ${r.json.coin_pool}`, true);
  await loadPlaylists(String(playlistId));
  loadPlaylistVideos(playlistId);
}

/* ---------------- Init ---------------- */
document.addEventListener("DOMContentLoaded", async () => {
  // Phase 1
  if (document.getElementById("btnUploadVideo")) {
    window.__quiz = [];
    window.__quizSaved = false;
    window.__codeSaved = false;

    await loadPlaylists();
    document.getElementById("btnNewPlaylist")?.addEventListener("click", createPlaylist);
    document.getElementById("btnSaveCodeProblem")?.addEventListener("click", saveCodeProblem);

    // ✅ ربط زر حفظ تعديل العنوان
    document.getElementById("btnSaveVideoTitle")?.addEventListener("click", saveVideoTitle);

    // ✅ اربط change للقائمة حتى يعرض فيديوهات الملف
    const sel = document.getElementById("playlistSelect");
    if (sel) {
      sel.addEventListener("change", () => loadPlaylistVideos(parseInt(sel.value || "0", 10)));
      loadPlaylistVideos(parseInt(sel.value || "0", 10));
    }

    document.getElementById("btnSavePlaylistCover")?.addEventListener("click", savePlaylistCover);

    addQuestion();

    document.getElementById("btnUploadVideo")?.addEventListener("click", uploadVideo);
    document.getElementById("btnAddQuestion")?.addEventListener("click", addQuestion);
    document.getElementById("btnSaveQuiz")?.addEventListener("click", saveQuiz);
    document.getElementById("btnSubmitPhase1")?.addEventListener("click", submitPhase1);
    document.getElementById("btnPublishPlaylist")?.addEventListener("click", publishPlaylist);
    document.getElementById("btnDeleteCodeProblem")?.addEventListener("click", deleteCodeProblem);
  }

  // Dashboard
  if (document.getElementById("companyName")) {
    loadMeIntoName();
  }
});
