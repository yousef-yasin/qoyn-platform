(async () => {
  // تأكد من الجلسة من الباك-إند
  const res = await fetch("/utbn-backend/api/me.php", { credentials: "include" });

  if (!res.ok) {
    window.location.href = "/utbn-web/login.html";
    return;
  }

  const data = await res.json().catch(() => ({}));

  if (!data.loggedIn) {
    window.location.href = "/utbn-web/login.html";
    return;
  }

  // عرض الإيميل
  document.getElementById("userEmail").textContent = data.email || "—";
})();

// Logout
document.getElementById("logoutBtn").addEventListener("click", async () => {
  await fetch("/utbn-backend/api/logout.php", { credentials: "include" });
  window.location.href = "/utbn-web/login.html";
});
