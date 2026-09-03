@extends('layouts.app')

@section('title')
    @yield('title')
@endsection

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Admin Top Bar --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-white/40">Админ</p>
                    <h1 class="text-lg font-bold text-white leading-tight">@yield('title')</h1>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex items-center gap-1 p-1 rounded-xl bg-slate-900/80 border border-white/5">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('admin.dashboard')
                              ? 'bg-gold text-slate-900 shadow-sm'
                              : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    Самбар
                </a>
                <a href="{{ route('admin.topics.index') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('admin.topics.*')
                              ? 'bg-gold text-slate-900 shadow-sm'
                              : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    Сэдвүүд
                </a>
                <a href="{{ route('admin.questions.index') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('admin.questions.*')
                              ? 'bg-gold text-slate-900 shadow-sm'
                              : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    Асуултууд
                </a>
                <a href="{{ route('admin.tests.index') }}"
                   class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('admin.tests.*')
                              ? 'bg-gold text-slate-900 shadow-sm'
                              : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    Тестүүд
                </a>
            </nav>
        </div>

        {{-- Content Area --}}
        <div class="relative">
            @if(request()->routeIs('admin.questions.create', 'admin.questions.edit', 'admin.tests.import.preview'))
                @include('admin.partials.math-toolbar')
            @endif

            @yield('admin_content')
        </div>

    </div>
</div>
@endsection
