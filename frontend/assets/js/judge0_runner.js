// utbn-web/assets/js/judge0_runner.js
const API_BASE = "/utbn-backend/api";

async function runOnJudge0({ code, stdin, languageId }) {
  const res = await fetch(`${API_BASE}/judge0_run.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify({
      source_code: code,
      stdin: stdin || "",
      language_id: languageId || 71,
      cpu_time_limit: 2,
      memory_limit: 128000,
    }),
  });

  const json = await res.json().catch(() => ({}));
  if (!res.ok || !json.ok) return { ok: false, json };

  return { ok: true, json };
}

// helper to show output
function formatOutput(r) {
  const j = r.json || {};
  const out = [];
  out.push(`Status: ${j.status?.description || "?"}`);

  if (j.compile_output) out.push("\n--- Compile Output ---\n" + j.compile_output);
  if (j.stderr) out.push("\n--- Stderr ---\n" + j.stderr);
  if (j.stdout) out.push("\n--- Output ---\n" + j.stdout);

  if (!j.compile_output && !j.stderr && !j.stdout) out.push("\n(no output)");

  return out.join("\n");
}

// ✅ لازم تكون عناصر الصفحة موجودة:
// - textarea#codeEditor
// - textarea#stdin
// - select#lang
// - button#runBtn
// - pre#outputBox
document.addEventListener("DOMContentLoaded", () => {
  const codeEl = document.getElementById("codeEditor");
  const stdinEl = document.getElementById("stdin");
  const langEl = document.getElementById("lang");
  const runBtn = document.getElementById("runBtn");
  const outEl = document.getElementById("outputBox");

  if (!runBtn) return;

  runBtn.addEventListener("click", async () => {
    const code = codeEl?.value || "";
    const stdin = stdinEl?.value || "";
    const languageId = parseInt(langEl?.value || "71", 10);

    outEl.textContent = "Running...\n";

    const r = await runOnJudge0({ code, stdin, languageId });
    if (!r.ok) {
      outEl.textContent =
        "ERROR\n" + JSON.stringify(r.json, null, 2);
      return;
    }

    outEl.textContent = formatOutput(r);
  });
});
