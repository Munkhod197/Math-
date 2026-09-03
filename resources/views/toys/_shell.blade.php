@php $accent = $badgeColor ?? '#2563EB'; @endphp

@once
<style>
  @keyframes toy-enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes toy-shake { 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }
  .toy-enter { animation: toy-enter .3s ease-out both; }
  .toy-shake { animation: toy-shake .3s ease-in-out; }
  .toy-answer { border: 1px solid rgba(15,32,68,.1); transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease; }
  .toy-answer:hover:not(:disabled) { transform: translateY(-2px); border-color: rgba(37,99,235,.45); box-shadow: 0 12px 20px -14px rgba(15,32,68,.5); }
  .toy-answer:active:not(:disabled) { transform: scale(.98); }
  .toy-answer:disabled { cursor: default; }
  @media (prefers-reduced-motion: reduce) { .toy-enter, .toy-shake { animation: none; } }
</style>
@endonce

<section class="px-4 py-8 sm:py-10">
  <div class="max-w-6xl mx-auto">
    <div class="relative overflow-hidden p-6 text-white bg-navy rounded-[2rem] shadow-xl shadow-navy/20 sm:p-8">
      <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(rgba(255,255,255,.6) 1px,transparent 1px);background-size:21px 21px"></div>
      <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><a href="{{ route('toys.index') }}" class="text-xs font-bold tracking-widest uppercase text-gold">Game Zone / {{ $badge }}</a><h1 class="mt-3 text-3xl font-extrabold sm:text-4xl">{{ $title }}</h1><p class="max-w-2xl mt-2 text-sm leading-6 text-white/70">{{ $subtitle }}</p></div>
        <a href="{{ route('toys.index') }}" class="inline-flex px-4 py-2.5 text-sm font-bold border rounded-xl border-white/15 bg-white/10 hover:bg-white/15">Бүх тоглоом</a>
      </div>
    </div>

    @include('toys._nav')

    <div class="grid gap-6 mt-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
      <div class="overflow-hidden bg-white border border-slate-200 rounded-[1.75rem] shadow-sm">
        <div class="grid grid-cols-2 gap-px bg-slate-200 sm:grid-cols-4">
          @foreach([['Оноо', 'toy-score', '0'], ['Шилдэг', 'toy-best', '0'], ['Combo', 'toy-combo', '0'], ['Амь', 'toy-lives', '3']] as [$label, $id, $value])
            <div class="px-4 py-4 text-center bg-white"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">{{ $label }}</p><p id="{{ $id }}" class="mt-1 text-2xl font-extrabold text-navy">{{ $value }}</p></div>
          @endforeach
        </div>

        <div class="p-5 sm:p-7">
          <div class="flex items-center gap-4">
            <div class="flex-1"><div class="flex justify-between text-[11px] font-bold tracking-wider uppercase text-slate-400"><span>Ахиц</span><span id="toy-round">1 / 10</span></div><div class="h-2 mt-2 overflow-hidden rounded-full bg-slate-100"><div id="toy-progress" class="h-full transition-all duration-300 rounded-full" style="width:0%;background:{{ $accent }}"></div></div></div>
            <button id="toy-reset" type="button" title="Шинээр эхлэх" class="flex items-center justify-center w-11 h-11 text-lg font-bold transition border rounded-xl border-slate-200 text-navy hover:bg-slate-50">↻</button>
          </div>
          <div class="mt-5"><div class="flex justify-between text-[11px] font-bold tracking-wider uppercase text-slate-400"><span>Хугацаа</span><span id="toy-timer-text">12 сек</span></div><div class="h-1.5 mt-2 overflow-hidden rounded-full bg-slate-100"><div id="toy-timer-bar" class="h-full rounded-full transition-all" style="width:100%;background:{{ $accent }}"></div></div></div>

          <div id="toy-panel" class="relative p-8 mt-6 overflow-hidden text-center border rounded-[1.5rem] border-slate-100 bg-slate-50 sm:p-12">
            <p class="text-[10px] font-bold tracking-[.24em] uppercase text-slate-400">Бодлого</p>
            <h2 id="toy-question" class="mt-4 text-3xl font-extrabold leading-tight text-navy sm:text-5xl">Бэлэн үү?</h2>
          </div>
          <div id="toy-answers" class="grid gap-3 mt-4 sm:grid-cols-2"></div>
          <p id="toy-result" class="min-h-6 mt-5 text-sm font-bold text-center text-slate-500">Түвшнээ сонгоод тоглоомоо эхлүүлээрэй.</p>
        </div>
      </div>

      <aside class="space-y-4">
        <div class="p-5 bg-white border border-slate-200 rounded-2xl"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Түвшин</p><div class="grid gap-2 mt-4">
          @foreach([['easy','Хялбар','12 сек'],['normal','Дундаж','9 сек'],['hard','Хэцүү','6 сек']] as [$value, $label, $time])
            <button type="button" data-difficulty="{{ $value }}" class="toy-level flex items-center justify-between px-4 py-3 text-sm font-bold border rounded-xl {{ $loop->first ? 'bg-navy text-white border-navy' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/40' }}"><span>{{ $label }}</span><span class="text-xs opacity-65">{{ $time }}</span></button>
          @endforeach
        </div></div>
        <div class="p-5 bg-white border border-slate-200 rounded-2xl"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Тусламж</p><div class="grid gap-2 mt-4">
          <button id="toy-power-5050" type="button" class="flex items-center justify-between px-3 py-3 text-sm font-bold border rounded-xl border-slate-200 text-navy hover:bg-slate-50"><span>50 / 50</span><span class="text-slate-400">1</span></button>
          <button id="toy-power-freeze" type="button" class="flex items-center justify-between px-3 py-3 text-sm font-bold border rounded-xl border-slate-200 text-navy hover:bg-slate-50"><span>Цаг зогсоох</span><span class="text-slate-400">1</span></button>
          <button id="toy-power-skip" type="button" class="flex items-center justify-between px-3 py-3 text-sm font-bold border rounded-xl border-slate-200 text-navy hover:bg-slate-50"><span>Алгасах</span><span class="text-slate-400">1</span></button>
        </div></div>
        <div class="p-5 rounded-2xl bg-brand/5 border border-brand/10"><p class="text-sm font-extrabold text-navy">Хурдан сонгох</p><p class="mt-2 text-sm leading-6 text-slate-600">Keyboard дээрх 1-4 товчоор хариултыг сонгоно. Зөв, хурдан хариулбал илүү их оноо авна.</p></div>
      </aside>
    </div>
  </div>
  <div id="toy-result-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-navy/70 backdrop-blur-sm">
    <div class="w-full max-w-md p-7 text-center bg-white shadow-2xl rounded-[1.75rem]">
      <p class="text-xs font-bold tracking-[.2em] uppercase text-gold">Game complete</p>
      <h2 class="mt-3 text-3xl font-extrabold text-navy">Сайн ажиллалаа!</h2>
      <div class="grid grid-cols-2 gap-3 mt-6 text-left"><div class="p-4 rounded-xl bg-slate-50"><p class="text-[10px] font-bold uppercase text-slate-400">Оноо</p><p id="toy-final-score" class="mt-1 text-2xl font-extrabold text-navy">0</p></div><div class="p-4 rounded-xl bg-slate-50"><p class="text-[10px] font-bold uppercase text-slate-400">Нарийвчлал</p><p id="toy-final-accuracy" class="mt-1 text-2xl font-extrabold text-navy">0%</p></div><div class="p-4 rounded-xl bg-emerald-50"><p class="text-[10px] font-bold uppercase text-emerald-600">XP</p><p id="toy-final-xp" class="mt-1 text-2xl font-extrabold text-emerald-700">+0</p></div><div class="p-4 rounded-xl bg-amber-50"><p class="text-[10px] font-bold uppercase text-amber-600">Coins</p><p id="toy-final-coins" class="mt-1 text-2xl font-extrabold text-amber-700">+0</p></div></div>
      <div class="grid grid-cols-2 gap-3 mt-6"><button id="toy-play-again" type="button" class="py-3 font-extrabold text-white rounded-xl bg-navy">Дахин тоглох</button><a href="{{ route('toys.index') }}" class="py-3 font-extrabold rounded-xl bg-slate-100 text-navy">Тоглоомууд</a></div>
    </div>
  </div>
</section>

<script>
window.MathMonToy = {
  boot(config) {
    const generator = config.generator, mode = config.mode, rounds = config.rounds || 10;
    const times = { easy: 12, normal: 9, hard: 6 };
    let difficulty = 'easy', score = 0, lives = 3, combo = 0, maxCombo = 0, correct = 0, round = 1, current, locked = false, timer, timeLeft, paused = false;
    let power5050 = 1, powerFreeze = 1, powerSkip = 1;
    const $ = (id) => document.getElementById(id);
    const question = $('toy-question'), answers = $('toy-answers'), result = $('toy-result'), progress = $('toy-progress'), timerBar = $('toy-timer-bar'), timerText = $('toy-timer-text');
    const bestKey = `mathmon_${mode}_best`;
    const shuffle = (items) => [...items].sort(() => Math.random() - .5);
    const setMessage = (message, color = '#64748b') => { result.textContent = message; result.style.color = color; };
    const render = () => {
      $('toy-score').textContent = score; $('toy-combo').textContent = combo; $('toy-lives').textContent = '♥'.repeat(lives) + '♡'.repeat(3-lives);
      $('toy-round').textContent = `${Math.min(round, rounds)} / ${rounds}`; progress.style.width = `${Math.min((round-1)/rounds*100, 100)}%`;
      const best = Math.max(Number(localStorage.getItem(bestKey) || 0), score); localStorage.setItem(bestKey, best); $('toy-best').textContent = best;
      [['toy-power-5050', power5050], ['toy-power-freeze', powerFreeze], ['toy-power-skip', powerSkip]].forEach(([id, left]) => { const button = $(id); button.disabled = !left || locked; button.classList.toggle('opacity-40', !left || locked); });
    };
    const stopTimer = () => { clearInterval(timer); timer = null; };
    const showAnswer = (isCorrect, selected) => [...answers.children].forEach((button) => { button.disabled = true; if (button.dataset.answer === current.answer) button.className = 'toy-answer px-5 py-5 text-xl font-extrabold rounded-xl border-emerald-300 bg-emerald-50 text-emerald-700'; else if (button === selected && !isCorrect) button.className = 'toy-answer toy-shake px-5 py-5 text-xl font-extrabold rounded-xl border-rose-300 bg-rose-50 text-rose-700'; });
    const startTimer = () => { stopTimer(); timeLeft = times[difficulty]; timerText.textContent = `${timeLeft} сек`; timerBar.style.width = '100%'; timerBar.style.background = '{{ $accent }}'; timer = setInterval(() => { if (paused) return; timeLeft -= .1; const percent = Math.max(0, timeLeft/times[difficulty]*100); timerBar.style.width = `${percent}%`; if (percent < 30) timerBar.style.background = '#e11d48'; timerText.textContent = `${Math.ceil(timeLeft)} сек`; if (timeLeft <= 0) { stopTimer(); if (!locked) { locked = true; lives--; combo = 0; showAnswer(false); setMessage(`Хугацаа дууслаа. Зөв хариу: ${current.answer}`, '#e11d48'); round++; render(); setTimeout(load, 950); } } }, 100); };
    const finish = () => { stopTimer(); answers.querySelectorAll('button').forEach(button => button.disabled = true); progress.style.width = '100%'; const roundsPlayed = Math.min(round - 1, rounds); const accuracy = Math.round(correct / Math.max(roundsPlayed, 1) * 100); const xp = Math.max(5, Math.floor(score / 2)); const coins = Math.max(1, Math.floor(score / 8)); setMessage(`Дууслаа. Таны оноо: ${score}`, '#059669');
      $('toy-final-score').textContent = score; $('toy-final-accuracy').textContent = `${accuracy}%`; $('toy-final-xp').textContent = `+${xp}`; $('toy-final-coins').textContent = `+${coins}`; $('toy-result-modal').classList.remove('hidden'); $('toy-result-modal').classList.add('flex');
      if (!score) return; fetch('{{ route('toys.score') }}', { method: 'POST', headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || ''}, body: JSON.stringify({ mode, difficulty, score, max_combo:maxCombo, correct_answers:correct, wrong_answers:Math.max(0, roundsPlayed-correct), accuracy, rounds_played:roundsPlayed, duration_seconds:0 }) }).then(response => response.json()).then(data => { if (data.ok) { $('toy-final-xp').textContent = `+${data.earned.xp}`; $('toy-final-coins').textContent = `+${data.earned.coins}`; setMessage(`Дууслаа. +${data.earned.xp} XP, +${data.earned.coins} Coins авлаа.`, '#059669'); } }).catch(() => {});
    };
    const load = () => { if (lives <= 0) return finish(); if (round > rounds) return finish(); locked = false; paused = false; current = generator({ difficulty: difficulty === 'normal' ? 'medium' : difficulty, round }); question.textContent = current.q; answers.innerHTML = ''; setMessage(current.hint || 'Зөв хариултыг сонго.'); shuffle(current.options.map(String)).slice(0,4).forEach((option, index) => { const button = document.createElement('button'); button.type = 'button'; button.dataset.answer = option; button.className = 'toy-answer toy-enter px-5 py-5 text-xl font-extrabold rounded-xl bg-white text-navy'; button.innerHTML = `<span class="mr-3 text-sm text-slate-400">${index+1}</span>${option}`; button.onclick = () => answer(option, button); answers.appendChild(button); }); render(); startTimer(); };
    const answer = (value, button) => { if (locked || button.classList.contains('hidden')) return; locked = true; stopTimer(); const ok = value === String(current.answer); if (ok) { combo++; maxCombo = Math.max(maxCombo, combo); correct++; const earned = 10 + Math.min(combo, 5); score += earned; setMessage(`Зөв! +${earned} оноо`, '#059669'); } else { lives--; combo = 0; setMessage(`Буруу. Зөв хариу: ${current.answer}`, '#e11d48'); } showAnswer(ok, button); round++; render(); setTimeout(load, 850); };
    const reset = () => { $('toy-result-modal').classList.add('hidden'); $('toy-result-modal').classList.remove('flex'); stopTimer(); score=0; lives=3; combo=0; maxCombo=0; correct=0; round=1; locked=false; power5050=1; powerFreeze=1; powerSkip=1; load(); };
    $('toy-reset').onclick = reset; $('toy-play-again').onclick = reset;
    $('toy-power-5050').onclick = () => { if (!power5050 || locked) return; power5050--; shuffle([...answers.children].filter(button => button.dataset.answer !== String(current.answer))).slice(0,2).forEach(button => button.classList.add('hidden')); render(); };
    $('toy-power-freeze').onclick = () => { if (!powerFreeze || locked || paused) return; powerFreeze--; paused=true; setMessage('Цаг 3 секунд зогслоо.', '#2563eb'); render(); setTimeout(() => { paused=false; }, 3000); };
    $('toy-power-skip').onclick = () => { if (!powerSkip || locked) return; powerSkip--; locked=true; stopTimer(); round++; setMessage('Алгаслаа.', '#64748b'); render(); setTimeout(load, 350); };
    document.querySelectorAll('.toy-level').forEach(button => button.onclick = () => { difficulty = button.dataset.difficulty; document.querySelectorAll('.toy-level').forEach(item => item.className = 'toy-level flex items-center justify-between px-4 py-3 text-sm font-bold border rounded-xl bg-white text-slate-600 border-slate-200 hover:border-brand/40'); button.className = 'toy-level flex items-center justify-between px-4 py-3 text-sm font-bold border rounded-xl bg-navy text-white border-navy'; reset(); });
    document.addEventListener('keydown', (event) => { const index = Number(event.key)-1; const button = answers.children[index]; if (button && !button.disabled) button.click(); });
    reset();
  }
};
</script>
