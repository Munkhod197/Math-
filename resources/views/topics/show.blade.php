@extends('layouts.app')

@section('title', $topic->name)

@push('head')
<style>
  .opt-btn:disabled{cursor:not-allowed;}
  .opt-btn.correct{background:#dcfce7;border-color:#22c55e;color:#166534;}
  .opt-btn.wrong{background:#fee2e2;border-color:#E11D48;color:#9f1239;}
</style>
@endpush

@section('content')
<section class="max-w-4xl px-4 py-10 mx-auto fade">
  <a href="{{ route('topics.index') }}" class="inline-flex items-center gap-2 mb-6 text-sm font-extrabold text-brand hover:underline">
    ← Бүх хичээл
  </a>

  <div class="p-6 mb-8 text-white shadow-xl bg-navy rounded-3xl">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
      <div class="text-6xl">{{ $topic->icon }}</div>
      <div class="flex-1">
        <div class="flex flex-wrap gap-2 mb-3">
          <span class="px-3 py-1 text-xs font-extrabold border rounded-full bg-white/10 border-white/15">{{ $topic->grade_level }}-р анги</span>
          <span class="px-3 py-1 text-xs font-extrabold border rounded-full bg-white/10 border-white/15">{{ $topic->questions->count() }} дасгал</span>
          <span class="px-3 py-1 text-xs font-extrabold rounded-full bg-gold text-navy">{{ $topic->test_questions_count }} тест бодлого</span>
        </div>
        <h1 class="text-3xl font-extrabold leading-tight">{{ $topic->name }}</h1>
        @if($topic->description)
          <p class="mt-2 text-sm leading-7 text-white/65">{{ $topic->description }}</p>
        @endif
      </div>
      <a href="{{ route('test.topic.start', $topic->slug) }}" class="inline-flex items-center justify-center px-5 py-3 font-extrabold bg-white shadow-lg rounded-2xl text-navy hover:bg-slate-100">
        Шалгалт өгөх
      </a>
    </div>
  </div>

  @if($topic->questions->isNotEmpty())
    <div class="p-5 mb-8 bg-white border border-slate-100 shadow-sm rounded-3xl">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-extrabold text-navy">Дасгалын явц</span>
        <span id="progress-text" class="text-sm font-semibold text-slate-500">0 / {{ $topic->questions->count() }}</span>
      </div>
      <div class="w-full h-3 overflow-hidden rounded-full bg-slate-100">
        <div id="progress-bar" class="h-full transition-all duration-500 rounded-full bg-gradient-to-r from-brand to-gold" style="width:0%"></div>
      </div>
      <p id="progress-hint" class="mt-2 text-xs text-slate-400">Дасгал бодоод дараа нь сэдвийн шалгалтаа өгөөрэй.</p>
    </div>
  @endif

  {{-- Видео хичээл --}}
  <div class="mb-8">
    <div class="flex items-center justify-between gap-4 mb-3">
      <h2 class="text-xl font-extrabold text-navy">{{ $topic->video_title ?? 'Видео хичээл' }}</h2>
      <span class="text-xs font-bold text-slate-400">
        {{ ($topic->video_file || $topic->video_url) ? 'Видео бэлэн' : 'Видео нэмэгдэнэ' }}
      </span>
    </div>

    {{-- 1) Админ дээр оруулсан видео файл --}}
    @if($topic->video_file)
      <div class="overflow-hidden bg-black shadow-xl rounded-3xl">
        <video controls class="w-full aspect-video" preload="metadata">
          <source src="{{ asset('storage/' . $topic->video_file) }}" type="video/mp4">
          <source src="{{ asset('storage/' . $topic->video_file) }}" type="video/quicktime">
          Таны браузер видео дэмжихгүй байна.
        </video>
      </div>

    {{-- 2) YouTube / гадаад URL --}}
    @elseif($topic->video_url)
      @php
        $embed = $topic->youtube_embed_url ?? null;
        if (! $embed && $topic->video_url) {
            if (preg_match('/(?:youtube\.com.*(?:v=|\/embed\/)|youtu\.be\/)([A-Za-z0-9_-]+)/', $topic->video_url, $m)) {
                $embed = 'https://www.youtube.com/embed/' . $m[1];
            }
        }
      @endphp

      @if($embed)
        <div class="overflow-hidden bg-black shadow-xl rounded-3xl aspect-video">
          <iframe class="w-full h-full"
            src="{{ $embed }}"
            title="{{ $topic->video_title ?? $topic->name }}"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
        </div>
      @else
        <a href="{{ $topic->video_url }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 font-bold text-brand hover:underline">
          Видео үзэх →
        </a>
      @endif

    {{-- 3) Видео байхгүй --}}
    @else
      <div class="p-12 text-center border-2 border-dashed bg-slate-50 border-slate-200 rounded-3xl">
        <div class="mb-3 text-5xl">🎬</div>
        <p class="font-extrabold text-navy">Видео хичээл удахгүй нэмэгдэнэ</p>
        <p class="mt-2 text-sm text-slate-500">Одоогоор доорх дасгалаар сэдвээ бататгаж болно.</p>
      </div>
    @endif
  </div>

  @if($topic->questions->isNotEmpty())
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-4">
        <h2 class="text-xl font-extrabold text-navy">Дасгал асуултууд</h2>
        <span class="px-3 py-1 text-sm font-extrabold rounded-full bg-brand/10 text-brand">{{ $topic->questions->count() }}</span>
      </div>
      <div class="grid gap-4">
        @foreach($topic->questions as $qi => $q)
          <article class="p-5 bg-white border border-slate-100 shadow-sm rounded-3xl" data-question-index="{{ $qi }}">
            <p class="mb-4 font-extrabold leading-7 text-navy" data-ai-question="{{ $q->question_text }}">{{ $qi+1 }}. {{ $q->question_text }}</p>
            @if(!empty($q->image_path))
              <img src="{{ asset('storage/' . $q->image_path) }}"
                   alt="Бодлогын зураг"
                   class="w-full max-h-72 mb-4 rounded-2xl border border-slate-200 bg-slate-50 object-contain">
            @endif
            <div class="grid gap-2 sm:grid-cols-2">
              @foreach(['A'=>$q->option_a,'B'=>$q->option_b,'C'=>$q->option_c,'D'=>$q->option_d] as $l=>$t)
                <button type="button"
                  class="opt-btn border-2 border-slate-200 rounded-2xl px-4 py-3 text-left font-semibold text-sm text-slate-700 hover:border-brand transition-colors"
                  id="pq{{ $qi }}{{ $l }}"
                  onclick="checkAns({{ $qi }},'{{ $l }}','{{ $q->correct_answer }}','{{ addslashes($q->explanation ?? '') }}')">
                  <span class="mr-2 font-extrabold text-slate-400">{{ $l }}.</span>{{ $t }}
                </button>
              @endforeach
            </div>
            <div id="expl{{ $qi }}" class="hidden px-4 py-3 mt-3 text-sm font-semibold border bg-blue-50 border-blue-100 text-brand rounded-2xl"></div>
          </article>
        @endforeach
      </div>
    </div>
  @endif

  <div class="p-8 text-center text-white bg-ruby rounded-3xl">
    <h3 class="mb-2 text-2xl font-extrabold">Сэдвийн шалгалт</h3>
    <p class="mb-5 text-sm leading-6 text-white/75">Энэ сэдэвт зориулсан бодлогуудаар өөрийгөө шалгаад дэлгэрэнгүй дүнгээ аваарай.</p>
    <a href="{{ route('test.topic.start', $topic->slug) }}" class="inline-flex items-center justify-center px-8 py-3 font-extrabold bg-white shadow-lg text-ruby rounded-2xl">
      Шалгалт эхлүүлэх
    </a>
  </div>
</section>
@endsection

@push('scripts')
<script>
const totalQuestions = {{ $topic->questions->count() }};
const answered = new Set();
const storageKey = 'mathmon_topic_{{ $topic->slug }}';

function updateProgress(){
  const pct = totalQuestions > 0 ? (answered.size / totalQuestions * 100) : 0;
  const bar = document.getElementById('progress-bar');
  const text = document.getElementById('progress-text');
  if(bar) bar.style.width = pct + '%';
  if(text) text.textContent = answered.size + ' / ' + totalQuestions;
  if(answered.size >= totalQuestions && totalQuestions > 0){
    document.getElementById('progress-hint').textContent = 'Сайн байна. Одоо шалгалтаар бататгаж болно.';
    localStorage.setItem(storageKey, 'done');
  }
}

function checkAns(qi,chosen,correct,expl){
  if(answered.has(qi)) return;
  ['A','B','C','D'].forEach(l=>{
    const b=document.getElementById('pq'+qi+l);
    if(b) b.disabled=true;
  });
  const chosenButton=document.getElementById('pq'+qi+chosen);
  const correctButton=document.getElementById('pq'+qi+correct);
  if(chosen===correct){ chosenButton.classList.add('correct'); }
  else { chosenButton.classList.add('wrong'); correctButton.classList.add('correct'); }
  if(expl){
    const el=document.getElementById('expl'+qi);
    el.classList.remove('hidden');
    el.textContent='Тайлбар: '+expl;
  }
  answered.add(qi);
  updateProgress();
}

if(localStorage.getItem(storageKey)==='done' && totalQuestions > 0){
  for(let i=0;i<totalQuestions;i++) answered.add(i);
  updateProgress();
}
</script>
@endpush
