@extends('layouts.app')

@section('title', 'Сэдвүүд — MathMon')

@section('content')
<section class="relative max-w-6xl px-4 py-12 mx-auto fade">

  {{-- Header --}}
  <div class="grid gap-6 mb-12 lg:grid-cols-[1fr_auto] lg:items-end">
    <div>
      <span class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 text-[11px] font-bold tracking-widest uppercase rounded-full bg-brand/10 text-brand border border-brand/15">
        <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
        Видео · Дасгал · Шалгалт
      </span>
      <h1 class="text-4xl font-extrabold tracking-tight text-navy sm:text-5xl">
        Сэдвүүд
      </h1>
      <p class="max-w-xl mt-3 text-sm leading-relaxed text-slate-500">
        1–12 ангийн бүх сэдэв. Сэдэв бүр дээр дасгал, видео, шалгалт хамт байрлана.
        Premium тэмдэгтэй сэдвүүдийг нээхийн тулд эрх идэвхжүүлнэ.
      </p>
    </div>

    <a href="{{ route('billing.index') }}"
       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-white transition rounded-2xl bg-navy hover:bg-navy/90 hover:shadow-lg hover:shadow-navy/25 active:scale-[0.98]">
      @if($hasPremium)
        <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        Premium идэвхтэй
      @else
        <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        Premium эрх нээх
      @endif
    </a>
  </div>

  {{-- Grade navigation --}}
  <div class="sticky top-4 z-20 mb-10 overflow-x-auto scrollbar-hide">
    <div class="inline-flex gap-2 p-2 bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-sm min-w-full sm:min-w-0">
      @foreach($topics as $navGrade => $navTopics)
        <a href="#grade-{{ $navGrade }}"
           data-grade="{{ $navGrade }}"
           class="grade-link shrink-0 inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200
                  {{ $loop->first ? 'bg-navy text-white shadow-md shadow-navy/20' : 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-navy' }}">
          {{ $navGrade }}-р анги
        </a>
      @endforeach
    </div>
  </div>

  {{-- Topics by grade --}}
  @foreach($topics as $grade => $gradeTopics)
    <div id="grade-{{ $grade }}" data-grade-section="{{ $grade }}" class="mb-14 scroll-mt-28">

      {{-- Grade header --}}
      <div class="flex items-center justify-between px-1 mb-5">
        <div class="flex items-center gap-3">
          <div class="flex items-center justify-center w-10 h-10 text-sm font-bold text-white rounded-xl bg-gradient-to-br from-navy to-brand shadow-md shadow-navy/20">
            {{ $grade }}
          </div>
          <div>
            <h2 class="text-xl font-extrabold text-navy">{{ $grade }}-р анги</h2>
            <p class="text-xs text-slate-400">{{ $gradeTopics->count() }} сэдэв</p>
          </div>
        </div>
      </div>

      {{-- Cards grid --}}
      <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($gradeTopics as $topic)
          <article class="group relative flex flex-col overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-sm
                          transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-200/60 hover:border-slate-200">

            {{-- Icon header --}}
            <a href="{{ $topic->user_can_access ? route('topics.show', $topic->slug) : route('billing.index') }}"
               class="relative flex items-center justify-center h-32 overflow-hidden text-5xl text-white
                      bg-gradient-to-br from-navy via-navy to-brand">

              {{-- Soft pattern --}}
              <div class="absolute inset-0 opacity-20"
                   style="background-image: radial-gradient(circle at 20% 80%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 24px 24px;"></div>

              <span class="relative z-10 transition-transform duration-300 group-hover:scale-110">
                {{ $topic->icon }}
              </span>

              {{-- Badge --}}
              <span class="absolute z-10 px-2.5 py-1 text-[10px] font-bold tracking-wide rounded-full right-3 top-3
                           {{ $topic->user_can_access
                                ? 'bg-white/20 text-white backdrop-blur-sm'
                                : 'bg-amber-400 text-navy shadow-sm' }}">
                @if($topic->user_can_access)
                  {{ $topic->test_questions_count }} бодлого
                @else
                  ★ Premium
                @endif
              </span>
            </a>

            {{-- Body --}}
            <div class="flex flex-col flex-1 p-5">
              <h3 class="text-[15px] font-bold leading-snug text-navy line-clamp-2">
                {{ $topic->name }}
              </h3>

              <p class="mt-2 text-sm leading-relaxed text-slate-500 line-clamp-2">
                {{ $topic->description }}
              </p>

              <div class="flex items-center justify-between gap-3 pt-4 mt-auto border-t border-slate-50">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  {{ $topic->questions_count }} дасгал
                </span>

                @if($topic->user_can_access)
                  <a href="{{ route('topics.show', $topic->slug) }}"
                     class="inline-flex items-center gap-1 text-sm font-bold text-brand transition hover:gap-1.5">
                    Үзэх
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                  </a>
                @else
                  <a href="{{ route('billing.index') }}"
                     class="inline-flex items-center gap-1 text-sm font-bold text-amber-500 transition hover:gap-1.5">
                    Нээх
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                  </a>
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
      link.classList.toggle('shadow-md', isActive);
      link.classList.toggle('shadow-navy/20', isActive);
      link.classList.toggle('bg-transparent', !isActive);
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

@push('styles')
<style>
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
@endsection
