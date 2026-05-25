(() => {
  const trigger = document.querySelector('.about-btn[data-i18n="qoyne"]');
  if (document.getElementById('qoyneChatRoot')) return;

  const styles = `
    #qoyneChatRoot{
      position:fixed;
      inset:0;
      z-index:10050;
      pointer-events:none;
      font-family:Poppins, Arial, sans-serif;
    }

    #qoyneChatRoot.open{
      pointer-events:auto;
    }

    .qoyne-chat-backdrop{
      position:absolute;
      inset:0;
      background:rgba(10,20,40,.20);
      opacity:0;
      transition:opacity .22s ease;
    }

    #qoyneChatRoot.open .qoyne-chat-backdrop{
      opacity:1;
      pointer-events:auto;
    }

    /* =========================
       DESKTOP / LAPTOP
       ========================= */
    .qoyne-chat-panel{
      position:absolute;
      right:22px;
      bottom:120px;
      width:170px;
      max-width:calc(100vw - 40px);
      height:260px;
      max-height:calc(100vh - 170px);
      background:#fff;
      border:1px solid rgba(10,46,93,.10);
      border-radius:28px;
      box-shadow:0 30px 90px rgba(0,0,0,.18);
      overflow:hidden;
      display:flex;
      flex-direction:column;
      transform:translateY(18px) scale(.985);
      opacity:0;
      pointer-events:none;
      transition:transform .22s ease, opacity .22s ease;
    }

    #qoyneChatRoot.open .qoyne-chat-panel{
      transform:translateY(0) scale(1);
      opacity:1;
      pointer-events:auto;
    }

    .qoyne-chat-head{
      flex:0 0 auto;
      padding:18px 18px 14px;
      background:linear-gradient(135deg,#0A2E5D 0%, #143f7a 100%);
      color:#fff;
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:14px;
    }

    .qoyne-chat-head-main{
      min-width:0;
    }

    .qoyne-chat-title{
      margin:0;
      font:800 20px Montserrat, sans-serif;
      letter-spacing:.2px;
    }

    .qoyne-chat-sub{
      margin-top:5px;
      font-size:12px;
      line-height:1.55;
      color:rgba(255,255,255,.92);
      max-width:280px;
    }

    .qoyne-chat-close{
      width:42px;
      height:42px;
      border:none;
      border-radius:999px;
      background:rgba(255,255,255,.14);
      color:#fff;
      font-size:24px;
      line-height:1;
      cursor:pointer;
      flex:0 0 42px;
      transition:transform .16s ease, background .16s ease;
    }

    .qoyne-chat-close:hover{
      transform:translateY(-1px);
      background:rgba(255,255,255,.24);
    }

    .qoyne-chat-body{
      flex:1 1 auto;
      min-height:0;
      display:flex;
      flex-direction:column;
      background:#f7f9fc;
    }

    .qoyne-chat-suggestions{
      flex:0 0 auto;
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:10px;
      padding:12px 14px 10px;
      background:#f7f9fc;
      border-bottom:1px solid rgba(10,46,93,.05);
    }

    .qoyne-chat-chip{
      border:1px solid rgba(10,46,93,.08);
      background:#fff;
      color:#0A2E5D;
      border-radius:18px;
      padding:12px 10px;
      min-height:54px;
      font-size:13px;
      font-weight:700;
      line-height:1.35;
      text-align:center;
      cursor:pointer;
      box-shadow:0 8px 18px rgba(0,0,0,.04);
      transition:transform .16s ease, background .16s ease, box-shadow .16s ease;
      white-space:normal;
    }

    .qoyne-chat-chip:hover{
      transform:translateY(-2px);
      background:#eef4ff;
      box-shadow:0 12px 24px rgba(0,0,0,.06);
    }

    .qoyne-chat-list{
      flex:1 1 auto;
      min-height:0;
      overflow:auto;
      padding:14px;
      display:flex;
      flex-direction:column;
      gap:12px;
      scrollbar-width:thin;
    }

    .qoyne-chat-row{
      display:flex;
    }

    .qoyne-chat-row.user{
      justify-content:flex-end;
    }

    .qoyne-chat-row.bot{
      justify-content:flex-start;
    }

    .qoyne-chat-bubble{
      max-width:84%;
      padding:13px 15px;
      border-radius:18px;
      font-size:14px;
      line-height:1.75;
      white-space:pre-wrap;
      word-break:break-word;
    }

    .qoyne-chat-row.user .qoyne-chat-bubble{
      background:#0A2E5D;
      color:#fff;
      border-bottom-right-radius:7px;
      box-shadow:0 10px 24px rgba(10,46,93,.18);
    }

    .qoyne-chat-row.bot .qoyne-chat-bubble{
      background:#fff;
      color:#122033;
      border:1px solid rgba(10,46,93,.06);
      border-bottom-left-radius:7px;
      box-shadow:0 8px 20px rgba(0,0,0,.04);
    }

    .qoyne-chat-foot{
      flex:0 0 auto;
      padding:12px;
      border-top:1px solid rgba(10,46,93,.08);
      background:#fff;
      display:flex;
      align-items:center;
      gap:10px;
    }

    .qoyne-chat-input{
      flex:1 1 auto;
      height:52px;
      border:1px solid rgba(10,46,93,.12);
      border-radius:16px;
      padding:0 16px;
      font-size:14px;
      outline:none;
      background:#fafbfc;
      transition:border-color .16s ease, background .16s ease, box-shadow .16s ease;
    }

    .qoyne-chat-input:focus{
      border-color:rgba(10,46,93,.28);
      background:#fff;
      box-shadow:0 0 0 4px rgba(10,46,93,.05);
    }

    .qoyne-chat-send{
      min-width:64px;
      height:52px;
      border:none;
      border-radius:16px;
      background:#FFC24A;
      color:#0A2E5D;
      font-weight:800;
      cursor:pointer;
      padding:0 16px;
      box-shadow:0 10px 22px rgba(255,194,74,.30);
      transition:transform .16s ease, filter .16s ease;
    }

    .qoyne-chat-send:hover{
      transform:translateY(-2px);
      filter:brightness(1.02);
    }

    /* =========================
       LARGE LAPTOP / DESKTOP
       ========================= */
    @media (min-width: 1280px){
      .qoyne-chat-panel{
        width:380px;
        height:540px;
        bottom:118px;
        right:20px;
      }
    }

    /* =========================
       SMALL LAPTOP / TABLET
       ========================= */
    @media (max-width: 1100px){
      .qoyne-chat-panel{
        right:14px;
        bottom:112px;
        width:340px;
        height:520px;
        max-height:calc(100vh - 150px);
      }
    }

    /* =========================
       MOBILE
       ========================= */
    @media (max-width: 640px){
      .qoyne-chat-panel{
        right:10px;
        left:10px;
        width:auto;
        bottom:108px;
        height:68vh;
        max-height:68vh;
        border-radius:24px;
      }

      .qoyne-chat-head{
        padding:16px 14px 12px;
      }

      .qoyne-chat-title{
        font-size:18px;
      }

      .qoyne-chat-sub{
        font-size:11px;
        max-width:none;
      }

      .qoyne-chat-suggestions{
        padding:10px 10px 8px;
        gap:8px;
      }

      .qoyne-chat-chip{
        min-height:48px;
        font-size:11.5px;
        padding:9px 8px;
      }

      .qoyne-chat-list{
        padding:12px 10px;
      }

      .qoyne-chat-bubble{
        max-width:88%;
        font-size:13px;
        padding:11px 12px;
      }

      .qoyne-chat-foot{
        padding:10px;
      }

      .qoyne-chat-input{
        height:48px;
        font-size:13px;
      }

      .qoyne-chat-send{
        min-width:50px;
        height:48px;
        padding:0 12px;
      }
    }
  `;

  const styleTag = document.createElement('style');
  styleTag.textContent = styles;
  document.head.appendChild(styleTag);

  const root = document.createElement('div');
  root.id = 'qoyneChatRoot';
  root.innerHTML = `
    <div class="qoyne-chat-backdrop"></div>

    <section class="qoyne-chat-panel" aria-label="QOYNE chat panel">
      <div class="qoyne-chat-head">
        <div class="qoyne-chat-head-main">
          <h3 class="qoyne-chat-title">QOYNE</h3>
          <div class="qoyne-chat-sub">Ask about pages, profile, results, courses, features, and how to use the website.</div>
        </div>
        <button class="qoyne-chat-close" type="button" aria-label="Close">×</button>
      </div>

      <div class="qoyne-chat-body">
        <div class="qoyne-chat-suggestions">
          <button class="qoyne-chat-chip" type="button">What does this page do?</button>
          <button class="qoyne-chat-chip" type="button">Where are my results?</button>
          <button class="qoyne-chat-chip" type="button">How do I open my profile?</button>
          <button class="qoyne-chat-chip" type="button">كيف أستخدم هذا الموقع؟</button>
        </div>

        <div class="qoyne-chat-list"></div>

        <div class="qoyne-chat-foot">
          <input class="qoyne-chat-input" type="text" placeholder="Ask about any page or feature..." />
          <button class="qoyne-chat-send" type="button">Send</button>
        </div>
      </div>
    </section>
  `;

  document.body.appendChild(root);

  const list = root.querySelector('.qoyne-chat-list');
  const input = root.querySelector('.qoyne-chat-input');
  const send = root.querySelector('.qoyne-chat-send');
  const closeBtn = root.querySelector('.qoyne-chat-close');
  const backdrop = root.querySelector('.qoyne-chat-backdrop');
  const currentPage = location.pathname.split('/').pop() || 'student-dashboard.php';

  function hasArabic(text) {
    return /[\u0600-\u06FF]/.test(text || '');
  }

  function addMessage(text, who = 'bot') {
    const row = document.createElement('div');
    row.className = `qoyne-chat-row ${who}`;

    const bubble = document.createElement('div');
    bubble.className = 'qoyne-chat-bubble';
    bubble.textContent = text;

    if (hasArabic(text)) {
      bubble.style.direction = 'rtl';
      bubble.style.textAlign = 'right';
    } else {
      bubble.style.direction = 'ltr';
      bubble.style.textAlign = 'left';
    }

    row.appendChild(bubble);
    list.appendChild(row);
    list.scrollTop = list.scrollHeight;
    return bubble;
  }

  function openPanel(e) {
    if (e) e.preventDefault();
    root.classList.add('open');
    setTimeout(() => input.focus(), 60);
  }

  function closePanel() {
    root.classList.remove('open');
  }

  window.openQoyneChat = openPanel;
  window.closeQoyneChat = closePanel;

  if (trigger) {
    trigger.addEventListener('click', openPanel);
  }

  closeBtn.addEventListener('click', closePanel);
  backdrop.addEventListener('click', closePanel);

  root.querySelectorAll('.qoyne-chat-chip').forEach((btn) => {
    btn.addEventListener('click', () => {
      input.value = btn.textContent.trim();
      doSend();
    });
  });

  async function doSend() {
    const message = (input.value || '').trim();
    if (!message) return;

    const isArabic = hasArabic(message);
    addMessage(message, 'user');
    input.value = '';

    const loading = addMessage(isArabic ? 'جاري التفكير...' : 'Thinking...', 'bot');

    try {
      const response = await fetch('api/qoyne_chat.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          message,
          current_page: currentPage
        })
      });

      const text = await response.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch (e) {
        loading.textContent = isArabic ? 'ردّ الخادم غير صالح.' : 'Server returned an invalid response.';
        return;
      }

      loading.textContent = data.answer || (isArabic ? 'لم يتم إرجاع جواب.' : 'No answer was returned.');

      if (hasArabic(loading.textContent)) {
        loading.style.direction = 'rtl';
        loading.style.textAlign = 'right';
      } else {
        loading.style.direction = 'ltr';
        loading.style.textAlign = 'left';
      }
    } catch (e) {
      loading.textContent = isArabic
        ? 'يوجد مشكلة في الاتصال. حاول مرة ثانية.'
        : 'Connection problem. Please try again.';
    }

    list.scrollTop = list.scrollHeight;
  }

  send.addEventListener('click', doSend);

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      doSend();
    }
  });

  addMessage(
    'Hello! I am QOYNE. Ask me about this page, your results, profile, courses, or ask me in العربية.',
    'bot'
  );
})();