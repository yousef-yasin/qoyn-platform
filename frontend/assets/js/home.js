/* utbn-web/assets/js/home.js
   Home dashboard logic + AI plan summary (term credits + OK analyze)
*/
(async function(){
  // apiGet/apiPost/escapeHtml are defined in app.js
  function el(id){ return document.getElementById(id); }

  async function loadHome() {
    try {
      const me = await apiGet("me.php");
      el("welcome").textContent = `أهلًا ${me.full_name} (${me.email})`;
    } catch (e) {
      location.replace("login.html");
      return;
    }

    const coins = await apiGet("coins.php");
    el("coinsTotal").textContent = coins.coins_total;
    el("phaseNow").textContent = coins.phase;
    el("nextTarget").textContent = coins.next_target;

    const sub = await apiGet("subscription_status.php");
    el("subBadge").textContent = sub.active ? "اشتراك فعّال ✅" : "بدون اشتراك";
    el("subBadge").className = "badge " + (sub.active ? "ok" : "warn");

    const tree = await apiGet("plan_tree.php");
    el("majorName").textContent = "تخصصك: " + (tree.major?.name || "-");

    const ul = el("coursesList");
    ul.innerHTML = "";
    (tree.courses || []).forEach(c => {
      const li = document.createElement("li");
      li.className = "item";
      li.innerHTML = `
        <div style="flex:1">
          <div><b>${escapeHtml(c.code)}</b> - ${escapeHtml(c.name)}</div>
          <div class="muted">Trainings: ${c.trainings_done}/${c.trainings_total}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <div class="progress"><div style="width:${c.progress}%"></div></div>
          <a class="btn ghost" href="course.html?course_id=${c.id}">فتح</a>
        </div>
      `;
      ul.appendChild(li);
    });
  }

  async function loadSettings(){
    try {
      const j = await apiGet("user_settings.php");
      if(j.ok){
        el("termCredits").value = String(j.term_credits || 15);
      }
    } catch(e){}
  }

  async function saveSettings(){
    const term_credits = parseInt(el("termCredits").value, 10) || 15;
    const j = await apiPost("user_settings.php", { term_credits });
    if(j.ok){
      el("aiStatus").textContent = "تم حفظ ساعات الفصل ✅";
    }
    return term_credits;
  }

  function renderList(container, items){
    const ul = document.createElement("ul");
    ul.className = "list";
    ul.style.marginTop = "8px";
    (items || []).forEach(it=>{
      const li = document.createElement("li");
      li.className = "item";
      li.innerHTML = `
        <div style="flex:1">
          <div><b>${escapeHtml(it.code || "")}</b> - ${escapeHtml(it.name || "")}</div>
          <div class="muted">ساعات: ${it.credits ?? "-"} ${it.grade ? `| العلامة: ${escapeHtml(it.grade)}` : ""}</div>
        </div>
      `;
      ul.appendChild(li);
    });
    container.appendChild(ul);
  }

  async function loadAiSummary(){
    const empty = el("aiEmpty");
    const content = el("aiContent");

    let j;
    try {
      j = await apiGet("ai/plan_summary.php");
    } catch(e){
      empty.style.display = "block";
      content.style.display = "none";
      return;
    }

    if(!j.ok){
      empty.style.display = "block";
      content.style.display = "none";
      return;
    }

    empty.style.display = "none";
    content.style.display = "block";

    const d = j.data || {};
    el("aiGpa").textContent = (d.meta && d.meta.gpa) ? d.meta.gpa : "-";
    el("aiRemaining").textContent = (d.meta && d.meta.remaining_hours) ? d.meta.remaining_hours : "-";
    el("aiTotal").textContent = (d.meta && d.meta.total_hours) ? d.meta.total_hours : "-";
    el("aiGeneratedAt").textContent = d.generated_at ? new Date(d.generated_at).toLocaleString() : "-";
    if(d.term_credits) el("termCredits").value = String(d.term_credits);

    const comp = el("aiCompleted");
    comp.innerHTML = "";
    const completed = d.completed_by_term || {};
    Object.keys(completed).sort().forEach(term=>{
      const box = document.createElement("div");
      box.className = "item";
      box.style.marginTop = "10px";
      const h = document.createElement("div");
      h.innerHTML = `<b>فصل ${escapeHtml(term)}</b>`;
      box.appendChild(h);
      renderList(box, completed[term]);
      comp.appendChild(box);
    });

    const reg = el("aiRegistered");
    reg.innerHTML = "";
    const registered = d.registered_by_term || {};
    Object.keys(registered).sort().forEach(term=>{
      const box = document.createElement("div");
      box.className = "item";
      box.style.marginTop = "10px";
      const h = document.createElement("div");
      h.innerHTML = `<b>مسجل - فصل ${escapeHtml(term)}</b>`;
      box.appendChild(h);
      renderList(box, registered[term]);
      reg.appendChild(box);
    });

    const sug = el("aiSuggested");
    sug.innerHTML = "";
    const suggested = d.suggested_terms || {};
    Object.keys(suggested).forEach(k=>{
      const box = document.createElement("div");
      box.className = "item";
      box.style.marginTop = "10px";
      const h = document.createElement("div");
      h.innerHTML = `<b>${escapeHtml(String(k).replaceAll("_"," "))}</b>`;
      box.appendChild(h);
      renderList(box, suggested[k]);
      sug.appendChild(box);
    });
  }

  async function analyzePlan(){
    const status = el("aiStatus");
    status.textContent = "جاري التحليل...";
    try {
      // run analysis
      const j = await apiGet("ai/plan_analyze.php");
      if(j.ok){
        status.textContent = "تم التحليل بنجاح ✅";
        await loadAiSummary();
      } else {
        status.textContent = j.msg || "فشل التحليل";
      }
    } catch(e){
      status.textContent = (e && e.msg) ? e.msg : "فشل التحليل";
      console.log(e);
    }
  }

  // UI events
  el("logoutBtn")?.addEventListener("click", async () => {
    try { await apiPost("logout.php"); } catch(e){}
    location.replace("login.html");
  });

  el("generateCertBtn")?.addEventListener("click", async () => {
    const msg = el("certMsg");
    msg.textContent = "جارٍ إنشاء الشهادة...";
    try {
      await apiPost("certificate_generate.php");
      msg.textContent = "تم ✅ افتح صفحة الشهادات";
      msg.style.color = "#86efac";
    } catch (e) {
      msg.textContent = "لا يمكن إنشاء شهادة الآن (بدك 10000 coins على الأقل)";
      msg.style.color = "#fca5a5";
    }
  });

  el("saveCreditsBtn")?.addEventListener("click", async ()=>{
    el("aiStatus").textContent = "";
    await saveSettings();
  });

  el("okAnalyzeBtn")?.addEventListener("click", async ()=>{
    // save credits first then analyze
    el("aiStatus").textContent = "";
    await saveSettings();
    await analyzePlan();
  });

  // init
  await loadHome();
  await loadSettings();
  await loadAiSummary();
})();
const API_BASE = "/utbn-backend/api";

// مجموعة تخصصات IT اللي بدك تشملها (زِد/عدّل براحتك)
const IT_MAJORS = [
  "AI", "Artificial Intelligence",
  "Computer Science", "CS",
  "Cyber", "Cyber Security", "Cybersecurity",
  "Information Technology", "IT",
  "Software Engineering", "SE",
  "Data Science"
];

function normalizeMajor(s){
  return String(s||"").trim().toLowerCase();
}

function isITMajor(major){
  const m = normalizeMajor(major);
  return IT_MAJORS.some(x => normalizeMajor(x) === m);
}

function esc(s){ return String(s??"").replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }

function renderPlaylistCard(p){
  const pid = p.playlist_id ?? p.id ?? "";
  const title = p.title ?? p.name ?? "Playlist";
  const created = p.created_at ?? "";
  return `
    <div class="card">
      <div style="font-weight:700;margin-bottom:6px">${esc(title)}</div>
      <div class="muted">${esc(created)}</div>
      <div style="margin-top:10px">
<a class="btn" href="student_playlist.php?playlist_id=${encodeURIComponent(pid)}">فتح البلاي ليست</a>      </div>
    </div>
  `;
}

async function fetchJSON(url){
  const r = await fetch(url, { credentials:"include" });
  return await r.json();
}

async function loadITPartnerPlaylists(){
  const box = document.getElementById("itPartnerPlaylists");
  const empty = document.getElementById("itPartnerEmpty");
  if(!box) return;

  box.innerHTML = "";
  empty.style.display = "none";

  // 1) نجيب تخصص الطالب الحالي من API جاهز عندك
  // عندك get_major.php موجود، خلينا نستخدمه:
  let myMajor = "";
  try{
    const mj = await fetchJSON(API_BASE + "/get_major.php");
    myMajor = mj.major || mj.data?.major || "";
  }catch(e){ /* ignore */ }

  // 2) إذا الطالب مش IT لا نعرض شي (أو خليها مخفية)
  if(!isITMajor(myMajor)){
    empty.style.display = "none";
    return;
  }

  // 3) نجمع بلاي ليستات كل تخصصات IT
  const map = new Map(); // لمنع التكرار

  for(const major of IT_MAJORS){
    try{
      const j = await fetchJSON(API_BASE + "/student_partner_major_playlists.php?major=" + encodeURIComponent(major));
      const items = j.items || j.playlists || j.data || [];
      if(Array.isArray(items)){
        for(const p of items){
          const id = String(p.playlist_id ?? p.id ?? "");
          if(id) map.set(id, p);
        }
      }
    }catch(e){ /* ignore */ }
  }

  const arr = Array.from(map.values());
  if(arr.length === 0){
    empty.style.display = "block";
    return;
  }

  box.innerHTML = arr.map(renderPlaylistCard).join("");
}

// شغّلها عند تحميل الهوم
document.addEventListener("DOMContentLoaded", () => {
  loadITPartnerPlaylists();
});