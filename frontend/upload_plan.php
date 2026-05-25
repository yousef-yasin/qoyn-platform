<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>إرفاق الخطة + التحليل</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    body{display:block}
    .container{width:min(1100px,94vw)}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media (max-width: 980px){.grid{grid-template-columns:1fr}}
    .drop{border:1px dashed rgba(255,255,255,.22);border-radius:18px;padding:18px;background:rgba(0,0,0,.16);transition:.15s ease}
    .drop.drag{transform:scale(1.01);border-color:rgba(34,197,94,.7);background:rgba(34,197,94,.08)}
    .preview{width:100%;min-height:520px;border:1px solid rgba(255,255,255,.12);border-radius:16px;overflow:hidden;background:rgba(0,0,0,.2)}
    .preview iframe{width:100%;height:100%;border:0}
    .preview img{width:100%;height:100%;object-fit:contain;display:block}
    .toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .small{font-size:12px}
    .hr{height:1px;background:rgba(255,255,255,.12);margin:12px 0}

    .filesList{margin-top:10px;display:grid;gap:8px}
    .fileRow{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px;border:1px solid rgba(255,255,255,.10);border-radius:14px;background:rgba(0,0,0,.12)}
    .fileRow b{font-weight:600}
    .fileRow .muted{opacity:.85}
    .thumbs{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;padding:12px}
    .thumb{border:1px solid rgba(255,255,255,.12);border-radius:14px;overflow:hidden;background:rgba(0,0,0,.18);aspect-ratio:3/4;display:grid;place-items:center}
    .thumb img{width:100%;height:100%;object-fit:cover}
    .thumb .muted{padding:10px;text-align:center}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div>
        <h1 style="margin:0 0 6px">إرفاق الخطة (صور) + التحليل بالذكاء</h1>
        <div class="muted" id="who">...</div>
      </div>
      <div class="toolbar">
        <a class="btn ghost" href="index.php">⬅ رجوع للرئيسية</a>
        <a class="btn gray" href="logout.php">Logout</a>
      </div>
    </div>

    <div class="grid" style="margin-top:14px">
      <div class="card">
        <h2 style="margin:0 0 10px">1) ارفع الخطة (صور)</h2>
        <div class="muted">
          لأسرع تحليل: ارفع <b>3 صور</b> (كل صفحة صورة). <br/>
          *PDF تم تعطيله هنا عشان السرعة (بدون Python).
        </div>

        <div id="drop" class="drop" style="margin-top:12px">
          <form id="uploadForm" class="list" style="gap:10px">
            <input class="input" name="title" placeholder="عنوان (اختياري) مثل: خطة 2026" />

            <!-- ✅ صور فقط + اختيار متعدد -->
            <input id="file" name="file" type="file" accept="image/*" multiple style="display:none" />
            <button type="button" class="btn ghost" id="pickBtn">اختيار صور (حتى 3)</button>

            <div class="item" style="justify-content:flex-start;gap:12px">
              <span class="badge" id="fileBadge">ما في صور مختارة</span>
              <span class="muted small" id="fileInfo"></span>
            </div>

            <div id="filesList" class="filesList" style="display:none"></div>

            <button class="btn primary" type="submit">رفع الصور</button>
            <div class="muted" id="uploadMsg" style="min-height:22px"></div>
          </form>

          <div class="muted small" style="margin-top:10px">تقدر تسحب الصور وتفلّتها هون.</div>
        </div>

        <div class="hr"></div>

        <h2 style="margin:0 0 10px" id="analyze">2) تحليل الخطة</h2>
        <div class="toolbar">
          <label class="muted">ساعات الفصل:</label>
          <select id="hours" class="btn ghost" style="padding:8px 10px">
            <option value="9">9</option>
            <option value="12">12</option>
            <option value="15" selected>15</option>
            <option value="18">18</option>
            <option value="21">21</option>
          </select>

          <!-- بدل صفحات PDF: عدد الصور -->
          <label class="muted">عدد الصور للتحليل:</label>
          <select id="imagesCount" class="btn ghost" style="padding:8px 10px">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3" selected>3</option>
          </select>

          <button type="button" class="btn ghost" id="analyzeBtn">تحليل بالذكاء الاصطناعي</button>
        </div>

        <div class="muted" id="analyzeMsg" style="margin-top:10px;min-height:22px">
          ارفع الصور أولاً، ثم اضغط تحليل.
        </div>
      </div>

      <div class="card">
        <h2 style="margin:0 0 10px">عرض آخر صور الخطة</h2>
        <div class="muted" id="planMeta">...</div>
        <div class="preview" style="margin-top:12px" id="preview">
          <div class="muted" style="padding:16px">لا يوجد صور مرفوعة بعد.</div>
        </div>
        <div style="margin-top:10px" class="muted small" id="planLink"></div>
      </div>
    </div>

    <div class="card" style="margin-top:14px">
      <h2 style="margin:0 0 10px">نتيجة التحليل (تظهر هنا)</h2>
      <div class="muted" id="analysisHint">بعد الضغط على "تحليل بالذكاء الاصطناعي" ستظهر النتائج هنا.</div>
      <div id="analysisBox" style="display:none">
        <div class="item">
          <div>
            <div><b>المعدل:</b> <span id="gpa">-</span></div>
            <div class="muted">الساعات المتبقية: <span id="remaining">-</span> | مجموع الساعات: <span id="total">-</span></div>
          </div>
          <span class="badge ok" id="updatedAt">-</span>
        </div>

        <h3 style="margin-top:14px">✅ المنجزة حسب الفصول</h3>
        <div id="completed"></div>

        <h3 style="margin-top:14px">📝 المسجّلة حسب الفصول</h3>
        <div id="registered"></div>

        <h3 style="margin-top:14px">📌 اقتراح الفصول القادمة</h3>
        <div id="suggested"></div>
      </div>
    </div>
  </div>

  <script src="assets/js/app.js"></script>

  <script>
    const API = "/utbn-backend/api";

    document.addEventListener("DOMContentLoaded", () => {

      const elFile  = document.getElementById("file");
      const elPick  = document.getElementById("pickBtn");
      const elBadge = document.getElementById("fileBadge");
      const elInfo  = document.getElementById("fileInfo");
      const elDrop  = document.getElementById("drop");
      const elFilesList = document.getElementById("filesList");

      function bytesToSize(bytes){
        if(!bytes && bytes !== 0) return "";
        const sizes = ["B","KB","MB","GB"];
        let i=0, v=bytes;
        while(v>=1024 && i<sizes.length-1){ v/=1024; i++; }
        return `${v.toFixed(i===0?0:1)} ${sizes[i]}`;
      }

      function getSelectedFiles(){
        const files = Array.from(elFile.files || []);
        // ✅ حد أقصى 3
        return files.slice(0, 3);
      }

      function renderSelectedFiles(){
        const files = getSelectedFiles();
        if(files.length === 0){
          elBadge.textContent = "ما في صور مختارة";
          elInfo.textContent = "";
          elFilesList.style.display = "none";
          elFilesList.innerHTML = "";
          return;
        }
        elBadge.textContent = `مختار ${files.length} صورة`;
        const total = files.reduce((s,f)=>s + (f.size||0), 0);
        elInfo.textContent = `المجموع: ${bytesToSize(total)} • سيتم رفع أول ${files.length} فقط`;

        elFilesList.style.display = "grid";
        elFilesList.innerHTML = files.map((f, idx)=> `
          <div class="fileRow">
            <div style="display:flex;flex-direction:column;gap:2px">
              <b>${idx+1}) ${f.name}</b>
              <div class="muted small">${bytesToSize(f.size)} • ${f.type || ""}</div>
            </div>
            <span class="badge">صورة</span>
          </div>
        `).join("");
      }

      elPick.addEventListener("click", ()=> elFile.click());
      elFile.addEventListener("change", renderSelectedFiles);

      ["dragenter","dragover"].forEach(evt=>{
        elDrop.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); elDrop.classList.add("drag"); });
      });
      ["dragleave","drop"].forEach(evt=>{
        elDrop.addEventListener(evt, (e)=>{ e.preventDefault(); e.stopPropagation(); elDrop.classList.remove("drag"); });
      });
      elDrop.addEventListener("drop", (e)=>{
        const files = e.dataTransfer.files;
        if(files && files.length){
          elFile.files = files; // browser يسمح
          renderSelectedFiles();
        }
      });

      async function loadMe(){
        try{
          const me = await apiGet("me.php");
          document.getElementById("who").textContent = `أهلًا ${me.full_name} (${me.email})`;
        }catch(e){ location.replace("login.html"); }
      }

      async function loadLatestPlanImages(){
        const box  = document.getElementById("preview");
        const meta = document.getElementById("planMeta");
        const link = document.getElementById("planLink");

        const list = await fetch(`${API}/attachments/list.php?type=plan`, {credentials:"include"}).then(r=>r.json());
        if(!Array.isArray(list) || list.length === 0){
          meta.textContent = "لا يوجد صور خطة مرفوعة.";
          box.innerHTML = '<div class="muted" style="padding:16px">لا يوجد صور مرفوعة بعد.</div>';
          link.textContent = "";
          return null;
        }

        // ✅ عرض آخر 3 صور
      const latest3 = list.slice(-3).reverse();

        meta.textContent = `آخر ${latest3.length} صور • آخر رفع: ${latest3[0].created_at}`;

        const thumbsHtml = latest3.map((it)=>{
          const url = `/utbn-backend/${it.file_path}`;
          return `
            <div class="thumb">
              <a href="${url}" target="_blank" style="width:100%;height:100%;display:block">
                <img src="${url}" alt="plan" />
              </a>
            </div>
          `;
        }).join("");

        box.innerHTML = `<div class="thumbs">${thumbsHtml}</div>`;

        link.innerHTML = latest3.map((it, idx)=>{
          const url = `/utbn-backend/${it.file_path}`;
          return `صفحة ${idx+1}: <a href="${url}" target="_blank">فتح</a>`;
        }).join(" • ");

        return latest3;
      }

      async function uploadPlan(e){
        e.preventDefault();
        const msg = document.getElementById("uploadMsg");
        msg.textContent = "جاري الرفع...";

        const files = getSelectedFiles();
        if(files.length === 0){ msg.textContent = "اختر صور أولاً"; return; }

        // ✅ نرفع واحدة واحدة بنفس endpoint الحالي (بدون تعديل backend)
        const title = (e.target.querySelector('input[name="title"]')?.value || "").trim();

        for(let i=0; i<files.length; i++){
          const f = files[i];

          // تحقق سريع نوع الملف
          if(!(f.type || "").startsWith("image/")){
            msg.textContent = `الملف رقم ${i+1} ليس صورة.`;
            return;
          }

          msg.textContent = `جاري رفع الصورة ${i+1}/${files.length}...`;

          const fd = new FormData();
          fd.append("type", "plan");
          // لو حاب تخلي عنوان لكل صفحة:
          fd.append("title", title ? `${title} (صفحة ${i+1})` : `خطة (صفحة ${i+1})`);
          fd.append("file", f);

          const res = await fetch(`${API}/attachments/upload.php`, {
            method:"POST",
            credentials:"include",
            body: fd
          });

          const j = await res.json().catch(()=>({}));
          if(!(res.ok && j.ok)){
            msg.textContent = "فشل رفع صورة ❌ " + (j.msg || j.error || "");
            return;
          }
        }

        msg.textContent = "تم رفع الصور ✅ الآن اضغط تحليل.";
        document.getElementById("analysisHint").style.display = "block";
        document.getElementById("analysisBox").style.display = "none";

        // تحديث العرض
        await loadLatestPlanImages();

        // reset
        e.target.reset();
        renderSelectedFiles();
      }

 let analyzing = false;

function sleep(ms){ return new Promise(r => setTimeout(r, ms)); }

async function analyzePlan(){
  if (analyzing) return;
  analyzing = true;

  const msg = document.getElementById("analyzeMsg");
  const btn = document.getElementById("analyzeBtn");

  try{
    if (btn) btn.disabled = true;

    const hours = parseInt(document.getElementById("hours").value || "15", 10);
    const imagesCount = parseInt(document.getElementById("imagesCount").value || "3", 10);

    let attempts = 0;

    while (attempts < 6) { // أقصى 6 محاولات
      attempts++;

      msg.textContent = `جاري التحليل... (محاولة ${attempts}/6)`;

      const res = await fetch(`${API}/ai/plan_analyze_gemini.php`, {
        method: "POST",
        credentials:"include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ hours, images: imagesCount })
      });

      const j = await res.json().catch(()=>({}));

      // ✅ إذا Gemini قال Rate limit: انتظر وعدّل رسالة للمستخدم
      if (j && j.error === "GEMINI_RATE_LIMIT") {
        const s = Number(j.retry_after || 60);

        for (let t = s; t >= 1; t--) {
          msg.textContent = `⏳ ضغط عالي على Gemini... إعادة المحاولة بعد ${t} ثانية`;
          await sleep(1000);
        }
        continue; // جرّب مرة ثانية
      }

      // ❌ أي فشل ثاني
      if (!(res.ok && j && j.ok)) {
        msg.textContent = (j.message || j.msg || j.error || "فشل التحليل");
        return;
      }

      // ✅ نجاح
      msg.innerHTML = `تم التحليل ✅ وتم حفظه في قاعدة البيانات.<br><br><a class="btn primary" href="courses.php">افتح صفحة الدورات (مواد إجباري + YouTube)</a>`;
      alert("تم التحليل بنجاح ✅");
      return;
    }

    msg.textContent = "⛔ ما زال Gemini يرفض الطلب (quota). جرّب لاحقاً أو فعّل Billing.";
  } finally {
    analyzing = false;
    if (btn) btn.disabled = false;
  }
}


      document.getElementById("uploadForm").addEventListener("submit", uploadPlan);
      document.getElementById("analyzeBtn").addEventListener("click", analyzePlan);

      loadMe();
      loadLatestPlanImages();
    });
  </script>
</body>
</html>
