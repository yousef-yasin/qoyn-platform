<?php
$secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");

if (session_status() === PHP_SESSION_NONE) {
  if (defined("PHP_VERSION_ID") && PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
      "lifetime" => 0,
      "path"     => "/",
      "secure"   => $secure,
      "httponly" => true,
      "samesite" => "Lax",
    ]);
  } else {
    session_set_cookie_params(0, "/");
  }
  session_start();
}

if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>شهاداتي</title>
  <link rel="stylesheet" href="assets/css/style.css"/>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root{
    --navy:#0A2E5D;
    --navy-2:#123c75;
    --navy-3:#1B5FAE;
    --yellow:#FFC24A;
    --yellow-2:#ffd978;
    --bg:#F6F7F9;
    --card:#ffffff;
    --text:#0B0B0B;
    --muted:rgba(10,46,93,.72);
    --line:rgba(10,46,93,.10);
    --line-2:rgba(10,46,93,.16);
    --shadow:0 18px 44px rgba(15,23,42,.08);
    --shadow-lg:0 26px 60px rgba(10,46,93,.16);
    --shadow-soft:0 10px 24px rgba(15,23,42,.06);
    --radius:24px;
    --radiusPill:999px;
    --container:1220px;
  }

  *{box-sizing:border-box}
  html{scroll-behavior:smooth}

  body{
    margin:0;
    background:
      radial-gradient(circle at top right, rgba(255,194,74,.18), transparent 18%),
      radial-gradient(circle at left top, rgba(10,46,93,.10), transparent 26%),
      linear-gradient(180deg, #f8fbff 0%, #f2f6fb 46%, #edf3fa 100%);
    color:var(--text);
    font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    overflow-x:hidden;
  }

  .muted{
    color:var(--muted) !important;
    opacity:1 !important;
  }

  .nav-wrap{
    position:fixed;
    top:0; left:0; right:0;
    z-index:9999;
    padding:18px 22px;
    transition:background .25s ease, box-shadow .25s ease, padding .25s ease;
    background:transparent;
  }

  .nav-wrap.scrolled{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
    box-shadow:var(--shadow-soft);
    padding:12px 22px;
  }

  .progress{
    position:absolute;
    top:0; left:0;
    height:3px;
    width:100%;
    opacity:0;
    transition:opacity .25s ease;
  }

  .nav-wrap.scrolled .progress{
    opacity:1;
  }

  .progress > div{
    height:100%;
    width:0%;
    background:linear-gradient(90deg, var(--navy), var(--yellow));
    transition:width .08s linear;
  }

  .nav{
    max-width:var(--container);
    margin:0 auto;
    display:flex;
    align-items:center;
    gap:16px;
  }

  .logo{
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:28px;
    letter-spacing:.6px;
    color:var(--navy);
    text-decoration:none;
    user-select:none;
  }

  .nav-spacer{flex:1}

  .nav-links{
    display:flex;
    align-items:center;
    gap:14px;
    margin:0;
    padding:0;
    list-style:none;
    flex-wrap:wrap;
  }

  .nav-links a{
    text-decoration:none;
    color:#111;
    font-weight:500;
    font-size:15px;
    padding:10px 14px;
    border-radius:var(--radiusPill);
    transition:color .2s ease, transform .2s ease, background .2s ease, font-weight .2s ease;
    white-space:nowrap;
  }

  .nav-links a:hover{
    color:var(--yellow);
    transform:translateY(-2px);
    font-weight:700;
  }

  .nav-links a.active{
    background:var(--yellow);
    color:#fff !important;
    font-weight:800;
  }

  .nav-logout{
    border:1px solid rgba(10,46,93,.22);
    font-weight:700;
    background:transparent;
  }

  .nav-logout:hover{
    background:var(--navy);
    color:#fff !important;
  }

  .nav-right{
    display:flex;
    align-items:center;
    gap:12px;
  }

  .lang-switch{
    display:flex;
    align-items:center;
    gap:8px;
    padding:6px;
    border-radius:999px;
    background:rgba(255,255,255,.84);
    border:1px solid rgba(10,46,93,.10);
    box-shadow:0 10px 22px rgba(10,46,93,.06);
  }

  .lang-btn{
    border:none;
    background:transparent;
    color:var(--navy);
    min-width:64px;
    height:38px;
    padding:0 14px;
    border-radius:999px;
    font-family:"Montserrat", sans-serif;
    font-weight:800;
    font-size:12px;
    cursor:pointer;
    transition:all .18s ease;
  }

  .lang-btn.active{
    background:linear-gradient(135deg, var(--navy), var(--navy-3));
    color:#fff;
    box-shadow:0 8px 18px rgba(10,46,93,.16);
  }

  .nav-monkey{
    height:58px;
    width:auto;
    display:block;
  }

  .container{
    width:100%;
    max-width:var(--container);
    margin:0 auto;
    padding:132px 22px 60px;
  }

  .hero-shell{
    position:relative;
    overflow:hidden;
    background:
      linear-gradient(135deg, rgba(10,46,93,.05), rgba(255,194,74,.10)),
      rgba(255,255,255,.75);
    border:1px solid rgba(10,46,93,.08);
    border-radius:36px;
    box-shadow:var(--shadow);
    padding:52px 42px 34px;
  }

  .hero-shell::before{
    content:"";
    position:absolute;
    width:260px;
    height:260px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(255,194,74,.24), transparent 68%);
    top:-110px;
    left:-60px;
    pointer-events:none;
  }

  .hero-shell::after{
    content:"";
    position:absolute;
    width:320px;
    height:320px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(10,46,93,.10), transparent 70%);
    bottom:-160px;
    right:-100px;
    pointer-events:none;
  }

  .page-hero{
    position:relative;
    z-index:1;
    text-align:center;
    margin:0;
  }

  .page-hero h1{
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:clamp(42px, 6vw, 68px);
    margin:0;
    letter-spacing:-1px;
    line-height:1.04;
    color:#111;
  }

  .page-hero .navy{
    color:var(--navy);
  }

  .page-subtitle{
    margin:14px auto 0;
    max-width:760px;
    font-size:16px;
    line-height:1.9;
    color:rgba(10,46,93,.82);
  }

  .hero-strip{
    position:relative;
    z-index:1;
    margin-top:28px;
    background:rgba(255,255,255,.88);
    border:1px solid rgba(255,255,255,.62);
    box-shadow:var(--shadow-lg);
    border-radius:30px;
    padding:28px 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    flex-wrap:wrap;
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
  }

  .hero-strip-left{
    display:flex;
    align-items:center;
    gap:14px;
  }

  .hero-icon{
    width:64px;
    height:64px;
    border-radius:20px;
    background:linear-gradient(135deg, var(--navy), var(--navy-3));
    box-shadow:0 16px 34px rgba(10,46,93,.18);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:28px;
    font-weight:900;
    font-family:"Montserrat", sans-serif;
  }

  .hero-strip h2{
    margin:0;
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:34px;
    line-height:1.06;
    color:var(--navy);
  }

  .hero-strip p{
    margin:6px 0 0 0;
    color:rgba(10,46,93,.72);
    font-size:14px;
  }

  .quick-chip{
    display:inline-flex;
    align-items:center;
    gap:10px;
    min-height:48px;
    padding:0 18px;
    border-radius:999px;
    background:rgba(255,194,74,.16);
    border:1px solid rgba(255,194,74,.34);
    color:var(--navy);
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:13px;
  }

  .card{
    position:relative;
    margin-top:24px;
    background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(250,252,255,.96));
    border:1px solid var(--line);
    border-radius:32px;
    box-shadow:var(--shadow);
    padding:34px 34px 30px;
    overflow:hidden;
  }

  .card::before{
    content:"";
    position:absolute;
    inset:0 auto auto 0;
    width:100%;
    height:6px;
    background:linear-gradient(90deg, var(--navy), var(--yellow));
  }

  .section-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:10px;
  }

  .section-title{
    margin:0;
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:30px;
    line-height:1.1;
    color:var(--navy);
    letter-spacing:-.4px;
  }

  .count-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:58px;
    height:44px;
    padding:0 16px;
    border-radius:999px;
    background:rgba(10,46,93,.06);
    border:1px solid rgba(10,46,93,.12);
    color:var(--navy);
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:14px;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.8);
  }

  .list{
    display:flex;
    flex-direction:column;
    gap:18px;
    margin-top:20px;
  }

  .item{
    position:relative;
    display:grid;
    grid-template-columns: 1fr auto;
    gap:18px;
    align-items:center;
    padding:24px 24px;
    border-radius:28px;
    border:1px solid rgba(10,46,93,.10);
    background:
      radial-gradient(circle at top left, rgba(255,194,74,.10), transparent 26%),
      linear-gradient(180deg, rgba(255,255,255,1), rgba(246,247,249,.94));
    box-shadow:0 12px 28px rgba(10,46,93,.06);
    transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    min-height:120px;
    overflow:hidden;
  }

  .item::after{
    content:"";
    position:absolute;
    width:150px;
    height:150px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(10,46,93,.06), transparent 70%);
    left:-40px;
    bottom:-70px;
    pointer-events:none;
  }

  .item:hover{
    transform:translateY(-5px);
    border-color:rgba(10,46,93,.20);
    box-shadow:0 24px 46px rgba(10,46,93,.12);
  }

  .item-main{
    min-width:0;
  }

  .cert-topline{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:6px;
  }

  .cert-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:46px;
    height:46px;
    padding:0 14px;
    border-radius:999px;
    background:rgba(255,194,74,.18);
    color:var(--navy);
    border:1px solid rgba(255,194,74,.42);
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:13px;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.65);
  }

  .cert-label{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:rgba(10,46,93,.72);
    font-size:13px;
    font-weight:600;
  }

  .item b{
    display:block;
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    color:#111;
    font-size:23px;
    line-height:1.25;
    letter-spacing:-.25px;
  }

  .cert-date{
    margin-top:9px;
    font-size:14px;
    color:rgba(10,46,93,.68);
  }

  .cert-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
    justify-content:flex-end;
  }

  .btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:48px;
    padding:0 20px;
    border-radius:999px;
    text-decoration:none;
    cursor:pointer;
    user-select:none;
    white-space:nowrap;
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:13px;
    letter-spacing:.2px;
    border:1px solid rgba(0,0,0,.08);
    transition:transform .15s ease, background .15s ease, box-shadow .15s ease, color .15s ease, border-color .15s ease;
    appearance:none;
    outline:none;
  }

  .btn:hover{
    transform:translateY(-2px);
  }

  .btn.view-btn{
    min-width:170px;
    background:linear-gradient(135deg, var(--navy) 0%, var(--navy-3) 100%);
    color:#fff;
    border-color:var(--navy);
    box-shadow:0 14px 28px rgba(10,46,93,.16);
  }

  .btn.view-btn:hover{
    background:linear-gradient(135deg, var(--yellow) 0%, var(--yellow-2) 100%);
    border-color:var(--yellow);
    color:#fff;
    box-shadow:0 18px 34px rgba(255,194,74,.22);
  }

  .empty-state{
    margin-top:14px;
    padding:34px 24px;
    border-radius:24px;
    background:rgba(10,46,93,.04);
    border:1px dashed rgba(10,46,93,.18);
    text-align:center;
    color:rgba(10,46,93,.72);
    line-height:1.9;
    font-size:15px;
  }

  @media (max-width: 900px){
    .container{
      padding:118px 18px 42px;
    }

    .hero-shell{
      padding:34px 20px 26px;
      border-radius:26px;
    }

    .hero-strip{
      padding:20px;
      border-radius:24px;
    }

    .hero-strip h2{
      font-size:24px;
    }

    .card{
      padding:26px 18px 22px;
      border-radius:26px;
    }

    .section-title{
      font-size:24px;
    }

    .item{
      grid-template-columns:1fr;
      align-items:flex-start;
    }

    .cert-actions{
      width:100%;
      justify-content:flex-start;
    }

    .nav{
      gap:10px;
    }

    .nav-links{
      gap:8px;
    }

    .nav-monkey{
      height:52px;
    }
  }

  @media (max-width: 640px){
    .nav-wrap{
      padding:12px 14px;
    }

    .nav-wrap.scrolled{
      padding:10px 14px;
    }

    .container{
      padding:108px 14px 34px;
    }

    .page-subtitle{
      font-size:14px;
    }

    .btn{
      width:100%;
    }

    .cert-actions{
      flex-direction:column;
    }

    .hero-strip-left{
      width:100%;
    }

    .cert-topline{
      align-items:flex-start;
    }

    .nav-right{
      width:100%;
      justify-content:flex-end;
      flex-wrap:wrap;
    }
  }
</style>
</head>
<body>

  <header class="nav-wrap" id="navWrap">
    <div class="progress"><div id="progressBar"></div></div>

    <nav class="nav">
      <a class="logo" href="index.php">QOYN</a>

      <div class="nav-spacer"></div>

      <ul class="nav-links">
        <li><a href="student-dashboard.php#home" id="navHome">Home</a></li>
        <li><a href="#" class="active" id="navCertificates">Certificates</a></li>
        <li><a href="index.php" id="navBack">Back</a></li>
        <li><a href="login.html" class="nav-logout" id="navLogout">Logout</a></li>
      </ul>

      <div class="nav-right">
        <div class="lang-switch">
          <button class="lang-btn active" id="langAr">AR</button>
          <button class="lang-btn" id="langEn">EN</button>
        </div>
        <img src="uploads/MONKEY.png" alt="QOYN Logo" class="nav-monkey">
      </div>
    </nav>
  </header>

  <div class="container">
    <section class="hero-shell">
      <div class="page-hero">
        <h1 id="heroTitle"><span class="navy">QOYN</span> Certificates</h1>
        <p class="page-subtitle" id="heroSubtitle">
          اعرض شهاداتك المعتمدة وحمّلها بتصميم مرتب ومتناسق مع هوية QOYN.
        </p>
      </div>

      <div class="hero-strip">
        <div class="hero-strip-left">
          <div class="hero-icon">C</div>
          <div>
            <h2 id="heroStripTitle">My Certificates</h2>
            <p id="heroStripText">View, open, and print your certificates in one place.</p>
          </div>
        </div>

        <div class="quick-chip" id="heroChip">Certified achievements</div>
      </div>
    </section>

    <section class="card">
      <div class="section-head">
        <h2 class="section-title" id="libraryTitle">Your Certificate Library</h2>
        <span class="count-badge" id="certCount">0</span>
      </div>

      <div class="muted" id="msg" style="min-height:20px"></div>
      <div id="list" class="list"></div>
    </section>
  </div>

<script>
const API_BASE = "/utbn-backend/api";

const list = document.getElementById("list");
const msg = document.getElementById("msg");
const certCount = document.getElementById("certCount");

const langAr = document.getElementById("langAr");
const langEn = document.getElementById("langEn");

let currentLang = localStorage.getItem("cert_lang") || "ar";

const translations = {
  ar: {
    navHome: "الرئيسية",
    navCertificates: "الشهادات",
    navBack: "رجوع",
    navLogout: "تسجيل الخروج",
    heroTitle: '<span class="navy">QOYN</span> Certificates',
    heroSubtitle: "اعرض شهاداتك المعتمدة بتصميم أنيق ومتناسق مع هوية QOYN.",
    heroStripTitle: "شهاداتي",
    heroStripText: "افتح شهاداتك واعرضها واطبعها بسهولة من مكان واحد.",
    heroChip: "إنجازات معتمدة",
    libraryTitle: "مكتبة الشهادات",
    loading: "جارٍ تحميل الشهادات...",
    empty: "ما عندك شهادات لحد الآن.",
    issued: "تاريخ الإصدار",
    view: "عرض الشهادة",
    badge: "شهادة"
  },
  en: {
    navHome: "Home",
    navCertificates: "Certificates",
    navBack: "Back",
    navLogout: "Logout",
    heroTitle: '<span class="navy">QOYN</span> Certificates',
    heroSubtitle: "View your verified certificates in a polished experience that matches the QOYN identity.",
    heroStripTitle: "My Certificates",
    heroStripText: "Open, view, and print your certificates from one beautiful place.",
    heroChip: "Certified achievements",
    libraryTitle: "Your Certificate Library",
    loading: "Loading certificates...",
    empty: "You do not have any certificates yet.",
    issued: "Issued at",
    view: "Open Certificate",
    badge: "Certificate"
  }
};

function applyLanguage(lang){
  currentLang = lang;
  localStorage.setItem("cert_lang", lang);

  document.documentElement.lang = lang;
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  document.body.dir = lang === "ar" ? "rtl" : "ltr";

  const t = translations[lang];

  document.getElementById("navHome").textContent = t.navHome;
  document.getElementById("navCertificates").textContent = t.navCertificates;
  document.getElementById("navBack").textContent = t.navBack;
  document.getElementById("navLogout").textContent = t.navLogout;
  document.getElementById("heroTitle").innerHTML = t.heroTitle;
  document.getElementById("heroSubtitle").textContent = t.heroSubtitle;
  document.getElementById("heroStripTitle").textContent = t.heroStripTitle;
  document.getElementById("heroStripText").textContent = t.heroStripText;
  document.getElementById("heroChip").textContent = t.heroChip;
  document.getElementById("libraryTitle").textContent = t.libraryTitle;

  langAr.classList.toggle("active", lang === "ar");
  langEn.classList.toggle("active", lang === "en");

  loadCerts();
}

langAr.addEventListener("click", () => applyLanguage("ar"));
langEn.addEventListener("click", () => applyLanguage("en"));

async function apiGet(f){
  const r = await fetch(API_BASE + "/" + f, { credentials:"include" });
  return r.json();
}

function escapeHtml(s){
  return (s ?? "").toString()
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

async function loadCerts(){
  const t = translations[currentLang];
  msg.textContent = t.loading;
  list.innerHTML = "";
  certCount.textContent = "0";

  const r = await apiGet("certificate_my.php");
  const items = (r && (r.certificates || r.items)) ? (r.certificates || r.items) : [];

  if (!items.length){
    msg.innerHTML = `<div class="empty-state">${t.empty}</div>`;
    return;
  }

  msg.textContent = "";
  certCount.textContent = String(items.length);

  items.forEach((c, index) => {
    const id = c.id;
    const title = c.title || t.badge;
    const issued = c.issued_at || "";
    const viewUrl = c.view_url || ("/utbn-backend/api/certificate_view.php?id=" + id);

    const row = document.createElement("div");
    row.className = "item";
    row.innerHTML = `
      <div class="item-main">
        <div class="cert-topline">
          <span class="cert-badge">#${index + 1}</span>
        </div>
        <div><b>${escapeHtml(title)}</b></div>
        <div class="cert-date">${t.issued}: ${escapeHtml(issued)}</div>
      </div>

      <div class="cert-actions">
        <a class="btn view-btn" href="${viewUrl}" target="_blank" rel="noopener">${t.view}</a>
      </div>
    `;
    list.appendChild(row);
  });
}

const navWrap = document.getElementById("navWrap");
const progressBar = document.getElementById("progressBar");

function updateScrollUI(){
  const y = window.scrollY || document.documentElement.scrollTop;
  navWrap.classList.toggle("scrolled", y > 10);

  const doc = document.documentElement;
  const scrollTop = doc.scrollTop;
  const scrollHeight = doc.scrollHeight - doc.clientHeight;
  const p = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
  progressBar.style.width = p.toFixed(2) + "%";
}

window.addEventListener("scroll", updateScrollUI, {passive:true});
updateScrollUI();

applyLanguage(currentLang);
</script>
</body>
</html>