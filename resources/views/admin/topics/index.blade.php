@extends('admin.layout')

@section('title', 'Админ — Сэдвүүд')

@section('admin_content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Сэдвүүд</h1>
            <p class="mt-1.5 text-sm text-white/45">
                Нийт <span class="text-white/70 font-medium">{{ $topics->total() }}</span> сэдэв
            </p>
        </div>
        <a href="{{ route('admin.topics.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gold text-slate-900 text-sm font-semibold
                  hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/30
                  active:scale-[0.98] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Шинэ сэдэв
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden shadow-xl shadow-black/25">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-slate-950/50">
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 w-16">ID</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35">Нэр</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 text-center w-24">Анги</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35">Видео</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 text-center w-28">Premium</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 text-right w-40">Үйлдэл</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($topics as $topic)
                        <tr class="group hover:bg-white/[0.025] transition-colors duration-150">
                            <td class="px-5 py-4 text-sm text-white/35 font-mono tabular-nums">
                                #{{ $topic->id }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-white/85 group-hover:text-white transition-colors">
                                    {{ $topic->name }}
                                </p>
                                <p class="mt-0.5 text-xs text-white/30 font-mono">{{ $topic->slug }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2.5 py-1 rounded-lg
                                             text-xs font-semibold bg-slate-800/90 text-white/70 border border-white/5">
                                    {{ $topic->grade_level }}-р
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($topic->video_title || $topic->video_url || $topic->video_file)
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-lg bg-violet-500/10 border border-violet-500/20
                                                     flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </span>
                                        <span class="text-sm text-white/65 truncate max-w-[200px]">
                                            {{ $topic->video_title ?? 'Видео байна' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm text-white/25">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($topic->is_premium)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                                 bg-gold/10 text-gold border border-gold/20">
                                        Premium
                                    </span>
                                @else
                                    <span class="text-xs text-white/25">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.topics.edit', $topic) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                              text-white/55 hover:text-white hover:bg-white/8
                                              transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Засах
                                    </a>
                                    <form action="{{ route('admin.topics.destroy', $topic) }}"
                                          method="POST"
                                          onsubmit="return confirm('Энэ сэдвийг устгах уу?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                                       text-rose-400/70 hover:text-rose-400 hover:bg-rose-500/10
                                                       transition-all duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Устгах
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-800/70 border border-white/5 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white/50">Одоогоор сэдэв байхгүй байна</p>
                                        <p class="mt-1 text-xs text-white/30">Эхний сэдвээ нэмээд эхлээрэй</p>
                                    </div>
                                    <a href="{{ route('admin.topics.create') }}"
                                       class="inline-flex items-center gap-1.5 mt-1 px-4 py-2 rounded-xl text-sm font-medium
                                              bg-gold/10 text-gold border border-gold/20
                                              hover:bg-gold/15 hover:border-gold/30 transition-all">
                                        Эхний сэдвээ нэмэх
                                        <span class="text-gold/60">→</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($topics->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-white/5 bg-slate-950/40">
                <p class="text-xs text-white/40 order-2 sm:order-1">
                    <span class="text-white/55 font-medium">{{ $topics->firstItem() }}–{{ $topics->lastItem() }}</span>
                    <span class="mx-1">/</span>
                    {{ $topics->total() }}
                </p>

                <div class="flex items-center gap-1 order-1 sm:order-2">
                    @if($topics->onFirstPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/15 cursor-not-allowed select-none">‹</span>
                    @else
                        <a href="{{ $topics->previousPageUrl() }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/55 hover:text-white hover:bg-white/8 transition">‹</a>
                    @endif

                    @php
                        $current = $topics->currentPage();
                        $last = $topics->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $topics->url(1) }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">1</a>
                        @if($start > 2)
                            <span class="w-8 h-8 flex items-center justify-center text-white/20 text-sm select-none">…</span>
                        @endif
                    @endif

                    @for($page = $start; $page <= $end; $page++)
                        @if($page === $current)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold bg-gold text-slate-900 shadow-sm shadow-gold/20">{{ $page }}</span>
                        @else
                            <a href="{{ $topics->url($page) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="w-8 h-8 flex items-center justify-center text-white/20 text-sm select-none">…</span>
                        @endif
                        <a href="{{ $topics->url($last) }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">{{ $last }}</a>
                    @endif

                    @if($topics->hasMorePages())
                        <a href="{{ $topics->nextPageUrl() }}"
                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/55 hover:text-white hover:bg-white/8 transition">›</a>
                    @else
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/15 cursor-not-allowed select-none">›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
