<?php
require_once __DIR__ . "/../utbn-backend/api/session_bootstrap.php";

if (
  !isset($_SESSION["user_id"]) ||
  !isset($_SESSION["role"]) ||
  $_SESSION["role"] !== "student"
) {
  header("Location: login.html");
  exit;
}
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title data-i18n="chat_page_title">Chat - Student</title>

<link rel="stylesheet" href="assets/css/style.css"/>
<script src="assets/js/i18n.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --navy:#0A2E5D;
  --navy-2:#113f7d;
  --gold:#FFC24A;
  --bg:#eef2f7;
  --bg-soft:#f7f9fc;
  --card:#ffffff;
  --text:#111827;
  --muted:#6b7280;
  --line:#e5e7eb;
  --line-2:#d7dfeb;
  --danger:#ef4444;
  --soft:#eef3f9;
  --bubble:#ffffff;
  --bubble-mine:linear-gradient(135deg,#0A2E5D 0%,#1c5eb0 100%);
  --shadow:0 14px 40px rgba(15,23,42,.09);
  --shadow-soft:0 8px 24px rgba(15,23,42,.06);
  --container:1450px;
  --sidebar-width:340px;
}

*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  overflow:hidden;
  font-family:"Poppins","Cairo",Arial,Helvetica,sans-serif;
  color:var(--text);
  background:
    radial-gradient(circle at top left, rgba(17,63,125,.10), transparent 28%),
    radial-gradient(circle at top right, rgba(255,194,74,.18), transparent 24%),
    linear-gradient(180deg,#f8fbff 0%, #eef3f8 100%);
}

html[dir="rtl"] body{
  font-family:"Cairo","Poppins",Arial,Helvetica,sans-serif;
}

h1,h2,h3,h4,b,strong,.btn,.qoyn-logo,.chat-name,.side-title,.thread-name,.title,.badge,.team-badge{
  font-family:"Montserrat","Cairo",sans-serif;
}

html[dir="rtl"] h1,
html[dir="rtl"] h2,
html[dir="rtl"] h3,
html[dir="rtl"] h4,
html[dir="rtl"] b,
html[dir="rtl"] strong,
html[dir="rtl"] .btn,
html[dir="rtl"] .qoyn-logo,
html[dir="rtl"] .chat-name,
html[dir="rtl"] .side-title,
html[dir="rtl"] .thread-name,
html[dir="rtl"] .title,
html[dir="rtl"] .badge,
html[dir="rtl"] .team-badge{
  font-family:"Cairo","Montserrat",sans-serif;
}

*::-webkit-scrollbar{width:10px;height:10px}
*::-webkit-scrollbar-thumb{background:#cfd9e6;border-radius:999px;border:2px solid transparent;background-clip:content-box}
*::-webkit-scrollbar-track{background:transparent}

.qoyn-topbar{
  position:sticky;
  top:0;
  z-index:9999;
  width:100%;
  height:88px;
  background:rgba(255,255,255,.76);
  backdrop-filter:blur(18px);
  border-bottom:1px solid rgba(10,46,93,.08);
}

.qoyn-topbar-inner{
  width:min(96vw, var(--container));
  height:100%;
  margin:0 auto;
  padding:14px 20px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;

}

.qoyn-logo{
  display:inline-flex;
  align-items:center;
  gap:12px;
  text-decoration:none;
  color:var(--navy);
  font-weight:900;
  font-size:28px;
  letter-spacing:.04em;
}

.qoyn-logo::before{
  content:"";
  width:14px;
  height:14px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--gold),#ffda83);
  box-shadow:0 0 0 6px rgba(255,194,74,.18);
}

.qoyn-right{display:flex;align-items:center;gap:16px}

.qoyn-link{
  text-decoration:none;
  color:var(--navy);
  font-weight:700;
  font-size:14px;
  padding:11px 18px;
  border-radius:999px;
  border:1px solid rgba(10,46,93,.10);
  background:rgba(255,255,255,.88);
  box-shadow:var(--shadow-soft);
  transition:transform .2s ease, box-shadow .2s ease, color .2s ease;
}
.qoyn-link:hover{transform:translateY(-1px);box-shadow:0 12px 22px rgba(10,46,93,.10);color:var(--navy-2)}

.topbar-logo{
  height:52px;
  width:52px;
  object-fit:cover;
  display:block;
  border-radius:16px;
  box-shadow:var(--shadow-soft);
  background:#fff;
  padding:3px;
}

.page{
  width:min(96vw, var(--container));
  height:calc(100vh - 88px);
  margin:0 auto;
  padding:18px 0 20px;

  overflow:hidden;
}


.layout{
  height:100%;
  display:grid;
  grid-template-columns:minmax(0, 1fr) var(--sidebar-width);
  grid-template-areas:"chat side";
  gap:18px;
  align-items:stretch;
}

html[dir="rtl"] .layout{
  grid-template-columns:var(--sidebar-width) minmax(0, 1fr);
  grid-template-areas:"side chat";
}

.chat-panel,
.sidebar{
  min-height:0;
  border:1px solid rgba(255,255,255,.6);
  background:rgba(255,255,255,.74);
  backdrop-filter:blur(18px);
  box-shadow:var(--shadow);
}

.chat-panel{
  grid-area:chat;
  display:flex;
  flex-direction:column;
  overflow:hidden;
  border-radius:34px;
  padding:0;
  position:relative;
}

.chat-panel::before{
  content:"";
  position:absolute;
  inset:0;
  background:
    radial-gradient(circle at top left, rgba(17,63,125,.08), transparent 22%),
    radial-gradient(circle at bottom right, rgba(255,194,74,.10), transparent 20%);
  pointer-events:none;
}

.sidebar{
  grid-area:side;

  display:flex;
  flex-direction:column;

  overflow:hidden;
  border-radius:30px;
  padding:12px;
}

.sidebar-head{
  padding:16px 16px 14px;
  font-size:15px;
  font-weight:900;
  color:var(--navy);
  border-bottom:1px solid rgba(10,46,93,.08);
}

.threads{
  flex:1;
  min-height:0;
  overflow:auto;
  padding:12px 4px 4px;
}

.thread-item{
  display:flex;
  align-items:flex-start;
  gap:12px;
  padding:14px;
  border-radius:22px;
  cursor:pointer;
  transition:transform .18s ease, background .18s ease, box-shadow .18s ease;
  border:1px solid transparent;
  margin-bottom:8px;
}

.thread-item:hover{
  background:rgba(17,63,125,.05);
  transform:translateY(-1px);
}

.thread-item.active{
  background:linear-gradient(135deg, rgba(10,46,93,.10), rgba(28,94,176,.06));
  border-color:rgba(17,63,125,.08);
  box-shadow:0 10px 22px rgba(17,63,125,.10);
}

.thread-avatar,
.msg-avatar{
  width:46px;
  height:46px;
  border-radius:16px;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-size:15px;
  font-weight:900;
  letter-spacing:.04em;
  flex:0 0 auto;
  box-shadow:0 8px 20px rgba(17,63,125,.18);
}
.thread-content{flex:1;min-width:0}
.thread-top{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  margin-bottom:4px;
}
.thread-name{font-size:14px;font-weight:900;color:#0f172a;line-height:1.35}
.thread-meta{font-size:12px;color:var(--muted);line-height:1.5;word-break:break-word}

.badge{
  min-width:24px;
  height:24px;
  padding:0 8px;
  border-radius:999px;
  background:linear-gradient(135deg,#ff5b66,#ef4444);
  color:#fff;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  font-weight:900;
  box-shadow:0 8px 18px rgba(239,68,68,.26);
}

.muted-box{
  padding:18px 16px;
  color:var(--muted);
  font-size:14px;
  border-radius:18px;
  background:rgba(255,255,255,.72);
  border:1px dashed rgba(10,46,93,.12);
}

.chat-head{
  position:relative;
  z-index:1;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  padding:20px 24px;
  border-bottom:1px solid rgba(10,46,93,.08);
  background:linear-gradient(180deg, rgba(255,255,255,.86) 0%, rgba(255,255,255,.55) 100%);
}

.chat-head-main{min-width:0}
.chat-name{
  font-size:26px;
  line-height:1.1;
  font-weight:900;
  color:var(--navy);
  letter-spacing:-.03em;
  margin:0;
}

.chat-sub{
  margin-top:8px;
  color:var(--muted);
  font-size:14px;
  line-height:1.6;
}

#chatTeamBadge{
  display:flex;
  align-items:center;
  justify-content:flex-end;
  flex:0 0 auto;
}
html[dir="rtl"] #chatTeamBadge{
  justify-content:flex-start;
}

.team-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 14px;
  border-radius:999px;
  font-size:11px;
  font-weight:900;
  background:#edf4ff;
  color:var(--navy);
  border:1px solid rgba(10,46,93,.08);
}
.team-badge::before{
  content:"";
  width:9px;
  height:9px;
  border-radius:50%;
  background:#22c55e;
  box-shadow:0 0 0 5px rgba(34,197,94,.14);
}

.messages{
  position:relative;
  z-index:1;
  flex:1;
  min-height:0;
  overflow:auto;
  padding:22px 24px;
  display:flex;
  flex-direction:column;
  gap:14px;
}

.empty-chat{
  margin:auto;
  max-width:460px;
  text-align:center;
  padding:26px 20px;
  border-radius:24px;
  border:1px dashed rgba(10,46,93,.14);
  background:rgba(255,255,255,.72);
  color:var(--muted);
  font-size:15px;
}

.msg-row{
  display:flex;
  gap:10px;
  align-items:flex-end;
  max-width:88%;
}
.msg-row.mine{align-self:flex-end;flex-direction:row-reverse}
.msg-row.other{align-self:flex-start}

html[dir="rtl"] .msg-row.mine{
  align-self:flex-start;
  flex-direction:row;
}
html[dir="rtl"] .msg-row.other{
  align-self:flex-end;
  flex-direction:row-reverse;
}

.msg-avatar{
  width:36px;
  height:36px;
  border-radius:14px;
  font-size:11px;
  margin-bottom:2px;
}

.msg{
  position:relative;
  width:fit-content;
  max-width:min(720px, 100%);
  padding:12px 15px 9px;
  border-radius:22px;
  white-space:pre-wrap;
  word-break:break-word;
  line-height:1.5;
  box-shadow:var(--shadow-soft);
  font-size:14px;
}

.msg.mine{
  background:var(--bubble-mine);
  color:#fff;
  border-bottom-right-radius:8px;
}

.msg.other{
  background:var(--bubble);
  color:#0f172a;
  border:1px solid rgba(10,46,93,.08);
  border-bottom-left-radius:8px;
}

html[dir="rtl"] .msg.mine{
  border-bottom-right-radius:22px;
  border-bottom-left-radius:8px;
}
html[dir="rtl"] .msg.other{
  border-bottom-left-radius:22px;
  border-bottom-right-radius:8px;
}

.msg.mine::after,
.msg.other::after{
  content:"";
  position:absolute;
  bottom:0;
  width:16px;
  height:16px;
}
.msg.mine::after{
  right:-6px;
  background:radial-gradient(circle at 0 0, transparent 15px, #1b5aa8 16px);
}
.msg.other::after{
  left:-6px;
  background:radial-gradient(circle at 100% 0, transparent 15px, #fff 16px);
}

html[dir="rtl"] .msg.mine::after{
  right:auto;
  left:-6px;
  background:radial-gradient(circle at 100% 0, transparent 15px, #1b5aa8 16px);
}
html[dir="rtl"] .msg.other::after{
  left:auto;
  right:-6px;
  background:radial-gradient(circle at 0 0, transparent 15px, #fff 16px);
}

.msg-text{position:relative;z-index:1}
.msg-time{
  margin-top:6px;
  font-size:11px;
  opacity:.75;
  display:flex;
  justify-content:flex-end;
}
html[dir="rtl"] .msg-time{
  justify-content:flex-start;
}

.composer-wrap{
  position:relative;
  z-index:1;
  padding:0 18px 18px;
}

.composer{
  background:rgba(255,255,255,.9);
  border:1px solid rgba(10,46,93,.10);
  border-radius:28px;
  box-shadow:var(--shadow-soft);
  overflow:hidden;

}

.composer-row{
  display:flex;
  align-items:flex-end;
  gap:12px;
  padding:14px 14px 12px;
}

html[dir="rtl"] .composer-row{
  flex-direction:row-reverse;
}

.composer textarea{
  width:100%;
  min-height:54px;
  height:54px;
  max-height:160px;
  resize:none;
  border:none;
  background:transparent;
  padding:16px 18px;
  border-radius:22px;
  outline:none;
  font-family:inherit;
  font-size:14px;
  
  color:#111;
  line-height:1.5;
}
.composer textarea::placeholder{color:#94a3b8}

html[dir="rtl"] .composer textarea{
  text-align:right;
}

.send-btn{
  width:50px;
  height:50px;
  border:none;
  border-radius:18px;
  background:linear-gradient(135deg,#0A2E5D,#1c5eb0);
  cursor:pointer;
  flex:0 0 auto;
  position:relative;
  box-shadow:0 14px 24px rgba(17,63,125,.22);
  transition:transform .18s ease, box-shadow .18s ease, opacity .18s ease;
  color:transparent;
}
.send-btn:hover{transform:translateY(-1px) scale(1.01);box-shadow:0 18px 28px rgba(17,63,125,.28)}
.send-btn:active{transform:translateY(0)}
.send-btn::before{
  content:"";

  position:absolute;
  inset:0;
  margin:auto;
  width:20px;
  height:20px;
  background:no-repeat center/contain;
  background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2.3' stroke-linecap='round' stroke-linejoin='round'><path d='M22 2L11 13'/><path d='M22 2L15 22L11 13L2 9L22 2Z'/></svg>");
}

html[dir="rtl"] .send-btn::before{
  transform:scaleX(-1);
}

.composer-actions{
  padding:0 18px 16px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:14px;
}
.composer-hint{color:var(--muted);font-size:12px}

@media (max-width:1100px){
  body{overflow:auto}
  .page{height:auto;min-height:calc(100vh - 88px);overflow:visible;padding-inline:0}
  .layout{grid-template-columns:1fr;grid-template-areas:"chat" "side";gap:18px}
  html[dir="rtl"] .layout{grid-template-columns:1fr;grid-template-areas:"chat" "side"}
  .chat-panel,.sidebar{min-height:auto}
  .messages{min-height:420px;max-height:420px}
}

@media (max-width:820px){
  .qoyn-topbar{height:78px}
  .page{min-height:calc(100vh - 78px);padding:12px 0 18px}
  .qoyn-topbar-inner{padding:12px 14px}
  .qoyn-logo{font-size:23px}
  .topbar-logo{height:46px;width:46px;border-radius:14px}
  .chat-head{padding:18px 18px 16px;align-items:flex-start;flex-direction:column}
  #chatTeamBadge{justify-content:flex-start}
  .messages{padding:18px}
  .composer-wrap{padding:0 14px 14px}
  .sidebar{padding:10px}
}

@media (max-width:640px){
  .page{width:min(100vw, var(--container));padding:10px 0 18px}
  .layout{gap:14px}
  .chat-panel,.sidebar{border-radius:22px}
  .sidebar-head{padding:14px 12px}
  .thread-item{padding:12px}
  .thread-avatar{width:42px;height:42px;border-radius:14px}
  .chat-name{font-size:22px}
  .chat-sub{font-size:13px}
  .msg-row{max-width:94%}
  .msg{font-size:13px;padding:11px 13px 8px}
  .composer-row{padding:10px 10px 8px}
  .composer textarea{padding:14px 12px}
  .composer-actions{padding:0 14px 14px;align-items:flex-start;flex-direction:column}
  .composer-hint{font-size:11px}
}
</style>
</head>

<body>

<div class="qoyn-topbar">
  <div class="qoyn-topbar-inner">
    <a href="student-dashboard.php#home" class="qoyn-logo">QOYN</a>

    <div class="qoyn-right">
      <a class="qoyn-link" href="student-dashboard.php#home" data-i18n="back">Back</a>
      <img src="uploads/MONKEY.png" class="topbar-logo" alt="MONKEY Logo">
    </div>
  </div>
</div>

<div class="page">
  <div class="layout">

    <div class="chat-panel">

      <div class="chat-head">
        <div class="chat-head-main">
          <div class="chat-name" id="chatTitle" data-i18n="select_chat">Select conversation</div>
          <div class="chat-sub" id="chatSub" data-i18n="chat_hint">If a company sends you a message it will appear here</div>
        </div>
        <div id="chatTeamBadge"></div>
      </div>

      <div class="messages" id="messagesBox">
        <div class="empty-chat" data-i18n="no_messages">No messages yet</div>
      </div>

      <div class="composer-wrap">
        <div class="composer">
          <div class="composer-row">
            <textarea id="messageInput" data-i18n-placeholder="write_message" placeholder="Write your message..."></textarea>
            <button class="send-btn" id="sendBtn" data-i18n="send" type="button">Send</button>
          </div>
          <div class="composer-actions">
            <div class="composer-hint" data-i18n="chat_input_hint">Press Enter to send · Shift + Enter for new line</div>
          </div>
        </div>
      </div>

    </div>

    <div class="sidebar">
      <div class="sidebar-head" data-i18n="companies_contacted">Companies that contacted you</div>

      <div class="threads" id="threadsBox">
        <div class="muted-box" data-i18n="loading">Loading...</div>
      </div>
    </div>

  </div>
</div>

<script>
const API = "../utbn-backend/api/chat";
let currentThreadId = 0;
let currentThreadName = "";
let refreshTimer = null;

function tt(key, fallback = ""){
  try{
    if (typeof t === "function") {
      const value = t(key);
      return value && value !== key ? value : (fallback || key);
    }
  }catch(e){}
  return fallback || key;
}

function esc(str){
  return String(str ?? "")
    .replace(/&/g,"&amp;")
    .replace(/</g,"&lt;")
    .replace(/>/g,"&gt;")
    .replace(/"/g,"&quot;")
    .replace(/'/g,"&#39;");
}

function getInitials(name){
  return String(name || "?")
    .trim()
    .split(/\s+/)
    .slice(0,2)
    .map(part => part.charAt(0).toUpperCase())
    .join("") || "?";
}

function colorFromName(name){
  const palettes = [
    "linear-gradient(135deg,#1d4ed8,#0ea5e9)",
    "linear-gradient(135deg,#7c3aed,#2563eb)",
    "linear-gradient(135deg,#0f766e,#14b8a6)",
    "linear-gradient(135deg,#ea580c,#f59e0b)",
    "linear-gradient(135deg,#db2777,#8b5cf6)",
    "linear-gradient(135deg,#334155,#0f172a)"
  ];
  const str = String(name || "default");
  let total = 0;
  for (let i = 0; i < str.length; i++) total += str.charCodeAt(i);
  return palettes[total % palettes.length];
}

function avatarMarkup(name, cls){
  return `<div class="${cls}" style="background:${colorFromName(name)}">${esc(getInitials(name))}</div>`;
}

async function getJSON(url){
  const res = await fetch(url,{credentials:"include"});
  return await res.json();
}

function applyDirFromDocument(){
  const dir = document.documentElement.getAttribute("dir") || "ltr";
  document.body.setAttribute("data-dir", dir);
}

function getChatSubText(){
  return tt("chat_professional_view", "Professional conversation view");
}

function getYouLabel(){
  return tt("you", "You");
}

function getCompanyLabel(){
  return tt("company", "Company");
}

async function loadThreads(){
  const box = document.getElementById("threadsBox");

  try{
    const res = await getJSON(`${API}/threads.php`);

    if(!res.ok){
      box.innerHTML = `<div class="muted-box">${esc(tt("failed_load_chats", "Failed to load chats"))}</div>`;
      return;
    }

    const threads = res.threads || [];

    if(!threads.length){
      box.innerHTML = `<div class="muted-box">${esc(tt("no_chats", "No chats found"))}</div>`;
      document.getElementById("chatTeamBadge").innerHTML = "";
      return;
    }

    box.innerHTML = threads.map(item => `
      <div class="thread-item ${Number(currentThreadId)===Number(item.id)?'active':''}" onclick="openThread(${Number(item.id)}, '${esc(String(item.other_name || ""))}')">
        ${avatarMarkup(item.other_name, "thread-avatar")}
        <div class="thread-content">
          <div class="thread-top">
            <div class="thread-name">${esc(item.other_name)}</div>
            ${Number(item.unread_count || 0) > 0 ? `<span class="badge">${Number(item.unread_count) > 99 ? '99+' : Number(item.unread_count)}</span>` : ``}
          </div>
          <div class="thread-meta">${esc(item.other_email || "")}</div>
        </div>
      </div>
    `).join("");

  }catch(e){
    box.innerHTML = `<div class="muted-box">${esc(tt("error_loading_chats", "Error loading chats"))}</div>`;
  }
}

async function openThread(threadId, threadName = ""){
  currentThreadId = threadId;
  if (threadName) currentThreadName = threadName;

  document.querySelectorAll(".thread-item").forEach(el => el.classList.remove("active"));
  const activeEl = Array.from(document.querySelectorAll(".thread-item")).find(el => {
    const clickAttr = el.getAttribute("onclick") || "";
    return clickAttr.includes(`openThread(${Number(threadId)}`);
  });
  if(activeEl) activeEl.classList.add("active");

  const res = await getJSON(`${API}/messages.php?thread_id=${threadId}`);
  const box = document.getElementById("messagesBox");

  if(!res.ok){
    box.innerHTML = `<div class="empty-chat">${esc(tt("error_loading_messages", "Error loading messages"))}</div>`;
    return;
  }

  const msgs = res.messages || [];
  const titleEl = document.getElementById("chatTitle");
  const subEl = document.getElementById("chatSub");
  const badgeEl = document.getElementById("chatTeamBadge");

  titleEl.textContent = currentThreadName || tt("select_chat", "Select conversation");
  subEl.textContent = currentThreadName ? getChatSubText() : tt("chat_hint", "If a company sends you a message it will appear here");
  badgeEl.innerHTML = currentThreadName ? `<div class="team-badge">${esc(currentThreadName)}</div>` : "";

  if(!msgs.length){
    box.innerHTML = `<div class="empty-chat">${esc(tt("no_messages", "No messages yet"))}</div>`;
    return;
  }

  box.innerHTML = msgs.map(m => {
    const isMine = m.sender_role === 'student';
    const personName = isMine ? getYouLabel() : (currentThreadName || getCompanyLabel());

    return `
      <div class="msg-row ${isMine ? 'mine' : 'other'}">
        ${avatarMarkup(personName, 'msg-avatar')}
        <div class="msg ${isMine ? 'mine' : 'other'}">
          <div class="msg-text">${esc(m.message)}</div>
          <div class="msg-time">${esc(m.created_at)}</div>
        </div>
      </div>
    `;
  }).join("");

  box.scrollTop = box.scrollHeight;
}

async function sendMessage(){
  const input = document.getElementById("messageInput");
  const text = input.value.trim();

  if(!currentThreadId){
    alert(tt("select_chat_first", "Please select a chat first"));
    return;
  }

  if(!text) return;

  const fd = new FormData();
  fd.append("thread_id", currentThreadId);
  fd.append("message", text);

  const res = await fetch(`${API}/send.php`,{
    method:"POST",
    body:fd,
    credentials:"include"
  });

  const j = await res.json();

  if(!j.ok){
    alert(j.error || tt("failed_send", "Failed to send"));
    return;
  }

  input.value = "";
  input.style.height = "54px";
  await openThread(currentThreadId, currentThreadName);
  await loadThreads();
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

window.addEventListener("languageChanged", () => {
  applyDirFromDocument();

  if(currentThreadId){
    openThread(currentThreadId, currentThreadName);
  }else{
    document.getElementById("chatTitle").textContent = tt("select_chat", "Select conversation");
    document.getElementById("chatSub").textContent = tt("chat_hint", "If a company sends you a message it will appear here");
  }

  loadThreads();
});

async function init(){
  applyDirFromDocument();
  await loadThreads();

  if(refreshTimer) clearInterval(refreshTimer);

  refreshTimer = setInterval(async()=>{
    await loadThreads();
    if(currentThreadId){
      await openThread(currentThreadId, currentThreadName);
    }
  }, 8000);
}

init();
</script>

</body>
</html>