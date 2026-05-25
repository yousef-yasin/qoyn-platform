<?php
require_once __DIR__ . "/includes/auth.php";
if (isset($_SESSION["user_id"])) {
    $role = (string)($_SESSION["role"] ?? "student");
    if ($role === "admin") {
        header("Location: dashboard.php");
    } elseif ($role === "partner") {
        header("Location: company.php");
    } else {
        header("Location: student-dashboard.php#home");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QOYN | Sign up</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --navy:#0A2E5D;
      --bg-left:#F3F4F6;
      --text:#0B0B0B;
      --muted:#9AA3AF;
      --line:rgba(0,0,0,.18);
      --shadow: 0 18px 50px rgba(0,0,0,.12);
      --radius: 16px;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      font-family:"Poppins", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text);
      background:#fff;
      min-height:100vh;
    }

    /* ===== PAGE LAYOUT (Animated swap Student/Partner) ===== */
    .page{
      height: 100vh;
      min-height:100vh;
      position: relative;
      overflow: hidden;
    }
.left, .right{
  position: absolute;
  top: 0;
  bottom: 0;
  height: 100vh;
  transition: left 1.2s cubic-bezier(.22,.61,.36,1);
}

/* ✅ أهم سطرين: خلي اليسار (اللي فيه الفورم) فوق */
.left{ z-index: 2; }
.right{ z-index: 1; }


    .left{
      left:0;
      width:70%;
      background: var(--bg-left);
      display:flex;
      flex-direction:column;
      padding: 28px 42px;
    }

    .right{
      left:70%;
      width:30%;
      height: 100vh;
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 26px;
      position:relative;
      overflow:hidden;

      background:
        radial-gradient(900px 500px at 30% 30%, rgba(255,255,255,.18), transparent 60%),
        radial-gradient(700px 420px at 70% 65%, rgba(0,0,0,.18), transparent 55%),
        linear-gradient(135deg, rgba(10,46,93,1), rgba(10,46,93,.86));
    }

    .page.partner-mode .right{ left:0; }
    .page.partner-mode .left{ left:30%; }

    .page.partner-mode .left .logo{
      margin-left:auto;
      text-align:right;
    }

    /* ===== LEFT CONTENT ===== */
    .logo{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 26px;
      letter-spacing:.6px;
      color: var(--navy);
      user-select:none;
    }

    .left-center{
      flex:1;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      text-align:center;
      padding: 18px 0;
    }

    .title{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 44px;
      letter-spacing:-.6px;
      margin: 0 0 14px 0;
      color:#111;
    }

    .social{
      display:flex;
      gap: 12px;
      align-items:center;
      justify-content:center;
      margin: 8px 0 18px;
    }

    .icon-btn{
      width: 44px;
      height: 44px;
      border-radius: 999px;
      display:grid;
      place-items:center;
      text-decoration:none;
      background:#fff;
      border: 1px solid rgba(0,0,0,.08);
      color: rgba(0,0,0,.55);
      box-shadow: 0 10px 25px rgba(0,0,0,.08);
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    }

    .icon-btn:hover{
      transform: translateY(-2px) scale(1.03);
      color: var(--navy);
      border-color: rgba(10,46,93,.35);
      box-shadow: 0 16px 34px rgba(0,0,0,.14);
    }

    .icon-btn svg{ width: 18px; height:18px; }

    .form{
      width: min(420px, 92%);
      display:flex;
      flex-direction:column;
      gap: 12px;
      margin-top: 6px;
    }

    .field{
      position:relative;
      width:100%;
      background:#fff;
      border: 1px solid rgba(0,0,0,.10);
      border-radius: 12px;
      padding: 14px 14px 14px 46px;
      box-shadow: 0 10px 26px rgba(0,0,0,.08);
      transition: border-color .18s ease, box-shadow .18s ease;
      text-align:left;
    }

    .field:focus-within{
      border-color: rgba(10,46,93,.45);
      box-shadow: 0 14px 34px rgba(0,0,0,.12);
    }

    .field .in-ic{
      position:absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(0,0,0,.35);
    }

    .field .in-ic svg{ width:18px; height:18px; }

    .field input{
      width:100%;
      border:0;
      outline:0;
      background: transparent;
      font-size: 14px;
      font-weight:500;
      color:#111;
    }

    .field input::placeholder{
      color: rgba(0,0,0,.35);
      font-weight:500;
    }

    .forgot{
      margin: 6px 0 0;
      font-size: 13px;
      color:#111;
      font-weight:500;
      display:inline-block;
      text-decoration:none;
    }

    .forgot-underline{
      width: 140px;
      height: 1px;
      background: var(--line);
      margin: 6px auto 8px;
      border-radius: 999px;
    }

    .actions{
      display:flex;
      gap: 12px;
      justify-content:center;
      margin-top: 6px;
    }

    .btn{
      appearance:none;
      border:0;
      cursor:pointer;
      border-radius: 14px;
      padding: 12px 22px;
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 13px;
      letter-spacing:.3px;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width: 150px;
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease, filter .18s ease;
      box-shadow: 0 16px 34px rgba(0,0,0,.14);
    }

    .btn-primary{
      background: var(--navy);
      color:#fff;
    }

    .btn-primary:hover{
      transform: translateY(-3px);
      filter: brightness(1.08);
      box-shadow: 0 22px 48px rgba(0,0,0,.20);
    }

    .btn-primary:active{
      transform: translateY(-1px);
      filter: brightness(.98);
    }

    /* ===== RIGHT CONTENT ===== */
    .right::before, .right::after{
      content:"";
      position:absolute;
      width: 320px;
      height: 320px;
       pointer-events: none;
      border-radius: 40px;
      background: rgba(255,255,255,.10);
      transform: rotate(18deg);
      opacity:.55;
    }
    .right::before{ left:-120px; top: 40px; }
    .right::after{ right:-140px; bottom: 70px; border-radius: 60px; opacity:.35; }

    .right-card{
      position:relative;
      text-align:center;
      max-width: 320px;
      padding: 10px 8px;
    }

    .right-title{
      font-family:"Montserrat", sans-serif;
      font-weight:800;
      font-size: 40px;
      margin: 0 0 10px 0;
      letter-spacing:-.5px;
    }

    .right-text{
      margin: 0 0 18px 0;
      color: rgba(255,255,255,.90);
      font-size: 13.5px;
      line-height: 1.8;
    }

    .btn-outline{
      background: transparent;
      border: 2px solid rgba(255,255,255,.85);
      color:#fff;
      box-shadow: none;
      min-width: 210px;
      padding: 12px 18px;
      border-radius: 999px;
      transition: transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    }

    .btn-outline:hover{
      transform: translateY(-3px);
      background: #fff;
      color: var(--navy);
      border-color: #fff;
    }

    .btn-outline:active{ transform: translateY(-1px); }

    /* Partner-only fields (if later you add partner signup inputs) */
    .partner-only{ display:none; }
    .page.partner-mode .partner-only{ display:block; }

    /* small message */
    #msg{
      min-height:22px;
      font-size: 13px;
      color: rgba(0,0,0,.55);
      margin-top: 2px;
    }

    /* Responsive */
    @media (max-width: 980px){
      .left, .right{
        position: relative;
        left: 0 !important;
        width: 100% !important;
        transition: none;
      }
      .page{ overflow: visible; }
      .right{ min-height: 42vh; height:auto; }
      .left{ padding: 22px 18px; }
      .title{ font-size: 36px; }
      .right-title{ font-size: 34px; }
    }
  </style>
</head>

<body>
  <div class="page">
    <!-- LEFT -->
    <section class="left" aria-label="Student signup">
      <div class="logo">QOYN</div>

      <div class="left-center">
        <!-- ✅ تبديل النصوص فقط -->
        <h1 class="title" id="formTitle">Sign up As Student</h1>

        <div class="social" aria-label="Social links">
          <!-- Instagram -->
          <a class="icon-btn" href="https://www.instagram.com/qoyn.jo?igsh=dnFoZ3pmMWZodzNo" target="_blank" rel="noopener" aria-label="Instagram">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Z" stroke="currentColor" stroke-width="2"/>
              <path d="M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2"/>
              <path d="M17.5 6.5h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </a>

          <!-- LinkedIn -->
          <a class="icon-btn" href="https://www.linkedin.com/in/qoyn-jo-0b3aab3aa" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M4 4h4v16H4V4Z" stroke="currentColor" stroke-width="2"/>
              <path d="M10 10h4v10h-4V10Z" stroke="currentColor" stroke-width="2"/>
              <path d="M14 11c1-1 2-1.5 3.5-1.5 2.5 0 4.5 1.8 4.5 5.5V20h-4v-4.5c0-1.8-.7-2.8-2-2.8-1 0-1.6.5-2 1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M6 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </a>
        </div>

        <!-- ✅ لا تغيّر IDs نهائيًا (مربوطة بالـ JS/DB) -->
        <form id="signupForm" class="form" autocomplete="on" novalidate>

          <!-- Full name -->
          <label class="field">
            <span class="in-ic" aria-hidden="true">
              <!-- user icon -->
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z" stroke="currentColor" stroke-width="2"/>
                <path d="M3 21c0-4.4 4-7 9-7s9 2.6 9 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <input id="full_name" placeholder="Full name" required />
          </label>
<!-- Company name (Partner) -->
<!-- Company name (Partner) -->
<label class="field partner-only">
  <span class="in-ic" aria-hidden="true">🏢</span>
  <input id="company_name" placeholder="Company name" />
</label>

<!-- Partner type (Partner) -->
<label class="field partner-only">
  <span class="in-ic" aria-hidden="true">🤝</span>
  <input id="partner_type" placeholder="Partner type (company / tutor / university)" />
</label>


          <!-- Email -->
          <label class="field">
            <span class="in-ic" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2"/>
                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2"/>
              </svg>
            </span>
            <input id="email" type="email" placeholder="Email" required />
          </label>

          <!-- Phone -->
          <label class="field">
            <span class="in-ic" aria-hidden="true">
              <!-- phone icon -->
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M6 2h5l2 5-3 2c1 3 3 5 6 6l2-3 5 2v5c-9 2-19-8-17-17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <input id="phone" placeholder="Phone (optional)" />
          </label>

          <!-- Password -->
          <label class="field">
            <span class="in-ic" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2"/>
              </svg>
            </span>
            <input id="password" type="password" placeholder="Password" required />
          </label>

          <!-- Confirm -->
          <label class="field">
            <span class="in-ic" aria-hidden="true">
              <!-- lock-check icon -->
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2"/>
                <path d="m9.2 16 1.6 1.6 3.8-3.8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <input id="confirm" type="password" placeholder="Confirm Password" required />
          </label>

          <div id="msg"></div>

          <div class="actions">
            <!-- ✅ نفس منطق signup الأصلي: submit -->
            <button class="btn btn-primary" type="submit">Sign up</button>

            <!-- ✅ نفس الزر القديم (id=goLogin) -->
            <button class="btn btn-primary" type="button" id="goLogin">Login</button>
          </div>
        </form>
      </div>
    </section>

    <!-- RIGHT -->
    <section class="right" aria-label="Partner signup invitation">
      <div class="right-card">
        <h2 class="right-title" id="sideTitle">Hello Partner!</h2>
        <p class="right-text" id="sideText">
          If you are a company, tutor, university, or institution, sign up as a Partner.
        </p>
        <button class="btn btn-outline" id="toggleModeBtn" type="button">
          Sign up As Partner
        </button>
      </div>
    </section>
  </div>
  <!-- ✅ نفس ملف JS الأصلي (لا نلمسه) -->



  <!-- ✅ JS صغير فقط لتبديل النصوص/الواجهة (ما له علاقة بالداتا) -->
  <script>
  const page = document.querySelector(".page");
  const formTitle = document.getElementById("formTitle");
  const sideTitle = document.getElementById("sideTitle");
  const sideText  = document.getElementById("sideText");
  const toggleBtn = document.getElementById("toggleModeBtn");

  let partnerMode = false;
function setField(id, show, required=false){
  const el = document.getElementById(id);
  if (!el) return;

  const field = el.closest(".field");
  if (field) field.style.display = show ? "block" : "none";

  // ❌ لا disabled
  if (required && show) {
    el.setAttribute("required", "required");
  } else {
    el.removeAttribute("required");
  }
}


  function applyMode(){
    if (partnerMode) {
      page.classList.add("partner-mode");

      formTitle.textContent = "Sign up As Partner";
      sideTitle.textContent = "Hello, Student!";
      sideText.textContent  = "Sign up to start your learning journey, earn QOYN Coins, and unlock real opportunities.";
      toggleBtn.textContent = "Sign up as student";

      // partner fields ON
      setField("company_name", true, true);
      setField("partner_type", true, true);

      // student fields OFF
      setField("full_name", false, false);
      setField("phone", false, false);

    } else {
      page.classList.remove("partner-mode");

      formTitle.textContent = "Sign up As Student";
      sideTitle.textContent = "Hello Partner!";
      sideText.textContent  = "If you are a company, tutor, university, or institution, sign up as a Partner.";
      toggleBtn.textContent = "Sign up As Partner";

      // student fields ON
      setField("full_name", true, true);
      setField("phone", true, false);

      // partner fields OFF
      setField("company_name", false, false);
      setField("partner_type", false, false);
    }
  }

  toggleBtn.addEventListener("click", () => {
    partnerMode = !partnerMode;
    applyMode();
  });

  applyMode();
  window.getSignupMode = () => (partnerMode ? "partner" : "student");

</script>


<script src="js/signup.js" defer></script>

</body>
</html> 