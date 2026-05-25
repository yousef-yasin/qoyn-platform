(function () {
  const lang = localStorage.getItem("lang") || "en";

  const translations = {
    login: {
      ar: {
        title: "QOYN | تسجيل الدخول",
        formTitleStudent: "تسجيل الدخول كطالب",
        formTitlePartner: "تسجيل الدخول كشريك",
        sideTitlePartner: "مرحباً بالشريك!",
        sideTitleStudent: "مرحباً أيها الطالب!",
        sideTextPartner: "إذا كنت شركة أو مدرباً أو جامعة أو مؤسسة، قم بتسجيل الدخول كشريك.",
        sideTextStudent: "سجّل الدخول لتكمل رحلتك التعليمية، وتكسب QOYN Coins، وتفتح فرصاً حقيقية.",
        toggleToPartner: "تسجيل الدخول كشريك",
        toggleToStudent: "تسجيل الدخول كطالب",
        email: "البريد الإلكتروني",
        password: "كلمة المرور",
        forgot: "هل نسيت كلمة المرور؟",
        login: "تسجيل الدخول",
        signup: "إنشاء حساب",
        name: "الاسم",
        partnerTypeDefault: "اختر نوع الشريك",
        university: "جامعة",
        company: "شركة",
        instructor: "مدرب"
      },
      en: {
        title: "QOYN | Login",
        formTitleStudent: "Login As Student",
        formTitlePartner: "Login As Partner",
        sideTitlePartner: "Hello Partner!",
        sideTitleStudent: "Hello, Student!",
        sideTextPartner: "If you are a company, tutor, university, or institution, log in as a Partner.",
        sideTextStudent: "Log in to continue your learning journey, earn QOYN Coins, and unlock real opportunities.",
        toggleToPartner: "Login As Partner",
        toggleToStudent: "Login As Student",
        email: "Email",
        password: "Password",
        forgot: "Forgot your password?",
        login: "Login",
        signup: "Sign up",
        name: "Name",
        partnerTypeDefault: "Select Your Partner Type",
        university: "University",
        company: "Company",
        instructor: "Instructor"
      }
    }
  };

  function getPageKey() {
    const path = window.location.pathname.toLowerCase();

    if (path.includes("login")) return "login";

    return null;
  }

  function applyDirection() {
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
  }

  function applyLoginTranslations(t) {
    if (!t) return;

    document.title = t.title;

    const formTitle = document.getElementById("formTitle");
    const sideTitle = document.getElementById("sideTitle");
    const sideText = document.getElementById("sideText");
    const toggleBtn = document.getElementById("toggleModeBtn");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const forgot = document.querySelector(".forgot");
    const submitBtn = document.querySelector(".btn-primary[type='submit']");
    const signupBtn = document.querySelector(".actions a.btn.btn-primary");
    const partnerNameInput = document.querySelector(".partner-only input[type='text']");
    const partnerTypeSelect = document.querySelector(".partner-only select");

    if (email) email.placeholder = t.email;
    if (password) password.placeholder = t.password;
    if (forgot) forgot.textContent = t.forgot;
    if (submitBtn) submitBtn.textContent = t.login;
    if (signupBtn) signupBtn.textContent = t.signup;
    if (partnerNameInput) partnerNameInput.placeholder = t.name;

    if (partnerTypeSelect && partnerTypeSelect.options.length >= 4) {
      partnerTypeSelect.options[0].text = t.partnerTypeDefault;
      partnerTypeSelect.options[1].text = t.university;
      partnerTypeSelect.options[2].text = t.company;
      partnerTypeSelect.options[3].text = t.instructor;
    }

    const isPartnerMode = document.querySelector(".page")?.classList.contains("partner-mode");

    if (isPartnerMode) {
      if (formTitle) formTitle.textContent = t.formTitlePartner;
      if (sideTitle) sideTitle.textContent = t.sideTitleStudent;
      if (sideText) sideText.textContent = t.sideTextStudent;
      if (toggleBtn) toggleBtn.textContent = t.toggleToStudent;
    } else {
      if (formTitle) formTitle.textContent = t.formTitleStudent;
      if (sideTitle) sideTitle.textContent = t.sideTitlePartner;
      if (sideText) sideText.textContent = t.sideTextPartner;
      if (toggleBtn) toggleBtn.textContent = t.toggleToPartner;
    }
  }

  function applyTranslations() {
    applyDirection();

    const pageKey = getPageKey();
    if (!pageKey) return;

    const t = translations[pageKey][lang];
    if (pageKey === "login") {
      applyLoginTranslations(t);
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    applyTranslations();

    const page = document.querySelector(".page");
    if (page) {
      const observer = new MutationObserver(function () {
        applyTranslations();
      });

      observer.observe(page, {
        attributes: true,
        childList: true,
        subtree: true,
        characterData: true
      });
    }
  });
})();