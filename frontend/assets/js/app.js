// utbn-web/assets/js/app.js
const API_BASE = "/utbn-backend/api";

async function apiGet(path) {
  const res = await fetch(`${API_BASE}/${path}`, { credentials: "include" });
  const ct = res.headers.get("content-type") || "";
  let data;
  if (ct.includes("application/json")) data = await res.json();
  else data = await res.text();
  if (!res.ok) throw data;
  return data;
}

async function apiPost(path, payload) {
  const res = await fetch(`${API_BASE}/${path}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(payload || {}),
  });
  const ct = res.headers.get("content-type") || "";
  let data;
  if (ct.includes("application/json")) data = await res.json();
  else data = await res.text();
  if (!res.ok) throw data;
  return data;
}

function qs(k){ return new URLSearchParams(location.search).get(k); }

function escapeHtml(str){
  return String(str ?? "").replace(/[&<>"']/g, s => (
    {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]
  ));
}
