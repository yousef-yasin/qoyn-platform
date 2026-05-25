let ytTimer = null;

document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("ytQ");
  if (input) {
    input.addEventListener("input", () => {
      clearTimeout(ytTimer);
      ytTimer = setTimeout(() => ytSearch(input.value.trim()), 400);
    });
  }

  // Load required courses list (from uploaded plan analysis)
  loadRequiredCourses();
  const reload = document.getElementById("reloadReq");
  if (reload) reload.addEventListener("click", loadRequiredCourses);
});

async function loadRequiredCourses() {
  const list = document.getElementById("requiredList");
  const majorEl = document.getElementById("majorName");
  if (!list) return;

  list.innerHTML = `<li class="item"><div class="muted">جاري تحميل مواد التخصص...</div></li>`;
  if (majorEl) majorEl.textContent = "...";

  try {
    const res = await fetch(`/utbn-backend/api/plan_required_courses.php`, { credentials: "include" });
    const j = await res.json().catch(() => ({}));
    if (!res.ok || !j.ok) {
      list.innerHTML = `<li class="item"><div class="muted">ما قدرنا نجيب مواد التخصص. ارجع لصفحة رفع الخطة واضغط تحليل.</div></li>`;
      return;
    }

    if (majorEl) {
      majorEl.textContent = j.major_name ? `التخصص: ${j.major_name}` : "التخصص: (غير معروف من الخطة)";
    }

    const courses = Array.isArray(j.required_courses) ? j.required_courses : [];
    if (courses.length === 0) {
      list.innerHTML = `<li class="item"><div class="muted">ما في مواد إجباري مخزنة. الحل: ارفع الخطة من جديد واضغط تحليل.</div></li>`;
      return;
    }

    list.innerHTML = "";
    courses.forEach((c) => {
      const li = document.createElement("li");
      li.className = "item";
      const q = `${c.code} ${c.name}`.trim();
      li.innerHTML = `
        <div style="flex:1">
          <b>${escapeHtml(c.code || "")}</b> - ${escapeHtml(c.name || "")}
          ${c.group_title ? `<div class="muted" style="margin-top:4px">${escapeHtml(c.group_title)}</div>` : ""}
        </div>
        <button class="btn ghost" type="button">عرض الدورات</button>
      `;
      li.querySelector("button")?.addEventListener("click", () => {
        const input = document.getElementById("ytQ");
        if (input) input.value = q;
        ytSearch(q);
        document.getElementById("ytList")?.scrollIntoView({ behavior: "smooth", block: "start" });
      });
      list.appendChild(li);
    });

    // Auto-load first course results to make the page feel alive
    const first = courses[0];
    if (first) {
      const q = `${first.code} ${first.name}`.trim();
      const input = document.getElementById("ytQ");
      if (input && !input.value) input.value = q;
      ytSearch(q);
    }
  } catch (e) {
    list.innerHTML = `<li class="item"><div class="muted">خطأ غير متوقع أثناء تحميل مواد التخصص</div></li>`;
  }
}

async function ytSearch(q) {
  const list = document.getElementById("ytList");
  if (!list) return;
  list.innerHTML = "";
  if (!q) return;

  list.innerHTML = `<li class="yt-item">جاري البحث...</li>`;

  try {
    const res = await fetch(
      `/utbn-backend/api/youtube_search.php?q=${encodeURIComponent(q)}&max=8`,
      { credentials: "include" }
    );

    if (!res.ok) {
      list.innerHTML = `<li class="yt-item">صار خطأ بتحميل النتائج</li>`;
      return;
    }

    const data = await res.json();

    if (!data.items || data.items.length === 0) {
      list.innerHTML = `<li class="yt-item">ما في نتائج</li>`;
      return;
    }

    list.innerHTML = "";
    data.items.forEach((p) => {
      const li = document.createElement("li");
      li.className = "yt-item";
      li.innerHTML = `
        <img src="${p.thumb}" class="yt-thumb" />
        <div class="yt-info">
          <div class="yt-title">${escapeHtml(p.title)}</div>
          <div class="yt-channel">${escapeHtml(p.channelTitle)}</div>
          <a class="yt-link" target="_blank"
             href="https://www.youtube.com/playlist?list=${p.playlistId}">
             فتح الدورة
          </a>
        </div>
      `;
      list.appendChild(li);
    });
  } catch (e) {
    list.innerHTML = `<li class="yt-item">خطأ غير متوقع</li>`;
  }
}

function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, (s) =>
    ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[s])
  );
}
