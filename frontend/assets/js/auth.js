// utbn-web/assets/js/auth.js
const API_BASE = "/utbn-backend/api";

async function apiPost(path, data) {
  const res = await fetch(`${API_BASE}/${path}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(data),
  });
  const json = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, json };
}

function setMsg(el, msg, ok = false) {
  if (!el) return;
  el.textContent = msg;
  el.style.color = ok ? "#86efac" : "#fca5a5";
}

document.addEventListener("DOMContentLoaded", () => {
  // ===== LOGIN =====
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const msg = document.getElementById("msg");
      setMsg(msg, "جارٍ تسجيل الدخول...");

      const email = (document.getElementById("email")?.value || "").trim();
      const password = document.getElementById("password")?.value || "";

      try {
        const r = await apiPost("login.php", { email, password });

        if (r.ok) {
          // لازم login.php يرجّع role: student/partner
          const role = r.json?.role || "student";

          setMsg(msg, "تم تسجيل الدخول ✅", true);

          if (role === "partner") {
            location.replace("company.php");
          } else {
            location.replace("index.php");
          }
          return;
        }

        setMsg(msg, "إيميل أو كلمة سر غلط ❌");
      } catch (err) {
        console.error(err);
        setMsg(msg, "مشكلة اتصال بالسيرفر ❌");
      }
    });

    const goSignup = document.getElementById("goSignup");
    if (goSignup) goSignup.addEventListener("click", () => (location.href = "signup.html"));
  }

  // ===== SIGNUP =====
  const signupForm = document.getElementById("signupForm");
  if (signupForm) {
    signupForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const msg = document.getElementById("msg");
      setMsg(msg, "جارٍ إنشاء الحساب...");

      const full_name = (document.getElementById("full_name")?.value || "").trim();
      const email = (document.getElementById("email")?.value || "").trim();
      const phone = (document.getElementById("phone")?.value || "").trim();
      const password = document.getElementById("password")?.value || "";
      const confirm = document.getElementById("confirm")?.value || "";

      if (!full_name || !email || !password) return setMsg(msg, "عبّي كل الحقول المطلوبة ❌");
      if (password !== confirm) return setMsg(msg, "كلمتا السر غير متطابقتين ❌");

      // 👇 هون القرار: student ولا partner حسب toggle
      const isPartner = (window.getSignupMode && window.getSignupMode() === true);

      const endpoint = isPartner ? "signup_partner.php" : "signup.php";

      // payload موحد (بالشريك رح نخزن full_name كـ company_name داخل الـ API)
      const payload = { full_name, email, phone, password };

      try {
        const r = await apiPost(endpoint, payload);

        if (r.ok) {
          setMsg(msg, isPartner ? "تم إنشاء حساب الشريك ✅ روح سجل دخول" : "تم إنشاء حساب الطالب ✅ روح سجل دخول", true);
          setTimeout(() => (location.href = "login.html"), 600);
          return;
        }

        if (r.status === 409) return setMsg(msg, "هذا الإيميل موجود مسبقًا ❌");
        setMsg(msg, "صار خطأ أثناء التسجيل ❌");
      } catch (err) {
        console.error(err);
        setMsg(msg, "مشكلة اتصال بالسيرفر ❌");
      }
    });

    const goLogin = document.getElementById("goLogin");
    if (goLogin) goLogin.addEventListener("click", () => (location.href = "login.html"));
  }
});
