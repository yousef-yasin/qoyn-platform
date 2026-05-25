<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}

$role = strtolower(trim((string)($_SESSION["role"] ?? "")));
if ($role !== "partner" && $role !== "company") {
  header("Location: index.php");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Our Chat - Company</title>
  <link rel="stylesheet" href="assets/css/style.css"/>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --gold:#D4AF37;
      --bg:#F6F7F9;
      --card:#ffffff;
      --text:#111827;
      --muted:#6b7280;
      --line:#e5e7eb;
      --danger:#d93025;
      --soft:#eef3f9;
      --shadow:0 10px 30px rgba(0,0,0,.08);
      --radius:999px;
      --container:1400px;
      --sidebar-width:320px;
    }

    *{box-sizing:border-box}

    html,body{
      height:100%;
      overflow:hidden;
    }

    body{
      margin:0;
      font-family:"Poppins", Arial, Helvetica, sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    h1,h2,h3,h4,b,strong,.btn,.qoyn-logo,.chat-name,.side-title,.student-name{
      font-family:"Montserrat", sans-serif;
    }

    .qoyn-topbar{
      position:sticky;
      top:0;
      z-index:9999;
      width:100%;
      height:86px;
      background:rgba(255,255,255,.92);
      backdrop-filter:blur(10px);
      box-shadow:var(--shadow);
      border-bottom:1px solid rgba(0,0,0,.06);
    }

    .qoyn-topbar-inner{
      width:min(96vw, var(--container));
      height:100%;
      margin:0 auto;
      padding:14px 22px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      direction:ltr;
    }

    .qoyn-logo{
      font-weight:800;
      font-size:28px;
      color:var(--navy);
      letter-spacing:.5px;
      text-decoration:none;
      white-space:nowrap;
    }

    .qoyn-right{
      display:flex;
      align-items:center;
      gap:18px;
    }

    .qoyn-link{
      text-decoration:none;
      color:#111;
      font-weight:500;
      font-size:15px;
      padding:10px 18px;
      border-radius:999px;
      transition: color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
      white-space:nowrap;
      border:1px solid rgba(10,46,93,.22);
      background:#fff;
      font-family:"Poppins", sans-serif;
    }

    .qoyn-link:hover{
      color:var(--gold);
      transform:translateY(-2px);
      font-weight:700;
    }

    .topbar-logo{
      height:56px;
      width:auto;
      display:block;
      flex:0 0 auto;
    }

    .page{
      width:min(100vw, var(--container));
      height:calc(100vh - 86px);
      margin:0 auto;
      padding:18px 0 20px;
      direction:ltr;
      overflow:hidden;
    }

    .layout{
      height:100%;
      display:grid;
      grid-template-columns:minmax(0, 1fr) var(--sidebar-width);
      grid-template-areas:"chat side";
      gap:0;
      align-items:stretch;
      direction:ltr;
    }

    .left-panel{
      grid-area:side;
      height:100%;
      padding:4px 0 4px 18px;
      border-left:1px solid var(--line);
      display:flex;
      flex-direction:column;
      min-width:0;
      overflow:hidden;
      justify-self:end;
      width:100%;
    }

    .side-title{
      padding:2px 0 14px;
      font-size:16px;
      font-weight:800;
      color:var(--navy);
      text-align:right;
    }

    .list-wrap{
      flex:1;
      min-height:0;
      overflow:auto;
      padding-right:0;
      padding-left:10px;
    }

    .student-item,
    .thread-item{
      padding:12px 0 14px;
      border-bottom:1px solid var(--line);
      background:transparent;
    }

    .student-item:last-child,
    .thread-item:last-child{
      border-bottom:none;
    }

    .student-name,
    .thread-name{
      font-size:14px;
      font-weight:800;
      margin-bottom:4px;
      color:#111;
      line-height:1.35;
    }

    .student-meta,
    .thread-meta{
      font-size:12px;
      color:var(--muted);
      line-height:1.45;
      margin-bottom:2px;
      word-break:break-word;
    }

    .student-actions{
      margin-top:10px;
      display:flex;
      gap:8px;
      flex-wrap:wrap;
    }

    .btn{
      border:none;
      border-radius:12px;
      padding:9px 14px;
      font-weight:800;
      cursor:pointer;
      transition:.2s ease;
    }

    .btn-primary{
      background:var(--navy);
      color:#fff;
      box-shadow:0 8px 20px rgba(10,46,93,.12);
    }

    .btn-primary:hover{
      opacity:.95;
      transform:translateY(-1px);
    }

    .btn-soft{
      background:var(--soft);
      color:var(--navy);
    }

    .thread-item{
      cursor:pointer;
      transition:.15s ease;
    }

    .thread-item:hover{
      background:#fafbfd;
    }

    .thread-item.active{
      background:#eef4ff;
      border-right:4px solid var(--navy);
      padding-right:10px;
    }

    .thread-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }

    .badge{
      min-width:22px;
      height:22px;
      padding:0 7px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:999px;
      background:var(--danger);
      color:#fff;
      font-size:12px;
      font-weight:800;
      font-family:"Montserrat", sans-serif;
    }

    .chat-card{
      grid-area:chat;
      height:100%;
      display:flex;
      flex-direction:column;
      background:transparent;
      border:none;
      box-shadow:none;
      overflow:hidden;
      padding:8px 28px 8px 0;
      width:100%;
      min-width:0;
    }

    .chat-header{
      flex:0 0 auto;
      padding:8px 0 18px;
      text-align:center;
      width:100%;
    }

    .chat-name{
      font-size:40px;
      line-height:1.15;
      font-weight:800;
      color:var(--navy);
      letter-spacing:-.02em;
      margin:0;
    }

    .chat-sub{
      margin-top:10px;
      color:var(--muted);
      font-size:15px;
      line-height:1.7;
      text-align:center;
    }

    .messages{
      flex:1;
      min-height:0;
      overflow:auto;
      padding:8px 6px 18px;
      background:transparent;
      width:100%;
    }

    .empty-chat{
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
      color:var(--muted);
      font-size:16px;
      padding:24px;
    }

    .msg{
      width:fit-content;
  max-width:80%;     /* يزيد العرض الأقصى */
  padding:8px 14px;   /* يقلل الارتفاع العامودي */
  line-height:1.4; 

      margin-bottom:12px;

      border-radius:16px;

      word-wrap:break-word;
      white-space:pre-wrap;
      box-shadow:0 4px 10px rgba(0,0,0,.04);
      font-family:"Poppins", sans-serif;
    }

    .msg.mine{
      margin-right:auto;
      background:var(--navy);
      color:#fff;
      border-bottom-right-radius:6px;
    }

    .msg.other{
      margin-left:auto;
      background:#fff;
      color:#111;
      border:1px solid var(--line);
      border-bottom-left-radius:6px;
    }

    .msg-time{
      margin-top:6px;
      font-size:11px;
      opacity:.75;
    }

    .composer{
      flex:0 0 auto;
      width:100%;
margin-left:auto;
      max-width:1000px; 
      background:#fff;
      border:1.5px solid #cfd8e3;
      border-radius:14px;
      box-shadow:0 2px 8px rgba(10,46,93,.04);
      overflow:hidden;
      align-self:stretch;
    }

    .composer textarea{
      width:100%;
      min-height:54px;
      height:54px;
      max-height:160px;
      resize:none;
      border:none;
      border-bottom:1px solid #e7edf3;
      padding:16px 54px 12px 16px;
      font-size:14px;
      outline:none;
      font-family:"Poppins", sans-serif;
      background:#fff;
      color:#111;
      line-height:1.45;
    }

    .composer textarea::placeholder{
      color:#8b96a7;
    }

    .composer-actions{
      min-height:48px;
      padding:10px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      direction:ltr;
      background:#fff;
    }

    .hint{
      color:var(--muted);
      font-size:12px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .send-icon-btn{
      width:30px;
      height:30px;
      border:none;
      background:transparent;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border-radius:50%;
      cursor:pointer;
      transition:transform .18s ease, background .18s ease;
      flex:0 0 auto;
    }

    .send-icon-btn:hover{
      transform:translateY(-1px);
      background:#f2f6fb;
    }

    .send-icon-btn svg{
      width:18px;
      height:18px;
      display:block;
      stroke:var(--navy);
      stroke-width:2;
      fill:none;
      stroke-linecap:round;
      stroke-linejoin:round;
    }

    .muted-box{
      padding:14px 0;
      color:var(--muted);
      font-size:14px;
      background:transparent;
    }

    .hidden-threads{
      display:none !important;
    }

    @media (max-width: 1100px){
      html,body{
        overflow:auto;
      }

      .page{
        height:auto;
        min-height:calc(100vh - 86px);
        overflow:visible;
        padding-inline:14px;
      }

      .layout{
        grid-template-columns:1fr;
        grid-template-areas:
          "chat"
          "side";
        gap:24px;
      }

      .left-panel{
        border-left:none;
        border-top:1px solid var(--line);
        padding:22px 0 0;
        height:auto;
      }

      .chat-card{
        height:auto;
        padding:8px 0;
      }

      .messages{
        min-height:380px;
        max-height:380px;
      }
    }

    @media (max-width: 980px){
      .qoyn-topbar{
        height:78px;
      }

      .page{
        min-height:calc(100vh - 78px);
      }

      .qoyn-topbar-inner{
        padding:12px 18px;
      }

      .qoyn-logo{
        font-size:24px;
      }

      .topbar-logo{
        height:46px;
      }

      .chat-name{
        font-size:32px;
      }
    }

    @media (max-width: 640px){
      .page{
        padding:16px 14px 18px;
      }

      .chat-card{
        padding:0;
      }

      .chat-name{
        font-size:28px;
      }

      .composer-actions{
        padding:10px 12px;
      }

      .hint{
        max-width:70%;
      }
    }
  </style>
</head>
<body>

  <div class="qoyn-topbar">
    <div class="qoyn-topbar-inner">
      <a href="company.php" class="qoyn-logo">QOYN</a>

      <div class="qoyn-right">
        <a href="company.php" class="qoyn-link">Back</a>
        <img src="uploads/MONKEY.png" class="topbar-logo" alt="MONKEY Logo">
      </div>
    </div>
  </div>

  <div class="page">
    
    <div class="layout">
      <div class="left-panel">
        <div class="side-title"> Students who submitted projects
  </div>
        <div class="list-wrap" id="studentsBox">
          <div class="muted-box">Loading...</div>
        </div>

        <div id="threadsBox" class="hidden-threads"></div>
      </div>

      <div class="chat-card">
        <div class="chat-header">
          <div class="chat-name" id="chatTitle">Choose a conversation</div>
          <div class="chat-sub" id="chatSub">Start a chat with the student or open an existing conversation.</div>
        </div>

        <div class="messages" id="messagesBox">
          <div class="empty-chat">لا توجد رسائل لعرضها حالياً</div>
        </div>

        <div class="composer">
          <textarea id="messageInput" placeholder="اكتب رسالتك هنا..."></textarea>
          <div class="composer-actions">
            <div class="hint">اضغط على أيقونة الإرسال لإرسال الرسالة</div>
            <button class="send-icon-btn" id="sendBtn" type="button" aria-label="Send">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M5 12h11"></path>
                <path d="M13 6l6 6-6 6"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
const API = "../utbn-backend/api/chat";

let currentThreadId = 0;
let currentThreadName = "";
let refreshTimer = null;

function esc(str){
  return String(str ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

async function getJSON(url){
  const res = await fetch(url, {
    credentials: "include"
  });
  return await res.json();
}

async function postJSON(url, data){
  const fd = new FormData();
  Object.keys(data).forEach(key => fd.append(key, data[key]));
  const res = await fetch(url, {
    method: "POST",
    body: fd,
    credentials: "include"
  });
  return await res.json();
}

async function loadStudents(){
  const box = document.getElementById("studentsBox");
  box.innerHTML = `<div class="muted-box">Loading...</div>`;

  try{
    const res = await getJSON(`${API}/company_students.php`);

    if(!res.ok){
      box.innerHTML = `<div class="muted-box">فشل تحميل الطلاب</div>`;
      return;
    }

    const items = Array.isArray(res.students) ? res.students : [];

    if(items.length === 0){
      box.innerHTML = `<div class="muted-box">لا يوجد طلاب سلّموا مشاريع بعد</div>`;
      return;
    }

    box.innerHTML = items.map(s => {
      const studentId = Number(s.student_id || 0);
      const sourceType = esc(s.source_type || "mixed");
      const refTitle = esc(s.ref_title || "");
      const studentName = esc(s.student_name || "Student");
      const studentEmail = esc(s.student_email || "");
      return `
        <div class="student-item">
          <div class="student-name">${studentName}</div>
          <div class="student-meta">${studentEmail}</div>
          <div class="student-meta">${refTitle}</div>
          <div class="student-actions">
            <button class="btn btn-primary"
              onclick="startChat(${studentId}, '${sourceType}', '${studentName.replace(/'/g, "\\'")}')">
              Start Chat
            </button>
          </div>
        </div>
      `;
    }).join("");

  }catch(e){
    box.innerHTML = `<div class="muted-box">حدث خطأ أثناء تحميل الطلاب</div>`;
  }
}

async function loadThreads(selectedId = null){
  const box = document.getElementById("threadsBox");

  try{
    const res = await getJSON(`${API}/threads.php`);

    if(!res.ok){
      box.innerHTML = `<div class="muted-box">فشل تحميل المحادثات</div>`;
      return;
    }

    const threads = Array.isArray(res.threads) ? res.threads : [];

    if(threads.length === 0){
      box.innerHTML = `<div class="muted-box">لا توجد محادثات بعد</div>`;
      return;
    }

    box.innerHTML = threads.map(t => {
      const threadId = Number(t.id || 0);
      const active = Number(selectedId || currentThreadId) === threadId ? "active" : "";
      const unread = Number(t.unread_count || 0);
      const name = esc(t.other_name || "Student");
      const email = esc(t.other_email || "");
      const phase = esc(t.phase_source || "");
      const safeNameJs = (t.other_name || "Student").replace(/'/g, "\\'");
      return `
        <div class="thread-item ${active}" onclick="openThread(${threadId}, '${safeNameJs}', '${email.replace(/'/g, "\\'")}')">
          <div class="thread-row">
            <div class="thread-name">${name}</div>
            ${unread > 0 ? `<span class="badge">${unread > 99 ? '99+' : unread}</span>` : ``}
          </div>
          <div class="thread-meta">${email}</div>
          <div class="thread-meta">${phase}</div>
        </div>
      `;
    }).join("");

  }catch(e){
    box.innerHTML = `<div class="muted-box">حدث خطأ أثناء تحميل المحادثات</div>`;
  }
}

async function startChat(studentId, phaseSource = "mixed", studentName = "Student"){
  try{
    const res = await postJSON(`${API}/start_thread.php`, {
      student_id: studentId,
      phase_source: phaseSource,
      phase2_submission_id: 0,
      phase3_project_id: 0,
      phase3_task_id: 0
    });

    if(!res.ok){
      alert("تعذر إنشاء المحادثة");
      return;
    }

    currentThreadId = Number(res.thread_id || 0);
    currentThreadName = studentName;

    await loadThreads(currentThreadId);
    await openThread(currentThreadId, studentName, "");

  }catch(e){
    alert("حدث خطأ أثناء بدء المحادثة");
  }
}

async function openThread(threadId, otherName = "Student", otherEmail = ""){
  currentThreadId = Number(threadId || 0);
  currentThreadName = otherName || "Student";

  document.getElementById("chatTitle").textContent = currentThreadName;
  document.getElementById("chatSub").textContent = otherEmail || "Direct conversation with the student";

  try{
    await postJSON(`${API}/mark_read.php`, { thread_id: currentThreadId });

    const res = await getJSON(`${API}/messages.php?thread_id=${currentThreadId}`);
    const box = document.getElementById("messagesBox");

    if(!res.ok){
      box.innerHTML = `<div class="empty-chat">تعذر تحميل الرسائل</div>`;
      return;
    }

    const msgs = Array.isArray(res.messages) ? res.messages : [];

    if(msgs.length === 0){
      box.innerHTML = `<div class="empty-chat">لا توجد رسائل بعد، ابدأ المحادثة الآن</div>`;
    }else{
      box.innerHTML = msgs.map(m => `
        <div class="msg ${m.sender_role === 'company' ? 'mine' : 'other'}">
          <div>${esc(m.message)}</div>
          <div class="msg-time">${esc(m.created_at)}</div>
        </div>
      `).join("");
    }

    box.scrollTop = box.scrollHeight;
    await loadThreads(currentThreadId);

  }catch(e){
    document.getElementById("messagesBox").innerHTML = `<div class="empty-chat">حدث خطأ أثناء تحميل الرسائل</div>`;
  }
}

async function sendMessage(){
  const input = document.getElementById("messageInput");
  const text = input.value.trim();

  if(!currentThreadId){
    alert("اختر محادثة أولاً");
    return;
  }

  if(!text){
    return;
  }

  try{
    const res = await postJSON(`${API}/send.php`, {
      thread_id: currentThreadId,
      message: text
    });

    if(!res.ok){
      alert("فشل إرسال الرسالة");
      return;
    }

    input.value = "";
    input.style.height = "54px";
    await openThread(currentThreadId, currentThreadName);

  }catch(e){
    alert("حدث خطأ أثناء إرسال الرسالة");
  }
}

document.getElementById("sendBtn").addEventListener("click", sendMessage);

document.getElementById("messageInput").addEventListener("input", function(){
  this.style.height = "54px";
  this.style.height = Math.min(this.scrollHeight, 160) + "px";
});

document.getElementById("messageInput").addEventListener("keydown", function(e){
  if(e.key === "Enter" && !e.shiftKey){
    e.preventDefault();
    sendMessage();
  }
});

async function init(){
  await loadStudents();
  await loadThreads();

  if(refreshTimer) clearInterval(refreshTimer);
  refreshTimer = setInterval(async () => {
    await loadThreads(currentThreadId || null);
    if(currentThreadId){
      await openThread(currentThreadId, currentThreadName);
    }
  }, 10000);
}

init();
</script>
</body>
</html>