@extends('admin.layout')

@section('title', 'Админ - Асуултууд')

@section('admin_content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Асуултууд</h1>
            <p class="mt-1.5 text-sm text-white/45">
                Нийт <span class="text-white/70 font-medium">{{ $questions->total() }}</span> асуулт
            </p>
        </div>
        <a href="{{ route('admin.questions.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                  bg-gold text-slate-900 text-sm font-semibold
                  hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/30
                  active:scale-[0.98] transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Шинэ асуулт
        </a>
    </div>

    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden shadow-xl shadow-black/25">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-slate-950/50">
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 w-16">ID</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35">Сэдэв</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35">Асуулт</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 text-center w-24">Хариулт</th>
                        <th class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-white/35 text-right w-40">Үйлдэл</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.04]">
                    @forelse($questions as $question)
                        <tr class="group hover:bg-white/[0.025] transition-colors duration-150">
                            <td class="px-5 py-4 text-sm text-white/35 font-mono tabular-nums">#{{ $question->id }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800/90 text-white/70 border border-white/5">
                                        {{ $question->topic->name ?? '-' }}
                                    </span>
                                    @if($question->topic)
                                        <span class="text-[11px] text-white/30 font-medium">{{ $question->topic->grade_level }}-р анги</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 max-w-md">
                                <p class="text-sm text-white/85 leading-snug group-hover:text-white transition-colors">
                                    {{ Str::limit($question->text, 90) }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg bg-gold/10 text-gold text-sm font-bold border border-gold/20 tabular-nums">
                                    {{ $question->answer }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.questions.edit', $question) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white/55 hover:text-white hover:bg-white/8 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Засах
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST"
                                          onsubmit="return confirm('Энэ асуултыг устгах уу?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-400/70 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-150">
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
                            <td colspan="5" class="px-5 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-800/70 border border-white/5 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white/50">Одоогоор асуулт байхгүй байна</p>
                                        <p class="mt-1 text-xs text-white/30">Эхний асуултаа нэмээд эхлээрэй</p>
                                    </div>
                                    <a href="{{ route('admin.questions.create') }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gold text-slate-900 text-sm font-semibold hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/30 active:scale-[0.98] transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Шинэ асуулт
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-white/5 bg-slate-950/40">
                <p class="text-xs text-white/40 order-2 sm:order-1">
                    <span class="text-white/55 font-medium">{{ $questions->firstItem() }}-{{ $questions->lastItem() }}</span>
                    <span class="mx-1">/</span>
                    {{ $questions->total() }}
                </p>

                <div class="flex items-center gap-1 order-1 sm:order-2">
                    @if($questions->onFirstPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/15 cursor-not-allowed select-none">&lt;</span>
                    @else
                        <a href="{{ $questions->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/55 hover:text-white hover:bg-white/8 transition">&lt;</a>
                    @endif

                    @php
                        $current = $questions->currentPage();
                        $last = $questions->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $questions->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">1</a>
                        @if($start > 2)
                            <span class="w-8 h-8 flex items-center justify-center text-white/20 text-sm select-none">...</span>
                        @endif
                    @endif

                    @for($page = $start; $page <= $end; $page++)
                        @if($page === $current)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold bg-gold text-slate-900 shadow-sm shadow-gold/20">{{ $page }}</span>
                        @else
                            <a href="{{ $questions->url($page) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="w-8 h-8 flex items-center justify-center text-white/20 text-sm select-none">...</span>
                        @endif
                        <a href="{{ $questions->url($last) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-white/55 hover:text-white hover:bg-white/8 transition">{{ $last }}</a>
                    @endif

                    @if($questions->hasMorePages())
                        <a href="{{ $questions->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/55 hover:text-white hover:bg-white/8 transition">&gt;</a>
                    @else
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white/15 cursor-not-allowed select-none">&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
