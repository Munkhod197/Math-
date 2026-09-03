@once
<style>
  .admin-math-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.75rem;
    height: 2.35rem;
    padding: 0 0.65rem;
    border-radius: 0.65rem;
    border: 1px solid rgba(255,255,255,0.09);
    background: rgba(15, 23, 42, 0.85);
    color: rgba(255,255,255,0.82);
    font-size: 0.82rem;
    font-weight: 800;
    transition: all 0.15s ease;
  }
  .admin-math-btn:hover {
    border-color: rgba(245, 158, 11, 0.45);
    background: rgba(245, 158, 11, 0.14);
    color: #fbbf24;
  }
</style>
@endonce

<div class="mb-5 rounded-2xl border border-white/5 bg-slate-900/55 p-3 shadow-lg shadow-black/10">
    <div class="flex flex-wrap items-center gap-2">
        @foreach([
            '$x^{2}$' => 'x²',
            '$x^{n}$' => 'xⁿ',
            '$x_{1}$' => 'x₁',
            '$\sqrt{x}$' => '√x',
            '$\sqrt[n]{x}$' => 'ⁿ√x',
            '$\frac{a}{b}$' => 'a/b',
            '$|x|$' => '|x|',
            '$\pi$' => 'π',
            '$\infty$' => '∞',
            '$\pm$' => '±',
            '$\times$' => '×',
            '$\div$' => '÷',
            '$\leq$' => '≤',
            '$\geq$' => '≥',
            '$\neq$' => '≠',
            '$\angle ABC$' => '∠',
            '$\triangle ABC$' => '△',
            '$\sin x$' => 'sin',
            '$\cos x$' => 'cos',
            '$\log x$' => 'log',
        ] as $insert => $label)
            <button type="button" class="admin-math-btn" data-admin-math-insert="{{ $insert }}">{{ $label }}</button>
        @endforeach
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    let activeField = null;

    function canUseField(el) {
        if (!el) return false;
        if (el.tagName === 'TEXTAREA') return true;
        if (el.tagName !== 'INPUT') return false;
        return ['text', 'search', 'url', 'email', 'number'].includes(el.type);
    }

    document.addEventListener('focusin', function (event) {
        if (canUseField(event.target)) {
            activeField = event.target;
        }
    });

    document.querySelectorAll('[data-admin-math-insert]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!canUseField(activeField)) {
                activeField = document.querySelector('textarea, input[type="text"]');
            }
            if (!activeField) return;

            const insert = button.getAttribute('data-admin-math-insert') || '';
            const start = activeField.selectionStart ?? activeField.value.length;
            const end = activeField.selectionEnd ?? activeField.value.length;
            activeField.value = activeField.value.slice(0, start) + insert + activeField.value.slice(end);
            activeField.focus();

            const tokenMatch = insert.match(/\{([a-zA-Z0-9]+)\}/);
            if (tokenMatch) {
                const tokenPos = insert.indexOf(tokenMatch[0]);
                activeField.setSelectionRange(start + tokenPos + 1, start + tokenPos + 1 + tokenMatch[1].length);
            } else {
                activeField.setSelectionRange(start + insert.length, start + insert.length);
            }

            activeField.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
});
</script>
@endonce
