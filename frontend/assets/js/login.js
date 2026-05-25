const form = document.getElementById("loginForm");
const errorEl = document.getElementById("error");

function showError(msg){ errorEl.textContent = msg || ""; }

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  showError("");

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;

  try {
    const res = await fetch("/utbn-backend/api/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ email, password, type: "student" }),
    });

    const data = await res.json().catch(() => ({}));

    if (res.ok && data.ok) {
      localStorage.setItem("csrf_token", data.csrf_token || "");
      window.location.href = data.redirect || "student-dashboard.php";
    } else {
      showError(data.error || "Login failed");
    }
  } catch (err) {
    showError("Server error / connection issue");
  }
});
