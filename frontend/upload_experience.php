<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.html"); exit; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>إرفاق الخبرات / الدورات</title>
  <link rel="stylesheet" href="assets/css/app.css">
  <style>
    .wrap{max-width:800px;margin:30px auto;color:#fff}
    .card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:16px}
    .row{display:flex;gap:10px;flex-wrap:wrap}
    input,button{padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,.15);background:rgba(0,0,0,.2);color:#fff}
    button{cursor:pointer}
    a{color:#fff}
    .item{padding:10px;border-bottom:1px solid rgba(255,255,255,.12)}
  </style>
</head>
<body>
<div class="wrap">
  <a href="index.php">⬅ رجوع</a>
  <h2>إرفاق الخبرات / الدورات</h2>

  <div class="card">
    <form id="f" class="row">
      <input name="title" placeholder="عنوان (اختياري) مثل: شهادة Python / خبرة تدريب" style="flex:1;min-width:240px">
      <input type="file" name="file" accept=".pdf,image/*" required>
      <button type="submit">رفع</button>
    </form>
    <p id="msg"></p>
  </div>

  <h3>مرفقاتك</h3>
  <div class="card" id="list"></div>
</div>

<script>
const API = "http://localhost/utbn-backend/api/attachments";

async function loadList(){
  const res = await fetch(`${API}/list.php?type=experience`, {credentials:"include"});
  const data = await res.json();
  const box = document.getElementById("list");
  box.innerHTML = "";
  data.forEach(x=>{
    const url = `http://localhost/utbn-backend/${x.file_path}`;
    box.innerHTML += `
      <div class="item">
        <b>${x.title || x.original_name}</b><br>
        <a href="${url}" target="_blank">فتح الملف</a>
        <div style="opacity:.8;font-size:12px">${x.created_at}</div>
      </div>
    `;
  });
}

document.getElementById("f").addEventListener("submit", async (e)=>{
  e.preventDefault();
  const msg = document.getElementById("msg");
  msg.textContent = "جاري الرفع...";

  const fd = new FormData(e.target);
  fd.append("type","experience");

  const res = await fetch(`${API}/upload.php`, {
    method:"POST",
    credentials:"include",
    body: fd
  });

  const j = await res.json().catch(()=>({}));
  if(res.ok){
    msg.textContent = "تم رفع الخبرات/الدورات ✅";
    e.target.reset();
    loadList();
  }else{
    msg.textContent = "فشل الرفع ❌ " + (j.error || "");
  }
});

loadList();
</script>
</body>
</html>
