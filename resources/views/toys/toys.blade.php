@extends('layouts.app')

@section('title', 'Тоглоом')

@section('content')
@once
<style>
  @keyframes game-center-enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  .game-center-enter { animation: game-center-enter .45s ease-out both; }
  .game-symbol-grid { background-image: linear-gradient(rgba(255,255,255,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.06) 1px,transparent 1px); background-size: 28px 28px; }
  .game-tile { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
  .game-tile:hover { transform: translateY(-3px); border-color: rgba(37,99,235,.28); box-shadow: 0 16px 28px -22px rgba(15,32,68,.55); }
  @media (prefers-reduced-motion: reduce) { .game-center-enter { animation: none; } .game-tile { transition: none; } }
</style>
@endonce

<section class="px-4 py-8 sm:py-10">
  <div class="max-w-6xl mx-auto game-center-enter">
    <header class="relative overflow-hidden text-white bg-navy rounded-2xl shadow-xl shadow-navy/20">
      <div class="absolute inset-0 game-symbol-grid"></div>
      <span class="absolute text-7xl font-black text-white/10 left-[7%] top-8">π</span>
      <span class="absolute text-8xl font-black text-white/10 right-[9%] bottom-3">Σ</span>
      <div class="relative grid gap-7 p-6 sm:p-9 lg:grid-cols-[1fr_19rem] lg:items-end">
        <div>
          <p class="text-xs font-extrabold tracking-[.2em] uppercase text-gold">MathMon Game Center</p>
          <h1 class="max-w-xl mt-3 text-3xl font-extrabold leading-tight sm:text-5xl">Тоглож сур.<br>Өдөр бүр ахиц гарга.</h1>
          <p class="max-w-xl mt-4 text-sm leading-7 text-white/70">Хурд, логик, алгебр, геометрийн чадвараа бодит оноо, XP, achievement-тэйгээр хөгжүүл.</p>
        </div>
        <div class="p-5 border border-white/15 rounded-xl bg-white/10 backdrop-blur-sm">
          <div class="flex items-center gap-3"><span class="flex items-center justify-center w-11 h-11 text-lg font-extrabold rounded-xl bg-gold text-navy">{{ $player->avatar }}</span><div class="min-w-0"><p class="font-extrabold truncate">{{ $player->name }}</p><p class="mt-0.5 text-xs text-white/60">Level {{ $player->level }} · {{ $player->rank }} зэрэглэл</p></div></div>
          <div class="flex justify-between mt-5 text-[11px] font-bold text-white/70"><span>XP ахиц</span><span>{{ number_format($player->xp) }} / {{ number_format($player->xp_next) }}</span></div>
          <div class="h-2 mt-2 overflow-hidden rounded-full bg-white/15"><div class="h-full rounded-full bg-gold transition-all duration-700" style="width:{{ $xpPercent }}%"></div></div>
        </div>
      </div>
    </header>

    <section class="grid grid-cols-2 mt-4 overflow-hidden bg-white border border-slate-200 rounded-xl sm:grid-cols-4">
      @foreach([['Түвшин',$player->level,'text-blue-600'],['Тоглосон',$player->games_completed,'text-violet-600'],['Зоос',number_format($player->coins),'text-amber-600'],['Нарийвчлал',$player->accuracy.'%','text-emerald-600']] as [$label,$value,$color])
        <div class="px-5 py-4 border-b border-r border-slate-100 last:border-r-0 sm:border-b-0"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">{{ $label }}</p><p class="mt-1 text-2xl font-extrabold {{ $color }}">{{ $value }}</p></div>
      @endforeach
    </section>

    <div class="grid gap-5 mt-7 lg:grid-cols-[1.5fr_.85fr]">
      <a href="{{ $featuredGame['route'] }}" class="game-tile relative min-h-[278px] overflow-hidden p-7 text-white rounded-2xl bg-gradient-to-br from-brand to-blue-700 shadow-lg shadow-brand/20">
        <span class="absolute text-[13rem] leading-none font-black text-white/10 -right-5 -bottom-10">x</span>
        <div class="relative flex flex-col h-full"><div class="flex items-center justify-between gap-3"><span class="px-3 py-1.5 text-[11px] font-bold rounded-lg bg-white/15">Өнөөдрийн сонголт</span><span class="text-xs font-bold text-white/65">{{ $featuredGame['duration'] }}</span></div><div class="mt-auto"><p class="text-xs font-bold tracking-widest text-white/60">FOCUSED PRACTICE</p><h2 class="mt-2 text-3xl font-extrabold sm:text-4xl">{{ $featuredGame['title'] }}</h2><p class="max-w-lg mt-3 text-sm leading-6 text-white/75">{{ $featuredGame['subtitle'] }}</p><div class="flex flex-wrap gap-2 mt-5 text-xs font-bold"><span class="px-3 py-2 rounded-lg bg-black/15">{{ $featuredGame['difficulty'] }}</span><span class="px-3 py-2 rounded-lg bg-black/15">{{ $featuredGame['reward'] }}</span><span class="px-3 py-2 rounded-lg bg-black/15">{{ $featuredGame['coins'] }}</span></div></div></div>
      </a>
      <section class="p-6 bg-white border border-slate-200 rounded-2xl"><div class="flex items-start justify-between"><div><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Шилдэг оноо</p><h2 class="mt-1 text-xl font-extrabold text-navy">Leaderboard</h2></div><span class="flex items-center justify-center w-9 h-9 text-sm font-extrabold rounded-lg bg-amber-50 text-amber-600">#</span></div><div class="mt-5 space-y-1.5">@forelse($leaderboard as $entry)<div class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $entry->user_id === auth()->id() ? 'bg-brand/5 border border-brand/10' : 'bg-slate-50' }}"><span class="w-4 text-xs font-extrabold text-slate-400">{{ $loop->iteration }}</span><span class="flex items-center justify-center w-8 h-8 text-xs font-extrabold rounded-lg bg-white text-navy">{{ strtoupper(mb_substr($entry->user->name,0,1)) }}</span><span class="flex-1 text-sm font-bold text-slate-700 truncate">{{ $entry->user->name }}</span><span class="text-sm font-extrabold text-brand">{{ $entry->score }}</span></div>@empty<div class="py-8 text-sm text-center text-slate-500">Эхний рекордыг та тогтоогоорой.</div>@endforelse</div></section>
    </div>

    <section class="grid gap-5 mt-5 lg:grid-cols-[1.35fr_.85fr]">
      <div class="relative p-6 overflow-hidden text-white rounded-2xl bg-gradient-to-br from-violet-700 to-brand"><span class="absolute text-8xl leading-none font-black text-white/10 -right-2 -bottom-5">10</span><div class="relative"><p class="text-[10px] font-bold tracking-widest uppercase text-white/60">Өнөөдрийн challenge</p><h2 class="mt-3 text-2xl font-extrabold">10 бодлого, 5 минут</h2><p class="max-w-md mt-2 text-sm leading-6 text-white/75">Тусгай sprint-ийг дуусгаад өдөр тутмын ахицаа үргэлжлүүлээрэй.</p><div class="flex flex-wrap gap-2 mt-5 text-xs font-bold"><span class="px-3 py-2 rounded-lg bg-white/15">+250 XP хүртэл</span><span class="px-3 py-2 rounded-lg bg-white/15">+100 Coins хүртэл</span></div><a href="{{ route('toys.algebra') }}" class="inline-flex px-4 py-3 mt-6 text-sm font-extrabold rounded-lg bg-white text-navy hover:bg-gold">Challenge эхлүүлэх</a></div></div>
      <div class="p-6 bg-white border border-slate-200 rounded-2xl"><div class="flex items-center justify-between"><div><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Цуврал</p><p class="mt-2 text-4xl font-extrabold text-navy">{{ $player->streak }}<span class="ml-1 text-base text-slate-400">өдөр</span></p></div><span class="text-3xl">{{ $player->streak ? '🔥' : '·' }}</span></div><div class="h-2 mt-6 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-rose-500" style="width:{{ min(100,$player->streak*14) }}%"></div></div><p class="mt-2 text-xs leading-5 text-slate-500">7 өдрийн цувралд {{ max(0,7-$player->streak) }} өдөр үлдлээ.</p></div>
    </section>

    <div class="flex flex-col gap-4 mt-10 mb-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Тоглоомууд</p><h2 class="mt-1 text-2xl font-extrabold text-navy">Өөрт тохирох горимоо сонго</h2></div><div class="flex gap-2 overflow-x-auto pb-1"> <button type="button" data-filter="all" class="game-filter px-3 py-2 text-xs font-bold text-white rounded-lg bg-navy">Бүгд</button>@foreach(['algebra'=>'Алгебр','geometry'=>'Геометр','logic'=>'Логик','speed'=>'Speed'] as $key=>$label)<button type="button" data-filter="{{ $key }}" class="game-filter px-3 py-2 text-xs font-bold rounded-lg bg-slate-100 text-slate-600">{{ $label }}</button>@endforeach</div></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach($categories as $game)<a data-category="{{ $game['category'] }}" href="{{ $game['route'] }}" class="game-card game-tile relative p-5 bg-white border border-slate-200 rounded-xl"><div class="flex items-start justify-between"><span class="flex items-center justify-center w-11 h-11 text-lg font-extrabold text-white rounded-lg {{ $game['accent'] }}">{{ $game['icon'] }}</span>@if($game['badge'])<span class="px-2 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-500">{{ $game['badge'] }}</span>@endif</div><h3 class="mt-5 text-lg font-extrabold text-navy">{{ $game['title'] }}</h3><p class="mt-1 text-sm text-slate-500">{{ $game['difficulty'] }}</p><div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100"><span class="text-xs font-bold text-slate-400">10 үе</span><span class="text-sm font-extrabold text-brand">Тоглох →</span></div></a>@endforeach</div>

    <div class="grid gap-5 mt-10 lg:grid-cols-2">
      <section class="p-6 bg-white border border-slate-200 rounded-2xl"><div class="flex items-center justify-between"><div><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Амжилтууд</p><h2 class="mt-1 text-xl font-extrabold text-navy">Таны цуглуулга</h2></div><span class="text-sm font-bold text-brand">{{ $achievements->where('unlocked',true)->count() }} / {{ $achievements->count() }}</span></div><div class="grid gap-3 mt-5 sm:grid-cols-2">@forelse($achievements->take(4) as $achievement)<div class="p-4 rounded-xl {{ $achievement->unlocked ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50' }}"><div class="flex justify-between gap-2"><p class="text-sm font-bold text-navy">{{ $achievement->title }}</p><span class="text-xs font-bold text-slate-400">{{ $achievement->progress }}%</span></div><div class="h-1.5 mt-3 overflow-hidden rounded-full bg-white"><div class="h-full rounded-full {{ $achievement->unlocked ? 'bg-amber-400' : 'bg-slate-300' }}" style="width:{{ $achievement->progress }}%"></div></div></div>@empty<div class="col-span-2 py-6 text-sm text-center text-slate-500">Анхны тоглоомоо дуусгаад achievement нээгээрэй.</div>@endforelse</div></section>
      <section class="p-6 bg-white border border-slate-200 rounded-2xl"><p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Сүүлийн тоглолтууд</p><h2 class="mt-1 text-xl font-extrabold text-navy">Тоглолтын түүх</h2><div class="mt-5 space-y-2">@forelse($recentScores as $score)<div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50"><span class="flex items-center justify-center w-9 h-9 text-xs font-extrabold rounded-lg bg-white text-brand">{{ strtoupper(mb_substr($score->mode,0,1)) }}</span><div class="flex-1 min-w-0"><p class="text-sm font-bold text-slate-700 truncate">{{ ucfirst($score->mode) }}</p><p class="text-xs text-slate-400">{{ $score->created_at->diffForHumans() }} · {{ $score->accuracy }}% зөв</p></div><div class="text-right"><p class="text-sm font-extrabold text-navy">{{ $score->score }}</p><p class="text-xs font-bold text-emerald-600">+{{ $score->xp_earned }} XP</p></div></div>@empty<div class="py-8 text-sm text-center text-slate-500">Тоглолтын түүх одоогоор алга.</div>@endforelse</div></section>
    </div>
  </div>
</section>
@push('scripts')
<script>document.addEventListener('DOMContentLoaded',()=>document.querySelectorAll('.game-filter').forEach(button=>button.addEventListener('click',()=>{const filter=button.dataset.filter;document.querySelectorAll('.game-filter').forEach(item=>item.className='game-filter px-3 py-2 text-xs font-bold rounded-lg bg-slate-100 text-slate-600');button.className='game-filter px-3 py-2 text-xs font-bold text-white rounded-lg bg-navy';document.querySelectorAll('.game-card').forEach(card=>card.classList.toggle('hidden',filter!=='all'&&card.dataset.category!==filter));})));</script>
@endpush
@endsection
