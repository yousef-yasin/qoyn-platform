/* utbn-web/assets/js/ai_plan.js
   Adds AI plan analysis UI on the home page without changing existing logic.
*/
(function(){
  function el(id){ return document.getElementById(id); }
  function safeSetText(id, txt){ const n=el(id); if(n) n.textContent = txt; }
  function safeShow(id, show){ const n=el(id); if(!n) return; n.style.display = show ? "" : "none"; }

  async function loadCredits(){
    try{
      const s = await apiGet("user_settings.php");
      const sel = el("termCredits");
      if(sel) sel.value = String(s.term_credits || 15);
    }catch(e){}
  }

  async function saveCredits(){
    const sel = el("termCredits");
    if(!sel) return;
    const term_credits = parseInt(sel.value || "15", 10);
    safeSetText("aiStatus","... حفظ");
    try{
      await apiPost("user_settings.php", { term_credits });
      safeSetText("aiStatus","تم الحفظ ✅");
    }catch(e){
      safeSetText("aiStatus","فشل الحفظ");
      console.error(e);
    }
  }

  function renderByTerm(obj){
    if(!obj || typeof obj !== "object") return "<div class='muted'>-</div>";
    const terms = Object.keys(obj);
    if(!terms.length) return "<div class='muted'>-</div>";
    return terms.map(t=>{
      const items = obj[t] || [];
      const li = items.map(it => `<li>${escapeHtml(it.code)} - ${escapeHtml(it.name)} (${it.credits})</li>`).join("");
      return `<div class="card" style="padding:10px;margin-top:8px">
        <div><b>${escapeHtml(t)}</b></div>
        <ul style="margin-top:6px">${li || "<li class='muted'>-</li>"}</ul>
      </div>`;
    }).join("");
  }

  function renderSuggested(obj){
    if(!obj || typeof obj !== "object") return "<div class='muted'>-</div>";
    const terms = Object.keys(obj);
    if(!terms.length) return "<div class='muted'>-</div>";
    return terms.map(t=>{
      const items = obj[t] || [];
      const li = items.map(it => `<li>${escapeHtml(it.code)} - ${escapeHtml(it.name)} (${it.credits})</li>`).join("");
      return `<div class="card" style="padding:10px;margin-top:8px">
        <div><b>${escapeHtml(t.replace("term_","الفصل المقترح "))}</b></div>
        <ul style="margin-top:6px">${li || "<li class='muted'>-</li>"}</ul>
      </div>`;
    }).join("");
  }

  async function loadSummary(){
    try{
      const res = await apiGet("ai/plan_summary.php");
      if(!res || !res.data){
        safeShow("aiEmpty", true);
        safeShow("aiContent", false);
        return;
      }
      const data = res.data;
      safeShow("aiEmpty", false);
      safeShow("aiContent", true);

      const meta = data.meta || {};
      safeSetText("aiGpa", meta.gpa || "-");
      safeSetText("aiTotal", meta.total_hours || "-");
      safeSetText("aiRemaining", meta.remaining_hours || "-");
      safeSetText("aiRecommended", data.recommended_term_credits || data.term_credits || "-");
      safeSetText("aiGeneratedAt", data.generated_at || "-");

      const completed = el("aiCompleted");
      if(completed) completed.innerHTML = renderByTerm(data.completed_by_term);

      const registered = el("aiRegistered");
      if(registered) registered.innerHTML = renderByTerm(data.registered_by_term);

      const sug = el("aiSuggested");
      if(sug) sug.innerHTML = renderSuggested(data.suggested_terms);

    }catch(e){
      safeShow("aiEmpty", true);
      safeShow("aiContent", false);
    }
  }

  async function runAnalyze(){
    safeSetText("aiStatus","جاري التحليل...");
    try{
      await saveCredits(); // ensure saved
      const res = await apiPost("ai/plan_analyze.php", {});
      if(res && res.ok){
        safeSetText("aiStatus","تم التحليل ✅");
        await loadSummary();
      }else{
        safeSetText("aiStatus","فشل التحليل");
        console.error(res);
      }
    }catch(e){
      safeSetText("aiStatus","فشل التحليل");
      console.error(e);
      alert("فشل التحليل. تأكد أنك رفعت الخطة (PDF/صورة) من صفحة الخطة الدراسية، وأن OPENAI_API_KEY مضبوط.");
    }
  }

  function bind(){
    const saveBtn = el("saveCreditsBtn");
    if(saveBtn) saveBtn.addEventListener("click", saveCredits);

    const okBtn = el("okAnalyzeBtn");
    if(okBtn) okBtn.addEventListener("click", runAnalyze);

    loadCredits();
    loadSummary();
  }

  if(document.readyState === "loading") document.addEventListener("DOMContentLoaded", bind);
  else bind();
})();
