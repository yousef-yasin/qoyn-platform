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

$user_id   = (int)($_SESSION["user_id"] ?? 0);
$full_name = trim($_SESSION["full_name"] ?? $_SESSION["name"] ?? "");
$email     = trim($_SESSION["email"] ?? "");
$show      = trim($_GET["show"] ?? "");

/* بيانات المستخدم الحالي */
$coins = 0;
$major_text = "";
$path_title = "";

$stmtMe = $conn->prepare("
  SELECT
    u.full_name,
    u.email,
    u.coins,
    u.major_text,
    lp.title AS path_title
  FROM users u
  LEFT JOIN user_selected_path usp ON usp.user_id = u.id
  LEFT JOIN learning_paths lp ON lp.id = usp.path_id
  WHERE u.id = ?
  LIMIT 1
");

if ($stmtMe) {
  $stmtMe->bind_param("i", $user_id);
  $stmtMe->execute();
  $resMe = $stmtMe->get_result();

  if ($rowMe = $resMe->fetch_assoc()) {
    $full_name  = trim($rowMe["full_name"] ?? $full_name);
    $email      = trim($rowMe["email"] ?? $email);
    $coins      = (int)($rowMe["coins"] ?? 0);
    $major_text = trim($rowMe["major_text"] ?? "");
    $path_title = trim($rowMe["path_title"] ?? "");
  }

  $stmtMe->close();
}

$avatar_initial = "U";
if ($full_name !== "") {
  $avatar_initial = mb_strtoupper(mb_substr($full_name, 0, 1, "UTF-8"), "UTF-8");
} elseif ($email !== "") {
  $avatar_initial = strtoupper(substr($email, 0, 1));
}

$students = [];
$companies = [];

/* الطلاب */
if ($show === "students") {
  $sqlStudents = "
    SELECT
      u.id,
      u.full_name,
      u.email,
      u.coins,
      u.major_text,
      lp.title AS path_title
    FROM users u
    LEFT JOIN user_selected_path usp ON usp.user_id = u.id
    LEFT JOIN learning_paths lp ON lp.id = usp.path_id
    WHERE u.role = 'student'
    ORDER BY u.full_name ASC
  ";

  $resStudents = $conn->query($sqlStudents);
  if ($resStudents) {
    while ($row = $resStudents->fetch_assoc()) {
      $students[] = $row;
    }
  }
}

/* الشركات */
if ($show === "companies") {
  $sqlCompanies = "SELECT id, full_name, email FROM users WHERE role IN ('partner','company') ORDER BY full_name ASC";
  $resCompanies = $conn->query($sqlCompanies);
  if ($resCompanies) {
    while ($row = $resCompanies->fetch_assoc()) {
      $companies[] = $row;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title data-i18n="student_info_page_title">QOYN | Student Info</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="assets/js/i18n.js"></script>

  <style>
  :root{
    --navy:#0A2E5D;
    --yellow:#FFC24A;
    --bg:#F6F7F9;
    --card:#ffffff;
    --text:#0B0B0B;
    --muted:#6b7280;
    --line:#e5e7eb;
    --shadow:0 10px 30px rgba(0,0,0,.08);
    --radius:999px;
    --container:1200px;
  }

  *{box-sizing:border-box}

  body{
    margin:0;
    font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color:var(--text);
    background:var(--bg);
    overflow-x:hidden;
  }

  .nav-wrap{
    position:fixed;
    top:0; left:0; right:0;
    z-index:9999;
    padding:18px 22px;
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    box-shadow:var(--shadow);
  }

  .nav{
    max-width:var(--container);
    margin:0 auto;
    display:flex;
    align-items:center;
    gap:16px;
  }

  .nav-right{
    display:flex;
    align-items:center;
    gap:14px;
  }

  .nav-right .nav-monkey{
    height:60px;
    width:auto;
    display:block;
    margin-left:50px;
  }

  .logo{
    font-family:"Montserrat", sans-serif;
    font-weight:800;
    font-size:28px;
    letter-spacing:.5px;
    color:var(--navy);
    text-decoration:none;
    user-select:none;
  }

  .nav-spacer{flex:1}

  .nav-links{
    display:flex;
    align-items:center;
    gap:18px;
    margin:0;
    padding:0;
    list-style:none;
    justify-content:flex-end;
  }

  .nav-links a{
    position:relative;
    text-decoration:none;
    color:#111;
    font-weight:500;
    font-size:15px;
    padding:10px 14px;
    border-radius:var(--radius);
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
    border:1px solid rgba(10,46,93,.25);
    font-weight:700;
    background:transparent;
  }

  .nav-logout:hover{
    background:var(--navy);
    color:#fff !important;
  }

  .avatar{
    width:42px;
    height:42px;
    border-radius:999px;
    display:grid;
    place-items:center;
    text-decoration:none;
    background:rgba(10,46,93,.08);
    border:1px solid rgba(10,46,93,.18);
    color:var(--navy);
    font-family:"Montserrat", sans-serif;
    font-weight:900;
    letter-spacing:.2px;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    box-shadow:0 10px 22px rgba(0,0,0,.06);
  }

  .avatar:hover{
    transform:translateY(-2px);
    background:rgba(10,46,93,.12);
    border-color:rgba(10,46,93,.28);
    box-shadow:0 16px 34px rgba(0,0,0,.10);
  }

  .lang-dropdown{
    position:relative;
  }

  .lang-trigger{
    display:inline-flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    min-width:110px;
    border:1px solid rgba(10,46,93,.15);
    background:#fff;
    color:#111;
    border-radius:var(--radius);
    padding:10px 14px;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  }

  .lang-menu{
    position:absolute;
    top:calc(100% + 8px);
    inset-inline-start:0;
    background:#fff;
    border:1px solid var(--line);
    border-radius:16px;
    box-shadow:var(--shadow);
    min-width:100%;
    padding:6px;
    display:none;
    z-index:10001;
  }

  .lang-dropdown.open .lang-menu{
    display:block;
  }

  .lang-option{
    width:100%;
    border:0;
    background:transparent;
    padding:10px 12px;
    border-radius:12px;
    text-align:start;
    cursor:pointer;
    font-weight:600;
    font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  }

  .lang-option:hover,
  .lang-option.active{
    background:#f3f4f6;
    color:var(--navy);
  }

  main{
    padding-top:120px;
    min-height:100vh;
  }

  .container{
    max-width:var(--container);
    margin:0 auto;
    padding:0 22px 40px;
    width:100%;
  }

  .page-head{
    margin-bottom:24px;
  }

  .page-title{
    font-family:"Montserrat", sans-serif;
    font-size:38px;
    line-height:1.1;
    margin:0 0 8px;
    color:var(--navy);
    font-weight:800;
  }

  .page-subtitle{
    margin:0;
    color:var(--muted);
    font-size:15px;
  }

  .profile-card{
    background:var(--card);
    border-radius:28px;
    box-shadow:var(--shadow);
    padding:28px;
    border:1px solid rgba(10,46,93,.06);
    margin:0 auto 20px;
    max-width:900px;
  }

  .profile-top{
    display:flex;
    align-items:center;
    gap:18px;
    flex-wrap:wrap;
    margin-bottom:26px;
  }

  .profile-avatar{
    width:86px;
    height:86px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:var(--navy);
    color:#fff;
    font-family:"Montserrat", sans-serif;
    font-size:32px;
    font-weight:800;
    box-shadow:0 14px 30px rgba(10,46,93,.18);
  }

  .profile-name{
    margin:0;
    font-size:28px;
    font-weight:800;
    color:var(--navy);
  }

  .profile-role{
    margin:6px 0 0;
    color:var(--muted);
    font-size:15px;
  }

  .info-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }

  .info-box{
    background:#f9fafb;
    border:1px solid var(--line);
    border-radius:18px;
    padding:18px;
  }

  .info-label{
    font-size:13px;
    color:var(--muted);
    margin-bottom:8px;
    font-weight:500;
  }

  .info-value{
    font-size:18px;
    font-weight:800;
    color:var(--text);
    word-break:break-word;
  }

  .actions{
    margin-top:24px;
    display:flex;
    gap:12px;
    flex-wrap:wrap;
  }

  .btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    border:none;
    border-radius:999px;
    padding:12px 18px;
    font-weight:700;
    font-size:14px;
    cursor:pointer;
    transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
  }

  .btn-primary{
    background:var(--navy);
    color:#fff;
    box-shadow:0 10px 20px rgba(10,46,93,.15);
  }

  .btn-primary:hover{
    transform:translateY(-2px);
  }

  .btn-light{
    background:#fff;
    color:var(--navy);
    border:1px solid rgba(10,46,93,.15);
  }

  .btn-light:hover{
    transform:translateY(-2px);
  }

  .list-card{
    background:var(--card);
    border-radius:28px;
    box-shadow:var(--shadow);
    padding:24px;
    border:1px solid rgba(10,46,93,.06);
    margin:20px auto 0;
    max-width:900px;
  }

  .list-title{
    font-family:"Montserrat", sans-serif;
    font-size:24px;
    color:var(--navy);
    margin:0 0 16px;
    font-weight:800;
  }

  .accordion{
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:14px;
    align-items:start;
  }

  .person-card{
    border:1px solid var(--line);
    border-radius:18px;
    background:#f9fafb;
    overflow:hidden;
    transition:all .22s ease;
    width:100%;
    min-width:0;
  }

  .person-card:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 24px rgba(0,0,0,.08);
  }

  .person-summary{
    cursor:pointer;
    list-style:none;
    padding:12px 14px;
    font-weight:800;
    color:var(--navy);
    font-size:14px;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:8px;
    width:100%;
    min-height:66px;
  }

  .person-summary::-webkit-details-marker{
    display:none;
  }

  .person-summary::after{
    content:"+";
    font-size:22px;
    font-weight:800;
    color:var(--navy);
    line-height:1;
    flex:0 0 auto;
    margin-top:2px;
  }

  details[open] .person-summary::after{
    content:"-";
  }

  .person-summary > span{
    display:block;
    width:100%;
    min-width:0;
    overflow:hidden;
  }

  .person-summary > span:first-child{
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .person-short{
    display:block;
    color:var(--muted);
    font-size:12px;
    font-weight:500;
    margin-top:4px;
    white-space:normal;
  }

  .person-content{
    padding:0 14px 14px;
    border-top:1px solid var(--line);
    background:#fff;
  }

  .person-content .info-label{
    margin-top:14px;
  }

  .empty-box{
    background:#f9fafb;
    border:1px dashed var(--line);
    border-radius:18px;
    padding:22px;
    color:var(--muted);
    font-weight:600;
  }

  @media (max-width: 1100px){
    .accordion{
      grid-template-columns:repeat(3, 1fr);
    }
  }

  @media (max-width: 900px){
    .info-grid{
      grid-template-columns:1fr;
    }

    .page-title{
      font-size:30px;
    }

    .nav{
      flex-wrap:wrap;
    }

    .nav-links{
      gap:10px;
      flex-wrap:wrap;
    }

    .accordion{
      grid-template-columns:repeat(2, 1fr);
    }
  }

  @media (max-width: 640px){
    .profile-card,
    .list-card{
      padding:20px;
      border-radius:22px;
    }

    .profile-name{
      font-size:24px;
    }

    .nav-right .nav-monkey{
      height:48px;
      margin-left:16px;
    }

    .accordion{
      grid-template-columns:1fr;
    }
  }
</style>
</head>
<body>

  <div class="nav-wrap">
    <div class="nav">
      <div class="nav-right">
        <a class="avatar" href="student-info.php" title="My Account" data-i18n-title="student_info_my_account">
          <?php echo htmlspecialchars($avatar_initial); ?>
        </a>
        <img src="uploads/MONKEY.png" alt="QOYN Logo" class="nav-monkey">
        <a href="index.php" class="logo">QOYN</a>
      </div>

      <div class="nav-spacer"></div>

      <ul class="nav-links">
        <li><a href="student-dashboard.php" data-i18n="nav_home">Home</a></li>
        <li><a href="my_courses.php" data-i18n="nav_my_courses">My Courses</a></li>
        <li><a href="my_project.php" data-i18n="nav_phase2">Phase 2</a></li>
        <li><a href="my_capstone.php" data-i18n="nav_phase3">Phase 3</a></li>
        <li><a href="student_chat.php" data-i18n="nav_chat">Chat</a></li>
        <li><a href="student-info.php" class="active" data-i18n="nav_my_account">My Account</a></li>

        <li>
          <div class="lang-dropdown" id="langDropdown">
            
            <div class="lang-menu" id="langMenu">
              <button class="lang-option" data-lang="en" type="button">English</button>
              <button class="lang-option" data-lang="ar" type="button">العربية</button>
            </div>
          </div>
        </li>

<li><a href="#" id="logoutBtn" class="nav-logout" data-i18n="nav_logout">Logout</a></li>
      </ul>
    </div>
  </div>

  <main>
    <div class="container">

      <div class="page-head">
        <h1 class="page-title" data-i18n="student_info_my_account">My Account</h1>
      </div>

      <div class="profile-card">
        <div class="profile-top">
          <div class="profile-avatar">
            <?php echo htmlspecialchars($avatar_initial); ?>
          </div>

          <div>
            <h2 class="profile-name">
              <?php echo htmlspecialchars($full_name !== "" ? $full_name : "Student"); ?>
            </h2>
            <p class="profile-role" data-i18n="student_info_student_account">Student Account</p>
          </div>
        </div>

        <div class="info-grid">
          <div class="info-box">
            <div class="info-label" data-i18n="student_info_major">Major</div>
            <div class="info-value">
              <?php echo htmlspecialchars($major_text !== "" ? $major_text : "Not available"); ?>
            </div>
          </div>

          <div class="info-box">
            <div class="info-label" data-i18n="student_info_path">Path</div>
            <div class="info-value">
              <?php echo htmlspecialchars($path_title !== "" ? $path_title : "Not selected yet"); ?>
            </div>
          </div>

          <div class="info-box">
            <div class="info-label" data-i18n="student_info_coins">Coins</div>
            <div class="info-value">
              🪙 <?php echo $coins; ?>
            </div>
          </div>

          <div class="info-box">
            <div class="info-label" data-i18n="student_info_email">Email</div>
            <div class="info-value">
              <?php echo htmlspecialchars($email !== "" ? $email : "Not available"); ?>
            </div>
          </div>
        </div>

        <div class="actions">
          <a href="student-dashboard.php" class="btn btn-primary" data-i18n="student_info_back_dashboard">Back to Dashboard</a>
          <a href="student-info.php?show=students" class="btn btn-light" data-i18n="student_info_all_students">All Students</a>
          <a href="student-info.php?show=companies" class="btn btn-light" data-i18n="student_info_companies">Companies</a>
        </div>
      </div>

      <?php if ($show === "students"): ?>
        <div class="list-card">
          <h3 class="list-title" data-i18n="student_info_all_students">All Students</h3>

          <?php if (!empty($students)): ?>
            <div class="accordion">
              <?php foreach ($students as $s): ?>
                <details class="person-card">
                  <summary class="person-summary">
                    <span>
                      <?php echo htmlspecialchars($s["full_name"] ?: "No name"); ?>
                      <span class="person-short" data-i18n="student_info_click_details">Click to show details</span>
                    </span>
                  </summary>

                  <div class="person-content">
                    <div class="info-label" data-i18n="student_info_student_id">Student ID</div>
                    <div class="info-value"><?php echo (int)($s["id"] ?? 0); ?></div>

                    <div class="info-label" data-i18n="student_info_email">Email</div>
                    <div class="info-value" style="font-size:16px;">
                      <?php echo htmlspecialchars($s["email"] ?: "No email"); ?>
                    </div>

                    <div class="info-label" data-i18n="student_info_major">Major</div>
                    <div class="info-value" style="font-size:16px;">
                      <?php echo htmlspecialchars($s["major_text"] ?: "Not available"); ?>
                    </div>

                    <div class="info-label" data-i18n="student_info_path">Path</div>
                    <div class="info-value" style="font-size:16px;">
                      <?php echo htmlspecialchars($s["path_title"] ?: "Not selected yet"); ?>
                    </div>

                    <div class="info-label" data-i18n="student_info_coins">Coins</div>
                    <div class="info-value">
                      🪙 <?php echo (int)($s["coins"] ?? 0); ?>
                    </div>
                  </div>
                </details>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-box" data-i18n="student_info_no_students">No students found.</div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($show === "companies"): ?>
        <div class="list-card">
          <h3 class="list-title" data-i18n="student_info_companies">Companies</h3>

          <?php if (!empty($companies)): ?>
            <div class="accordion">
              <?php foreach ($companies as $c): ?>
                <details class="person-card">
                  <summary class="person-summary">
                    <span>
                      <?php echo htmlspecialchars($c["full_name"] ?: "No name"); ?>
                      <span class="person-short" data-i18n="student_info_click_details">Click to show details</span>
                    </span>
                  </summary>

                  <div class="person-content">
                    <div class="info-label" data-i18n="student_info_company_id">Company ID</div>
                    <div class="info-value"><?php echo (int)($c["id"] ?? 0); ?></div>

                    <div class="info-label" data-i18n="student_info_email">Email</div>
                    <div class="info-value" style="font-size:16px;">
                      <?php echo htmlspecialchars($c["email"] ?: "No email"); ?>
                    </div>
                  </div>
                </details>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-box" data-i18n="student_info_no_companies">No companies found.</div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const currentLangText = document.getElementById("currentLangText");
      const langOptions = document.querySelectorAll(".lang-option");

      function updateLangButton() {
        const lang = (typeof getCurrentLang === "function") ? getCurrentLang() : (localStorage.getItem("lang") || "en");
        if (currentLangText) {
          currentLangText.textContent = lang === "ar" ? "العربية" : "English";
        }
        langOptions.forEach(btn => {
          btn.classList.toggle("active", btn.dataset.lang === lang);
        });
      }

      updateLangButton();

      document.addEventListener("languageChanged", updateLangButton);
    });
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