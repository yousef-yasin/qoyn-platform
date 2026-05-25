async function postJSON(url, payload) {
  const res = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    credentials: "include",
    body: JSON.stringify(payload || {})
  });

  const text = await res.text();
  let data = {};
  try {
    data = JSON.parse(text);
  } catch (e) {
    throw new Error(text || "Invalid JSON response");
  }

  if (!res.ok) {
    throw new Error(data.error || "Request failed");
  }

  return data;
}

function esc(s) {
  return String(s ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

async function loadSkillGap() {
  const box = document.getElementById("skillGapBox");
  box.innerHTML = "Loading...";

  try {
    const data = await postJSON("/utbn-backend/api/skill_gap.php", {});

    if (!data.ok) {
      box.innerHTML = `<div style="color:red;">${esc(data.error || "Skill gap failed")}</div>`;
      return;
    }

    const matched = (data.matched_skills || []).map(x =>
      `<li>${esc(x.skill_name)}${x.matched_by ? ` <small>(matched by: ${esc(x.matched_by)})</small>` : ""}</li>`
    ).join("");

    const missing = (data.missing_skills || []).map(x =>
      `<li>${esc(x.skill_name)} <small>(weight: ${esc(x.weight)})</small></li>`
    ).join("");

    box.innerHTML = `
      <div><strong>Target Role:</strong> ${esc(data.target_role_key)}</div>
      <div><strong>Coverage Score:</strong> ${esc(data.coverage_score)}</div>
      <div><strong>Gap Score:</strong> ${esc(data.gap_score)}</div>
      <hr>
      <div><strong>Matched Skills</strong></div>
      <ul>${matched || "<li>No matched skills</li>"}</ul>
      <div><strong>Missing Skills</strong></div>
      <ul>${missing || "<li>No missing skills</li>"}</ul>
    `;
  } catch (e) {
    box.innerHTML = `<div style="color:red;">${esc(e.message)}</div>`;
  }
}

async function loadRecommendations() {
  const box = document.getElementById("recommendBox");
  box.innerHTML = "Loading...";

  try {
    const data = await postJSON("/utbn-backend/api/recommend_playlists.php", {});

    if (!data.ok) {
      box.innerHTML = `<div style="color:red;">${esc(data.error || "Recommendations failed")}</div>`;
      return;
    }

    const items = (data.recommendations || []).map(r => `
      <div style="border:1px solid #ddd; border-radius:10px; padding:10px; margin-bottom:10px;">
        <div><strong>${esc(r.title)}</strong></div>
        <div>${esc(r.description || "")}</div>
        <div><small>Score: ${esc(r.score)}</small></div>
        <div><small>Reason: ${esc(r.reason)}</small></div>
      </div>
    `).join("");

    box.innerHTML = items || "<div>No recommendations found.</div>";
  } catch (e) {
    box.innerHTML = `<div style="color:red;">${esc(e.message)}</div>`;
  }
}

async function askMentor(projectId, teamId) {
  const q = document.getElementById("mentorQuestion");
  const box = document.getElementById("mentorAnswerBox");
  const question = (q.value || "").trim();

  if (!question) {
    box.innerHTML = `<div style="color:red;">Please write a question first.</div>`;
    return;
  }

  box.innerHTML = "Thinking...";

  try {
    const data = await postJSON("/utbn-backend/api/mentor_chat.php", {
      project_id: projectId,
      team_id: teamId,
      question: question,
      chat_context: []
    });

    if (!data.ok) {
      box.innerHTML = `<div style="color:red;">${esc(data.error || "Mentor failed")}</div>`;
      return;
    }

    const sources = (data.sources || []).map(s => `
      <li>
        <strong>${esc(s.source)}</strong><br>
        <small>${esc(s.snippet)}</small>
      </li>
    `).join("");

    box.innerHTML = `
      <div style="border:1px solid #ddd; border-radius:10px; padding:12px;">
        <div><strong>Answer</strong></div>
        <p>${esc(data.answer)}</p>
        <hr>
        <div><strong>Sources</strong></div>
        <ul>${sources || "<li>No sources</li>"}</ul>
      </div>
    `;
  } catch (e) {
    box.innerHTML = `<div style="color:red;">${esc(e.message)}</div>`;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const btnGap = document.getElementById("btnLoadSkillGap");
  const btnRec = document.getElementById("btnLoadRecommendations");
  const btnMentor = document.getElementById("btnAskMentor");

  const root = document.body;
  const projectId = Number(root.dataset.projectId || 0);
  const teamId = Number(root.dataset.teamId || 0);

  if (btnGap) {
    btnGap.addEventListener("click", loadSkillGap);
  }

  if (btnRec) {
    btnRec.addEventListener("click", loadRecommendations);
  }

  if (btnMentor) {
    btnMentor.addEventListener("click", function () {
      askMentor(projectId, teamId);
    });
  }
});