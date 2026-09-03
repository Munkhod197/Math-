@extends('admin.layout')

@section('title', 'Асуулт нэмэх')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
<style>
  .math-btn {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    min-width: 3.25rem;
    padding: 0.4rem 0.5rem;
    border-radius: 0.75rem;
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.85);
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.15s;
    cursor: pointer;
  }
  .math-btn:hover {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fbbf24;
  }
  .math-btn small {
    font-size: 0.6rem;
    font-weight: 500;
    color: rgba(255,255,255,0.35);
    line-height: 1;
  }
  .math-btn:hover small { color: rgba(251, 191, 36, 0.7); }
  .katex-preview { min-height: 2.5rem; }
  .math-group-title {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.3);
    margin-bottom: 0.35rem;
  }
</style>
@endpush

@section('admin_content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">Асуулт нэмэх</h1>
        <p class="mt-2 text-white/50 text-sm">
            Доорх товчнуудаас тэмдэгт дарж оруулна. Тоогоо <code class="text-gold/80">{}</code> дотор бичнэ.
        </p>
    </div>

    <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl shadow-black/20">
        @csrf

        {{-- Сэдэв --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Сэдэв <span class="text-rose-400">*</span>
            </label>
            <div class="relative">
                <select name="topic_id" required
                        class="w-full appearance-none p-3.5 pr-10 rounded-xl bg-slate-950/70 border border-white/10
                               text-white focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40">
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">
                            {{ $topic->name }} ({{ $topic->grade_level }}-р анги)
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-white/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Math toolbar — бүлэглэсэн, хялбар --}}
        <div class="space-y-3 p-4 rounded-xl bg-slate-950/50 border border-white/5">
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-white/70">Математик тэмдэгт</label>
                <span class="text-[10px] text-white/30">Товч → талбар руу автомат орно</span>
            </div>

            {{-- Язгуур, зэрэг --}}
            <div>
                <div class="math-group-title">Язгуур · Зэрэг</div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="math-btn" data-insert="$\sqrt{x}$" data-cursor="-2">√x<small>язгуур</small></button>
                    <button type="button" class="math-btn" data-insert="$\sqrt[3]{x}$" data-cursor="-2">³√x<small>куб</small></button>
                    <button type="button" class="math-btn" data-insert="$\sqrt[n]{x}$" data-cursor="-2">ⁿ√x<small>n-р</small></button>
                    <button type="button" class="math-btn" data-insert="$x^{2}$" data-cursor="-2">x²<small>квадрат</small></button>
                    <button type="button" class="math-btn" data-insert="$x^{n}$" data-cursor="-2">xⁿ<small>зэрэг</small></button>
                    <button type="button" class="math-btn" data-insert="$x_{1}$" data-cursor="-2">x₁<small>индекс</small></button>
                </div>
            </div>

            {{-- Бутархай, хаалт --}}
            <div>
                <div class="math-group-title">Бутархай · Хаалт</div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="math-btn" data-insert="$\frac{a}{b}$" data-cursor="-4">a/b<small>бутархай</small></button>
                    <button type="button" class="math-btn" data-insert="$\dfrac{a}{b}$" data-cursor="-4">A/B<small>том</small></button>
                    <button type="button" class="math-btn" data-insert="$(x)$" data-cursor="-2">( )<small>хаалт</small></button>
                    <button type="button" class="math-btn" data-insert="$|x|$" data-cursor="-2">|x|<small>модуль</small></button>
                </div>
            </div>

            {{-- Матриц --}}
            <div>
                <div class="math-group-title">Матриц</div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="math-btn" data-insert="$\begin{pmatrix} a & b \\ c & d \end{pmatrix}$" data-cursor="0">( )<small>2×2</small></button>
                    <button type="button" class="math-btn" data-insert="$\begin{bmatrix} a & b \\ c & d \end{bmatrix}$" data-cursor="0">[ ]<small>2×2</small></button>
                    <button type="button" class="math-btn" data-insert="$\begin{pmatrix} a & b & c \\ d & e & f \\ g & h & i \end{pmatrix}$" data-cursor="0">( )<small>3×3</small></button>
                    <button type="button" class="math-btn" data-insert="$\begin{vmatrix} a & b \\ c & d \end{vmatrix}$" data-cursor="0">| |<small>det</small></button>
                </div>
            </div>

            {{-- Тэмдэгтүүд --}}
            <div>
                <div class="math-group-title">Тэмдэгт</div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="math-btn" data-insert="$\pi$">π<small>пи</small></button>
                    <button type="button" class="math-btn" data-insert="$\infty$">∞<small>хязгааргүй</small></button>
                    <button type="button" class="math-btn" data-insert="$\pm$">±<small>plus-minus</small></button>
                    <button type="button" class="math-btn" data-insert="$\times$">×<small>үржих</small></button>
                    <button type="button" class="math-btn" data-insert="$\div$">÷<small>хуваах</small></button>
                    <button type="button" class="math-btn" data-insert="$\leq$">≤<small>бага</small></button>
                    <button type="button" class="math-btn" data-insert="$\geq$">≥<small>их</small></button>
                    <button type="button" class="math-btn" data-insert="$\neq$">≠<small>тэнцүү биш</small></button>
                    <button type="button" class="math-btn" data-insert="$\approx$">≈<small>ойролцоо</small></button>
                    <button type="button" class="math-btn" data-insert="$^\circ$">°<small>градус</small></button>
                    <button type="button" class="math-btn" data-insert="$\angle$">∠<small>өнцөг</small></button>
                    <button type="button" class="math-btn" data-insert="$\triangle$">△<small>гурвалжин</small></button>
                </div>
            </div>

            {{-- Триг / лог --}}
            <div>
                <div class="math-group-title">Тригонометр · Лог</div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" class="math-btn" data-insert="$\sin$" data-cursor="0">sin</button>
                    <button type="button" class="math-btn" data-insert="$\cos$" data-cursor="0">cos</button>
                    <button type="button" class="math-btn" data-insert="$\tan$" data-cursor="0">tan</button>
                    <button type="button" class="math-btn" data-insert="$\log$" data-cursor="0">log</button>
                    <button type="button" class="math-btn" data-insert="$\ln$" data-cursor="0">ln</button>
                    <button type="button" class="math-btn" data-insert="$e^{x}$" data-cursor="-2">eˣ</button>
                </div>
            </div>
        </div>

        {{-- Асуулт --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Асуултын текст <span class="text-rose-400">*</span>
            </label>
            <textarea name="text" id="question_text" rows="3" required
                      placeholder="Жишээ: $\sqrt{49}$ хэд вэ?"
                      class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                             text-white placeholder-white/30 resize-y font-mono text-sm
                             focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40"></textarea>
            <div class="katex-preview p-3 rounded-xl bg-slate-950/40 border border-white/5 text-white text-sm" id="preview_text">
                <span class="text-white/30">Preview энд гарна...</span>
            </div>
        </div>

        <div class="space-y-2 rounded-xl bg-slate-950/45 border border-white/5 p-4">
            <label class="block text-sm font-medium text-white/70">Зурагтай бодлого</label>
            <input type="file" name="image" accept="image/*"
                   class="block w-full text-sm text-white/60 file:mr-4 file:rounded-lg file:border-0 file:bg-gold file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-amber-400">
            <p class="text-xs text-white/35">График, геометрийн зураг, screenshot зэргийг PNG/JPG/WEBP хэлбэрээр хавсаргаж болно.</p>
        </div>

        {{-- Хариултууд --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-white/70">
                Сонголтууд <span class="text-rose-400">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $name => $label)
                    <div class="space-y-1.5">
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center
                                         rounded-lg bg-gold/10 text-gold text-xs font-bold border border-gold/20">
                                {{ $label }}
                            </span>
                            <input name="{{ $name }}" id="opt_{{ $name }}" required
                                   placeholder="Жишээ: $7$"
                                   class="w-full pl-12 pr-3.5 py-3.5 rounded-xl bg-slate-950/70 border border-white/10
                                          text-white placeholder-white/30 font-mono text-sm
                                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40" />
                        </div>
                        <div class="katex-preview px-2 py-1 text-xs text-white/70" id="preview_{{ $name }}"></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Зөв хариулт --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Зөв хариулт <span class="text-rose-400">*</span>
            </label>
            <div class="flex flex-wrap gap-3">
                @foreach(['A', 'B', 'C', 'D'] as $opt)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="answer" value="{{ $opt }}" required class="peer sr-only"
                               {{ $opt === 'A' ? 'checked' : '' }}>
                        <span class="flex items-center justify-center w-12 h-12 rounded-xl
                                     bg-slate-950/70 border border-white/10 text-white/70 font-semibold
                                     peer-checked:bg-gold peer-checked:text-slate-900 peer-checked:border-gold
                                     peer-checked:shadow-lg peer-checked:shadow-gold/20
                                     hover:border-white/20 transition-all duration-200">
                            {{ $opt }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Тайлбар --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Тайлбар <span class="text-white/40 font-normal">(заавал биш)</span>
            </label>
            <textarea name="explanation" id="explanation_text" rows="2"
                      placeholder="Жишээ: $\sqrt{49}=7$ учир нь $7\times 7=49$"
                      class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                             text-white placeholder-white/30 resize-y font-mono text-sm
                             focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40"></textarea>
            <div class="katex-preview p-3 rounded-xl bg-slate-950/40 border border-white/5 text-white text-sm" id="preview_explanation"></div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.questions.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 transition-all">
                Болих
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                           bg-gold text-slate-900 font-semibold text-sm
                           hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/25 active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Хадгалах
            </button>
        </div>
    </form>

    {{-- Хялбар заавар --}}
    <div class="mt-6 p-5 rounded-2xl bg-slate-900/40 border border-white/5 text-sm space-y-3">
        <p class="font-semibold text-white/80">Хэрхэн ашиглах вэ?</p>
        <ol class="list-decimal list-inside space-y-1.5 text-white/50 text-xs">
            <li>Асуулт эсвэл хариултын талбар дээр дарж focus хийнэ</li>
            <li>Дээрх товчнуудаас тэмдэгт сонгоно (автомат орно)</li>
            <li><code class="text-gold/70">x</code>, <code class="text-gold/70">a</code>, <code class="text-gold/70">n</code> гэх мэтийг өөрийн тоогоор сольно</li>
            <li>Доорх Preview-д зөв харагдаж байгаа эсэхийг шалгана</li>
        </ol>
        <div class="grid sm:grid-cols-2 gap-2 font-mono text-xs pt-2 border-t border-white/5">
            <div class="text-white/40"><span class="text-gold">Язгуур:</span> $\sqrt{49}$ → 7</div>
            <div class="text-white/40"><span class="text-gold">Зэрэг:</span> $2^{10}$ → 1024</div>
            <div class="text-white/40"><span class="text-gold">Бутархай:</span> $\frac{3}{4}$</div>
            <div class="text-white/40"><span class="text-gold">Матриц:</span> товч дарж a,b,c,d солино</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    let activeField = document.getElementById('question_text');

    ['question_text', 'opt_a', 'opt_b', 'opt_c', 'opt_d', 'explanation_text'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('focus', function () { activeField = el; });
        el.addEventListener('input', function () { renderPreview(id); });
    });

    document.querySelectorAll('.math-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!activeField) {
                activeField = document.getElementById('question_text');
            }
            const insert = btn.getAttribute('data-insert');
            const cursorOff = parseInt(btn.getAttribute('data-cursor') || '0', 10);
            const start = activeField.selectionStart;
            const end = activeField.selectionEnd;
            const val = activeField.value;

            activeField.value = val.slice(0, start) + insert + val.slice(end);
            activeField.focus();

            // Курсорыг {} дотор байрлуулах
            let pos = start + insert.length + cursorOff;
            // Хамгийн сүүлийн {x} эсвэл {a} гэх мэтийг select хийх
            const match = insert.match(/\{([a-zA-Z0-9]+)\}/);
            if (match && cursorOff !== 0) {
                const token = match[0]; // {x}
                const tokenPos = insert.lastIndexOf(token);
                if (tokenPos >= 0) {
                    const selStart = start + tokenPos + 1;
                    const selEnd = selStart + match[1].length;
                    activeField.setSelectionRange(selStart, selEnd);
                } else {
                    activeField.setSelectionRange(pos, pos);
                }
            } else {
                activeField.setSelectionRange(pos, pos);
            }

            renderPreview(activeField.id);
        });
    });

    function renderPreview(fieldId) {
        const map = {
            'question_text': 'preview_text',
            'opt_a': 'preview_a',
            'opt_b': 'preview_b',
            'opt_c': 'preview_c',
            'opt_d': 'preview_d',
            'explanation_text': 'preview_explanation',
        };
        const el = document.getElementById(fieldId);
        const preview = document.getElementById(map[fieldId]);
        if (!el || !preview) return;

        const text = el.value.trim();
        if (!text) {
            preview.innerHTML = fieldId === 'question_text'
                ? '<span class="text-white/30">Preview энд гарна...</span>'
                : '';
            return;
        }
        preview.textContent = text;

        if (typeof renderMathInElement === 'undefined') return;
        try {
            renderMathInElement(preview, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                ],
                throwOnError: false,
            });
        } catch (e) {}
    }
})();
</script>
@endpush
@endsection
