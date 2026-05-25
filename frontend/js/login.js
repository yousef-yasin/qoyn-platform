console.log("login.js loaded ✅");

const form = document.getElementById("loginForm");
const errorEl = document.getElementById("error");

function showError(msg) {
  if (!errorEl) return;
  errorEl.textContent = msg || "";
}

function isPartnerMode() {
  // 1) إذا عندك متغير partnerMode بالصفحة (inline script)
  if (typeof window.partnerMode !== "undefined") return !!window.partnerMode;

  // 2) افحص body
  if (document.body.classList.contains("partner-mode")) return true;

  // 3) افحص .page
  const page = document.querySelector(".page");
  return !!(page && page.classList.contains("partner-mode"));
}


if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim().toLowerCase();
    const password = document.getElementById("password").value;

    const type = isPartnerMode() ? "partner" : "student";

    showError("");

    try {
      const res = await fetch("/utbn-backend/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ email, password, type }),
      });
      const data = await res.json().catch(() => ({}));
      console.log("LOGIN:", res.status, data);

      if (!res.ok || !data.ok) {
        if (data.error === "WRONG_ROLE") {
          showError("انت بتحاول تسجل دخول بالنوع الغلط (Student/Partner).");
        } else {
          showError(data.error || data.message || `فشل تسجيل الدخول (Status: ${res.status})`);
        }
        return;
      }

      localStorage.setItem("csrf_token", data.csrf_token || "");

      const role = data.role || type;

      if (role === "admin") {
        window.location.replace("/utbn-web/dashboard.php");
      } else if (role === "partner") {
        window.location.replace("/utbn-web/company.php");
      } else {
        window.location.replace("student-dashboard.php#home");
      }

    } catch (err) {
      console.error(err);
      showError("مشكلة اتصال بالسيرفر. تأكد أنك مشغل Apache (XAMPP).");
    }
  });
}
