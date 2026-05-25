
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// إذا عندك role ومخزّن "partner" استخدمه (اختياري)
if (isset($_SESSION["role"]) && $_SESSION["role"] !== "partner") {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="en" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Company Dashboard</title>

    <!-- Fonts (نفس الرئيسية) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
:root{--navy:#082A5E;--blue:#2F6BFF;--cyan:#22C7B8;--bg:#F6F7FB;--text:#0B2454;--muted:#52627A;--line:#DDE7F5;--card:#fff;--shadow:0 18px 45px rgba(8,42,94,.07);--container:1320px}
*{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);background:var(--bg);overflow-x:hidden}body::before{content:"";position:fixed;inset:0;pointer-events:none;background:radial-gradient(circle at 96% 38%,rgba(47,107,255,.08) 0 1px,transparent 2px) 0 0/28px 28px,radial-gradient(circle at 93% 76%,rgba(34,199,184,.08) 0 1px,transparent 2px) 0 0/28px 28px;opacity:.75;z-index:-1}
.nav-wrap{position:fixed;top:0;left:0;right:0;z-index:1000;padding:18px 18px 0;background:transparent;transition:.25s ease}.nav-wrap.scrolled{padding-top:10px}.nav{width:min(var(--container),calc(100% - 16px));margin:0 auto;height:68px;padding:0 32px;display:flex;align-items:center;gap:22px;direction:ltr;background:rgba(255,255,255,.9);border:1px solid rgba(8,42,94,.06);border-radius:0 0 18px 18px;box-shadow:0 14px 40px rgba(8,42,94,.05);backdrop-filter:blur(12px)}.logo{font-family:"Montserrat",sans-serif;font-weight:800;font-size:32px;letter-spacing:.8px;color:var(--navy);text-decoration:none;line-height:1}.nav-spacer{flex:1}.nav-links{display:flex;align-items:center;gap:36px;margin:0;padding:0;list-style:none;direction:ltr}.nav-links a{position:relative;display:inline-flex;align-items:center;min-height:38px;color:#111827;text-decoration:none;font-size:13px;font-weight:600;padding:7px 0;transition:.2s ease;white-space:nowrap}.nav-links a::after{content:"";position:absolute;left:50%;bottom:0;width:0;height:2px;background:var(--blue);transform:translateX(-50%);transition:.2s ease;border-radius:999px}.nav-links a:hover{color:var(--blue);transform:translateY(-1px)}.nav-links a:hover::after,.nav-links a.active::after{width:100%}.nav-links a.active{color:var(--blue);font-weight:700;background:transparent!important}.nav-login{padding:11px 24px!important;border:1px solid #D8E2F0;border-radius:999px;color:var(--navy)!important;font-weight:800!important;box-shadow:0 6px 18px rgba(8,42,94,.03)}.nav-login::after{display:none}.nav-login:hover{background:var(--navy);color:#fff!important;transform:translateY(-2px)}.nav-monkey{width:43px;height:54px;object-fit:contain;display:block;flex:0 0 auto}.progress{display:none}.progress>div{display:none}
main{padding:6px 0 0}section{min-height:680px;width:min(var(--container),calc(100% - 28px));margin:0 auto 18px;padding:126px 50px 44px;display:flex;align-items:center;background:#fff;border:1px solid rgba(8,42,94,.05);border-radius:16px;box-shadow:var(--shadow);scroll-margin-top:90px;position:relative;overflow:hidden}.container{max-width:none;width:100%;margin:0 auto;padding:0;direction:ltr}h1,h2,h3,.phaseTitle,.about-headline{font-family:"Montserrat",sans-serif;color:var(--navy);letter-spacing:-.04em}.muted{color:#324462;font-size:13px;line-height:1.9}.t-navy{color:var(--navy)}
#partner{padding-top:120px;background:#fff}#partner::after,#about::after{content:"";position:absolute;right:52px;bottom:36px;width:120px;height:80px;opacity:.55;background:radial-gradient(circle,#C9D8F3 1.7px,transparent 2px) 0 0/26px 26px}.partner-hero-inner{width:100%;display:grid;grid-template-columns:.95fr 1.15fr;align-items:center;gap:50px;direction:ltr;position:relative;z-index:1}.partner-left{min-width:0}.partner-title-wrap{position:relative;width:auto}.partner-welcome{display:none}.partner-big{margin:0;font-family:"Montserrat",sans-serif;font-weight:800;font-size:0;line-height:1.12;color:var(--navy);letter-spacing:-.05em;text-transform:none}.partner-big::before{content:"Partner with QOYN";font-size:48px;white-space:pre}.partner-hero-text{max-width:520px;margin:24px 0 0;color:#30415E;font-size:18px;line-height:1.9}.partner-actions{display:flex;gap:24px;align-items:center;margin-top:36px;flex-wrap:wrap}.hero-chip{position:absolute;left:49%;top:47%;transform:translate(-50%,-50%);z-index:4;width:185px;min-height:116px;background:#fff;border-radius:16px;box-shadow:0 18px 45px rgba(8,42,94,.11);border:1px solid rgba(8,42,94,.06);padding:22px 24px;color:#30415E;font-size:13px;line-height:1.55}.hero-chip .mini-icon{margin-bottom:12px}.partner-visual{position:relative;min-height:460px;display:flex;align-items:flex-end;justify-content:center}.partner-visual::before{content:"";position:absolute;width:510px;height:510px;border-radius:50%;right:16px;top:22px;background:#EDF4FF;box-shadow:inset 0 0 0 54px #DDE9FF;z-index:0}.partner-visual::after{content:"";position:absolute;width:420px;height:420px;border-radius:50%;right:61px;top:68px;border:32px solid rgba(197,215,250,.7);border-left-color:transparent;border-bottom-color:transparent;transform:rotate(25deg);z-index:1}.partner-visual img{position:relative;z-index:2;width:min(100%,450px);height:505px;object-fit:contain;object-position:bottom;filter:drop-shadow(0 22px 34px rgba(8,42,94,.16));transform:none}.stats-row{position:absolute;left:50px;right:50px;bottom:58px;display:grid;grid-template-columns:repeat(3,1fr);gap:34px;z-index:3;max-width:760px}.stat{display:flex;align-items:center;gap:16px}.stat-icon,.mini-icon,.card-icon{width:46px;height:46px;border-radius:12px;background:#EEF4FF;color:#1355D8;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto}.stat strong{display:block;font-family:"Montserrat";font-size:24px;color:var(--navy);line-height:1}.stat span{display:block;font-size:12px;color:#33425D;margin-top:7px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:14px 28px;border-radius:12px;text-decoration:none;border:0;font-family:"Montserrat",sans-serif;font-weight:800;font-size:13px;cursor:pointer;transition:.2s ease;white-space:nowrap}.btn.primary{background:var(--navy);color:#fff;box-shadow:0 12px 24px rgba(8,42,94,.18)}.btn.primary:hover{transform:translateY(-2px);background:#0B3678;box-shadow:0 18px 32px rgba(8,42,94,.22)}.btn.ghost{background:#fff;color:var(--navy);border:1px solid #DCE6F3;box-shadow:none}.btn.ghost:hover{transform:translateY(-2px);border-color:#BFD0EA;background:#F9FBFF}
#about{align-items:flex-start;padding-top:130px;background:#fff;min-height:680px}.about-split{width:100%;direction:ltr}.about-top{background:transparent;min-height:0;height:auto;padding:0;display:grid;grid-template-columns:.72fr 1.28fr;align-items:start;text-align:left;gap:56px}.about-top-inner{max-width:440px;padding:0}.eyebrow{font-family:"Montserrat";font-size:12px;font-weight:800;text-transform:uppercase;color:#1355D8;margin-bottom:18px;letter-spacing:.02em}.about-headline{font-size:44px;line-height:1.25;margin:0 0 24px;font-weight:800}.about-subtext{font-size:15px;color:#30415E;line-height:1.9;margin:0;max-width:420px}.about-steps{position:relative;padding-top:62px}.about-line{position:absolute;top:28px;left:8%;right:8%;height:1px;background:linear-gradient(90deg,#7869FF,#4596FF,#23C7B6)}.about-numbers{position:absolute;top:0;left:0;right:0;display:grid;grid-template-columns:repeat(3,1fr)}.about-num{justify-self:center;width:38px;height:38px;border-radius:50%;background:#fff;border:1.5px solid currentColor;color:#6D63FF;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800}.about-num:nth-child(2){color:#2F89FF}.about-num:nth-child(3){color:#1FC3B0}.about-bottom{padding:0;background:transparent}.about-phase-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:56px;text-align:left}.about-phase-card{padding:0}.about-phase-card .card-icon{width:88px;height:88px;border-radius:50%;margin:0 auto 28px;background:#F1EDFF;color:#6D63FF}.about-phase-card:nth-child(2) .card-icon{background:#EEF5FF;color:#1784FF}.about-phase-card:nth-child(3) .card-icon{background:#E8FAF7;color:#20BFAF}.about-phase-title{font-family:"Montserrat";font-size:17px;line-height:1.45;color:var(--navy);font-weight:800;margin:0 0 18px}.about-phase-desc{font-size:13.5px;line-height:2;color:#324462;margin:0;max-width:250px}.about-circles{display:none}
#paths{display:block;min-height:560px;padding-top:122px;background:#fff}.topbar{direction:ltr;text-align:left;margin-bottom:30px}.topbar h1,#paths .topbar h1,#phases .topbar h1{font-size:34px;line-height:1.2;margin:0 0 8px;font-weight:800;color:var(--navy)}#paths .topbar{display:block;text-align:left}.pathsGrid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:34px;align-items:stretch}.phaseCard{background:#fff;border:1px solid #DAE4F1;border-radius:18px;padding:30px 28px;min-height:205px;display:flex;flex-direction:column;justify-content:space-between;box-shadow:none;transition:.22s ease;text-align:left}.phaseCard:hover{transform:translateY(-6px);box-shadow:0 24px 45px rgba(8,42,94,.10);border-color:#C6D6EC}.phaseBadge{display:none}.phaseTitle{font-size:17px;line-height:1.35;margin:0 0 14px;color:var(--navy);font-weight:800;letter-spacing:-.02em}.phaseDesc{font-size:14px;line-height:2;color:#324462;margin:0}.openBtn{align-self:flex-start;margin-top:26px;padding:12px 28px;border-radius:10px}.pathIcon{width:70px;height:70px;border-radius:50%;background:#F1EDFF;color:#6D63FF;display:flex;align-items:center;justify-content:center;margin:0 0 24px}.pathsGrid .phaseCard:nth-child(2) .pathIcon{background:#EEF5FF;color:#1682FF}.pathsGrid .phaseCard:nth-child(3) .pathIcon{background:#E8FAF7;color:#20BFAF}.pathsGrid .phaseCard:nth-child(4) .pathIcon{background:#FFF1E6;color:#FF8A38}.list{display:grid!important}.list[style*="display:none"]{display:none!important}.item{display:flex;border-top:1px solid #E6EEF7;padding-top:14px;margin-top:12px}
#phases{display:block;padding-top:150px;background:#fff;min-height:640px;text-align:left;direction:ltr}.phase-progress{position:relative;margin:36px 0 50px}.phase-progress .about-line{position:static;display:block;margin:0 34px}.phase-progress .about-numbers{top:-19px}.phaseGrid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:38px}.phaseGrid .phaseCard{min-height:330px;padding:34px}.phaseGrid .card-icon{width:76px;height:76px;border-radius:50%;margin:0 0 34px;background:#F1EDFF;color:#6D63FF}.phaseGrid .phaseCard:nth-child(2) .card-icon{background:#EEF5FF;color:#1682FF}.phaseGrid .phaseCard:nth-child(3) .card-icon{background:#E8FAF7;color:#20BFAF}.phaseGrid .phaseTitle{font-size:22px;margin-bottom:22px}.phaseGrid .phaseDesc{max-width:270px}.hintBar{display:none}
#social{min-height:auto;background:#082A5E;color:#fff;display:block;padding:40px 50px 26px;margin-top:-10px}.footer2-grid{display:grid;grid-template-columns:1.3fr .75fr .9fr;gap:80px;direction:ltr}.footer2-brand{font-family:"Montserrat";font-weight:800;font-size:24px;letter-spacing:.5px;margin-bottom:18px}.footer2-brand::before{content:"◐";font-size:28px;color:#A896FF;margin-right:8px;vertical-align:-2px}.footer2-desc{max-width:330px;margin:0;color:rgba(255,255,255,.9);font-size:13px;line-height:1.9}.footer2-title{font-weight:800;margin-bottom:16px}.footer2-links{display:grid;gap:10px}.footer2-links a{color:#fff;text-decoration:none;font-size:14px;transition:.2s}.footer2-links a:hover{color:#CFE0FF;transform:translateX(3px)}.social-icons{display:flex;gap:12px}.social-icon{width:42px;height:42px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.12);color:#fff;transition:.2s}.social-icon:hover{transform:translateY(-3px);background:rgba(255,255,255,.2)}.social-icon svg{width:21px;height:21px;fill:currentColor}.footer2-divider{height:1px;background:rgba(255,255,255,.18);margin:34px 0 14px}.footer2-bottom{font-size:13px;color:rgba(255,255,255,.85);direction:ltr;text-align:left}
@media (max-width:1100px){.nav-links{gap:16px}.pathsGrid{grid-template-columns:repeat(2,1fr)}.partner-hero-inner{grid-template-columns:1fr}.stats-row{position:relative;left:auto;right:auto;bottom:auto;margin-top:28px}.hero-chip{display:none}.about-top{grid-template-columns:1fr}.about-steps{padding-top:70px}}
@media (max-width:760px){.nav{padding:0 18px;overflow-x:auto}.nav-links{gap:14px}.nav-monkey{display:none}section{width:calc(100% - 16px);padding:108px 22px 32px;min-height:auto}.partner-big::before{font-size:34px}.partner-hero-text{font-size:15px}.partner-visual{min-height:340px}.partner-visual::before{width:320px;height:320px;right:50%;transform:translateX(50%);top:20px}.partner-visual::after{display:none}.partner-visual img{height:340px}.stats-row,.phaseGrid,.pathsGrid,.about-phase-grid,.footer2-grid{grid-template-columns:1fr}.about-headline{font-size:32px}.about-line{left:5%;right:5%}.about-phase-desc{max-width:none}.phaseGrid .phaseCard{min-height:auto}.footer2-grid{gap:28px}}
    

/* ===== FINAL FIX: connected full-screen layout + fixed full-width navbar ===== */
html,body{width:100%;min-height:100%;background:#fff !important;overflow-x:hidden;}
body::before{opacity:.45;z-index:0;}
.nav-wrap,.nav-wrap.scrolled{position:fixed !important;top:0 !important;left:0 !important;right:0 !important;width:100% !important;padding:0 !important;margin:0 !important;background:rgba(255,255,255,.94) !important;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:0 10px 34px rgba(8,42,94,.06);z-index:9999 !important;}
.nav{width:100% !important;max-width:none !important;height:86px !important;margin:0 !important;padding:0 clamp(28px,4vw,58px) !important;border-radius:0 !important;border:0 !important;border-bottom:1px solid rgba(8,42,94,.08) !important;background:transparent !important;box-shadow:none !important;}
.logo{font-size:34px !important;}
main{position:relative;z-index:1;padding:86px 0 0 !important;margin:0 !important;background:#fff !important;}
main section,section{width:100% !important;max-width:none !important;margin:0 !important;border:0 !important;border-radius:0 !important;box-shadow:none !important;background:#fff !important;scroll-margin-top:86px !important;overflow:hidden;}
section > .container,.partner-hero-inner,.about-split,#social > .container{width:100% !important;max-width:1440px !important;margin-left:auto !important;margin-right:auto !important;}
#partner{min-height:calc(100vh - 86px) !important;padding:70px clamp(34px,5vw,70px) 54px !important;}
#about{min-height:680px !important;padding:120px clamp(34px,5vw,70px) 80px !important;}
#paths{min-height:560px !important;padding:92px clamp(34px,5vw,70px) 64px !important;}
#phases{min-height:680px !important;padding:92px clamp(34px,5vw,70px) 84px !important;}
#social{width:100% !important;margin:0 !important;border-radius:0 !important;border:0 !important;box-shadow:none !important;padding:48px clamp(34px,5vw,70px) 28px !important;background:#082A5E !important;}
.partner-hero-inner{min-height:560px;}
.stats-row{left:0 !important;right:auto !important;bottom:20px !important;}
#partner::after,#about::after{right:clamp(36px,5vw,76px) !important;}
.nav-links a.active{color:var(--blue) !important;}
@media (max-width:1100px){.nav{height:auto !important;min-height:78px !important;gap:14px !important;}main{padding-top:78px !important;}#partner,#about,#paths,#phases{padding-left:28px !important;padding-right:28px !important;}.stats-row{bottom:auto !important;}}
@media (max-width:760px){.nav{padding:0 18px !important;border-radius:0 !important;}main section,section{width:100% !important;margin:0 !important;border-radius:0 !important;}#partner,#about,#paths,#phases{padding-left:20px !important;padding-right:20px !important;}}


/* ===== Hero refinements requested: smaller first screen + fixed circle/person/card alignment ===== */
#partner{
    min-height:560px !important;
    padding-top:26px !important;
    padding-bottom:28px !important;
}
#partner .partner-hero-inner{
    min-height:500px !important;
    grid-template-columns:.88fr 1.12fr !important;
    gap:22px !important;
    align-items:center !important;
}
.partner-left{
    padding-top:0 !important;
    align-self:center !important;
    transform:translateY(-8px);
}
.partner-big::before{
    font-size:46px !important;
    line-height:1.06 !important;
}
.partner-hero-text{
    margin-top:18px !important;
    max-width:560px !important;
    font-size:17px !important;
    line-height:1.75 !important;
}
.partner-actions{
    margin-top:28px !important;
}
.partner-visual{
    min-height:468px !important;
    align-self:center !important;
    justify-content:center !important;
    align-items:flex-end !important;
    transform:translateX(-12px);
}
.partner-visual::before{
    width:430px !important;
    height:430px !important;
    right:auto !important;
    left:50% !important;
    top:18px !important;
    transform:translateX(-50%) !important;
    background:#EEF5FF !important;
    box-shadow:inset 0 0 0 46px #DCE9FF !important;
}
.partner-visual::after{
    width:350px !important;
    height:350px !important;
    right:auto !important;
    left:50% !important;
    top:62px !important;
    transform:translateX(-50%) rotate(22deg) !important;
    border-width:27px !important;
    border-color:rgba(196,214,249,.72) !important;
    border-left-color:transparent !important;
    border-bottom-color:transparent !important;
}
.partner-visual img{
    width:390px !important;
    height:435px !important;
    object-fit:contain !important;
    object-position:center bottom !important;
    filter:drop-shadow(0 16px 24px rgba(8,42,94,.12)) !important;
    transform:translateY(5px) !important;
}
.hero-chip{
    left:51.5% !important;
    top:45% !important;
    width:178px !important;
    min-height:106px !important;
    padding:20px 22px !important;
    border-radius:15px !important;
    line-height:1.55 !important;
    box-shadow:0 18px 40px rgba(8,42,94,.10) !important;
}
.hero-chip .mini-icon{
    width:43px !important;
    height:43px !important;
    margin-bottom:10px !important;
}
.stats-row{
    bottom:24px !important;
    max-width:730px !important;
}
#partner::after{
    bottom:22px !important;
}
@media (max-width:1100px){
    #partner .partner-hero-inner{grid-template-columns:1fr !important;min-height:auto !important;}
    .partner-left{transform:none !important;}
    .partner-visual{transform:none !important;}
    .hero-chip{display:none !important;}
}
@media (max-width:760px){
    #partner{padding-top:24px !important;}
    .partner-visual::before{width:320px !important;height:320px !important;}
    .partner-visual img{height:335px !important;width:300px !important;}
}

/* ===== Paths playlists button placement fix ===== */
#paths .pathsGrid{
    align-items:start !important;
}
#paths .phaseCard{
    overflow:hidden !important;
    min-width:0 !important;
}
#paths .phaseCard > div:nth-child(2){
    justify-content:flex-start !important;
}
#paths .phaseCard > div:nth-child(2) .btn{
    padding:11px 22px !important;
    min-width:150px !important;
}
#paths .list{
    width:100% !important;
    margin-top:18px !important;
    display:grid !important;
    gap:0 !important;
}
#paths .list[style*="display:none"]{
    display:none !important;
}
#paths .item{
    display:block !important;
    width:100% !important;
    padding:16px 0 18px !important;
    margin:0 !important;
    border-top:1px solid #E6EEF7 !important;
}
#paths .item > div{
    width:100% !important;
    flex:none !important;
}
#paths .item b{
    display:block !important;
    max-width:100% !important;
    overflow-wrap:anywhere !important;
    word-break:normal !important;
}
#paths .item .muted{
    margin-top:4px !important;
    overflow-wrap:anywhere !important;
}
#paths .item .btn{
    display:flex !important;
    width:100% !important;
    max-width:170px !important;
    min-height:40px !important;
    margin:12px 0 0 !important;
    padding:10px 14px !important;
    border-radius:11px !important;
    font-size:12px !important;
    line-height:1.2 !important;
    white-space:normal !important;
    text-align:center !important;
}
#paths .item .btn + .btn{
    margin-top:8px !important;
}
@media (max-width:1100px){
    #paths .item .btn{max-width:190px !important;}
}


/* ===== FINAL HERO CIRCLE/PERSON ONLY FIX =====
   Keeps all other sections/functions unchanged. */
#partner .partner-visual{
    min-height:468px !important;
    align-items:flex-end !important;
    justify-content:center !important;
    overflow:visible !important;
    transform:translateX(-18px) !important;
}
#partner .partner-visual::before{
    width:430px !important;
    height:430px !important;
    left:50% !important;
    right:auto !important;
    top:54px !important;
    transform:translateX(-50%) !important;
    border-radius:50% !important;
    background:radial-gradient(circle at 52% 45%,#F7FBFF 0 41%,#EAF3FF 42% 61%,#DCE9FF 62% 100%) !important;
    box-shadow:inset 0 0 0 45px rgba(219,232,255,.95) !important;
    z-index:0 !important;
}
#partner .partner-visual::after{
    width:350px !important;
    height:350px !important;
    left:50% !important;
    right:auto !important;
    top:98px !important;
    transform:translateX(-50%) rotate(22deg) !important;
    border-radius:50% !important;
    border:27px solid rgba(196,214,249,.72) !important;
    border-left-color:transparent !important;
    border-bottom-color:transparent !important;
    z-index:1 !important;
}
#partner .partner-visual img{
    width:392px !important;
    height:440px !important;
    max-width:none !important;
    object-fit:contain !important;
    object-position:center bottom !important;
    position:relative !important;
    z-index:2 !important;
    transform:translateY(-38px) !important;
    filter:drop-shadow(0 16px 24px rgba(8,42,94,.12)) !important;
}
@media (max-width:760px){
    #partner .partner-visual{transform:none !important;min-height:360px !important;}
    #partner .partner-visual::before{width:320px !important;height:320px !important;top:38px !important;}
    #partner .partner-visual::after{width:258px !important;height:258px !important;top:72px !important;border-width:20px !important;}
    #partner .partner-visual img{width:300px !important;height:335px !important;transform:translateY(-28px) !important;}
}
    </style>
</head>

<body>

<!-- NAV -->
<header class="nav-wrap" id="navWrap">
    <div class="progress"><div id="progressBar"></div></div>

    <nav class="nav">
        <a class="logo" href="#partner">QOYN</a>
        <div class="nav-spacer"></div>

        <ul class="nav-links" id="navLinks">
            <li><a href="company_reviews.php">Student Projects</a></li>
            <li><a href="company_chat.php">Our Chat</a></li>
            <li><a href="company_analytics.php">Profile</a></li>
            <li><a data-section="about" href="#about">About</a></li>
            <li><a data-section="paths" href="#paths">Paths</a></li>
            <li><a data-section="phases" href="#phases">Phases</a></li>
            <li><a href="login.html" class="nav-login">Logout</a></li>
        </ul>

        <img src="uploads/MONKEY.png" class="nav-monkey" alt="MONKEY Logo">
    </nav>
</header>

<main>

   <section id="partner" class="partner-hero">
    <div class="partner-hero-inner">
        <div class="partner-left">
            <div class="partner-title-wrap">
                <div class="partner-welcome">Welcome</div>
                <h2 class="partner-big">PARTNER</h2>
            </div>

            <p class="partner-hero-text">
                A dedicated space for companies to collaborate with QOYN by providing real learning experiences,
                skill-based challenges, and professional projects that empower students and connect talent to industry needs.
            </p>

            <div class="partner-actions">
                <a class="btn primary" href="#phases">Become a Partner <span aria-hidden="true">→</span></a>
                <a class="btn ghost" href="#about">Learn More</a>
            </div>
        </div>

        <div class="hero-chip">
            <span class="mini-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M16 11a4 4 0 1 0-8 0" stroke="currentColor" stroke-width="1.8"/><path d="M4 20c.7-4 3.5-6 8-6s7.3 2 8 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M18 8v4M20 10h-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
            Building Future Talents Together
        </div>

        <div class="partner-visual">
            <img src="uploads/aa.png" alt="Company">
        </div>

        <div class="stats-row" aria-label="QOYN statistics">
            <div class="stat"><span class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M16 11a4 4 0 1 0-8 0" stroke="currentColor" stroke-width="1.8"/><path d="M4 20c.7-4 3.5-6 8-6s7.3 2 8 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><div><strong>20K+</strong><span>Students Empowered</span></div></div>
            <div class="stat"><span class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 8h14v12H5z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8V5h8v3" stroke="currentColor" stroke-width="1.8"/><path d="m9 14 2 2 4-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div><strong>500+</strong><span>Projects Delivered</span></div></div>
            <div class="stat"><span class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M5 8h14v12H5z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8V5h8v3" stroke="currentColor" stroke-width="1.8"/><path d="m9 14 2 2 4-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div><strong>100+</strong><span>Partner Companies</span></div></div>
        </div>
    </div>
</section>

  <!-- 2) About -->
<section id="about">
    <div class="about-split" dir="ltr">

        <!-- Top -->
        <div class="about-top">
            <div class="about-top-inner">
                <h2 class="about-headline">
                    Your Role Across the <span class="t-navy">QOYN</span> Phases
                </h2>
                <p class="about-subtext">We guide students through a structured 3-phase journey designed to turn learning into real-world impact.</p>

            </div>

            <div class="about-steps">
                <span class="about-line"></span>
                <div class="about-numbers"><span class="about-num">01</span><span class="about-num">02</span><span class="about-num">03</span></div>

                <div class="about-bottom">
                    <div class="container">
                        <div class="about-phase-grid">

                    <!-- Left -->
                    <div class="about-phase-card">
                        <div class="card-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M6 10v4.5c0 1.7 2.7 3.5 6 3.5s6-1.8 6-3.5V10" stroke="currentColor" stroke-width="1.7"/></svg></div>
                        <div class="about-phase-title">Individual Company Projects</div>
                        <p class="about-phase-desc">
                            Provide unique short-term projects that simulate real work scenarios for students. Through your projects, students apply their skills, receive AI evaluation, and gain valuable hands-on experience
                        </p>
                    </div>

                    <!-- Middle -->
                    <div class="about-phase-card">
                        <div class="card-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="m8 16-4-4 4-4M16 8l4 4-4 4M14 5l-4 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                        <div class="about-phase-title">Individual Company Projects</div>
                        <p class="about-phase-desc">
                            Provide unique short-term projects that simulate real work scenarios for students. Through your projects, students apply their skills, receive AI evaluation, and gain valuable hands-on experience
                        </p>
                    </div>

                    <!-- Right -->
                    <div class="about-phase-card">
                        <div class="card-icon" aria-hidden="true"><svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v12H4z" stroke="currentColor" stroke-width="1.7"/><path d="M9 7V5h6v2M8 12h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></div>
                        <div class="about-phase-title">Large Collaborative Industry Projects</div>
                        <p class="about-phase-desc">
                            Launch real company-scale projects where AI forms multidisciplinary student teams. Companies can observe performance, mentor teams, and recruit top talent before graduation.
                        </p>
                    </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<section id="paths">
  <div class="container" dir="ltr">
    <div class="topbar">
      <div>
        <h1>Available Paths</h1>
        <div class="muted">Choose a path, then pick the playlists you want to clone to your company.</div>
      </div>
    </div>

    <div id="pathsBox" class="pathsGrid" style="margin-top:16px"></div>
  </div>
</section>

    <!-- 3) Phases -->
    <section id="phases">
        <div class="container">

            <div class="topbar">
                <div>
                    <h1>Manage Your Contributions</h1>
                    <div class="muted">Welcome <b id="companyName">...</b></div>
                    <div class="muted" style="margin-top:6px"> Choose the stage and complete the steps in order. </div>
                </div>
            </div>

            <div class="phase-progress" aria-hidden="true">
                <span class="about-line"></span>
                <div class="about-numbers"><span class="about-num">01</span><span class="about-num">02</span><span class="about-num">03</span></div>
            </div>

            <div class="phaseGrid">

                <div class="phaseCard">
                    <div>
                        <div class="card-icon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M3 7h7l2 2h9v10H3V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></div>
                        <span class="phaseBadge">PHASE 1</span>
                        <div class="phaseTitle">Make your video</div>
                        <p class="phaseDesc">Write a title for the video, upload it, add questions with answers and explanations, then submit the stage.</p>
                    </div>
                    <a class="btn primary openBtn" href="company_phase1.php">Open</a>
                </div>

                <div class="phaseCard">
                    <div>
                        <div class="card-icon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M3 7h7l2 2h9v10H3V7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></div>
                        <span class="phaseBadge">PHASE 2</span>
                        <div class="phaseTitle">Course Project</div>
                        <p class="phaseDesc"> Select a course or enter it manually, then write a clear project with a full description for the students.</p>
                    </div>
                    <a class="btn primary openBtn" href="company_phase2.php">Open</a>
                </div>

                <div class="phaseCard">
                    <div>
                        <div class="card-icon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M6 4v16M6 5h11l-2 4 2 4H6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/></svg></div>
                        <span class="phaseBadge">PHASE 3</span>
                        <div class="phaseTitle">Big Project (Capstone)</div>
                        <p class="phaseDesc">Define the final capstone project with the requirements and submission method.</p>
                    </div>
                    <a class="btn primary openBtn" href="company_phase3.php">Open</a>
                </div>

            </div>
        </div>
    </section>

    <!-- 4) Social (Footer) -->
    <section id="social">
        <div class="container" dir="ltr">

            <div class="footer2-grid">

                <div>
                    <div class="footer2-brand">QOYN</div>
                    <p class="footer2-desc">
                        QOYN is an AI-powered platform that transforms learning into measurable value. We guide students through
                        structured skill paths, reward achievements with Coins, and connect them to real projects and career
                        opportunities before graduation.
                    </p>
                </div>

                <div>
                    <div class="footer2-title">Quick Links</div>
                    <nav class="footer2-links">
                        <a href="company_analytics.php">Profile</a>
                        <a href="#about">About</a>
                        <a href="#phases">Phases</a>
                    </nav>
                </div>

                <div>
                    <div class="footer2-title">Follow</div>
                    <div class="social-icons">
                        <a class="social-icon" href="https://www.instagram.com/qoyn.jo?igsh=dnFoZ3pmMWZodzNo" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.8A3.95 3.95 0 0 0 3.8 7.75v8.5a3.95 3.95 0 0 0 3.95 3.95h8.5a3.95 3.95 0 0 0 3.95-3.95v-8.5a3.95 3.95 0 0 0-3.95-3.95h-8.5Zm8.95 1.35a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8A3.2 3.2 0 1 0 12 15.2 3.2 3.2 0 0 0 12 8.8Z"/>
                            </svg>
                        </a>

                        <a class="social-icon" href="https://www.linkedin.com/in/qoyn-jo-0b3aab3aa" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A2.02 2.02 0 1 0 5.3 7.04 2.02 2.02 0 0 0 5.25 3Zm15.19 9.74c0-3.45-1.84-5.05-4.29-5.05-1.98 0-2.87 1.09-3.37 1.86V8.5H9.4c.04.7 0 11.5 0 11.5h3.38v-6.42c0-.34.03-.68.13-.92.27-.68.88-1.38 1.91-1.38 1.35 0 1.9 1.03 1.9 2.55V20H20V13.5c0-.35.01-.51.01-.76Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <div class="footer2-divider"></div>

            <div class="footer2-bottom">
                © 2026 QOYN. All rights reserved.
            </div>
        </div>
    </section>

</main>

<script src="assets/js/company.js"></script>

<script>
    // ===== Navbar scroll behavior + progress =====
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

    window.addEventListener("scroll", updateScrollUI, { passive:true });
    updateScrollUI();

    // ===== Active link highlight by section in view =====
    const links = document.querySelectorAll("#navLinks a[data-section]");
    const sections = Array.from(document.querySelectorAll("main section[id]"));

    function setActive(id){
        links.forEach(a => a.classList.toggle("active", a.dataset.section === id));
    }

    const observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter(e => e.isIntersecting)
            .sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];

        if (visible && visible.target && visible.target.id){
            setActive(visible.target.id);
        }
    }, { threshold: [0.25, 0.4, 0.6] });

    sections.forEach(sec => observer.observe(sec));

    const initial = (location.hash || "#partner").replace("#","");
    setActive(initial);
</script>
<script>
const API = "/utbn-backend/api";

function el(tag, cls){
  const x = document.createElement(tag);
  if(cls) x.className = cls;
  return x;
}

async function getJson(url){
  const r = await fetch(url, { credentials:"include" });
  const j = await r.json().catch(()=> ({}));
  if(!r.ok || !j.ok) throw j;
  return j;
}

async function postForm(url, data){
  const r = await fetch(url, {
    method:"POST",
    credentials:"include",
    headers: {"Content-Type":"application/x-www-form-urlencoded"},
    body: new URLSearchParams(data).toString()
  });
  const j = await r.json().catch(()=> ({}));
  if(!r.ok || !j.ok) throw j;
  return j;
}

function pathIcon(idx){
  const icons = [
    '<svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M6.3 6.3l2.1 2.1M15.6 15.6l2.1 2.1M17.7 6.3l-2.1 2.1M8.4 15.6l-2.1 2.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><rect x="8" y="8" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/></svg>',
    '<svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M9 4a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3M15 4a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3M9 8h6M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
    '<svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7"/><path d="m9 12 2 2 4-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    '<svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M6 5h12v14H6z" stroke="currentColor" stroke-width="1.7"/><path d="M9 15l2-3 2 2 2-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
  ];
  return icons[idx % icons.length];
}

async function loadPaths(){
  const box = document.getElementById("pathsBox");
  if(!box) return;

  box.innerHTML = `<div style="grid-column:1/-1">Loading paths...</div>`;
  let r;
  try{
    r = await getJson(`${API}/company_available_paths.php`);
  }catch(e){
    box.innerHTML = `<div style="grid-column:1/-1">No paths available (or API error).</div>`;
    return;
  }

  box.innerHTML = "";
  if(!r.items || r.items.length === 0){
    box.innerHTML = `<div style="grid-column:1/-1">No paths activated for your company yet.</div>`;
    return;
  }

  for(const [idx, p] of r.items.entries()){
    const card = el("div","phaseCard");
    card.style.minHeight = "auto";

    card.innerHTML = `
      <div>
        <div class="pathIcon" aria-hidden="true">${pathIcon(idx)}</div>
        <div class="phaseTitle">${p.title}</div>
        <div class="muted">Role: <b>${p.role_name || p.role_key}</b> | Path ID: ${p.id}</div>
      </div>
      <div style="display:flex; gap:10px; margin-top:14px; justify-content:flex-end; flex-wrap:wrap">
        <button class="btn ghost">View Playlists</button>
      </div>
      <div class="list" style="margin-top:12px; display:none; gap:10px"></div>
    `;

    const btn = card.querySelector("button");
    const list = card.querySelector(".list");

    btn.onclick = async ()=>{
      if(list.style.display === "block"){
        list.style.display = "none";
        btn.textContent = "View Playlists";
        return;
      }

      btn.textContent = "Loading...";
      list.innerHTML = "";
      list.style.display = "block";

      let pls;
      try{
        pls = await getJson(`${API}/company_path_playlists.php?path_id=${p.id}`);
      }catch(e){
        list.innerHTML = "Failed to load playlists.";
        btn.textContent = "View Playlists";
        return;
      }

      btn.textContent = "Hide Playlists";

      for(const pl of pls.items){
        const row = el("div","item");
        row.style.justifyContent = "space-between";
        row.style.alignItems = "center";
        row.style.gap = "10px";

        row.innerHTML = `
          <div style="flex:1">
            <div><b>${pl.name}</b></div>
<div class="muted">
  Subject: ${pl.template_subject || "-"} | Template ID: ${pl.id}
  ${pl.is_selected ? '<span style="margin-left:8px;color:#2ecc71;font-weight:800">✅ Selected</span>' : ''}
</div>          </div>
          <button class="btn primary">Add to my company</button>
        `;

        const addBtn = row.querySelector("button");
        addBtn.onclick = async ()=>{
  addBtn.disabled = true;
  addBtn.textContent = "Adding...";
  try{
    const rr = await postForm(`${API}/company_clone_template_playlist.php`, {
      template_playlist_id: pl.id,
      path_id: p.id
    });

    addBtn.textContent = "Added ✅";
    addBtn.classList.add("ghost");
    addBtn.disabled = true;

    // ✅ افتح Phase 1 مباشرة على النسخة الجديدة
    const go = document.createElement("a");
    go.className = "btn primary";
    go.textContent = "Open Phase 1";
    go.href = `company_phase1.php?playlist_id=${rr.new_playlist_id}`;
    row.appendChild(go);

  }catch(e){
    addBtn.disabled = false;
    addBtn.textContent = "Add to my company";
    alert("Failed to clone playlist");
  }
};

        list.appendChild(row);
      }
    };

    box.appendChild(card);
  }
}

loadPaths();
</script>
</body>
</html>




