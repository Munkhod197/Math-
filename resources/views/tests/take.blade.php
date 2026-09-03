@extends('layouts.app')

@section('title','Тест өгөх')
@section('hide_ai_helper', true)

@section('content')
<section class="max-w-3xl px-4 py-8 mx-auto test-page fade">
  <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-xs font-extrabold tracking-widest uppercase text-brand">Тест</p>
      <h1 class="text-xl font-extrabold text-navy">
        @if(isset($topic))
          {{ $topic->icon }} {{ $topic->name }}
        @else
          Сонгосон сэдвүүд
        @endif
      </h1>
    </div>
    <div class="flex items-center gap-2">
      <div id="timer" class="px-4 py-2 text-sm font-bold text-white rounded-full test-timer bg-navy">00:00</div>
      <div class="px-4 py-2 text-sm font-bold rounded-full bg-slate-100 text-navy"><span id="cur">1</span> / {{ $questions->count() }}</div>
    </div>
  </div>

  <div class="h-3 mb-6 overflow-hidden rounded-full bg-slate-200">
    <div id="prog" class="h-full transition-all rounded-full test-progress bg-gradient-to-r from-brand to-gold" style="width:0%"></div>
  </div>

  <form method="POST" action="{{ route('test.submit') }}" id="form">
    @csrf
    <input type="hidden" name="student_name" value="{{ $studentName ?? 'Сурагч' }}">
    @if(isset($topic))
      <input type="hidden" name="topic_slug" value="{{ $topic->slug }}">
    @endif

    @foreach($questions as $i => $q)
      <div class="qcard {{ $i===0?'active':'' }}" id="qc{{ $i }}">
        <article class="p-6 bg-white border shadow-sm border-slate-100 rounded-3xl card-hover">
          <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Бодлого {{ $i+1 }}</span>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-ruby/10 text-ruby">Тооцоолол</span>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-500 sm:ml-auto">{{ $q->topic->icon }} {{ $q->topic->name }}</span>
          </div>
          <p class="mb-5 text-lg font-extrabold leading-8 text-navy">{{ $q->question_text }}</p>
          @if(!empty($q->image_path))
            <img src="{{ asset('storage/' . $q->image_path) }}"
                 alt="Бодлогын зураг"
                 class="w-full max-h-80 mb-5 rounded-2xl border border-slate-200 bg-slate-50 object-contain">
          @endif

          <div class="flex flex-col gap-3">
            @foreach(['A'=>$q->option_a,'B'=>$q->option_b,'C'=>$q->option_c,'D'=>$q->option_d] as $l=>$t)
              @php $optionId = 'q'.$q->id.'_'.$l; @endphp
              <label for="{{ $optionId }}" class="cursor-pointer" onclick="markAnswered({{ $i }})">
                <input id="{{ $optionId }}" type="radio" name="answers[{{ $q->id }}]" value="{{ $l }}" class="sr-only peer">
                <div class="flex items-center gap-3 px-4 py-3 border-2 opt-inner border-slate-200 rounded-2xl transition-all peer-checked:border-brand peer-checked:bg-brand/10">
                  <span class="opt-key w-9 h-9 min-w-[2.25rem] rounded-xl bg-slate-100 flex items-center justify-center font-extrabold text-sm text-slate-500 transition-all peer-checked:bg-brand peer-checked:text-brand">{{ $l }}</span>
                  <span class="text-sm font-semibold text-slate-700">{{ $t }}</span>
                </div>
              </label>
            @endforeach
          </div>

          <div class="flex justify-between mt-6">
            <button type="button" onclick="goTo({{ $i-1 }})" class="px-5 py-3 text-sm font-bold transition-colors border-2 btn-bounce border-slate-200 rounded-2xl text-slate-600 hover:border-slate-300 disabled:opacity-30" {{ $i===0?'disabled':'' }}>← Өмнөх</button>
            @if($i < $questions->count()-1)
              <button type="button" onclick="goTo({{ $i+1 }})" class="px-5 py-3 text-sm font-bold text-white transition-colors btn-bounce bg-navy rounded-2xl hover:bg-blue-950">Дараах →</button>
            @else
              <button type="button" onclick="trySubmit()" class="px-5 py-3 text-sm font-bold text-white transition-colors btn-bounce bg-emerald-600 rounded-2xl hover:bg-emerald-700">Дуусгах</button>
            @endif
          </div>
        </article>
      </div>
    @endforeach

    <div class="flex flex-wrap justify-center gap-2 mt-5">
      @foreach($questions as $i => $q)
        <button type="button" class="dot {{ $i===0?'current':'' }}" id="dot{{ $i }}" onclick="goTo({{ $i }})">{{ $i+1 }}</button>
      @endforeach
    </div>

    <div id="warn" class="hidden px-4 py-3 mt-4 text-sm font-semibold border rounded-2xl bg-amber-50 border-amber-200 text-amber-800 fade"></div>

    <div class="mt-6 text-center">
      <button type="button" onclick="trySubmit()" class="px-10 py-4 text-lg font-extrabold text-white btn-submit-test bg-ruby hover:bg-red-700 rounded-2xl">
        Тест дуусгах
      </button>
    </div>
  </form>
</section>
@endsection

@push('scripts')
<script>
const total={{ $questions->count() }};
let cur=0;
const ans=new Set();

function goTo(i){
  if(i<0||i>=total)return;
  const prev=document.getElementById('qc'+cur);
  prev.classList.add('slide-out');
  setTimeout(()=>{
    prev.classList.remove('active','slide-out');
    document.getElementById('dot'+cur).classList.remove('current');
    cur=i;
    document.getElementById('qc'+cur).classList.add('active');
    document.getElementById('dot'+cur).classList.add('current');
    document.getElementById('cur').textContent=cur+1;
  },160);
}
function markAnswered(i){
  if(ans.has(i))return;
  ans.add(i);
  const d=document.getElementById('dot'+i);
  d.classList.add('answered');
  document.getElementById('prog').style.width=(ans.size/total*100)+'%';
  setTimeout(()=>{if(i<total-1)goTo(i+1);},300);
}
function trySubmit(){
  const missing=[];
  for(let i=0;i<total;i++)if(!ans.has(i))missing.push(i+1);
  if(missing.length){
    const w=document.getElementById('warn');
    w.classList.remove('hidden');
    w.textContent=missing.length+' бодлого хариулагдаагүй: '+missing.slice(0,8).join(', ')+(missing.length>8?'...':'');
    goTo(missing[0]-1);
    return;
  }
  document.getElementById('form').submit();
}
let secs=0;
setInterval(()=>{
  secs++;
  const m=String(Math.floor(secs/60)).padStart(2,'0');
  const s=String(secs%60).padStart(2,'0');
  const el=document.getElementById('timer');
  el.textContent=m+':'+s;
  if(secs>900)el.classList.add('urgent');
},1000);
</script>
@endpush
