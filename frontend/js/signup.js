console.log("signup.js loaded ✅");

document.getElementById("goLogin")?.addEventListener("click", () => {
  window.location.href = "login.html";
});

const form = document.getElementById("signupForm");
const msg = document.getElementById("msg");

function showMsg(t) {
  if (!msg) return;
  msg.textContent = t || "";
}

function isPartnerMode() {
  // مصدر الحقيقة الوحيد
  if (typeof window.getSignupMode === "function") {
    return window.getSignupMode() === "partner";
  }
  return false;
}



form?.addEventListener("submit", async (e) => {
  e.preventDefault();

  const partnerMode = isPartnerMode();

  const email = (document.getElementById("email")?.value || "").trim().toLowerCase();
  const password = document.getElementById("password")?.value || "";
  const confirm = document.getElementById("confirm")?.value || "";

  showMsg("");

  if (!email || !password) {
    showMsg("لازم تعبي الإيميل وكلمة المرور");
    return;
  }

  if (password.length < 6) {
    showMsg("كلمة المرور لازم تكون 6 أحرف أو أكثر");
    return;
  }

  if (password !== confirm) {
    showMsg("كلمة المرور وتأكيدها مش نفس الشي");
    return;
  }

  let endpoint = "";
  let payload = {};

  if (partnerMode) {
    const company_name =
      ((document.getElementById("company_name")?.value) ||
       (document.getElementById("full_name")?.value) ||
       "").trim();

    const partner_type =
      ((document.getElementById("partner_type")?.value) ||
       (document.getElementById("type")?.value) ||
       "").trim();

    if (!company_name || !partner_type) {
      showMsg("لازم تعبي Company name و Partner type");
      return;
    }

    endpoint = "/utbn-backend/api/signup_partner.php";
    payload = { company_name, email, partner_type, password };

  } else {
    const full_name = (document.getElementById("full_name")?.value || "").trim();
    const phone = (document.getElementById("phone")?.value || "").trim();

    if (!full_name) {
      showMsg("لازم تعبي الاسم الكامل");
      return;
    }

    endpoint = "/utbn-backend/api/signup.php";
    payload = { full_name, email, phone, password };
  }

  try {
    const res = await fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(payload),
    });

    const data = await res.json().catch(() => ({}));
    console.log("SIGNUP:", endpoint, res.status, data);

    const success = (data.ok === true || data.success === true);
    if (!res.ok || !success) {
      showMsg("فشل إنشاء الحساب: " + (data.error || data.message || `Status: ${res.status}`));
      return;
    }

    alert("تم إنشاء الحساب ✅ سجل دخول الآن");
    window.location.href = "login.html";
  } catch (err) {
    console.error(err);
    showMsg("مشكلة اتصال بالسيرفر. تأكد أنك مشغل Apache (XAMPP).");
  }
});
