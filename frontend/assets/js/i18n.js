let MESSAGES = {};

function getCurrentLang() {
  const lang = localStorage.getItem("lang");

  if (lang === "ar" || lang === "en") {
    return lang;
  }

  return "ar";
}

async function loadTranslations(lang) {
  const safeLang = (lang === "ar" || lang === "en") ? lang : "ar";
  const res = await fetch("assets/locales/" + safeLang + ".json");

  if (!res.ok) {
    throw new Error("Translation file not found: " + safeLang);
  }

  MESSAGES = await res.json();
}

function t(key) {
  return MESSAGES[key] || key;
}

function setDocumentLanguage(lang) {
  document.documentElement.lang = lang;
  document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
}

function applyTranslations() {
  document.querySelectorAll("[data-i18n]").forEach(el => {
    const key = el.dataset.i18n;
    const value = t(key);

    if (el.dataset.i18nHtml === "true") {
      el.innerHTML = value.replace(/\n/g, "<br>");
    } else {
      el.textContent = value;
    }
  });

  document.querySelectorAll("[data-i18n-placeholder]").forEach(el => {
    const key = el.dataset.i18nPlaceholder;
    el.placeholder = t(key);
  });

  document.querySelectorAll("[data-i18n-title]").forEach(el => {
    const key = el.dataset.i18nTitle;
    el.title = t(key);
  });

  const titleEl = document.querySelector("title[data-i18n]");
  if (titleEl) {
    document.title = t(titleEl.dataset.i18n);
  }
}

async function setLanguage(lang) {
  localStorage.setItem("lang", lang);
  await loadTranslations(lang);
  setDocumentLanguage(lang);
  applyTranslations();
if (typeof loadTasks === "function") {
  await loadTasks();
}
  document.dispatchEvent(new CustomEvent("languageChanged", {
    detail: { lang }
  }));
}

document.addEventListener("DOMContentLoaded", async () => {
  const lang = getCurrentLang();
  await loadTranslations(lang);
  setDocumentLanguage(lang);
  applyTranslations();

  const dropdown = document.getElementById("langDropdown");
  const trigger = document.getElementById("langTrigger");
  const currentLangText = document.getElementById("currentLangText");
  const options = document.querySelectorAll(".lang-option");

  if (dropdown && trigger && currentLangText && options.length) {
    currentLangText.textContent = lang === "ar" ? "العربية" : "English";

    options.forEach(option => {
      option.classList.toggle("active", option.dataset.lang === lang);

      option.addEventListener("click", async function () {
        const selectedLang = this.dataset.lang;
        await setLanguage(selectedLang);

        currentLangText.textContent = selectedLang === "ar" ? "العربية" : "English";

        options.forEach(opt => {
          opt.classList.toggle("active", opt.dataset.lang === selectedLang);
        });

        dropdown.classList.remove("open");
      });
    });

    trigger.addEventListener("click", function () {
      dropdown.classList.toggle("open");
    });

    document.addEventListener("click", function (e) {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove("open");
      }
    });
  }
  
});