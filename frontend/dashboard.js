const statusEl = document.getElementById("status");
const btn = document.getElementById("btnAnalyze");
const metaEl = document.getElementById("meta");
const suggestedEl = document.getElementById("suggested");

function setStatus(text) {
  statusEl.textContent = text;
}

function escapeHtml(s) {
  return String(s ?? "")
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

function renderResult(result) {
  // META
  const m = result.meta || {};
  metaEl.style.display = "block";
  metaEl.innerHTML = `
    <h3 style="margin:0 0 10px;">ملخص الطالب</h3>
    <div class="row">
      <span class="badge">الاسم: ${escapeHtml(m.student_name || "-")}</span>
      <span class="badge">الرقم: ${escapeHtml(m.student_id || "-")}</span>
      <span class="badge">المعدل: ${escapeHtml(m.gpa ?? "-")}</span>
      <span class="badge">ساعات الخطة: ${escapeHtml(m.total_hours ?? "-")}</span>
      <span class="badge">المتبقي: ${escapeHtml(m.remaining_hours ?? "-")}</span>
    </div>
  `;

  // SUGGESTED TERMS
  const terms = result.suggested_terms || {};
  const termKeys = Object.keys(terms);

  suggestedEl.style.display = "block";
  let html = `<h3 style="margin:0 0 10px;">المواد المقترحة (حسب ساعاتك)</h3>`;

  if (termKeys.length === 0) {
    html += `<div class="muted">لا يوجد اقتراحات حالياً (ممكن لأن كل المواد المتبقية لها متطلبات غير مكتملة أو ما في مواد).</div>`;
    suggestedEl.innerHTML = html;
    return;
  }

  termKeys.forEach((k) => {
    const t = terms[k];
    html += `
      <div style="margin-top:12px; padding-top:10px; border-top:1px solid #1f2937;">
        <div class="row">
          <span class="badge">${escapeHtml(k)}</span>
          <span class="badge">مجموع الساعات: ${escapeHtml(t.hours)}</span>
        </div>
        <ul>
          ${(t.courses || []).map(c => `<li>${escapeHtml(c.name)} <span class="muted">(${escapeHtml(c.credits)} ساعات)</span></li>`).join("")}
        </ul>
      </div>
    `;
  });

  suggestedEl.innerHTML = html;
}

async function analyzePlan() {
  const hours = Number(document.getElementById("hours").value || 15);
  const pages = Number(document.getElementById("pages").value || 3);

  setStatus("جاري التحليل...");
  btn.disabled = true;

  try {
    const url = `/utbn-backend/api/ai/plan_analyze_gemini.php?hours=${encodeURIComponent(hours)}&pages=${encodeURIComponent(pages)}`;

    const res = await fetch(url, {
      method: "GET",
      credentials: "include" // مهم جداً عشان الـ session
    });

    const data = await res.json();

    if (!data.ok) {
      if (data.error === "NOT_LOGGED_IN") {
        alert("لازم تسجّل دخول أولاً.");
      } else if (data.error === "NO_PLAN_UPLOADED") {
        alert("ارفع الخطة أولاً (PDF/صورة).");
      } else {
        alert("صار خطأ: " + (data.error || "UNKNOWN"));
        console.log(data);
      }
      setStatus("خطأ");
      return;
    }

    renderResult(data.result);
    setStatus("تم ✅");
  } catch (e) {
    console.error(e);
    setStatus("فشل الاتصال");
    alert("مشكلة اتصال أو خطأ بالسيرفر.");
  } finally {
    btn.disabled = false;
  }
}

btn.addEventListener("click", analyzePlan);
