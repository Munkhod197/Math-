@extends('admin.layout')

@section('title', 'Import preview')

@section('admin_content')
@php
    $total = count($items);
    $percent = $total > 0 ? round(($readyCount / $total) * 100) : 0;
@endphp

{{-- KaTeX --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js"></script>

<style>
    .math-btn {
        padding: 0.3rem 0.55rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.75);
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1;
    }
    .math-btn:hover {
        background: rgba(245,158,11,0.18);
        color: #fbbf24;
        border-color: rgba(245,158,11,0.35);
        transform: translateY(-1px);
    }
    .math-btn:active {
        transform: translateY(0);
    }
    .preview-box {
        background: linear-gradient(135deg, rgba(15,23,42,0.8), rgba(15,23,42,0.5));
    }
</style>

<div class="max-w-6xl mx-auto pb-12">

    {{-- Header Card --}}
    <div class="relative overflow-hidden mb-8 bg-slate-900/70 border border-white/5 rounded-3xl p-6 sm:p-7 shadow-2xl shadow-black/30">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(245,158,11,0.08),transparent_50%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(56,189,248,0.05),transparent_50%)]"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gold/10 text-gold border border-gold/20 text-xs font-bold tracking-wide">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $filename }}
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Import Preview</h1>
                <p class="mt-2 text-sm text-white/50">
                    <span class="text-emerald-400 font-semibold">{{ $readyCount }}</span>
                    /
                    <span class="text-white/70">{{ $total }}</span>
                    асуулт хадгалахад бэлэн
                </p>
            </div>

            <div class="lg:w-80">
                <div class="flex items-center justify-between text-xs text-white/40 mb-2.5">
                    <span class="font-medium">Бэлэн байдал</span>
                    <span class="font-bold text-gold">{{ $percent }}%</span>
                </div>
                <div class="h-3 rounded-full bg-slate-950/80 border border-white/5 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-gold shadow-lg shadow-gold/20 transition-all duration-700"
                         style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="relative mt-5 flex items-start gap-3 rounded-2xl border border-sky-500/15 bg-sky-500/5 px-4 py-3.5">
            <div class="mt-0.5 w-8 h-8 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-sky-200">Математик тэмдэгт</p>
                <p class="mt-0.5 text-xs text-sky-100/50 leading-relaxed">
                    Текст бичиж байхдаа товчлууруудыг дарж тэмдэгт оруулна. Жишээ: x², √, ≤, π гэх мэт.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="relative mt-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="relative mt-4 rounded-2xl border border-rose-500/25 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6" id="import-form">
        @csrf

        {{-- Sticky Action Bar --}}
        <div class="sticky top-3 z-20 bg-slate-950/90 backdrop-blur-xl border border-white/8 rounded-2xl p-3.5 shadow-2xl shadow-black/40">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap gap-1.5 max-h-20 overflow-y-auto">
                    @foreach($items as $index => $item)
                        @php
                            $isReady = filled($item['topic_id'] ?? null)
                                && filled($item['question_text'] ?? null)
                                && filled($item['option_a'] ?? null)
                                && filled($item['option_b'] ?? null)
                                && filled($item['option_c'] ?? null)
                                && filled($item['option_d'] ?? null)
                                && filled($item['correct_answer'] ?? null);
                        @endphp
                        <a href="#q{{ $index }}"
                           class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border transition-all duration-150
                                  {{ $isReady
                                      ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30 hover:bg-emerald-500/25'
                                      : 'bg-amber-500/10 text-amber-300 border-amber-500/25 hover:bg-amber-500/20' }}">
                            {{ $index + 1 }}
                        </a>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" formaction="{{ route('admin.tests.import.update') }}"
                            class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm font-semibold text-white/70
                                   hover:text-white hover:bg-white/10 hover:border-white/15 transition-all">
                        Preview хадгалах
                    </button>
                    <button type="submit" formaction="{{ route('admin.tests.import.save') }}"
                            class="px-5 py-2.5 rounded-xl bg-gold text-slate-900 text-sm font-bold
                                   hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/25
                                   active:scale-[0.98] transition-all">
                        Database-д импортлох
                    </button>
                </div>
            </div>
        </div>

        @foreach($items as $index => $item)
            @php
                $ready = filled($item['topic_id'] ?? null)
                    && filled($item['question_text'] ?? null)
                    && filled($item['option_a'] ?? null)
                    && filled($item['option_b'] ?? null)
                    && filled($item['option_c'] ?? null)
                    && filled($item['option_d'] ?? null)
                    && filled($item['correct_answer'] ?? null);
            @endphp

            <section id="q{{ $index }}"
                     class="scroll-mt-28 group relative bg-slate-900/60 border border-white/5 rounded-3xl p-5 sm:p-7
                            shadow-xl shadow-black/20 hover:border-white/10 transition-colors duration-200">

                {{-- Question header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3.5">
                        <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-950 border border-white/10
                                     text-white font-extrabold text-lg flex items-center justify-center shadow-inner">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-tight">Асуулт #{{ $index + 1 }}</h2>
                            <p class="text-xs text-white/35 mt-0.5">Засварлаад импортлоно</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border
                                 {{ $ready
                                     ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/25'
                                     : 'bg-amber-500/10 text-amber-300 border-amber-500/25' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $ready ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                        {{ $ready ? 'Ready' : 'Засах шаардлагатай' }}
                    </span>
                </div>

                <div class="grid lg:grid-cols-[1fr_16rem] gap-5">
                    <div class="space-y-5">

                        {{-- Question text --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2.5">
                                Асуултын текст
                            </label>

                            <div class="math-toolbar flex flex-wrap gap-1 mb-2.5 p-2.5 rounded-2xl bg-slate-950/50 border border-white/5"
                                 data-target="q{{ $index }}-text">
                                <button type="button" class="math-btn" data-insert="$x^{2}$">x²</button>
                                <button type="button" class="math-btn" data-insert="$x^{3}$">x³</button>
                                <button type="button" class="math-btn" data-insert="$x^{n}$">xⁿ</button>
                                <button type="button" class="math-btn" data-insert="$x_{1}$">x₁</button>
                                <button type="button" class="math-btn" data-insert="$\sqrt{}$">√</button>
                                <button type="button" class="math-btn" data-insert="$\sqrt[3]{}$">∛</button>
                                <button type="button" class="math-btn" data-insert="$\sqrt[n]{}$">ⁿ√</button>
                                <button type="button" class="math-btn" data-insert="$\frac{}{}$">a/b</button>
                                <button type="button" class="math-btn" data-insert="$\dfrac{}{}$">A/B</button>
                                <button type="button" class="math-btn" data-insert="$=$">=</button>
                                <button type="button" class="math-btn" data-insert="$\neq$">≠</button>
                                <button type="button" class="math-btn" data-insert="$\approx$">≈</button>
                                <button type="button" class="math-btn" data-insert="$\equiv$">≡</button>
                                <button type="button" class="math-btn" data-insert="$\pm$">±</button>
                                <button type="button" class="math-btn" data-insert="$\mp$">∓</button>
                                <button type="button" class="math-btn" data-insert="$\times$">×</button>
                                <button type="button" class="math-btn" data-insert="$\div$">÷</button>
                                <button type="button" class="math-btn" data-insert="$\cdot$">·</button>
                                <button type="button" class="math-btn" data-insert="$+$">+</button>
                                <button type="button" class="math-btn" data-insert="$-$">−</button>
                                <button type="button" class="math-btn" data-insert="$<$">&lt;</button>
                                <button type="button" class="math-btn" data-insert="$>$">&gt;</button>
                                <button type="button" class="math-btn" data-insert="$\leq$">≤</button>
                                <button type="button" class="math-btn" data-insert="$\geq$">≥</button>
                                <button type="button" class="math-btn" data-insert="$\angle$">∠</button>
                                <button type="button" class="math-btn" data-insert="$^\circ$">°</button>
                                <button type="button" class="math-btn" data-insert="$\triangle$">△</button>
                                <button type="button" class="math-btn" data-insert="$\perp$">⊥</button>
                                <button type="button" class="math-btn" data-insert="$\parallel$">∥</button>
                                <button type="button" class="math-btn" data-insert="$\pi$">π</button>
                                <button type="button" class="math-btn" data-insert="$\alpha$">α</button>
                                <button type="button" class="math-btn" data-insert="$\beta$">β</button>
                                <button type="button" class="math-btn" data-insert="$\theta$">θ</button>
                                <button type="button" class="math-btn" data-insert="$\Delta$">Δ</button>
                                <button type="button" class="math-btn" data-insert="$\infty$">∞</button>
                                <button type="button" class="math-btn" data-insert="$\%$">%</button>
                                <button type="button" class="math-btn" data-insert="$($">(</button>
                                <button type="button" class="math-btn" data-insert="$)$">)</button>
                                <button type="button" class="math-btn" data-insert="$[$">[</button>
                                <button type="button" class="math-btn" data-insert="$]$">]</button>
                                <button type="button" class="math-btn" data-insert="$|$">|</button>
                            </div>

                            <textarea name="questions[{{ $index }}][question_text]" rows="3" required
                                      id="q{{ $index }}-text"
                                      data-math-source="q{{ $index }}-text"
                                      class="math-input w-full p-4 rounded-2xl bg-slate-950/60 border border-white/8 text-white
                                             placeholder-white/25 resize-y focus:outline-none focus:ring-2 focus:ring-gold/40
                                             focus:border-gold/30 transition text-[15px] leading-relaxed">{{ old("questions.$index.question_text", $item['question_text'] ?? '') }}</textarea>

                            <div class="mt-2.5 p-3.5 rounded-2xl preview-box border border-white/5 text-sm text-white/85 min-h-[2.75rem]">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-white/25 block mb-1.5">Харагдах байдал</span>
                                <div id="q{{ $index }}-text-preview" class="math-preview leading-relaxed"></div>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="rounded-2xl bg-slate-950/40 border border-white/5 p-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2.5">
                                Зурагтай бодлого
                            </label>
                            <input type="hidden" name="questions[{{ $index }}][image_path]" value="{{ old("questions.$index.image_path", $item['image_path'] ?? '') }}">
                            @if(!empty($item['image_path']))
                                <img src="{{ asset('storage/' . $item['image_path']) }}"
                                     alt="Асуултын зураг"
                                     class="mb-3 max-h-52 rounded-xl border border-white/10 bg-white object-contain">
                            @endif
                            <input type="file"
                                   name="questions[{{ $index }}][image]"
                                   accept="image/*"
                                   class="block w-full text-sm text-white/50
                                          file:mr-4 file:rounded-xl file:border-0 file:bg-gold file:px-4 file:py-2.5
                                          file:text-sm file:font-bold file:text-slate-900
                                          hover:file:bg-amber-400 file:cursor-pointer file:transition">
                            <p class="mt-2 text-xs text-white/30">PNG, JPG, WEBP дэмжинэ</p>
                        </div>

                        {{-- Options --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-3">
                                Хариултууд
                            </label>
                            <div class="grid sm:grid-cols-2 gap-4">
                                @foreach(['option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D'] as $field => $label)
                                    <div class="rounded-2xl bg-slate-950/30 border border-white/5 p-3">
                                        <div class="math-toolbar flex flex-wrap gap-1 mb-2" data-target="q{{ $index }}-{{ $field }}">
                                            <button type="button" class="math-btn" data-insert="$x^{2}$">x²</button>
                                            <button type="button" class="math-btn" data-insert="$x^{3}$">x³</button>
                                            <button type="button" class="math-btn" data-insert="$\sqrt{}$">√</button>
                                            <button type="button" class="math-btn" data-insert="$\frac{}{}$">a/b</button>
                                            <button type="button" class="math-btn" data-insert="$=$">=</button>
                                            <button type="button" class="math-btn" data-insert="$\neq$">≠</button>
                                            <button type="button" class="math-btn" data-insert="$\pm$">±</button>
                                            <button type="button" class="math-btn" data-insert="$\times$">×</button>
                                            <button type="button" class="math-btn" data-insert="$\div$">÷</button>
                                            <button type="button" class="math-btn" data-insert="$\leq$">≤</button>
                                            <button type="button" class="math-btn" data-insert="$\geq$">≥</button>
                                            <button type="button" class="math-btn" data-insert="$^\circ$">°</button>
                                            <button type="button" class="math-btn" data-insert="$\pi$">π</button>
                                            <button type="button" class="math-btn" data-insert="$\infty$">∞</button>
                                        </div>

                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg
                                                         bg-gold/15 text-gold border border-gold/25
                                                         flex items-center justify-center text-xs font-bold z-10">
                                                {{ $label }}
                                            </span>
                                            <input name="questions[{{ $index }}][{{ $field }}]" required
                                                   id="q{{ $index }}-{{ $field }}"
                                                   data-math-source="q{{ $index }}-{{ $field }}"
                                                   value="{{ old("questions.$index.$field", $item[$field] ?? '') }}"
                                                   class="math-input w-full pl-12 pr-3.5 py-3.5 rounded-xl bg-slate-950/60
                                                          border border-white/8 text-white placeholder-white/25
                                                          focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold/30 transition">
                                        </div>
                                        <div class="mt-2 px-3 py-2 rounded-xl preview-box border border-white/5 text-xs text-white/70 min-h-[1.85rem]">
                                            <div id="q{{ $index }}-{{ $field }}-preview" class="math-preview"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Explanation --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2.5">
                                Тайлбар
                            </label>
                            <textarea name="questions[{{ $index }}][explanation]" rows="2"
                                      id="q{{ $index }}-explanation"
                                      data-math-source="q{{ $index }}-explanation"
                                      class="math-input w-full p-4 rounded-2xl bg-slate-950/60 border border-white/8 text-white
                                             placeholder-white/25 resize-y focus:outline-none focus:ring-2 focus:ring-gold/40
                                             focus:border-gold/30 transition text-[15px]">{{ old("questions.$index.explanation", $item['explanation'] ?? '') }}</textarea>
                            <div class="mt-2.5 p-3.5 rounded-2xl preview-box border border-white/5 text-sm text-white/85 min-h-[2.25rem]">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-white/25 block mb-1.5">Харагдах байдал</span>
                                <div id="q{{ $index }}-explanation-preview" class="math-preview leading-relaxed"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <aside class="space-y-4">
                        <div class="rounded-2xl bg-slate-950/40 border border-white/5 p-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2.5">
                                Сэдэв
                            </label>
                            <select name="questions[{{ $index }}][topic_id]" required
                                    class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/8 text-white
                                           focus:outline-none focus:ring-2 focus:ring-gold/40 focus:border-gold/30 transition">
                                <option value="">Сонгох</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" @selected(old("questions.$index.topic_id", $item['topic_id'] ?? null) == $topic->id)>
                                        {{ $topic->name }} ({{ $topic->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rounded-2xl bg-slate-950/40 border border-white/5 p-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-white/40 mb-2.5">
                                Зөв хариу
                            </label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $answer)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="questions[{{ $index }}][correct_answer]" value="{{ $answer }}"
                                               class="peer sr-only"
                                               @checked(old("questions.$index.correct_answer", $item['correct_answer'] ?? null) === $answer)>
                                        <span class="h-12 rounded-xl bg-slate-950/70 border border-white/8 text-white/50
                                                     flex items-center justify-center font-bold text-sm
                                                     peer-checked:bg-gold peer-checked:text-slate-900 peer-checked:border-gold
                                                     peer-checked:shadow-lg peer-checked:shadow-gold/20
                                                     hover:border-white/20 transition-all duration-150">
                                            {{ $answer }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        @endforeach
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.math-toolbar').forEach(toolbar => {
        const targetId = toolbar.getAttribute('data-target');
        const target = document.getElementById(targetId);
        if (!target) return;

        toolbar.querySelectorAll('.math-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const insert = btn.getAttribute('data-insert') || '';
                const start = target.selectionStart;
                const end = target.selectionEnd;
                const text = target.value;

                target.value = text.substring(0, start) + insert + text.substring(end);
                target.focus();
                target.selectionStart = target.selectionEnd = start + insert.length;
                target.dispatchEvent(new Event('input'));
            });
        });
    });

    function renderMath(el, text) {
        if (!el) return;
        el.innerHTML = text || '<span class="text-white/20">—</span>';
        if (window.renderMathInElement) {
            try {
                renderMathInElement(el, {
                    delimiters: [
                        {left: '$$', right: '$$', display: true},
                        {left: '$', right: '$', display: false},
                        {left: '\\(', right: '\\)', display: false},
                        {left: '\\[', right: '\\]', display: true}
                    ],
                    throwOnError: false
                });
            } catch (e) {}
        }
    }

    function updatePreview(input) {
        const key = input.getAttribute('data-math-source');
        if (!key) return;
        const preview = document.getElementById(key + '-preview');
        renderMath(preview, input.value);
    }

    document.querySelectorAll('.math-input').forEach(input => {
        updatePreview(input);
        input.addEventListener('input', () => updatePreview(input));
    });
});
</script>
@endsection
