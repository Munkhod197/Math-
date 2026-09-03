<!DOCTYPE html>
<html lang="mn" class="scroll-smooth">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'MathMon')</title>
    <meta name="description" content="MathMon - Монгол сурагчдад зориулсан математикийн тест, видео хичээл, тоглоомын платформ">
    <meta name="theme-color" content="#0F2044">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
    @endphp

    @if($manifest && isset($manifest['resources/css/app.css']['file']))
        <link rel="stylesheet" href="{{ asset('build/'.$manifest['resources/css/app.css']['file']) }}">
        <script type="module" src="{{ asset('build/'.$manifest['resources/js/app.js']['file']) }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
    @stack('head')
</head>
<body class="flex flex-col min-h-screen antialiased text-slate-800">
<div id="loader" class="fixed inset-0 bg-[#0F2044] z-[9999] flex flex-col items-center justify-center gap-4">
    <div class="loader-ring"><span></span><span></span><span></span></div>
    <div class="text-sm text-white/70">MathMon ачаалж байна...</div>
</div>

@php
  $authUser = auth()->user();

  $navItems = [
    [route('home'), '🏠', 'Нүүр', request()->routeIs('home')],
    [route('topics.index'), '📚', 'Сэдэв', request()->routeIs('topics.*')],
    [route('test.start'), '📝', 'Тест', request()->routeIs('test.*') && !request()->routeIs('topics.*')],
    [route('ai-teacher.index'), '🤖', 'AI Багш', request()->routeIs('ai-teacher.*')],
    [route('toys.index'), '🎮', 'Тоглоом', request()->routeIs('toys.*')],
    [route('billing.index'), '💳', 'Төлбөр', request()->routeIs('billing.*')],
  ];

  if ($authUser && $authUser->is_admin && \Illuminate\Support\Facades\Route::has('admin.dashboard')) {
      $navItems[] = [route('admin.dashboard'), '🛠️', 'Админ', request()->routeIs('admin.*')];
  }
@endphp

<header id="site-header" class="sticky top-0 z-50 transition-all duration-300 site-header">
    <div class="header-bg"></div>
    <div class="max-w-6xl px-4 mx-auto">
        <div class="flex items-center justify-between h-[4.25rem]">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                <div class="flex items-center justify-center w-10 h-10 text-lg border logo-badge rounded-xl bg-gold/15 border-gold/25">📐</div>
                <div>
                    <div class="text-xl font-extrabold text-white">Math<span class="text-gold">Mon</span></div>
                    <div class="hidden sm:block text-[11px] text-white/50">Математик сурах орчин</div>
                </div>
            </a>

            <nav aria-label="Primary navigation" class="items-center hidden gap-1 p-1 border lg:flex bg-white/5 rounded-2xl border-white/10 backdrop-blur">
                @foreach($navItems as [$url,$icon,$label,$active])
                    <a href="{{ $url }}" class="nav-link px-3 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $active ? 'nav-link-active' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        {{ $icon }} {{ $label }}
                    </a>
                @endforeach
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="ml-1">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-sm font-semibold nav-link rounded-xl text-white/70 hover:text-white hover:bg-white/10">Гарах</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold nav-link rounded-xl text-white/70 hover:text-white hover:bg-white/10">Нэвтрэх</a>
                @endguest
            </nav>

            <button id="menu-btn" type="button" aria-controls="mobile-nav" aria-expanded="false"
                    class="flex items-center justify-center w-10 h-10 text-white border lg:hidden rounded-xl bg-white/10 border-white/15">
                <svg id="menu-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="menu-close" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav id="mobile-nav" class="hidden pb-4 lg:hidden">
            <div class="flex flex-col gap-1 p-2 border bg-white/5 border-white/10 rounded-2xl backdrop-blur">
                @foreach($navItems as [$url,$icon,$label,$active])
                    <a href="{{ $url }}" class="px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 {{ $active ? 'nav-link-active' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                        {{ $icon }} {{ $label }}
                    </a>
                @endforeach
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="px-4 py-3">
                        @csrf
                        <button type="submit" class="w-full text-sm font-semibold text-left text-white/70 rounded-xl">Гарах</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-3 text-sm font-semibold text-white/70 rounded-xl">Нэвтрэх</a>
                @endguest
            </div>
        </nav>
    </div>
    <div class="header-accent"></div>
</header>

@if(session('error'))
<div class="max-w-6xl px-4 mx-auto mt-4 fade">
    <div class="px-4 py-3 text-red-800 border border-red-200 bg-red-50 rounded-xl">{{ session('error') }}</div>
</div>
@endif

@if(session('success'))
<div class="max-w-6xl px-4 mx-auto mt-4 fade">
    <div class="px-4 py-3 border bg-emerald-50 border-emerald-200 text-emerald-800 rounded-xl">{{ session('success') }}</div>
</div>
@endif

<main class="flex-1">@yield('content')</main>

@auth
  @unless(View::hasSection('hide_ai_helper') || request()->routeIs('admin.*'))
    @include('partials.ai-helper')
  @endunless
@endauth

<footer class="mt-16 site-footer">
    <div class="footer-main">
        <div class="max-w-6xl px-4 py-14 mx-auto">
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-4 group">
                        <div class="flex items-center justify-center w-10 h-10 text-lg border logo-badge rounded-xl bg-gold/15 border-gold/25 group-hover:bg-gold/25 transition">
                            📐
                        </div>
                        <div class="text-xl font-extrabold text-white">
                            Math<span class="text-gold">Mon</span>
                        </div>
                    </a>
                    <p class="text-sm leading-relaxed text-white/50 max-w-xs">
                        Монгол сурагчдад зориулсан математикийн тест, видео хичээл, тоглоом, ахицын зөвлөмж.
                    </p>
                </div>

                {{-- Navigation --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold tracking-wider text-white uppercase">Холбоос</h3>
                    <ul class="space-y-2.5">
                        <li><a class="footer-link text-sm text-white/50 transition hover:text-gold" href="{{ route('home') }}">Нүүр</a></li>
                        <li><a class="footer-link text-sm text-white/50 transition hover:text-gold" href="{{ route('topics.index') }}">Сэдэв</a></li>
                        <li><a class="footer-link text-sm text-white/50 transition hover:text-gold" href="{{ route('test.start') }}">Тест</a></li>
                        <li><a class="footer-link text-sm text-white/50 transition hover:text-gold" href="{{ route('toys.index') }}">Тоглоом</a></li>
                        <li><a class="footer-link text-sm text-white/50 transition hover:text-gold" href="{{ route('billing.index') }}">Төлбөр</a></li>
                    </ul>
                </div>

                {{-- Features --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold tracking-wider text-white uppercase">Боломжууд</h3>
                    <ul class="space-y-2.5 text-sm text-white/50">
                        <li class="flex items-center gap-2">
                            <span class="text-gold">▸</span> 1–12 ангийн сэдэв
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-gold">▸</span> Сул тал илрүүлэх тест
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-gold">▸</span> Оноотой mini games
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-gold">▸</span> Дүн шинжилгээ & давтлага
                        </li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="mb-4 text-sm font-bold tracking-wider text-white uppercase">Холбоо барих</h3>
                    <ul class="space-y-2.5 text-sm text-white/50">
                        <li class="flex items-center gap-2">
                            <span class="text-gold">✉</span>
                            <a href="mailto:info@mathmon.mn" class="hover:text-white transition">info@mathmon.mn</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-gold">📍</span>
                            Улаанбаатар, Монгол
                        </li>
                    </ul>
                    <div class="flex gap-3 mt-5">
                        <a href="#" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-white/60 hover:bg-gold/20 hover:text-gold hover:border-gold/30 transition" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 border border-white/10 text-white/60 hover:bg-gold/20 hover:text-gold hover:border-gold/30 transition" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="footer-bottom border-t border-white/5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 max-w-6xl px-4 py-5 mx-auto text-xs text-white/40">
            <span>© {{ date('Y') }} MathMon. Бүх эрх хуулиар хамгаалагдсан.</span>
            <span class="flex items-center gap-1.5">
                Made with <span class="text-gold">♥</span> for Mongolia
            </span>
        </div>
    </div>
</footer>

<script>
(function () {
  function killLoader() {
    var loader = document.getElementById('loader');
    if (!loader) return;
    loader.style.transition = 'opacity 0.35s ease';
    loader.style.opacity = '0';
    setTimeout(function () { loader.remove(); }, 350);
  }
  if (document.readyState === 'complete') killLoader();
  else window.addEventListener('load', killLoader);
  setTimeout(killLoader, 600);

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('menu-btn');
    var nav = document.getElementById('mobile-nav');
    var open = document.getElementById('menu-open');
    var close = document.getElementById('menu-close');
    btn && btn.addEventListener('click', function () {
      nav.classList.toggle('hidden');
      open.classList.toggle('hidden');
      close.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
    });

    if (typeof renderMathInElement !== 'undefined') {
      renderMathInElement(document.body, {
        delimiters: [
          {left: '$$', right: '$$', display: true},
          {left: '$', right: '$', display: false},
          {left: '\\(', right: '\\)', display: false},
          {left: '\\[', right: '\\]', display: true}
        ],
        throwOnError: false
      });
    }
  });
})();
</script>
@stack('scripts')
</body>
</html>
