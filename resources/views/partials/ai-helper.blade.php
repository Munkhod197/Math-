<div id="ai-helper-root" class="ai-helper-root">
  <div id="ai-helper-panel" class="hidden ai-helper-panel" role="dialog" aria-labelledby="ai-helper-title" aria-hidden="true">
    <div class="flex items-center justify-between gap-3 px-4 py-3 text-white bg-navy">
      <div>
        <p id="ai-helper-title" class="text-sm font-extrabold">AI Тайлбар</p>
        <p id="ai-helper-source" class="text-[11px] text-white/60"></p>
      </div>
      <button id="ai-helper-close" type="button" class="flex items-center justify-center w-8 h-8 text-lg rounded-lg bg-white/10 hover:bg-white/20" aria-label="Хаах">×</button>
    </div>

    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
      <p class="text-[10px] font-extrabold tracking-widest uppercase text-slate-400">Одоогийн бодлого</p>
      <p id="ai-helper-problem" class="mt-1 text-sm font-bold leading-6 text-navy"></p>
    </div>

    <div id="ai-helper-answer" class="px-4 py-4 overflow-y-auto text-sm leading-7 max-h-72 text-slate-700">
      <p class="text-slate-400">Тайлбар авахын тулд доорх товчийг дарна уу.</p>
    </div>
  </div>

  <button id="ai-helper-btn" type="button" class="ai-helper-btn" aria-expanded="false" aria-controls="ai-helper-panel">
    <span class="ai-helper-btn-ring" aria-hidden="true"></span>
    <span class="text-xl leading-none">🤖</span>
    <span class="font-extrabold">AI Тайлбар</span>
  </button>
</div>

<script>
(function () {
  const init = () => {
    const root = document.getElementById('ai-helper-root');
    const btn = document.getElementById('ai-helper-btn');
    const panel = document.getElementById('ai-helper-panel');
    const closeBtn = document.getElementById('ai-helper-close');
    const problemEl = document.getElementById('ai-helper-problem');
    const sourceEl = document.getElementById('ai-helper-source');
    const answerEl = document.getElementById('ai-helper-answer');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const sendUrl = @json(route('ai-teacher.send'));

    if (!root || !btn || !panel || !csrf) return;

    const skipPattern = /^(бэлэн үү|\.{3}|дууслаа|\?+|—+)$/i;
    let open = false;
    let loading = false;

    window.__mathmonActiveQuestion = window.__mathmonActiveQuestion || null;

    document.querySelectorAll('[data-ai-question]').forEach((node) => {
      node.closest('article, .qcard, div')?.addEventListener('pointerdown', () => {
        window.__mathmonActiveQuestion = node.getAttribute('data-ai-question') || node.textContent.trim();
      });
    });

    const escapeHtml = (value) => String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;');

    const renderMath = (node) => {
      if (typeof renderMathInElement === 'undefined' || !node) return;
      renderMathInElement(node, {
        delimiters: [
          { left: '$$', right: '$$', display: true },
          { left: '$', right: '$', display: false },
          { left: '\\(', right: '\\)', display: false },
          { left: '\\[', right: '\\]', display: true },
        ],
        throwOnError: false,
      });
    };

    const cleanQuestion = (text) => text.replace(/^\d+\.\s*/, '').replace(/\s+/g, ' ').trim();

    const readQuestion = (id) => {
      const node = document.getElementById(id);
      if (!node) return null;
      const text = cleanQuestion(node.textContent || '');
      if (!text || skipPattern.test(text)) return null;
      return text;
    };

    const detectProblem = () => {
      if (window.__mathmonActiveQuestion) {
        return { text: cleanQuestion(window.__mathmonActiveQuestion), source: 'Сонгосон бодлого' };
      }

      for (const id of ['toy-question', 'question', 'game-question']) {
        const text = readQuestion(id);
        if (text) return { text, source: id === 'toy-question' ? 'Тоглоом' : 'Бодлого' };
      }

      const articles = [...document.querySelectorAll('article[data-question-index]')];
      const visible = articles.find((article) => {
        const rect = article.getBoundingClientRect();
        return rect.top < window.innerHeight * 0.75 && rect.bottom > 120;
      });

      if (visible) {
        const node = visible.querySelector('[data-ai-question]');
        if (node) {
          return {
            text: cleanQuestion(node.getAttribute('data-ai-question') || node.textContent || ''),
            source: 'Дасгал',
          };
        }
      }

      const pageTitle = document.querySelector('main h1')?.textContent?.trim();
      if (pageTitle) {
        return { text: null, pageTitle, source: 'Ерөнхий' };
      }

      return { text: null, source: 'Ерөнхий' };
    };

    const setPanelOpen = (value) => {
      open = value;
      panel.classList.toggle('hidden', !open);
      panel.classList.toggle('ai-helper-panel-open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    };

    const showAnswer = (html) => {
      answerEl.innerHTML = html;
      renderMath(answerEl);
    };

    const explain = async () => {
      if (loading) return;

      const detected = detectProblem();

      if (detected.text) {
        problemEl.textContent = detected.text;
      } else if (detected.pageTitle) {
        problemEl.textContent = `"${detected.pageTitle}" хуудсанд одоогоор тодорхой бодлого олдсонгүй.`;
      } else {
        problemEl.textContent = 'Одоогоор бодлого олдсонгүй. Тоглоом эсвэл дасгал хэсэг рүү орно уу.';
      }

      sourceEl.textContent = detected.source;
      setPanelOpen(true);

      if (!detected.text) {
        showAnswer('<p class="text-slate-500">Бодлого харагдах хэсэг рүү орсон үед дахин дарна уу.</p>');
        return;
      }

      loading = true;
      btn.disabled = true;
      showAnswer('<p class="text-slate-400">AI багш тайлбар бэлдэж байна...</p>');

      const message = `Дараах бодлогыг монгол хэл дээр алхам алхмаар тайлбарла:\n\n${detected.text}`;

      try {
        const response = await fetch(sendUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: JSON.stringify({
            message,
            mode: 'explain',
            level: 'normal',
            context: {
              page: document.title,
              problem: detected.text,
              source: detected.source,
            },
          }),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
          const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
          throw new Error(firstError || data.message || 'Алдаа гарлаа');
        }

        showAnswer(escapeHtml(data.message.message).replace(/\n/g, '<br>'));
      } catch (error) {
        showAnswer(`<p class="text-rose-600">${escapeHtml(error.message || 'Тайлбар авахад алдаа гарлаа.')}</p>`);
      } finally {
        loading = false;
        btn.disabled = false;
      }
    };

    btn.addEventListener('click', () => {
      if (open && !loading) {
        setPanelOpen(false);
        return;
      }
      explain();
    });

    closeBtn.addEventListener('click', () => setPanelOpen(false));

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && open) setPanelOpen(false);
    });

    root.classList.add('ai-helper-ready');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>
