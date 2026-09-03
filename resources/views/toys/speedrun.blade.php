@extends('layouts.app')

@section('title', 'Хурдан бодолт')

@section('content')
<section class="px-4 py-8 sm:py-10">
  <div class="max-w-5xl mx-auto">
    <div class="relative overflow-hidden p-7 text-white bg-navy rounded-[2rem] shadow-xl shadow-navy/20 sm:p-10">
      <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(rgba(255,255,255,.7) 1px,transparent 1px);background-size:22px 22px"></div>
      <div class="relative"><a href="{{ route('toys.index') }}" class="text-xs font-bold tracking-widest uppercase text-gold">Game Zone / Speedrun</a><h1 class="mt-3 text-3xl font-extrabold sm:text-5xl">Хурдан бодолт</h1><p class="max-w-xl mt-3 text-sm leading-7 text-white/70">60 секундэд аль болох олон бодлогыг зөв бод. Зөв дараалал нь combo оноо нэмнэ.</p></div>
    </div>
    @include('toys._nav')

    <div class="overflow-hidden mt-6 bg-white border border-slate-200 rounded-[1.75rem] shadow-sm">
      <div class="grid grid-cols-3 gap-px bg-slate-200"><div class="p-5 text-center bg-white"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Оноо</p><p id="score" class="mt-1 text-3xl font-extrabold text-navy">0</p></div><div class="p-5 text-center bg-white"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Хугацаа</p><p id="timer" class="mt-1 text-3xl font-extrabold text-brand">60</p></div><div class="p-5 text-center bg-white"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Шилдэг</p><p id="best" class="mt-1 text-3xl font-extrabold text-amber-500">0</p></div></div>
      <div class="p-5 sm:p-8"><div class="h-2 overflow-hidden rounded-full bg-slate-100"><div id="timerBar" class="h-full bg-brand transition-all" style="width:100%"></div></div><div class="p-8 mt-6 text-center border border-slate-100 rounded-[1.5rem] bg-slate-50 sm:p-12"><p class="text-[10px] font-bold tracking-[.24em] uppercase text-slate-400">Бодлого</p><h2 id="question" class="mt-4 text-4xl font-extrabold text-navy sm:text-6xl">Бэлэн үү?</h2></div><div id="answers" class="grid gap-3 mt-4 sm:grid-cols-2"></div><p id="message" class="min-h-6 mt-5 text-sm font-bold text-center text-slate-500">Эхлэх дээр дарж sprint-ээ эхлүүл.</p><button id="start" class="w-full py-4 mt-4 font-extrabold text-white transition rounded-xl bg-navy hover:bg-brand">Эхлэх</button></div>
    </div>
  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = (id) => document.getElementById(id), total = 60, bestKey = 'mathmon_speedrun_best';
  let score = 0, correct = 0, attempted = 0, combo = 0, maxCombo = 0, answer = 0, time = total, timer, running = false;
  const shuffle = (items) => [...items].sort(() => Math.random() - .5);
  const setMessage = (message, color = '#64748b') => { $('message').textContent = message; $('message').style.color = color; };
  const question = () => { const ops = time < 20 ? ['+','-','×','÷'] : ['+','-','×']; const op = ops[Math.floor(Math.random()*ops.length)]; let a = Math.floor(Math.random()*11)+2, b = Math.floor(Math.random()*11)+2; if (op === '-') { if (b > a) [a,b] = [b,a]; answer = a-b; } else if (op === '×') answer = a*b; else if (op === '÷') { answer=a; a*=b; } else answer=a+b; $('question').textContent = `${a} ${op} ${b} = ?`; const values = new Set([answer]); while (values.size < 4) values.add(Math.max(0, answer + Math.floor(Math.random()*9)-4)); $('answers').innerHTML = ''; shuffle([...values]).forEach((value, index) => { const button=document.createElement('button'); button.type='button'; button.className='toy-answer px-5 py-5 text-xl font-extrabold rounded-xl bg-white text-navy'; button.innerHTML=`<span class="mr-3 text-sm text-slate-400">${index+1}</span>${value}`; button.onclick=()=>respond(value, button); $('answers').appendChild(button); }); };
  const respond = (value, button) => { if (!running) return; attempted++; if (value === answer) { combo++; maxCombo=Math.max(maxCombo,combo); correct++; const gain=1+(combo>=5?1:0); score+=gain; button.classList.add('bg-emerald-50','text-emerald-700','border-emerald-300'); setMessage(`Зөв! +${gain}`, '#059669'); } else { combo=0; button.classList.add('bg-rose-50','text-rose-700','border-rose-300'); setMessage('Буруу. Дараагийн бодлого руу орлоо.', '#e11d48'); } $('score').textContent=score; [...$('answers').children].forEach(item => item.disabled=true); setTimeout(question, 220); };
  const finish = () => { running=false; clearInterval(timer); const best=Math.max(score, Number(localStorage.getItem(bestKey)||0)); localStorage.setItem(bestKey,best); $('best').textContent=best; $('answers').innerHTML=''; $('question').textContent='Дууслаа'; $('start').textContent='Дахин эхлэх'; $('start').classList.remove('hidden'); setMessage(`Таны оноо: ${score}`, '#059669'); if (!score) return; fetch('{{ route('toys.score') }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},body:JSON.stringify({mode:'speedrun',difficulty:'normal',score,max_combo:maxCombo,correct_answers:correct,wrong_answers:Math.max(0,attempted-correct),accuracy:Math.round(correct/Math.max(attempted,1)*100),rounds_played:Math.max(attempted,1),duration_seconds:60})}).then(r=>r.json()).then(data=>{if(data.ok)setMessage(`Дууслаа. +${data.earned.xp} XP, +${data.earned.coins} Coins авлаа.`, '#059669')}).catch(()=>{}); };
  const start = () => { clearInterval(timer); score=0;correct=0;attempted=0;combo=0;maxCombo=0;time=total;running=true;$('score').textContent=0;$('timer').textContent=time;$('timerBar').style.width='100%';$('start').classList.add('hidden');setMessage('Эхэллээ!');question();timer=setInterval(()=>{time--; $('timer').textContent=time;$('timerBar').style.width=`${time/total*100}%`;if(time<=0)finish();},1000); };
  $('start').onclick=start; $('best').textContent=localStorage.getItem(bestKey)||0; document.addEventListener('keydown',event=>{const button=$('answers').children[Number(event.key)-1];if(button&&!button.disabled)button.click();if(event.key==='Enter'&&!running)start();});
});
</script>
@endpush
@endsection
