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

require_once __DIR__ . "/../utbn-backend/api/db.php";

$user_id = (int)$_SESSION["user_id"];
$show_phase3_level2_btn = false;
$phase3_level2_project_id = 0;

/*
  الزر يظهر إذا الطالب سلّم على الأقل تسليم واحد في phase3 level1
  من جدول phase3_task_submissions
*/
$sql = "
  SELECT project_id
  FROM phase3_task_submissions
  WHERE student_id = ?
    AND submitted_at IS NOT NULL
  ORDER BY submitted_at DESC
  LIMIT 1
";

$stmt = $conn->prepare($sql);
if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($row && !empty($row["project_id"])) {
    $show_phase3_level2_btn = true;
    $phase3_level2_project_id = (int)$row["project_id"];
  }
}
?>

<!doctype html>
<html lang="en" dir="ltr"><head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>UTBN - Student</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <link rel="stylesheet" href="assets/css/assistant.css"/>

  <!-- ✅ Fonts (QOYN theme) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

 <style>
  .role-btn{
    display:inline-flex;
    align-items:center;
    gap:12px;
    padding:16px 20px;
    border-radius:18px;
    border:1px solid rgba(10,46,93,.10);
    background:#fff;
    cursor:pointer;
    font-weight:800;
    font-size:15px;
    color:#0f172a;
    box-shadow:0 8px 20px rgba(15,23,42,.05);
    transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
  }
  .role-btn:hover{
    box-shadow:0 16px 34px rgba(15,23,42,.10);
    transform:translateY(-2px);
    border-color:rgba(10,46,93,.18);
    background:#fbfdff;
  }
  .role-btn.active{
    border-color:rgba(10,46,93,.45);
    box-shadow:0 0 0 4px rgba(10,46,93,.08), 0 14px 30px rgba(15,23,42,.08);
    background:rgba(10,46,93,.03);
  }
  .role-pill{
    padding:7px 12px;
    border-radius:999px;
    background:rgba(10,46,93,.08);
    border:1px solid rgba(10,46,93,.16);
    color:var(--navy);
    font-weight:900;
    font-size:14px;
  }

  :root{
    --navy:#0A2E5D;
    --navyHover:#144270;
    --navySoft:#1A4B86;
    --yellow:#FFC24A;
    --yellowSoft:#ffd978;
    --bg:#F6F7F9;
    --card:#ffffff;
    --text:#0f172a;
    --textSoft:#334155;
    --muted:#64748b;
    --stroke:rgba(10,46,93,.10);
    --stroke2:rgba(10,46,93,.08);
    --shadow:0 18px 45px rgba(15,23,42,.08);
    --shadow-soft:0 10px 24px rgba(15,23,42,.06);
    --radius:18px;
    --radius-lg:26px;
    --radius-xl:32px;
    --container:1360px;
  }

  *{box-sizing:border-box}

  html{scroll-behavior:smooth}

 body{
  margin:0;
  font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif !important;
  color:var(--text) !important;

  background:
    radial-gradient(circle at 15% 20%, rgba(255,194,74,.10), transparent 25%),
    radial-gradient(circle at 85% 25%, rgba(10,46,93,.06), transparent 28%),
    radial-gradient(circle at 50% 80%, rgba(10,46,93,.04), transparent 35%),
    linear-gradient(180deg, #ffffff 0%, #f9fbff 100%) !important;

  padding-top:0 !important;
}

  .container{
    max-width:1320px !important;
    margin-left:0 !important;
    margin-right:auto !important;
    padding:0 16px 76px !important;
  }

  h1,h2{
    font-family:"Montserrat", sans-serif !important;
    font-weight:800 !important;
    letter-spacing:-.4px;
    color:#111 !important;
  }

  .muted{
    color:var(--muted) !important;
    opacity:1 !important;
    font-size:16px !important;
    line-height:1.9 !important;
  }

  .card{
    background:rgba(255,255,255,.85) !important;
    backdrop-filter:blur(6px);
    border:1px solid rgba(10,46,93,.08) !important;
    box-shadow:var(--shadow-soft) !important;
    border-radius:26px !important;
  }

  .btn{
    font-family:"Montserrat", sans-serif !important;
    font-weight:800 !important;
    letter-spacing:.2px !important;
    background:linear-gradient(135deg, var(--navy) 0%, var(--navySoft) 100%) !important;
    color:#fff !important;
    border:1px solid rgba(10,46,93,.08) !important;
    border-radius:16px !important;
    box-shadow:0 14px 28px rgba(10,46,93,.16) !important;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease !important;
    width:fit-content;
    min-height:60px;
    padding:16px 24px !important;
    font-size:16px !important;
  }

  .btn:hover{
    transform:translateY(-2px) !important;
    background:linear-gradient(135deg, var(--navyHover) 0%, var(--navy) 100%) !important;
    box-shadow:0 20px 42px rgba(10,46,93,.20) !important;
  }

  .btn.ghost,.btn.gray,.btn.primary{
    background:linear-gradient(135deg, var(--navy) 0%, var(--navySoft) 100%) !important;
    color:#fff !important;
  }
  .btn.ghost:hover,.btn.gray:hover,.btn.primary:hover{
    color:#fff !important;
  }

  .input, input, textarea, select,
  input.input, textarea.input, select.input{
    background:#fff !important;
    border:1px solid rgba(10,46,93,.12) !important;
    border-radius:16px !important;
    color:#111 !important;
    font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif !important;
    box-shadow:none !important;
    outline:none !important;
    transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    font-size:17px !important;
  }

  input:focus, textarea:focus, select:focus, .input:focus{
    border-color:rgba(10,46,93,.35) !important;
    box-shadow:0 0 0 4px rgba(10,46,93,.08) !important;
    transform:translateY(-1px);
  }

  .pill{
    background:#fff !important;
    border:1px solid rgba(10,46,93,.08) !important;
    border-radius:999px !important;
    box-shadow:0 10px 24px rgba(15,23,42,.05) !important;
    color:#111 !important;
    font-weight:700;
    padding:12px 16px !important;
    font-size:15px;
  }

  .item{
    background:#fff !important;
    border:1px solid rgba(10,46,93,.08) !important;
    border-radius:22px !important;
    box-shadow:0 12px 26px rgba(15,23,42,.06) !important;
  }

  .badge{
    border-radius:999px !important;
    padding:10px 14px !important;
    font-weight:800 !important;
    font-family:"Montserrat", sans-serif !important;
    font-size:14px !important;
  }

  .badge.ok{
    background:rgba(10,46,93,.08) !important;
    color:var(--navy) !important;
    border:1px solid rgba(10,46,93,.16) !important;
  }

  .qoyn-layout{
    min-height:100vh;
    display:flex;
    width:100%;
    background:var(--bg);
    direction:ltr;
    gap:8px;
  }

  .qoyn-main .container{
    max-width:var(--container) !important;
    padding:0 28px 76px !important;
  }

  .sidebar-link{
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
    text-decoration:none;
    color:rgba(10,46,93,.58);
    padding:14px 8px;
    border-radius:20px;
    transition:background .18s ease, transform .18s ease, color .18s ease, opacity .18s ease;
    font-weight:600;
    text-align:center;
    background:transparent;
    border:0;
  }

  .sidebar-link:hover{
    background:rgba(10,46,93,.05);
    color:var(--navy);
    transform:translateY(-1px);
  }

  .sidebar-link.active{
    color:var(--navy);
    background:rgba(10,46,93,.06);
  }

  .sidebar-icon{
    width:26px;
    height:26px;
    display:flex;
    align-items:center;
    justify-content:center;
    line-height:0;
  }

  .sidebar-icon svg{
    width:24px;
    height:24px;
    display:block;
    stroke:currentColor;
  }

  .sidebar-text{
    display:block;
    font-size:13px;
    line-height:1.5;
    max-width:105px;
    text-align:center;
    word-break:break-word;
    font-family:"Poppins", sans-serif;
    font-weight:600;
  }

  .sidebar-btn{
    appearance:none;
    -webkit-appearance:none;
    font:inherit;
    cursor:pointer;
    text-align:center;
  }

  .sidebar-btn:focus{
    outline:none;
  }

  .phase-link{
    background:transparent !important;
    border:0 !important;
  }

  .qoyn-main{
    width:calc(100% - 150px);
    background:transparent;
    direction:rtl;
  }

  .qoyn-sidebar{
    width:150px;
    min-width:150px;
    color:var(--navy);
    padding:22px 12px 24px;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:18px;
    border-right:1px solid rgba(10,46,93,.10);
    box-shadow:none;
    position:relative;
    background:transparent;
  }

  .sidebar-brand{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin-top:0;
    margin-bottom:22px;
  }

  .sidebar-logo-text{
    text-decoration:none;
    color:var(--navy);
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    font-size:32px;
    letter-spacing:1px;
    line-height:1;
    text-align:center;
    display:block;
  }

  .sidebar-nav{
    margin-top:2px;
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
  }

  .qoyn-welcome{
    margin-bottom:26px;
    text-align:left;
    font-size:18px;
    color:rgba(15,23,42,.72);
    font-weight:600;
    direction:ltr;
    padding:0 2px;
  }

  .flat-section{
    margin-top:36px;
    direction:ltr;
    text-align:left;
  }

  .flat-section h2{
    margin:0 0 18px 0;
    font-size:44px;
    line-height:1.12;
    text-align:left !important;
    color:#0A2E5D !important;
  }

  .flat-inline{
    display:flex;
    gap:14px;
    flex-wrap:wrap;
    align-items:center;
  }

  .flat-stack{
    display:flex;
    flex-direction:column;
    gap:16px;
    align-items:flex-start;
  }

  .small-btn{
    padding:14px 18px !important;
    min-width:auto !important;
    width:fit-content !important;
    font-size:15px !important;
    box-shadow:0 10px 24px rgba(10,46,93,.12) !important;
  }

  .paths-grid{
    display:grid !important;
    grid-template-columns:repeat(4, 1fr) !important;
    gap:22px !important;
    margin-top:12px;
    width:100%;
  }

  .path-select-card{
    width:100% !important;
    min-width:0 !important;
    display:flex;
    flex-direction:column;
    align-items:stretch;
    padding:0;
    overflow:hidden;
    border-radius:24px;
    background:#fff;
    border:1px solid rgba(10,46,93,.08);
    box-shadow:var(--shadow-soft);
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    transform:scale(1.03);
  }

  .path-select-card:hover{
    transform:scale(1.03) translateY(-4px);
    box-shadow:0 20px 42px rgba(15,23,42,.12);
    background:rgba(10,46,93,.02);
    border-color:rgba(10,46,93,.22);
  }

  .path-select-media{
    width:100%;
    height:165px;
    flex:0 0 165px;
    display:block;
    object-fit:cover;
    background:#0B0B0B;
  }

  .path-select-title{
    font-size:15px;
    padding:20px;
    font-family:"Montserrat", sans-serif;
    font-weight:800;
    color:#0A2E5D;
    text-align:left;
    line-height:1.45;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .flat-info{
    display:flex;
    flex-direction:column;
    gap:12px;
    align-items:flex-start;
  }

  .flat-row{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:center;
  }

  .plain-block{
    display:flex;
    flex-direction:column;
    gap:9px;
    align-items:flex-start;
  }

  .plain-block b,.plain-block div,.plain-block span{
    text-align:left;
  }

  .no-box{
    background:transparent !important;
    border:0 !important;
    box-shadow:none !important;
    padding:0 !important;
    border-radius:0 !important;
  }

  .row{margin-top:22px !important}
  .col{margin-top:18px !important}
  .row{
    display:flex !important;
    gap:22px !important;
    align-items:stretch !important;
    flex-wrap:wrap !important;
  }

  .col{
    flex:1 1 360px !important;
    width:auto !important;
    margin-top:0 !important;
  }

  .major-row{margin-top:-6px !important}

  .major-card{
    background:transparent !important;
    border:0 !important;
    box-shadow:none !important;
    border-radius:0 !important;
    padding:0 !important;
    direction:ltr !important;
    text-align:left !important;
    max-width:1020px;
  }

  #majorInput{
    width:540px;
    max-width:100%;
    height:64px;
    font-size:17px;
    padding:16px 18px;
  }

  .major-card h2{
    font-family:"Montserrat", sans-serif;
    font-weight:800;
    margin-bottom:8px;
  }

  .major-card .muted{
    text-align:left !important;
    margin-bottom:12px;
  }

  .major-input-row{
    justify-content:flex-start !important;
  }

  .course-row{margin-top:16px !important}

  .course-card{
    direction:ltr !important;
    text-align:left !important;
    background:transparent !important;
    border:0 !important;
    box-shadow:none !important;
    border-radius:0 !important;
    padding:0 !important;
  }
  .course-card *{
    direction:ltr !important;
    text-align:left !important;
  }
  .course-card input,.course-card textarea{
    text-align:left !important;
  }
  .course-card input[type="file"]{
    direction:ltr !important;
  }

  .coins-card,
  .reward-card{
    background:transparent !important;
    border:0 !important;
    box-shadow:none !important;
    border-radius:0 !important;
    padding:0 !important;
    direction:ltr !important;
    text-align:left !important;
  }

  #rolesCard{
    background:rgba(255,255,255,.88) !important;
    border:1px solid rgba(10,46,93,.08) !important;
    box-shadow:var(--shadow-soft) !important;
    border-radius:24px !important;
    padding:20px !important;
  }

  .course-fields-wrap input,
  .course-fields-wrap textarea,
  .course-fields-wrap input[type="file"]{
    width:100%;
    max-width:1020px;
  }

  #csTitle{
    min-height:64px;
    font-size:17px;
    padding:16px 20px;
  }

  #csDesc{
    min-height:235px;
    font-size:17px;
    padding:18px 20px;
  }

  #csFile{
    min-height:62px;
    padding:16px 18px;
  }

  .stats-two-col{
    display:flex;
    gap:64px;
    align-items:flex-start;
    justify-content:flex-start;
    flex-wrap:wrap;
    margin-top:36px;
    direction:ltr;
  }

  .stats-two-col > section{
    flex:1 1 360px;
    margin-top:0;
  }

  .coins-card,
  .reward-card{
    min-width:0;
  }

  .reward-card .plain-block,
  .coins-card .plain-block{
    gap:8px;
  }

  #generateCertBtn{
    display:none !important;
  }

  @media (max-width:1100px){
    .paths-grid{
      grid-template-columns:repeat(3, minmax(0,1fr)) !important;
    }
  }
@media (max-width:980px){
  .qoyn-layout{flex-direction:column}
  .qoyn-sidebar{
    width:100%;
    min-width:0;
    flex-direction:row;
    justify-content:flex-start;
    align-items:center;
    overflow-x:auto;
    padding:16px 14px;
    border-right:0;
    border-bottom:1px solid rgba(10,46,93,.10);
    gap:14px;
  }
  .sidebar-brand{
    width:auto;
    flex:0 0 auto;
    margin-bottom:0;
    align-items:center;
    margin-top:6px;
  }

  .sidebar-logo-text{
    font-size:22px;
  }
  .sidebar-nav{
    width:auto;
    flex-direction:row;
    align-items:flex-start;
    gap:8px;
    flex:0 0 auto;
    margin-top:0;
  }
  .sidebar-link{
    width:96px;
    min-height:80px;
    flex:0 0 auto;
    padding:10px 6px;
  }
  .qoyn-main{width:100%}
  .qoyn-main .container{padding:20px 18px 54px !important}
  .flat-section h2{font-size:30px}
  .paths-grid{grid-template-columns:repeat(2, minmax(0,1fr)) !important}
  .row{display:block !important}
  .col{width:100% !important;margin-top:18px !important}
  .container{padding:22px 18px 54px !important}
  h1{font-size:28px !important}
  .stats-two-col{
    flex-direction:column;
    gap:24px;
  }
  .stats-two-col > section{
    width:100%;
  }
}

  @media (max-width:640px){

    .qoyn-sidebar{
      padding:16px 12px;
    }

    .container,
    .qoyn-main .container{
      padding:0 10px 60px !important;
    }

    .flat-section{
      margin-top:24px;
    }

    .flat-section h2{
      font-size:24px;
    }

    .qoyn-welcome{
      margin-bottom:16px;
      font-size:16px;
    }

    .paths-grid{
      grid-template-columns:1fr !important;
    }

    #majorInput{
      width:100%;
    }

    .flat-inline,
    .major-input-row{
      width:100%;
    }

    .flat-inline .btn,
    .major-input-row .btn{
      width:100%;
    }
  }

/* ===== QOYN index redesign: same functions, new layout only ===== */
:root{--navy:#0A2E5D;--navy2:#123d78;--yellow:#FFC24A;--bg:#F7F9FC;--text:#10233f;--muted:#6b7788;--line:#dfe6ef}
body{margin:0!important;background:var(--bg)!important;color:var(--text)!important;font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif!important;overflow-x:hidden}
h1,h2,h3,b{font-family:"Montserrat",sans-serif!important}.qoyn-layout{min-height:100vh;display:flex!important;width:100%;background:var(--bg)!important;direction:ltr!important;gap:0!important}.qoyn-sidebar{width:122px!important;min-width:122px!important;min-height:100vh!important;background:#fff!important;border-right:1px solid rgba(10,46,93,.08)!important;padding:24px 14px 18px!important;display:flex!important;flex-direction:column!important;align-items:center!important;gap:22px!important;position:sticky!important;top:0!important;align-self:flex-start!important;z-index:20!important;box-shadow:none!important}.sidebar-brand{width:100%!important;margin:0 0 18px!important;display:flex!important;justify-content:center!important}.sidebar-logo-text{color:var(--navy)!important;text-decoration:none!important;font-family:"Montserrat",sans-serif!important;font-weight:900!important;font-size:25px!important;letter-spacing:.5px!important;line-height:1!important}.sidebar-nav{width:100%!important;display:flex!important;flex-direction:column!important;gap:16px!important;align-items:stretch!important;margin:0!important}.sidebar-link{position:relative!important;width:100%!important;min-height:48px!important;display:grid!important;grid-template-columns:22px 1fr!important;align-items:center!important;gap:10px!important;padding:12px 10px!important;border-radius:12px!important;border:0!important;background:transparent!important;text-decoration:none!important;color:#506176!important;cursor:pointer!important;font:inherit!important;transition:.18s ease!important;text-align:left!important}.sidebar-link:hover,.sidebar-link.active,.sidebar-link:first-child{background:#f4f7fc!important;color:var(--navy)!important}.sidebar-link.active::before,.sidebar-link:first-child::before{content:""!important;position:absolute!important;left:-14px!important;top:7px!important;bottom:7px!important;width:3px!important;border-radius:20px!important;background:var(--navy)!important}.sidebar-icon{width:22px!important;height:22px!important;display:grid!important;place-items:center!important;line-height:0!important}.sidebar-icon svg{width:19px!important;height:19px!important;stroke:currentColor!important;display:block!important}.sidebar-text{font-size:10px!important;line-height:1.25!important;font-weight:700!important;text-align:left!important;color:inherit!important;max-width:none!important}.sidebar-btn{appearance:none!important;-webkit-appearance:none!important;text-align:left!important}#generateCertBtn{display:none!important}.qoyn-main{width:calc(100% - 122px)!important;background:var(--bg)!important;direction:ltr!important}.container,.qoyn-main .container{width:100%!important;max-width:none!important;padding:24px 28px 24px!important;margin:0!important}.qoyn-welcome{margin:0 0 18px!important;color:#25364d!important;font-size:11px!important;font-weight:700!important;direction:ltr!important;text-align:left!important;padding:0!important}.qoyn-welcome::before{content:"👋 "}.flat-section{margin-top:22px!important;direction:ltr!important;text-align:left!important}.flat-section h2,.major-card h2,.course-card h2,.coins-card h2,.reward-card h2{margin:0 0 16px!important;color:var(--navy)!important;font-size:22px!important;line-height:1.15!important;font-weight:900!important;letter-spacing:-.5px!important;text-align:left!important}.flat-section h2::after,.major-card h2::after{content:"";display:block;width:28px;height:3px;background:var(--yellow);border-radius:99px;margin-top:9px}.course-card h2::after,.coins-card h2::after,.reward-card h2::after{display:none!important}.major-card,.course-card,.coins-card,.reward-card{background:transparent!important;border:0!important;box-shadow:none!important;padding:0!important;border-radius:0!important;direction:ltr!important;text-align:left!important}.flat-inline{display:flex!important;gap:12px!important;align-items:center!important;flex-wrap:wrap!important}.flat-stack{display:flex!important;flex-direction:column!important;gap:12px!important;align-items:stretch!important}.major-input-row{max-width:640px!important;flex-wrap:nowrap!important}input,textarea,select,.input{font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif!important;background:#fff!important;color:#23344d!important;border:1px solid #d9e1ec!important;border-radius:9px!important;outline:none!important;box-shadow:0 2px 8px rgba(16,35,63,.025)!important;font-size:11px!important;transition:.18s ease!important}input:focus,textarea:focus,select:focus,.input:focus{border-color:#8fa4c1!important;box-shadow:0 0 0 3px rgba(10,46,93,.07)!important;transform:none!important}#majorInput{width:520px!important;height:38px!important;min-height:38px!important;padding:0 42px 0 18px!important;font-size:12px!important;font-weight:700!important;border-radius:10px!important;appearance:auto!important}.btn,.btn.primary,.btn.ghost,.btn.gray{font-family:"Montserrat",sans-serif!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:9px!important;min-height:38px!important;height:38px!important;padding:0 19px!important;border:0!important;border-radius:10px!important;background:linear-gradient(135deg,#173d78,#092a5b)!important;color:#fff!important;font-size:10px!important;font-weight:900!important;box-shadow:0 8px 18px rgba(10,46,93,.20)!important;cursor:pointer!important;transition:.18s ease!important;width:auto!important;min-width:auto!important}.btn:hover{transform:translateY(-2px)!important;box-shadow:0 12px 24px rgba(10,46,93,.25)!important}.small-btn{min-width:76px!important}#saveMajorBtn::before,#csSubmitBtn::before{content:"▣";font-size:12px;opacity:.9}#analyzeCoursesBtn{background:#fff!important;color:var(--navy)!important;border:1px solid #d9e1ec!important;box-shadow:0 5px 14px rgba(16,35,63,.08)!important;min-width:182px!important}#analyzeCoursesBtn::before{content:"✣";color:var(--yellow);font-size:13px}#analyzeCoursesBtn::after{content:"→";font-size:14px;margin-left:8px}.muted{color:var(--muted)!important;font-size:11px!important;line-height:1.55!important;font-weight:600!important;opacity:1!important}#majorMsg{color:#27a577!important;position:relative!important;padding-left:22px!important;margin-top:8px!important}#majorMsg::before{content:"✓";position:absolute;left:0;top:0;width:14px;height:14px;border-radius:50%;border:1px solid #27a577;display:grid;place-items:center;font-size:9px;line-height:1}.paths-grid{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:16px!important;width:100%!important;margin-top:8px!important}.path-select-card{position:relative!important;width:100%!important;min-width:0!important;height:176px!important;padding:0!important;overflow:hidden!important;background:#fff!important;border:1px solid #dfe6ef!important;border-radius:10px!important;box-shadow:0 8px 20px rgba(16,35,63,.06)!important;display:flex!important;flex-direction:column!important;align-items:stretch!important;cursor:pointer!important;transition:.18s ease!important;text-align:left!important;transform:none!important}.path-select-card:hover,.path-select-card.selected{border-color:#173d78!important;box-shadow:0 12px 28px rgba(16,35,63,.10)!important;transform:translateY(-2px)!important}.path-select-card::before{content:"Course";position:absolute;top:18px;left:20px;z-index:2;background:#173d78;color:#fff;border-radius:99px;padding:4px 12px;font-size:8px;font-weight:900;font-family:"Montserrat",sans-serif}.path-select-card.selected::after{content:"🟡 Selected";position:absolute;top:-11px;left:18px;z-index:3;background:#fff7df;color:#d59a00;border:1px solid #ffe4a5;border-radius:99px;padding:5px 11px;font-size:8px;font-weight:900;box-shadow:0 6px 14px rgba(16,35,63,.08)}.path-select-media{width:100%!important;height:112px!important;object-fit:cover!important;background:#f3f6fb!important;display:block!important;padding-top:18px!important;flex:0 0 112px!important}.path-select-title{padding:13px 48px 12px 20px!important;font-family:"Montserrat",sans-serif!important;color:var(--navy)!important;font-size:10px!important;font-weight:900!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;line-height:1.3!important;text-align:left!important}.path-select-title::after{content:"→";position:absolute;right:13px;bottom:12px;width:25px;height:25px;border-radius:50%;display:grid;place-items:center;background:#fff;border:1px solid #dfe6ef;color:var(--navy);font-size:14px;font-weight:900;box-shadow:0 4px 10px rgba(16,35,63,.06)}.path-select-card.selected .path-select-title::after{background:#123d78;color:#fff;border-color:#123d78}.course-card h2,.coins-card h2,.reward-card h2{display:flex!important;align-items:center!important;gap:12px!important}.course-card h2::before{content:"▢"!important;width:28px;height:28px;border-radius:10px;background:#f3f6fb;border:1px solid #e4ebf5;display:inline-grid;place-items:center;flex:0 0 28px;color:var(--navy);font-size:13px;text-align:center;line-height:27px}.course-fields-wrap{max-width:none!important;width:100%!important;display:grid!important;grid-template-columns:1fr 1fr!important;gap:12px!important;align-items:start!important}.course-fields-wrap input,.course-fields-wrap textarea,.course-fields-wrap input[type=file]{max-width:none!important}#csTitle,#csDesc{width:100%!important;min-height:56px!important;height:56px!important;padding:12px 16px!important;font-size:10px!important;resize:none!important}#csDesc{grid-column:2!important;grid-row:1!important}#csTitle{grid-column:1!important;grid-row:1!important}#csFile{grid-column:1 / -1!important;width:100%!important;min-height:44px!important;height:44px!important;padding:12px 16px!important;border:1px dashed #cbd7e6!important;background:#fbfdff!important;text-align:center!important;font-size:10px!important}.course-fields-wrap .flat-inline{grid-column:1 / -1!important;gap:12px!important;margin-top:-2px!important}#csMsg{grid-column:1 / -1!important;min-height:15px!important}.item,#rolesCard,.card{background:#fff!important;border:1px solid #dfe6ef!important;border-radius:12px!important;box-shadow:0 8px 20px rgba(16,35,63,.06)!important}#rolesCard{padding:16px!important;margin-top:14px!important}.role-btn{border:1px solid #dfe6ef!important;background:#fff!important;border-radius:12px!important;padding:10px 12px!important;font-weight:800!important;color:var(--navy)!important;cursor:pointer!important}.role-btn.active{border-color:var(--navy)!important;box-shadow:0 0 0 3px rgba(10,46,93,.08)!important}.role-pill{background:#eef4fb!important;color:var(--navy)!important;border-radius:99px!important;padding:4px 8px!important;font-size:10px!important;margin-left:8px!important}.stats-two-col{display:grid!important;grid-template-columns:1fr 1fr!important;gap:20px!important;margin-top:20px!important;direction:ltr!important}.stats-two-col>section{margin-top:0!important}.coins-card,.reward-card{min-height:158px!important;background:#fff!important;border:1px solid #dfe6ef!important;border-radius:12px!important;box-shadow:0 8px 20px rgba(16,35,63,.06)!important;padding:18px!important;position:relative!important;overflow:hidden!important}.coins-card::after{content:"🪙";position:absolute;right:30px;top:28px;font-size:48px;filter:drop-shadow(0 8px 12px rgba(255,194,74,.18))}.coins-card h2,.reward-card h2{font-size:16px!important;margin-bottom:12px!important}.coins-card h2::before,.reward-card h2::before{display:none!important}.flat-info,.plain-block{display:flex!important;flex-direction:column!important;gap:7px!important;align-items:flex-start!important;text-align:left!important}#coinsTotal{font-size:15px!important;color:var(--navy)!important}.badge{display:inline-flex!important;align-items:center!important;gap:6px!important;border-radius:99px!important;padding:8px 14px!important;font-size:10px!important;font-weight:900!important;font-family:"Montserrat",sans-serif!important}.badge.ok{background:#fff4d4!important;color:#8a6a00!important;border:0!important}.badge.ok::before{content:"👑"}#lrBox{position:relative!important;padding-left:152px!important;min-height:94px!important;margin-top:4px!important}#lrBox::before{content:"▶";position:absolute;left:0;top:0;width:130px;height:90px;border-radius:13px;background:linear-gradient(135deg,#234f99,#071f4b);color:#fff;display:grid;place-items:center;font-size:38px;box-shadow:0 10px 26px rgba(10,46,93,.18)}#lrBox .plain-block{gap:9px!important;font-size:11px!important;padding-top:4px!important}#lrTotal{color:var(--navy)!important;font-size:15px!important}#lrEmpty{margin-top:10px!important}#aiStatsBox{display:flex!important;flex-wrap:wrap!important;gap:8px!important;margin-top:12px!important}#aiStatsBox[style*="display:none"]{display:none!important}.pill{display:inline-flex!important;background:#fff!important;border:1px solid #dfe6ef!important;border-radius:99px!important;box-shadow:0 5px 14px rgba(16,35,63,.04)!important;color:#25364d!important;font-size:10px!important;font-weight:700!important;padding:8px 12px!important}.qoyne-floating-assistant,#qoyneFloatingAssistant{right:22px!important;bottom:16px!important;z-index:9999!important}@media(max-width:1100px){.paths-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}.stats-two-col{grid-template-columns:1fr!important}}@media(max-width:820px){.qoyn-layout{flex-direction:column!important}.qoyn-sidebar{position:relative!important;width:100%!important;min-width:0!important;min-height:auto!important;flex-direction:row!important;justify-content:flex-start!important;overflow-x:auto!important;border-right:0!important;border-bottom:1px solid rgba(10,46,93,.08)!important}.sidebar-brand{width:auto!important;margin:0 12px 0 0!important}.sidebar-nav{flex-direction:row!important;width:auto!important}.sidebar-link{min-width:96px!important}.qoyn-main{width:100%!important}.course-fields-wrap{grid-template-columns:1fr!important}#csDesc{grid-column:1!important;grid-row:auto!important}#csTitle{grid-column:1!important;grid-row:auto!important}}@media(max-width:560px){.container,.qoyn-main .container{padding:18px 14px 36px!important}.paths-grid{grid-template-columns:1fr!important}.major-input-row{flex-wrap:wrap!important}#majorInput{width:100%!important}.btn{width:100%!important}.flat-section h2{font-size:20px!important}#lrBox{padding-left:0!important;padding-top:104px!important}}



/* ===== Final requested tweaks: paths, course details, sidebar promo, AI bot ===== */
.sidebar-learn-card,
.qoyne-floating-assistant,
#qoyneFloatingAssistant{
  display:none !important;
  visibility:hidden !important;
  opacity:0 !important;
  pointer-events:none !important;
}
.paths-grid{grid-template-columns:repeat(4,310px)!important;justify-content:space-between!important;gap:22px!important;align-items:stretch!important;}
.path-select-card{height:215px!important;border-radius:12px!important;}
.path-select-media{height:138px!important;flex-basis:138px!important;object-fit:cover!important;padding-top:18px!important;}
.path-select-title{min-height:77px!important;padding:17px 52px 16px 20px!important;font-size:10.5px!important;display:flex!important;align-items:center!important;}
.path-select-title::after{right:15px!important;bottom:18px!important;width:27px!important;height:27px!important;}
.course-card{margin-top:34px!important;}
.course-fields-wrap{gap:14px!important;}
#csTitle,#csDesc{min-height:72px!important;height:72px!important;padding:16px 18px!important;font-size:11px!important;border-radius:10px!important;}
#csFile{min-height:58px!important;height:58px!important;padding:18px!important;border-radius:10px!important;}
.course-fields-wrap .flat-inline{gap:14px!important;margin-top:0!important;}
#csSubmitBtn,#analyzeCoursesBtn{height:44px!important;min-height:44px!important;border-radius:11px!important;padding:0 24px!important;}
#analyzeCoursesBtn{min-width:210px!important;}
@media(max-width:1350px){.paths-grid{grid-template-columns:repeat(4,minmax(240px,1fr))!important;gap:18px!important;}}
@media(max-width:1100px){.paths-grid{grid-template-columns:repeat(2,minmax(260px,1fr))!important;justify-content:stretch!important;}}
@media(max-width:560px){.paths-grid{grid-template-columns:1fr!important;}}

  

/* FINAL requested sizing: bigger Choose Major select and right-side buttons */
.major-card h2{
  font-size:28px!important;
  line-height:1.15!important;
  margin-bottom:20px!important;
}

.major-card h2::after{
  width:34px!important;
  height:3px!important;
  margin-top:11px!important;
}

.major-input-row{
  max-width:820px!important;
  gap:14px!important;
  align-items:center!important;
}

#majorInput{
  width:650px!important;
  height:48px!important;
  min-height:48px!important;
  padding:0 48px 0 22px!important;
  font-size:14px!important;
  font-weight:800!important;
  border-radius:12px!important;
}

#saveMajorBtn{
  height:48px!important;
  min-height:48px!important;
  min-width:112px!important;
  padding:0 28px!important;
  font-size:13px!important;
  border-radius:12px!important;
}

#saveMajorBtn::before{
  font-size:14px!important;
}

#csSubmitBtn,
#analyzeCoursesBtn{
  height:44px!important;
  min-height:44px!important;
  padding:0 24px!important;
  font-size:11.5px!important;
  border-radius:11px!important;
}

.path-select-title::after{
  width:32px!important;
  height:32px!important;
  right:16px!important;
  bottom:17px!important;
  font-size:16px!important;
}

@media(max-width:820px){
  .major-input-row{max-width:100%!important;}
  #majorInput{width:100%!important;}
  #saveMajorBtn{width:auto!important;}
}

@media(max-width:560px){
  #saveMajorBtn{width:100%!important;}
}

  

/* ===== Final tweak: bigger sidebar menu items ===== */
.qoyn-sidebar{
  width:150px!important;
  min-width:150px!important;
  padding:28px 16px 18px!important;
}

.sidebar-brand{
  margin-bottom:28px!important;
}

.sidebar-logo-text{
  font-size:30px!important;
  letter-spacing:.7px!important;
}

.sidebar-nav{
  gap:22px!important;
}

.sidebar-link{
  min-height:58px!important;
  grid-template-columns:30px 1fr!important;
  gap:12px!important;
  padding:15px 14px!important;
  border-radius:14px!important;
}

.sidebar-link.active::before,
.sidebar-link:first-child::before{
  left:-16px!important;
  top:8px!important;
  bottom:8px!important;
  width:4px!important;
}

.sidebar-icon{
  width:30px!important;
  height:30px!important;
}

.sidebar-icon svg{
  width:24px!important;
  height:24px!important;
  stroke-width:2.2!important;
}

.sidebar-text{
  font-size:13px!important;
  line-height:1.12!important;
  font-weight:800!important;
  letter-spacing:-.15px!important;
}

.qoyn-main{
  width:calc(100% - 150px)!important;
}

@media(max-width:820px){
  .qoyn-sidebar{
    width:100%!important;
    min-width:0!important;
  }
  .qoyn-main{
    width:100%!important;
  }
  .sidebar-link{
    min-width:126px!important;
  }
}


/* ===== Sidebar final fix: no broken text ===== */
.qoyn-sidebar{
  width:220px!important;
  min-width:220px!important;
  padding:28px 18px 18px!important;
  align-items:stretch!important;
}
.sidebar-brand{
  justify-content:flex-start!important;
  padding-left:10px!important;
  margin-bottom:28px!important;
}
.sidebar-logo-text{
  font-size:32px!important;
  white-space:nowrap!important;
}
.sidebar-nav{
  width:100%!important;
  gap:20px!important;
  align-items:stretch!important;
}
.sidebar-link{
  width:100%!important;
  min-height:60px!important;
  display:flex!important;
  align-items:center!important;
  justify-content:flex-start!important;
  gap:16px!important;
  padding:15px 18px!important;
  border-radius:18px!important;
  white-space:nowrap!important;
  overflow:visible!important;
  text-align:left!important;
}
.sidebar-icon{
  flex:0 0 28px!important;
  width:28px!important;
  height:28px!important;
}
.sidebar-icon svg{
  width:23px!important;
  height:23px!important;
}
.sidebar-text{
  display:block!important;
  flex:1 1 auto!important;
  min-width:0!important;
  max-width:none!important;
  white-space:nowrap!important;
  overflow:visible!important;
  text-overflow:clip!important;
  word-break:normal!important;
  overflow-wrap:normal!important;
  line-break:auto!important;
  font-size:14px!important;
  line-height:1!important;
  font-weight:800!important;
  letter-spacing:-.1px!important;
  text-align:left!important;
}
.sidebar-link.active::before,
.sidebar-link:first-child::before{
  left:-18px!important;
  top:9px!important;
  bottom:9px!important;
  width:4px!important;
}
.qoyn-main{
  width:calc(100% - 220px)!important;
}
@media(max-width:820px){
  .qoyn-sidebar{
    width:100%!important;
    min-width:0!important;
    padding:16px!important;
    align-items:center!important;
  }
  .sidebar-brand{
    padding-left:0!important;
    margin-bottom:0!important;
  }
  .sidebar-link{
    min-width:auto!important;
    width:auto!important;
  }
  .qoyn-main{
    width:100%!important;
  }
}
  
/* ===== Requested tweak: make Choose a Path cards narrower ===== */
.paths-grid{grid-template-columns:repeat(4,270px)!important;justify-content:space-between!important;gap:18px!important;align-items:stretch!important;}
.path-select-card{width:270px!important;max-width:270px!important;}
@media(max-width:1350px){.paths-grid{grid-template-columns:repeat(4,250px)!important;gap:16px!important}.path-select-card{width:250px!important;max-width:250px!important}}
@media(max-width:1100px){.paths-grid{grid-template-columns:repeat(2,minmax(260px,1fr))!important;justify-content:stretch!important}.path-select-card{width:100%!important;max-width:none!important}}
@media(max-width:560px){.paths-grid{grid-template-columns:1fr!important}}
.phase-progress-wrap{
  margin-top:14px;
  width:100%;
  max-width:420px;
}

.phase-progress-bar{
  width:100%;
  height:10px;
  background:#e6ebf2;
  border-radius:999px;
  overflow:hidden;
}

.phase-progress-fill{
  height:100%;
  width:0%;
  background:#0A2E5D;
  border-radius:999px;
  transition:width .5s ease;
}

.phase-progress-text{
  margin-top:6px;
  font-size:13px;
  font-weight:700;
  color:#0A2E5D;
  text-align:right;
}
</style>
</head>

<body style="display:block">
  <div class="qoyn-layout">

<!-- LEFT: Sidebar -->
<aside class="qoyn-sidebar" dir="ltr">
  <div class="sidebar-brand">
    <a class="sidebar-logo-text" href="#">QOYN</a>
  </div>

  <nav class="sidebar-nav">
    <a class="sidebar-link" href="student-dashboard.php#home">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="home">Home</span>
    </a>

    <a class="sidebar-link" href="my_courses.php">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15.5a2.5 2.5 0 0 0-2.5-2.5H4V5.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
          <path d="M4 16V6a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="my_courses">My Courses</span>
    </a>

    <a class="sidebar-link" href="courses.php">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 6 4 9.5 12 13l8-3.5L12 6Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
          <path d="M7 11.2V15c0 .7 2.2 2 5 2s5-1.3 5-2v-3.8" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="all_courses">All Courses</span>
    </a>

    <button class="sidebar-link sidebar-btn" id="generateCertBtn" type="button">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M7 4h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.9"/>
          <path d="M8 18h8" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
          <path d="M10 14v4m4-4v4" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="generate_phase1_certificate">Generate Phase 1 Certificate</span>
    </button>

    <button class="sidebar-link sidebar-btn" id="showCertsBtn" type="button">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M7 3h7l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
          <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
          <path d="M9 13h6M9 17h6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="view_certificates">View Certificates</span>
    </button>

<a class="sidebar-link" href="#" id="logoutBtn">
        <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 17l5-5-5-5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M20 12H9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
          <path d="M11 20H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
      <span class="sidebar-text" data-i18n="logout">Logout</span>
    </a>
  </nav>
  <div class="sidebar-learn-card"><div class="sidebar-learn-icon">👑</div><b>Keep learning,<br>keep growing</b><p>Complete courses, earn coins and unlock new opportunities.</p><a href="courses.php">Learn More →</a></div>
</aside>    

    <!-- RIGHT: Main -->
    <main class="qoyn-main">
      <div class="container">
        <div class="qoyn-welcome" id="welcome">...</div>

        <!-- ===== Choosing Your Major ===== -->
        <section class="flat-section" dir="ltr">
          <div class="major-card">
            <h2 data-i18n="choose_your_major">Choose Your Major</h2>

            <div class="flat-inline major-input-row" style="margin-top:12px">
              <select id="majorInput" style="min-width:260px">
                <option value="" data-i18n="choose_major_option">Choose your Major</option>
                <option value="AI" data-i18n="major_ai">Artificial Intelligence (AI)</option>
                <option value="CS" data-i18n="major_cs">Computer Science (CS)</option>
                <option value="SE" data-i18n="major_se">Software Engineering (SE)</option>
                <option value="Cyber Security" data-i18n="major_cyber">Cyber Security</option>
              </select>

              <button class="btn primary small-btn" id="saveMajorBtn" data-i18n="save">Save</button>
            </div>

            <div class="muted" id="majorMsg" style="margin-top:10px; min-height:18px"></div>
          </div>
        </section>

        <!-- ===== Choose a Path ===== -->
        <section class="flat-section" dir="ltr">
          <h2 data-i18n="choose_path_optional">Choose a Path (Optional)</h2>
          <div id="allPathsGrid" class="paths-grid"></div>
          <div class="muted" id="pathPickMsg" style="margin-top:10px;min-height:18px"></div>
        </section>

        <!-- ===== Course Details + Attachment ===== -->
        <section class="flat-section" dir="ltr">
          <div class="course-card">
            <h2 data-i18n="course_details_attachment">Course Details + Attachment</h2>

            <div class="flat-stack course-fields-wrap" style="margin-top:12px; max-width:900px">
              <input id="csTitle" data-i18n-placeholder="course_title_placeholder" placeholder="Course title (Example: Data Science Basics)" />
              <textarea id="csDesc" rows="6" data-i18n-placeholder="course_desc_placeholder" placeholder="Write what you would like to work on" style="resize:vertical"></textarea>
              <input id="csFile" type="file" accept=".pdf,image/*" />

              <div class="flat-inline">
                <button class="btn primary small-btn" id="csSubmitBtn" data-i18n="save">Save</button>
                <button class="btn primary small-btn" type="button" id="analyzeCoursesBtn" data-i18n="start_course_analysis">Start Course Analysis</button>
              </div>

              <div class="muted" id="csMsg" style="min-height:18px"></div>
            </div>
          </div>

          <div id="aiStatsBox" style="margin-top:10px;display:none">
            <div class="pill"><span data-i18n="attempts">Attempts</span>: <b id="aiN">0</b></div>
            <div class="pill" style="margin-right:8px"><span data-i18n="average_grade">Average Grade</span>: <b id="aiAvgScore">0%</b></div>
            <div class="pill" style="margin-right:8px"><span data-i18n="average_view_time">Average View Time</span>: <b id="aiAvgWatch">0%</b></div>
            <div class="pill" style="margin-right:8px"><span data-i18n="average_time">Average Time</span>: <b id="aiAvgTime">0s</b></div>
            <div class="pill" style="margin-right:8px"><span data-i18n="difficulty">Difficulty</span>: <b id="aiAvgDiff">0</b></div>
          </div>

          <div class="muted" id="aiHint" style="margin-top:10px;display:none"></div>

          <!-- ✅ Role Suggestions Box -->
          <div id="rolesCard" class="item" style="margin-top:12px; display:none; flex-direction:column; gap:10px">
            <div style="font-weight:800" data-i18n="select_suggested_major_top3">Select the Suggested Major (Top 3)</div>
            <div class="muted" data-i18n="choose_suggested_path_desc">Choose the path that suits you, and we will build a learning path for it.</div>

            <div id="rolesBox" style="display:flex; gap:10px; flex-wrap:wrap"></div>

            <div class="muted" id="rolesMsg" style="min-height:18px"></div>

            <button class="btn ghost small-btn" type="button" id="generatePathBtn" style="display:none" data-i18n="generate_path_for_selected_role">
              Generate path for selected role
            </button>
          </div>
        </section>

        <div class="stats-two-col">

          <section class="flat-section" dir="ltr">
            <div class="coins-card">
              <h2 data-i18n="coins_phases">Coins &amp; Phases</h2>

              <div class="flat-info" style="margin-top:12px">
                <div data-i18n="phase_targets">Phase1 → 10000 | Phase2 → 20000 | Phase3</div>
<div class="phase-progress-wrap">
  <div class="phase-progress-bar">
    <div class="phase-progress-fill" id="phaseProgressFill"></div>
  </div>
  <div class="phase-progress-text" id="phaseProgressText">0%</div>
</div>
                <div class="plain-block">
                  <div><b id="coinsTotal">0</b> <span data-i18n="coins">coins</span></div>
                  <div class="muted">
                    <span data-i18n="phase">Phase</span>: <b id="phaseNow">1</b> |
                    <span data-i18n="next_target">Next target</span>: <b id="nextTarget">10000</b>
                  </div>
                  <span class="badge ok" id="subBadge">...</span>
                </div>

                <div class="muted" id="certMsg" style="min-height:20px"></div>

                <div id="certListBox" style="margin-top:12px; display:none">
                  <div class="muted" style="margin-bottom:8px" data-i18n="your_certificates">Your Certificates:</div>
                  <div id="certList" class="list" style="gap:10px"></div>
                </div>
              </div>
            </div>
          </section>

          <section class="flat-section" dir="ltr">
            <div class="reward-card">
              <h2 data-i18n="latest_video_reward">Latest Video Reward</h2>

              <div id="lrEmpty" class="muted" style="margin-top:10px">
                <span data-i18n="no_rewards_yet">There are no rewards yet.</span>
              </div>

              <div id="lrBox" style="margin-top:10px; display:none">
                <div class="plain-block">
                  <div><b id="lrTitle">-</b></div>
                  <div><span data-i18n="views">Views</span>: <b id="lrBase">0</b> <span data-i18n="coin">coin</span></div>
                  <div><span data-i18n="questions">Questions</span>: <b id="lrQuiz">0</b> <span data-i18n="coin">coin</span></div>
                  <div class="muted"><span data-i18n="total">Total</span>: <b id="lrTotal">0</b> <span data-i18n="coin">coin</span></div>
                </div>
              </div>
            </div>
          </section>

        </div>

      </div>
    </main>
  </div>

<script>
const API_BASE = "/utbn-backend/api";

function t(key){
  const lang = localStorage.getItem("lang") || "en";
  const dict = window.translations && window.translations[lang] ? window.translations[lang] : {};
  return dict[key] || key;
}

async function apiGet(f){
  const r = await fetch(API_BASE+"/"+f,{credentials:"include"});
  const t2 = await r.text();
  try{ return JSON.parse(t2); }
  catch(e){
    console.error("❌ BAD JSON from:", f, "\nStatus:", r.status, "\nBody:", t2.slice(0,300));
    return { ok:false, error:"BAD_JSON", endpoint:f, status:r.status, raw:t2 };
  }
}

async function apiPostJson(f,b={}){
  const r = await fetch(API_BASE+"/"+f,{
    method:"POST",
    credentials:"include",
    headers:{"Content-Type":"application/json"},
    body:JSON.stringify(b)
  });
  const t2 = await r.text();
  try{ return JSON.parse(t2); }
  catch(e){
    console.error("❌ BAD JSON from:", f, "\nStatus:", r.status, "\nBody:", t2.slice(0,300));
    return { ok:false, error:"BAD_JSON", endpoint:f, status:r.status, raw:t2 };
  }
}

async function apiPostForm(f, obj = {}) {
  const body = new URLSearchParams(obj).toString();

  const r = await fetch(API_BASE + "/" + f, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body
  });

  const t2 = await r.text();
  try { return JSON.parse(t2); }
  catch (e) {
    console.error("❌ BAD JSON from:", f, "\nStatus:", r.status, "\nBody:", t2.slice(0, 300));
    return { ok: false, error: "BAD_JSON", endpoint: f, status: r.status, raw: t2 };
  }
}

let selectedRoleKey = "";
let selectedRole = null;

function renderTopRoles(roles){
  const rolesCard = document.getElementById("rolesCard");
  const rolesBox  = document.getElementById("rolesBox");
  const rolesMsg  = document.getElementById("rolesMsg");
  const genBtn    = document.getElementById("generatePathBtn");

  if(!rolesCard || !rolesBox || !rolesMsg || !genBtn) return;

  rolesCard.style.display = "flex";
  rolesBox.innerHTML = "";
  rolesMsg.textContent = "";
  genBtn.style.display = "none";
  selectedRoleKey = "";
  selectedRole = null;

  if(!roles || !roles.length){
    rolesMsg.textContent = t("no_suggested_roles");
    return;
  }

  roles.forEach(r=>{
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "role-btn";
    btn.innerHTML = `
      <span>${escapeHtml(r.role_name)}</span>
      <span class="role-pill">${escapeHtml(r.score_percent)}%</span>
    `;

    btn.onclick = ()=>{
      [...rolesBox.querySelectorAll(".role-btn")].forEach(x=>x.classList.remove("active"));
      btn.classList.add("active");

      selectedRoleKey = r.role_key;
      selectedRole = r;
      rolesMsg.textContent = `${t("selected_role")}: ${r.role_name}`;
      genBtn.style.display = "inline-flex";
    };

    rolesBox.appendChild(btn);
  });
}

async function loadTopRoles(){
  const rolesMsg = document.getElementById("rolesMsg");
  if(rolesMsg) rolesMsg.textContent = t("loading_top_roles");

  const r = await apiGet("get_top_roles.php");
  if(!r || !r.ok){
    if(rolesMsg) rolesMsg.textContent = t("failed_fetch_roles");
    return;
  }
  renderTopRoles(r.top_roles);
}

async function saveRoleChoice(role){
  const r = await apiPostForm("set_user_role.php", {
    role_key: role.role_key,
    role_name: role.role_name,
    score: String(role.score ?? 0)
  });
  return r && r.ok;
}

document.getElementById("generatePathBtn")?.addEventListener("click", async ()=>{
  const rolesMsg = document.getElementById("rolesMsg");

  if(!selectedRole){
    if(rolesMsg) rolesMsg.textContent = t("choose_role_first");
    return;
  }

  if(rolesMsg) rolesMsg.textContent = t("saving_choice");
  const ok = await saveRoleChoice(selectedRole);

  if(!ok){
    if(rolesMsg) rolesMsg.textContent = t("failed_save_selected_role");
    return;
  }

  if(rolesMsg) rolesMsg.textContent = t("generating_path");
  window.location.href = "my_courses.php?role_key=" + encodeURIComponent(selectedRole.role_key);
});

async function loadLastReward(){
  const r = await apiGet("last_video_reward.php");
  if(!r || !r.found) return;
  lrEmpty.style.display="none";
  lrBox.style.display="block";
  lrTitle.textContent = r.title;
  lrBase.textContent  = r.base_coin;
  lrQuiz.textContent  = r.quiz_coin;
  lrTotal.textContent = r.total_coin;
}

const majorInput = document.getElementById("majorInput");
const majorMsg = document.getElementById("majorMsg");
const saveMajorBtn = document.getElementById("saveMajorBtn");

async function loadMajor(){
  const r = await apiGet("get_major.php");
  if(!r || !r.ok) return;

  if(r.major_text){
    majorInput.value = r.major_text;
    majorMsg.textContent = t("done");
    majorInput.disabled = false;
    saveMajorBtn.disabled = false;
  } else {
    majorMsg.textContent = t("choose_your_major");
    majorInput.disabled = false;
    saveMajorBtn.disabled = false;
  }
}

saveMajorBtn.onclick = async ()=>{
  const major = majorInput.value.trim();

  if(!major){
    majorMsg.textContent = t("choose_major_first");
    return;
  }

  majorMsg.textContent = t("waiting");
  saveMajorBtn.disabled = true;

  const r1 = await apiPostForm("set_major.php", { major });

  if(!(r1 && r1.ok)){
    majorMsg.textContent = t("try_again");
    saveMajorBtn.disabled = false;
    return;
  }

  majorMsg.textContent = t("preparing_major_materials");
  const r2 = await apiPostForm("assign_plan.php", { major });

  if(r2 && r2.ok){
    majorMsg.textContent = t("major_updated_and_materials_prepared");
  } else {
    majorMsg.textContent = t("major_saved_no_matching_plan");
  }

  saveMajorBtn.disabled = false;
};

async function load(){
  const me = await apiGet("me.php");
  welcome.textContent = `${t("welcome")} ${me.full_name}`;

  const coins = await apiGet("coins.php");
  const total = parseInt(coins.coins_total || "0", 10);

  coinsTotal.textContent = total;
updatePhaseProgress(total);
function updatePhaseProgress(total){
  total = Number(total || 0);

  let currentStart = 0;
  let nextTarget = 10000;

  if(total >= 20000){
    currentStart = 20000;
    nextTarget = 30000;
  }else if(total >= 10000){
    currentStart = 10000;
    nextTarget = 20000;
  }

  const progress = total - currentStart;
  const range = nextTarget - currentStart;

  const percent = Math.min(Math.round((progress / range) * 100), 100);

  const fill = document.getElementById("phaseProgressFill");
  const text = document.getElementById("phaseProgressText");

  if(fill) fill.style.width = percent + "%";
  if(text) text.textContent = percent + "%";
}
  const isPhase2 = total >= 10000;
  const isPhase3 = total >= 20000;

  phaseNow.textContent = isPhase3 ? "3" : (isPhase2 ? "2" : "1");
  nextTarget.textContent = isPhase3 ? "30000" : (isPhase2 ? "20000" : "10000");

  const goBtn2 = document.getElementById("goPhase2Btn");
  if (goBtn2) goBtn2.style.display = isPhase2 ? "flex" : "none";

  const goBtn3 = document.getElementById("goPhase3Btn");
  if (goBtn3) goBtn3.style.display = isPhase3 ? "flex" : "none";

  const certBtn = document.getElementById("generateCertBtn");
  if (certBtn) certBtn.textContent = t("generate_phase1_certificate");

  const sub = await apiGet("subscription_status.php");
  subBadge.textContent = sub.active ? t("active_subscription") : t("no_subscription");
}

function escapeHtml(s){
  return (s ?? "").toString()
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

generateCertBtn.onclick = async ()=>{
  certMsg.textContent = t("creating");

  const r = await apiPostJson("certificate_generate.php", { phase: 1 });
  if (!r || !r.ok) { certMsg.textContent = t("failed_create_certificate"); return; }
  certMsg.textContent = t("done");

  location.href = "certificates.php";
};

showCertsBtn.onclick = ()=>{
  location.href = "certificates.php";
};

load();
loadMajor();
loadLastReward();

async function submitCourseSubmission(){
  const titleEl = document.getElementById("csTitle");
  const descEl  = document.getElementById("csDesc");
  const fileEl  = document.getElementById("csFile");
  const msgEl   = document.getElementById("csMsg");
  const btnEl   = document.getElementById("csSubmitBtn");

  if(!titleEl || !descEl || !msgEl || !btnEl) return;

  const course_title = titleEl.value.trim();
  const description  = descEl.value.trim();
  const file         = (fileEl && fileEl.files && fileEl.files[0]) ? fileEl.files[0] : null;

  if(!course_title || !description){
    msgEl.textContent = t("write_course_title_and_description_first");
    return;
  }

  msgEl.textContent = t("saving");
  btnEl.disabled = true;

  try{
    const fd = new FormData();
    fd.append("course_title", course_title);
    fd.append("description", description);
    if(file) fd.append("file", file);

    const r = await fetch("/utbn-backend/api/course_submission_create.php", {
      method: "POST",
      credentials: "include",
      body: fd
    });

    const j = await r.json().catch(()=>({}));
    if(!r.ok || !j.ok){
      msgEl.textContent = j.error || t("failed_save");
      btnEl.disabled = false;
      return;
    }

    msgEl.textContent = t("saved_successfully");

    titleEl.value = "";
    descEl.value = "";
    if(fileEl) fileEl.value = "";

  }catch(e){
    msgEl.textContent = t("failed_save");
  }finally{
    btnEl.disabled = false;
  }
}

document.getElementById("csSubmitBtn")?.addEventListener("click", submitCourseSubmission);
</script>

<script src="assets/js/assistant.js"></script>
<script src="assets/js/i18n.js"></script>

<script>
document.getElementById("analyzeCoursesBtn")?.addEventListener("click", async ()=>{
  const msgEl = document.getElementById("csMsg");
  const btnEl = document.getElementById("analyzeCoursesBtn");
  if(msgEl) msgEl.textContent = t("analyzing_courses_and_extracting_skills");
  if(btnEl) btnEl.disabled = true;

  try{
    const r1 = await apiGet("extract_user_skills.php");
    if(!r1 || !r1.ok){
      msgEl.textContent = t("failed_extract_skills");
      btnEl.disabled = false;
      return;
    }

    msgEl.textContent = `${t("skills_extracted")}: ${r1.matched_skills_count || 0}. ${t("suggesting_roles")}`;
    await loadTopRoles();

    msgEl.textContent = t("choose_role_from_suggestions");
  } catch(e){
    msgEl.textContent = t("analysis_error");
  } finally {
    if(btnEl) btnEl.disabled = false;
  }
});

async function loadAllPaths(){
  const box = document.getElementById("allPathsGrid");
  const msg = document.getElementById("pathPickMsg");
  msg.textContent = t("loading_paths");

  const r = await apiGet("get_all_paths.php");
  if(!r.ok){
    msg.textContent = t("failed_to_load_paths");
    return;
  }

  box.innerHTML = "";

  const pathImages = {
    "FULLSTACK AI Path": "assets/fullstack.png",
    "ML_ENGINEER AI Path": "assets/ml.png",
    "PENTESTER AI Path": "assets/pentester.png",
    "ALGORITHM_ENGINEER AI Path": "assets/algorithm.png"
  };

  r.paths.forEach(p => {
    const b = document.createElement("button");
    b.type = "button";
    b.className = "path-select-card";

    const imagePath = pathImages[p.title] || "uploads/default-path.png";

    b.innerHTML = `
      <img class="path-select-media" src="${imagePath}" alt="${escapeHtml(p.title)}">
      <div class="path-select-title">${escapeHtml(p.title)}</div>
    `;

    b.onclick = async ()=>{
      msg.textContent = t("saving");
      const rr = await apiPostJson("set_selected_path.php", { path_id: Number(p.id) });
      msg.textContent = rr.ok ? t("path_selected") : t("failed_to_save");
    };

    box.appendChild(b);
  });

  msg.textContent = "";
}

loadAllPaths();
</script>
<script>
document.getElementById("logoutBtn")?.addEventListener("click", async function(e){
  e.preventDefault();

  try {
    await fetch("/utbn-backend/api/logout.php", {
      method: "POST",
      credentials: "include",
      headers: {
        "X-CSRF-Token": localStorage.getItem("csrf_token") || ""
      }
    });
  } catch (err) {}

  localStorage.removeItem("csrf_token");
  window.location.href = "login.html";
});
</script>
</body>
</html>

