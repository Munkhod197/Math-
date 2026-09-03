@extends('layouts.app')

@section('title','MathMon')

@section('content')
@php
  $topics = $topics ?? collect();
  $totalTopics = $topics->sum(fn($g) => $g->count());
  $gradeCount = $topics->count();
  $hasPremium = auth()->user()?->billing_status === 'active' && (! auth()->user()?->billing_ends_at || auth()->user()->billing_ends_at->isFuture());
@endphp

<section class="relative overflow-hidden home-hero">
  <div class="home-hero-bg"></div>
  <div class="home-hero-glow home-hero-glow-1"></div>
  <div class="home-hero-glow home-hero-glow-2"></div>

  {{-- graph-paper texture + floating glyphs — same signature as auth pages, ties the site together --}}
  <div class="absolute inset-0 opacity-[0.05] pointer-events-none" style="background-image:
      linear-gradient(rgba(255,255,255,.9) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.9) 1px, transparent 1px);
      background-size: 32px 32px;"></div>
  <span class="absolute font-serif select-none pointer-events-none text-7xl top-6 left-[38%] text-white/[0.06] rotate-6">π</span>
  <span class="absolute font-serif select-none pointer-events-none text-5xl bottom-10 left-8 text-white/[0.06] -rotate-12">√x</span>
  <span class="absolute font-serif select-none pointer-events-none text-8xl top-1/3 right-4 text-white/[0.05] rotate-3">Σ</span>
  <span class="absolute font-serif select-none pointer-events-none text-4xl bottom-24 right-1/3 text-white/[0.06] -rotate-6">∫</span>

  <div class="relative max-w-6xl px-4 mx-auto py-14 sm:py-20 fade">
    <div class="grid gap-10 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
      <div>
        <span class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-xs font-bold tracking-widest uppercase border rounded-full bg-white/10 border-white/15 text-white/80">
          Монгол сурагчдад зориулсан
        </span>
        <h1 class="text-4xl font-extrabold leading-tight text-white sm:text-6xl">
          Математикаа тестээр шалгаж, хичээлээр бататга
        </h1>
        <p class="max-w-2xl mt-5 text-base leading-8 text-white/65">
          Сэдвээ сонгоод бодлого бодно, дүнгээ харна, сул талаа илрүүлээд яг хэрэгтэй хичээл рүүгээ орно.
        </p>
        <div class="flex flex-col gap-4 mt-9 sm:flex-row">
          <a href="{{ route('test.start') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-extrabold home-btn-primary rounded-2xl bg-gold text-navy">
            Тест эхлүүлэх
          </a>
          <a href="{{ route('topics.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition border home-btn-secondary rounded-2xl border-white/20 bg-white/10 hover:bg-white/15">
            Хичээлүүд үзэх
          </a>
        </div>
      </div>

      <div class="grid gap-3 sm:grid-cols-2">
        @foreach([
          ['Сэдэв', $totalTopics, '1-12 ангийн үндсэн агуулга'],
          ['Анги', $gradeCount, 'Ангиар ангилсан хичээл'],
          ['Тест', 'AI', 'Сэдэв бүрт шинэ бодлого'],
          ['Эрх', $hasPremium ? 'Premium' : 'Free', $hasPremium ? 'Бүх хичээл нээлттэй' : 'Preview горим идэвхтэй'],
        ] as [$label,$value,$desc])
          <div class="p-5 transition border home-stat rounded-3xl bg-white/10 border-white/10 hover:bg-white/[0.14]">
            <div class="text-3xl font-extrabold text-white">{{ $value }}</div>
            <div class="mt-2 text-sm font-bold text-gold">{{ $label }}</div>
            <div class="mt-1 text-xs leading-5 text-white/45">{{ $desc }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section class="max-w-6xl px-4 mx-auto py-14 home-page-shell">
  <div class="grid gap-5 mb-14 sm:grid-cols-3">
    @foreach([
      ['🧪', 'Сул талаа олно', 'Тестийн дүнгээр ямар сэдвээ давтах хэрэгтэйг шууд харна.'],
      ['🎬', 'Хичээлээр бататгана', 'Сэдэв бүрийн тайлбар, дасгал, шалгалтыг нэг дороос ашиглана.'],
      ['📊', 'Ахиц хянадаг', 'Зөв, буруу хариулт болон давтах төлөвлөгөө автоматаар гарна.'],
    ] as [$icon,$title,$desc])
      <article class="p-6 transition bg-white border home-feature border-slate-100 rounded-3xl hover:-translate-y-1 hover:shadow-lg">
        <div class="flex items-center justify-center mb-5 text-2xl border w-14 h-14 rounded-2xl bg-brand/5 border-brand/10">{{ $icon }}</div>
        <h2 class="text-lg font-extrabold text-navy">{{ $title }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $desc }}</p>
      </article>
    @endforeach
  </div>

  <section class="relative mb-14 overflow-hidden rounded-[2rem] border border-brand/10 bg-gradient-to-br from-navy via-blue-900 to-brand p-7 text-white shadow-[0_24px_80px_rgba(15,32,68,0.16)]">
    <span class="absolute font-serif select-none pointer-events-none text-8xl -top-6 -right-4 text-white/[0.05] rotate-6">∑</span>

    <div class="relative grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
      <div>
        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.22em] text-white/80">
          Premium эрх
        </span>
        <h2 class="mt-4 text-2xl font-extrabold leading-tight sm:text-3xl">
          Бүх сэдэв, бүх хичээл, бүх тайлбар нэг дороос
        </h2>
        <p class="max-w-2xl mt-3 text-sm leading-7 text-white/70 sm:text-base">
          Free горимоор эхний хэсгүүдийг судалж, Premium эрх авснаар бүх агуулгыг нээлттэй ашиглах боломжтой. Сурцгаа бодлого, видео, тайлбар гээд бүх зүйлийг нэг дороос үргэлжлүүлээрэй.
        </p>

        <div class="flex flex-col gap-3 mt-6 sm:flex-row">
          <a href="{{ route('billing.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-extrabold home-btn-primary rounded-2xl bg-gold text-navy">
            Эрх сонгох
          </a>
          <a href="{{ route('topics.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition border rounded-2xl border-white/20 bg-white/10 hover:bg-white/15">
            Сэдвүүд үзэх
          </a>
        </div>
      </div>

      <div class="rounded-[1.5rem] border border-white/15 bg-white/10 p-5 backdrop-blur-xl">
        <div class="grid gap-3 sm:grid-cols-2">
          <div class="p-4 transition rounded-2xl bg-slate-950/25 hover:bg-slate-950/35">
            <p class="text-2xl font-extrabold text-white">🎥</p>
            <p class="mt-2 font-extrabold text-white">Видео хичээл</p>
            <p class="mt-1 text-sm leading-6 text-white/70">Сэдэв бүрийн тайлбарыг бүрэн нээнэ.</p>
          </div>
          <div class="p-4 transition rounded-2xl bg-slate-950/25 hover:bg-slate-950/35">
            <p class="text-2xl font-extrabold text-white">🧠</p>
            <p class="mt-2 font-extrabold text-white">Тестийн тайлбар</p>
            <p class="mt-1 text-sm leading-6 text-white/70">Яагаад буруу болсон, юу давтах ёстойг шууд харна.</p>
          </div>
          <div class="p-4 transition rounded-2xl bg-slate-950/25 sm:col-span-2 hover:bg-slate-950/35">
            <p class="text-2xl font-extrabold text-white">📈</p>
            <p class="mt-2 font-extrabold text-white">Ахицын дүн</p>
            <p class="mt-1 text-sm leading-6 text-white/70">Суралцах явц, давтах сэдвүүдээ нэг харагдацад авчирна.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <p class="text-xs font-extrabold tracking-widest uppercase text-brand">Сэдвүүд</p>
      <h2 class="mt-1 text-3xl font-extrabold text-navy">Ангиар сурах</h2>
      <p class="mt-2 text-sm text-slate-500">1–12 ангийн {{ $totalTopics }} сэдэв. Free горимоор эхнийх нь нээлттэй, бусад нь Premium.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('topics.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-extrabold bg-white border rounded-2xl border-navy/15 text-navy">
        Бүх сэдэв →
      </a>
      <a href="{{ route('billing.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-extrabold text-white rounded-2xl bg-navy">
        Premium эрх
      </a>
    </div>
  </div>

  <div class="mb-8 overflow-x-auto section-nav-sticky">
    <div class="inline-flex gap-3 px-4 py-3 border rounded-full shadow-sm bg-white/90 backdrop-blur border-slate-200">
      @foreach($topics as $navGrade => $navTopics)
        <a href="#grade-{{ $navGrade }}" data-grade="{{ $navGrade }}" class="grade-link inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold transition hover:bg-slate-100 hover:text-navy {{ $loop->first ? 'bg-navy text-white' : 'bg-white text-slate-600' }}">
          {{ $navGrade }}-р анги
        </a>
      @endforeach
    </div>
  </div>

  @foreach($topics as $grade => $gradeTopics)
    <div id="grade-{{ $grade }}" data-grade-section="{{ $grade }}" class="mb-10 scroll-mt-28">
      <div class="flex items-center gap-3 mb-4">
        <span class="home-grade-badge">{{ $grade }}-р анги</span>
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-sm font-semibold text-slate-400">{{ $gradeTopics->count() }} сэдэв</span>
      </div>
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($gradeTopics as $topic)
          <article class="relative flex flex-col overflow-hidden transition bg-white border shadow-sm border-slate-100 rounded-3xl hover:-translate-y-1 hover:shadow-xl">
            <a href="{{ $topic->user_can_access ? route('topics.show', $topic->slug) : route('billing.index') }}" class="flex items-center justify-between px-5 py-4 text-white bg-gradient-to-br from-navy to-brand">
              <span class="text-3xl">{{ $topic->icon }}</span>
              <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $topic->user_can_access ? 'bg-white/15 text-white' : 'bg-gold text-navy' }}">
                {{ $topic->user_can_access ? $topic->grade_level.'-р' : 'Premium' }}
              </span>
            </a>
            <div class="flex flex-col flex-1 p-5">
              <h3 class="text-base font-extrabold leading-snug text-navy">{{ $topic->name }}</h3>
              <p class="flex-1 mt-2 text-xs leading-5 text-slate-500 line-clamp-2">{{ $topic->description }}</p>
              <div class="grid gap-2 mt-4">
                <a href="{{ $topic->user_can_access ? route('topics.show', $topic->slug) : route('billing.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-extrabold rounded-xl {{ $topic->user_can_access ? 'bg-brand/10 text-brand' : 'bg-gold/15 text-amber-700' }}">
                  {{ $topic->user_can_access ? 'Үзэх →' : 'Эрх нээх' }}
                </a>
                @if($topic->user_can_access)
                  <a href="{{ route('test.topic.start', $topic->slug) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-extrabold text-white rounded-xl bg-navy">Шалгалт</a>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  @endforeach
</section>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('[data-grade-section]');
    const links = document.querySelectorAll('.grade-link');

    const updateActive = (grade) => {
      let activeLink = null;
      links.forEach((link) => {
        const isActive = link.dataset.grade === grade;
        link.classList.toggle('bg-navy', isActive);
        link.classList.toggle('text-white', isActive);
        link.classList.toggle('bg-white', !isActive);
        link.classList.toggle('text-slate-600', !isActive);
        if (isActive) activeLink = link;
      });
      activeLink?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          updateActive(entry.target.dataset.gradeSection);
        }
      });
    }, {
      rootMargin: '-35% 0px -55% 0px',
      threshold: 0.25,
    });

    sections.forEach((section) => observer.observe(section));
  });
</script>
@endpush
@endsection
