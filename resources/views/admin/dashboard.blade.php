@extends('admin.layout')

@section('title', 'Админ самбар')

@section('admin_content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Админ самбар</h1>
            <p class="mt-2 text-sm text-white/50">
                Системийн статистик, сэдэв, асуулт, хэрэглэгчдийг хялбархан удирдана
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.topics.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      bg-emerald-500/90 text-white hover:bg-emerald-400
                      shadow-lg shadow-emerald-500/20 transition-all duration-200 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Сэдэв нэмэх
            </a>
            <a href="{{ route('admin.questions.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                      bg-gold text-slate-900 hover:bg-amber-400
                      shadow-lg shadow-gold/25 transition-all duration-200 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Асуулт нэмэх
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-10">
        {{-- Нийт хэрэглэгч --}}
        <div class="relative overflow-hidden p-5 rounded-2xl bg-slate-900/60 border border-white/5
                    hover:border-white/10 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-white/40">Нийт хэрэглэгч</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20
                            flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Админ --}}
        <div class="relative overflow-hidden p-5 rounded-2xl bg-slate-900/60 border border-white/5
                    hover:border-white/10 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-white/40">Админ хэрэглэгч</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($adminUsers) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-violet-500/10 border border-violet-500/20
                            flex items-center justify-center text-violet-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Сэдэв --}}
        <div class="relative overflow-hidden p-5 rounded-2xl bg-slate-900/60 border border-white/5
                    hover:border-white/10 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-white/40">Нийт сэдэв</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($totalTopics) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20
                            flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Асуулт --}}
        <div class="relative overflow-hidden p-5 rounded-2xl bg-slate-900/60 border border-white/5
                    hover:border-white/10 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-white/40">Нийт асуулт</p>
                    <p class="mt-2 text-3xl font-bold text-white">{{ number_format($totalQuestions) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20
                            flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @php
        $recentTopics = \App\Models\Topic::orderBy('updated_at', 'desc')->limit(6)->get();
        $recentQuestions = \App\Models\Question::with('topic')->orderBy('created_at', 'desc')->limit(6)->get();
    @endphp

    {{-- Recent Sections --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        {{-- Recent Topics --}}
        <div class="rounded-2xl bg-slate-900/60 border border-white/5 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <h2 class="text-base font-semibold text-white">Сүүлд шинэчлэгдсэн сэдвүүд</h2>
                <a href="{{ route('admin.topics.index') }}"
                   class="text-xs font-medium text-white/40 hover:text-gold transition-colors">
                    Бүгдийг харах →
                </a>
            </div>

            <ul class="divide-y divide-white/5">
                @forelse($recentTopics as $t)
                    <li class="flex items-center justify-between px-6 py-3.5 hover:bg-white/[0.02] transition-colors group">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white/90 truncate">{{ $t->name }}</p>
                            <p class="mt-0.5 text-xs text-white/40">
                                {{ $t->grade_level }}-р анги • {{ $t->updated_at?->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('admin.topics.edit', $t) }}"
                           class="shrink-0 ml-4 px-3 py-1.5 rounded-lg text-xs font-medium
                                  text-white/40 hover:text-white hover:bg-white/5 transition-all">
                            Засах
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-white/30">
                        Шинэ сэдэв алга
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Recent Questions --}}
        <div class="rounded-2xl bg-slate-900/60 border border-white/5 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <h2 class="text-base font-semibold text-white">Сүүлд нэмсэн асуултууд</h2>
                <a href="{{ route('admin.questions.index') }}"
                   class="text-xs font-medium text-white/40 hover:text-gold transition-colors">
                    Бүгдийг харах →
                </a>
            </div>

            <ul class="divide-y divide-white/5">
                @forelse($recentQuestions as $q)
                    <li class="flex items-start justify-between gap-4 px-6 py-3.5 hover:bg-white/[0.02] transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-white/90 leading-snug line-clamp-2">
                                {{ Str::limit($q->text, 90) }}
                            </p>
                            <p class="mt-1 text-xs text-white/40">
                                {{ $q->topic?->name ?? '—' }} • {{ $q->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        <div class="shrink-0 flex flex-col items-end gap-1.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg
                                         bg-gold/10 text-gold text-xs font-bold border border-gold/20">
                                {{ $q->answer }}
                            </span>
                            <a href="{{ route('admin.questions.edit', $q) }}"
                               class="text-xs text-white/40 hover:text-white transition-colors">
                                Засах
                            </a>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-white/30">
                        Шинэ асуулт алга
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Admin Note --}}
    <div class="rounded-2xl bg-slate-900/40 border border-dashed border-white/10 p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-800/80 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white/80">Админ тэмдэглэл</h3>
                <p class="mt-1.5 text-sm text-white/40 leading-relaxed">
                    Дараа хийх ажлууд: видео аплоад, CSV импорт/экспорт, шалгалтын үр дүнгийн тайлан.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection