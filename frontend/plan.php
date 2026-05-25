<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>الخطة الدراسية</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    body{display:block}
    .navrow{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
    .navrow .btn{white-space:nowrap}
    .attachments{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
    .att{flex:1;min-width:220px}
    .att .thumb{margin-top:10px;border-radius:12px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:#0b1220}
    .att img{width:100%;height:180px;object-fit:cover;display:block}
    .att .thumb iframe{width:100%;height:180px;border:0}

    .attcard{position:relative}
    .attactions{display:flex;gap:8px;justify-content:flex-start;align-items:center;margin-top:10px}
    .delbtn{cursor:pointer}
  </style>
</head>
<body style="display:block">
  <div class="container">
    <div class="topbar">
      <div>
        <h1>الخطة الدراسية</h1>
        <div class="muted" id="majorName">...</div>
      </div>
      <div class="navrow">
        <a class="btn ghost" href="index.php">الرئيسية</a>
        <button class="btn primary" type="button" id="toggleUpload">إرفاق خطة (PDF/صورة)</button>
      </div>
    </div>

    <div class="row" style="margin-top:14px">
      <div class="col">
        <div class="card">
          <h2>مرفقات الخطة</h2>
          <div class="muted">اضغط على أي مرفق لعرضه بحجم كامل.</div>

          <!-- Inline upload (بدون صفحة جديدة) -->
          <div class="card" id="uploadBox" style="margin-top:12px;display:none">
            <form id="uploadForm" class="row" style="display:flex;gap:10px;flex-wrap:wrap">
              <input name="title" placeholder="عنوان (اختياري) مثل: خطة IT 2026" style="flex:1;min-width:240px" class="input" />
              <input type="file" name="file" accept=".pdf,image/*" required class="input" />
              <button class="btn primary" type="submit">رفع</button>
            </form>
            <div class="muted" id="uploadMsg" style="margin-top:8px;min-height:18px"></div>
          </div>

          <div class="attachments" id="planAttachments"></div>
          <div class="muted" id="noPlanAtt" style="margin-top:10px;display:none">لا يوجد مرفقات بعد.</div>
        </div>

        <div class="card" style="margin-top:14px">
          <h2>شجرة الخطة الدراسية</h2>
          <ul class="list" id="coursesList" style="margin-top:12px"></ul>
          <div class="muted" id="emptyTree" style="margin-top:10px;display:none">الخطة فاضية حاليًا — طبيعي إذا لسه ما أضفت مساقات في قاعدة البيانات.</div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/app.js"></script>
  <script>
    const API_ATTACH = "http://localhost/utbn-backend/api/attachments";

    function esc(s){ return String(s||"").replace(/[&<>"']/g, c=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[c])); }

    // حذف مرفق
    async function delAtt(id, type){
      if(!confirm("متأكد بدك تمسح هالملف؟")) return;
      const fd = new FormData();
      fd.append("id", id);
      fd.append("type", type);
      const r = await fetch(`${API_ATTACH}/delete.php`, {method:"POST", body:fd, credentials:"include"});
      if(!r.ok){ alert("صار خطأ بالحذف"); return; }
      await loadAttachments();
    }

    async function loadTree(){
      const tree = await apiGet("plan_tree.php");
      document.getElementById("majorName").textContent = "تخصصك: " + tree.major.name;

      const ul = document.getElementById("coursesList");
      ul.innerHTML = "";
      if(!tree.courses || tree.courses.length === 0){
        document.getElementById("emptyTree").style.display = "block";
        return;
      }
      document.getElementById("emptyTree").style.display = "none";
      tree.courses.forEach(c => {
        const li = document.createElement("li");
        li.className = "item";
        li.innerHTML = `
          <div style="flex:1">
            <div><b>${esc(c.code)}</b> - ${esc(c.name)}</div>
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

    async function loadAttachments(){
      const res = await fetch(`${API_ATTACH}/list.php?type=plan`, {credentials:"include"});
      if(!res.ok){ location.replace("login.html"); return; }
      const arr = await res.json();
      const box = document.getElementById("planAttachments");
      box.innerHTML = "";
      if(!arr || arr.length === 0){
        document.getElementById("noPlanAtt").style.display = "block";
        return;
      }
      document.getElementById("noPlanAtt").style.display = "none";

      // show last 6
      arr.slice(0,6).forEach(it=>{
        const url = `http://localhost/utbn-backend/${it.file_path}`;
        const isImg = (it.mime_type||"").startsWith("image/");
        box.innerHTML += `
          <div class="card att attcard">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap">
              <div>
                <a href="view_attachment.php?type=plan&id=${it.id}" style="text-decoration:none;color:inherit">
                  <b>${esc(it.title || it.original_name)}</b>
                </a>
                <div class="muted" style="margin-top:4px">${it.created_at || ""}</div>
              </div>
              <div class="attactions">
                <a class="btn ghost" href="view_attachment.php?type=plan&id=${it.id}">عرض كامل</a>
                <button class="btn danger delbtn" type="button" onclick="delAtt(${it.id}, 'plan')">حذف</button>
              </div>
            </div>

            <a href="view_attachment.php?type=plan&id=${it.id}" style="text-decoration:none">
              <div class="thumb">
                ${isImg ? `<img src="${url}" alt="${esc(it.title||it.original_name)}">`
                        : `<iframe src="${url}"></iframe>`}
              </div>
            </a>
          </div>
        `;
      });
    }

    // رفع مرفق داخل نفس الصفحة
    async function bindUpload(){
      const btn = document.getElementById("toggleUpload");
      const box = document.getElementById("uploadBox");
      btn.addEventListener("click", ()=>{
        box.style.display = (box.style.display === "none" ? "block" : "none");
      });

      const form = document.getElementById("uploadForm");
      const msg = document.getElementById("uploadMsg");
      form.addEventListener("submit", async (e)=>{
        e.preventDefault();
        msg.textContent = "جاري الرفع...";
        const fd = new FormData(form);
        fd.append("type","plan");
        const res = await fetch(`${API_ATTACH}/upload.php`, {method:"POST", credentials:"include", body:fd});
        const j = await res.json().catch(()=>({}));
        if(res.ok){
          msg.textContent = "تم رفع الخطة ✅";
          form.reset();
          await loadAttachments();
        }else{
          msg.textContent = "فشل الرفع ❌ " + (j.error || "");
        }
      });
    }

    async function boot(){
      try{
        bindUpload();
        await loadAttachments();
        await loadTree();
      }catch(e){
        console.error(e);
      }
    }
    boot();
  </script>
</body>
</html>
