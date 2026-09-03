@extends('layouts.app')

@section('title', 'AI Багш')
@section('hide_ai_helper', true)

@section('content')
@php
  $modes = [
    'explain' => ['label' => 'Тайлбарлах', 'desc' => 'Ойлголтыг энгийнээр'],
    'solve_together' => ['label' => 'Хамт бодох', 'desc' => 'Алхам алхмаар'],
    'check' => ['label' => 'Шалгах', 'desc' => 'Хариу зөв эсэх'],
    'hint' => ['label' => 'Санамж', 'desc' => 'Бүтэн хариу биш'],
    'practice' => ['label' => 'Дасгал', 'desc' => 'Шинэ бодлого'],
  ];

  $levels = [
    'simple' => 'Энгийн',
    'normal' => 'Стандарт',
    'detailed' => 'Дэлгэрэнгүй',
  ];

  $prompts = [
    '2x + 5 = 13 яаж бодох вэ?',
    'Пифагорын теоремийг тайлбарла',
    '3, 6, 12, 24 дарааллын дараагийн тоо?',
    'Тэгш өнцөгтийн талбай хэрхэн олох вэ?',
  ];
@endphp

<section class="px-4 py-10 ai-teacher-page">
  <div class="max-w-6xl mx-auto">
    <div class="relative overflow-hidden text-white bg-navy rounded-[2rem] shadow-2xl shadow-navy/20">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_14%_20%,rgba(245,158,11,.22),transparent_24%),radial-gradient(circle_at_88%_18%,rgba(56,189,248,.24),transparent_28%)]"></div>
      <div class="relative grid gap-8 p-6 sm:p-8 lg:grid-cols-[1.1fr_.9fr] lg:items-center lg:p-10">
        <div>
          <span class="inline-flex items-center gap-2 px-4 py-2 mb-5 text-xs font-extrabold tracking-widest uppercase border rounded-full border-white/15 bg-white/10 text-white/75">
            🤖 MathMon AI Багш
          </span>
          <h1 class="text-3xl font-extrabold leading-tight sm:text-5xl">Математикийн асуултаа хамт шийдье</h1>
          <p class="max-w-2xl mt-4 text-sm leading-7 sm:text-base text-white/65">
            Бодлого, томъёо, ойлголтоо монгол хэл дээр асуу. AI багш тайлбарлаж, алхам алхмаар зааж, дасгал өгнө.
          </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
          @foreach([
            ['💬', '5 горим', 'Тайлбар, хамт бодох, шалгах'],
            ['📐', 'KaTeX', 'Томъёо зөв харагдана'],
            ['🧠', 'Ухаалаг', 'OpenAI эсвэл built-in tutor'],
            ['📚', 'Түүх', 'Ярилцлага хадгалагдана'],
          ] as [$icon, $title, $desc])
            <div class="p-5 border rounded-3xl bg-white/10 border-white/10">
              <div class="text-2xl">{{ $icon }}</div>
              <h2 class="mt-3 font-extrabold">{{ $title }}</h2>
              <p class="mt-1 text-sm leading-6 text-white/55">{{ $desc }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="grid gap-6 mt-8 lg:grid-cols-[17rem_1fr]">
      <aside class="p-5 bg-white border border-slate-100 shadow-lg rounded-[1.75rem] shadow-navy/5 h-fit lg:sticky lg:top-24">
        <div class="flex items-center justify-between gap-3 mb-4">
          <p class="text-[10px] font-extrabold tracking-widest uppercase text-brand">Ярилцлага</p>
          <a href="{{ route('ai-teacher.index') }}" class="px-3 py-1.5 text-xs font-extrabold text-white rounded-xl bg-navy hover:brightness-110">Шинэ</a>
        </div>

        <div class="space-y-2 max-h-[28rem] overflow-y-auto">
          @forelse($conversations as $conversation)
            <a href="{{ route('ai-teacher.index', ['conversation' => $conversation->id]) }}"
               class="block px-4 py-3 text-sm transition border rounded-2xl {{ ($active?->id === $conversation->id) ? 'border-brand/30 bg-brand/5 text-navy' : 'border-slate-100 text-slate-600 hover:border-slate-200 hover:bg-slate-50' }}">
              <p class="font-extrabold truncate">{{ $conversation->title }}</p>
              <p class="mt-1 text-xs text-slate-400">{{ $conversation->updated_at->diffForHumans() }}</p>
            </a>
          @empty
            <p class="px-2 py-6 text-sm text-center text-slate-400">Одоогоор ярилцлага байхгүй. Эхний асуултаа илгээнэ үү.</p>
          @endforelse
        </div>
      </aside>

      <div class="overflow-hidden bg-white border border-slate-100 shadow-xl rounded-[2rem] shadow-navy/5">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 sm:px-7">
          <div>
            <p class="text-[10px] font-extrabold tracking-widest uppercase text-slate-400">Сургалтын горим</p>
            <p id="ai-mode-label" class="mt-1 text-sm font-extrabold text-navy">{{ $modes['explain']['label'] }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            @foreach($levels as $value => $label)
              <button type="button" data-level="{{ $value }}"
                      class="ai-level px-3 py-2 text-xs font-extrabold transition border rounded-xl {{ $value === 'normal' ? 'bg-navy text-white border-navy' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-navy/20' }}">
                {{ $label }}
              </button>
            @endforeach
          </div>
        </div>

        <div class="px-5 py-4 border-b border-slate-100 sm:px-7">
          <div class="flex gap-2 pb-1 overflow-x-auto ai-mode-scroll">
            @foreach($modes as $value => $meta)
              <button type="button" data-mode="{{ $value }}"
                      class="ai-mode flex-none px-4 py-2.5 text-xs font-extrabold transition border rounded-2xl {{ $value === 'explain' ? 'bg-brand/10 text-brand border-brand/20' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/20 hover:text-brand' }}">
                {{ $meta['label'] }}
              </button>
            @endforeach
          </div>
        </div>

        <div id="ai-chat" class="flex flex-col gap-4 px-5 py-6 overflow-y-auto sm:px-7 ai-chat-scroll" style="min-height: 22rem; max-height: 32rem;">
          @forelse($messages as $message)
            <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
              <div class="max-w-[88%] px-4 py-3 text-sm leading-7 rounded-2xl ai-message {{ $message->role === 'user' ? 'bg-navy text-white rounded-br-md' : 'bg-slate-50 text-slate-700 border border-slate-100 rounded-bl-md' }}">
                {!! nl2br(e($message->message)) !!}
              </div>
            </div>
          @empty
            <div id="ai-empty" class="flex flex-col items-center justify-center py-10 text-center">
              <div class="flex items-center justify-center w-16 h-16 text-3xl rounded-3xl bg-brand/10">🤖</div>
              <h2 class="mt-5 text-xl font-extrabold text-navy">Сайн байна уу!</h2>
              <p class="max-w-md mt-2 text-sm leading-7 text-slate-500">
                Математикийн асуултаа бичээрэй. Би тайлбарлаж, хамт бодож, дасгал өгч чадна.
              </p>
              <div class="flex flex-wrap justify-center gap-2 mt-6">
                @foreach($prompts as $prompt)
                  <button type="button" data-prompt="{{ $prompt }}"
                          class="px-4 py-2 text-xs font-bold transition border rounded-full border-slate-200 text-slate-600 hover:border-brand/30 hover:bg-brand/5 hover:text-brand">
                    {{ $prompt }}
                  </button>
                @endforeach
              </div>
            </div>
          @endforelse
        </div>

        <form id="ai-form" class="p-5 border-t border-slate-100 sm:p-7">
          @csrf
          <input type="hidden" id="ai-conversation-id" value="{{ $active?->id }}">
          <label for="ai-message" class="sr-only">Асуулт</label>
          <div class="flex flex-col gap-3 sm:flex-row">
            <textarea id="ai-message" rows="2" maxlength="4000" placeholder="Жишээ нь: 3x + 4 = 19 хэрхэн бодох вэ?"
                      class="flex-1 px-4 py-3 text-sm resize-none auth-input min-h-[3.25rem]"></textarea>
            <button id="ai-send" type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-extrabold text-white transition rounded-2xl bg-navy hover:-translate-y-0.5 hover:shadow-lg hover:shadow-navy/20 disabled:opacity-60 disabled:cursor-not-allowed">
              Илгээх
            </button>
          </div>
          <p id="ai-status" class="mt-3 text-xs font-semibold text-slate-400">Enter = илгээх · Shift+Enter = шинэ мөр</p>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@push('head')
<style>
  .ai-mode-scroll,
  .ai-chat-scroll {
    scrollbar-width: thin;
  }
  .ai-message .katex {
    font-size: 1.05em;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modes = @json($modes);
  let mode = 'explain';
  let level = 'normal';
  let sending = false;

  const chat = document.getElementById('ai-chat');
  const form = document.getElementById('ai-form');
  const input = document.getElementById('ai-message');
  const sendBtn = document.getElementById('ai-send');
  const statusEl = document.getElementById('ai-status');
  const conversationInput = document.getElementById('ai-conversation-id');
  const modeLabel = document.getElementById('ai-mode-label');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content
    || form.querySelector('input[name="_token"]')?.value;

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

  const escapeHtml = (value) => value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

  const appendMessage = (role, text) => {
    document.getElementById('ai-empty')?.remove();

    const wrap = document.createElement('div');
    wrap.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

    const bubble = document.createElement('div');
    bubble.className = `max-w-[88%] px-4 py-3 text-sm leading-7 rounded-2xl ai-message ${
      role === 'user'
        ? 'bg-navy text-white rounded-br-md'
        : 'bg-slate-50 text-slate-700 border border-slate-100 rounded-bl-md'
    }`;
    bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');

    wrap.appendChild(bubble);
    chat.appendChild(wrap);
    renderMath(bubble);
    chat.scrollTop = chat.scrollHeight;

    return bubble;
  };

  document.querySelectorAll('.ai-mode').forEach((button) => {
    button.addEventListener('click', () => {
      mode = button.dataset.mode;
      document.querySelectorAll('.ai-mode').forEach((item) => {
        item.classList.remove('bg-brand/10', 'text-brand', 'border-brand/20');
        item.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
      });
      button.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
      button.classList.add('bg-brand/10', 'text-brand', 'border-brand/20');
      modeLabel.textContent = modes[mode]?.label || mode;
    });
  });

  document.querySelectorAll('.ai-level').forEach((button) => {
    button.addEventListener('click', () => {
      level = button.dataset.level;
      document.querySelectorAll('.ai-level').forEach((item) => {
        item.classList.remove('bg-navy', 'text-white', 'border-navy');
        item.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
      });
      button.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
      button.classList.add('bg-navy', 'text-white', 'border-navy');
    });
  });

  document.querySelectorAll('[data-prompt]').forEach((button) => {
    button.addEventListener('click', () => {
      input.value = button.dataset.prompt || '';
      input.focus();
    });
  });

  chat.querySelectorAll('.ai-message').forEach((node) => renderMath(node));

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = input.value.trim();
    if (!message || sending) return;

    sending = true;
    sendBtn.disabled = true;
    statusEl.textContent = 'AI багш хариу бодож байна...';
    appendMessage('user', message);
    input.value = '';

    const typing = appendMessage('assistant', '...');

    try {
      const response = await fetch(@json(route('ai-teacher.send')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({
          conversation_id: conversationInput.value ? Number(conversationInput.value) : null,
          message,
          mode,
          level,
        }),
      });

      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(firstError || data.message || 'Алдаа гарлаа');
      }

      conversationInput.value = data.conversation.id;
      typing.innerHTML = escapeHtml(data.message.message).replace(/\n/g, '<br>');
      renderMath(typing);

      if (!window.location.search.includes(`conversation=${data.conversation.id}`)) {
        const url = new URL(window.location.href);
        url.searchParams.set('conversation', data.conversation.id);
        window.history.replaceState({}, '', url);
      }

      statusEl.textContent = 'Хариу ирлээ.';
    } catch (error) {
      typing.innerHTML = escapeHtml('Одоогоор хариу өгч чадсангүй. Дахин оролдоно уу.');
      statusEl.textContent = error.message || 'Алдаа гарлаа.';
    } finally {
      sending = false;
      sendBtn.disabled = false;
      input.focus();
    }
  });

  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      form.requestSubmit();
    }
  });

  chat.scrollTop = chat.scrollHeight;
});
</script>
@endpush
