(() => {
  const API = "/utbn-backend/api/assistant_chat.php";

  function el(tag, attrs = {}, children = []) {
    const e = document.createElement(tag);
    Object.entries(attrs).forEach(([k, v]) => {
      if (k === "class") e.className = v;
      else if (k === "html") e.innerHTML = v;
      else e.setAttribute(k, v);
    });
    children.forEach((c) =>
      e.appendChild(typeof c === "string" ? document.createTextNode(c) : c)
    );
    return e;
  }

  function addMsg(list, text, who = "user") {
    const row = el("div", { class: `aiRow ${who}` }, [
      el("div", { class: "aiBubble" }, [text]),
    ]);
    list.appendChild(row);
    list.scrollTop = list.scrollHeight;
  }

  // ✅ Safe JSON parsing: لو السيرفر رجّع HTML رح نكشفه وما نكسر الكود
  async function ask(q) {
    let res;
    try {
      res = await fetch(API, {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: q, question: q, q }), // دعم لكل المفاتيح
      });
    } catch (e) {
      console.error("Fetch failed:", e);
      return { ok: false, error: "NETWORK_ERROR" };
    }

    const text = await res.text();

    // حاول نفك JSON
    try {
      const json = JSON.parse(text);
      return json;
    } catch (e) {
      // هنا غالباً PHP رجّع HTML (Warnings/Fatal)
      console.error("Assistant API returned NON-JSON:", text);
      return {
        ok: false,
        error: "SERVER_RETURNED_HTML",
        details: text.slice(0, 4000), // أول جزء من الرد للتشخيص
      };
    }
  }

  function mount() {
    if (document.getElementById("utbnAiWidget")) return;

    const widget = el("div", { id: "utbnAiWidget", class: "utbnAiWidget" }, [
      el("button", { class: "utbnAiFab", type: "button", title: "UTBN AI" }, [
        el("span", { class: "utbnAiFabDot" }),
        "AI",
      ]),
      el("div", { class: "utbnAiPanel" }, [
        el("div", { class: "utbnAiHeader" }, [
          el("div", { class: "utbnAiTitle" }, ["UTBN AI"]),
          el("button", { class: "utbnAiClose", type: "button" }, ["×"]),
        ]),
        el("div", { class: "utbnAiHint" }, ["اسألني عن صفحات الموقع أو أي سؤال عام"]),
        el("div", { class: "utbnAiList" }),
        el("div", { class: "utbnAiComposer" }, [
          el("input", {
            class: "utbnAiInput",
            type: "text",
            placeholder: "اكتب سؤالك هنا...",
          }),
          el("button", { class: "utbnAiSend", type: "button" }, ["إرسال"]),
        ]),
      ]),
    ]);

    document.body.appendChild(widget);

    const fab = widget.querySelector(".utbnAiFab");
    const panel = widget.querySelector(".utbnAiPanel");
    const closeBtn = widget.querySelector(".utbnAiClose");
    const list = widget.querySelector(".utbnAiList");
    const input = widget.querySelector(".utbnAiInput");
    const send = widget.querySelector(".utbnAiSend");

    function open() {
      panel.classList.add("open");
      input.focus();
    }
    function close() {
      panel.classList.remove("open");
    }

    fab.addEventListener("click", open);
    closeBtn.addEventListener("click", close);

    async function doSend() {
      const q = (input.value || "").trim();
      if (!q) return;

      input.value = "";
      addMsg(list, q, "user");

      // loading bubble
      addMsg(list, "...", "bot");
      const last = list.lastElementChild;

      const out = await ask(q);

      // عرض نتيجة واضحة
      if (out.ok) {
        last.querySelector(".aiBubble").textContent = out.answer || "";
      } else {
        // لو رجع HTML من السيرفر، خليه مفهوم للمستخدم
        const msg =
          out.error === "SERVER_RETURNED_HTML"
            ? "SERVER ERROR (PHP) — افتح Console / Network وشوف details"
            : (out.error || "ERROR");
        last.querySelector(".aiBubble").textContent = msg;
      }
    }

    send.addEventListener("click", doSend);
    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter") doSend();
    });

    // greeting
    addMsg(list, "أهلًا! اكتب اسم صفحة (مثل courses.php) أو اسأل أي سؤال.", "bot");
  }

  document.addEventListener("DOMContentLoaded", mount);
})();
