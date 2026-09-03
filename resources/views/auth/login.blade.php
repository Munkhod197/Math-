@extends('layouts.app')

@section('title','Нэвтрэх')

@section('content')
<section class="max-w-5xl px-4 py-12 mx-auto">
  <div class="grid overflow-hidden bg-white border shadow-2xl login-card border-slate-100 rounded-[2rem] lg:grid-cols-[1fr_.9fr]">

    {{-- LEFT: brand / signature panel --}}
    <aside class="relative p-8 overflow-hidden text-white login-aside lg:p-10">

      {{-- graph-paper texture --}}
      <div class="absolute inset-0 opacity-[0.07]" style="background-image:
          linear-gradient(rgba(255,255,255,.9) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,.9) 1px, transparent 1px);
          background-size: 28px 28px;"></div>

      {{-- large faint protractor arc, signature shape --}}
      <svg class="absolute -right-16 -bottom-16 w-72 h-72 opacity-[0.10]" viewBox="0 0 200 200" fill="none">
        <circle cx="100" cy="100" r="90" stroke="white" stroke-width="1.5"/>
        <circle cx="100" cy="100" r="65" stroke="white" stroke-width="1"/>
        <path d="M100 10 L100 190 M10 100 L190 100" stroke="white" stroke-width="1"/>
        @for($i = 0; $i < 24; $i++)
          <line x1="{{ 100 + 90*cos(deg2rad($i*15)) }}" y1="{{ 100 + 90*sin(deg2rad($i*15)) }}"
                x2="{{ 100 + 80*cos(deg2rad($i*15)) }}" y2="{{ 100 + 80*sin(deg2rad($i*15)) }}"
                stroke="white" stroke-width="1"/>
        @endfor
      </svg>

      {{-- floating math glyphs --}}
      <span class="absolute font-serif text-4xl select-none top-10 right-10 text-white/10 rotate-6">π</span>
      <span class="absolute text-3xl font-serif select-none top-32 right-24 text-white/10 -rotate-12">√x</span>
      <span class="absolute text-5xl font-serif select-none bottom-24 left-6 text-white/10 rotate-3">∫</span>
      <span class="absolute text-3xl font-serif select-none bottom-10 right-16 text-white/10 -rotate-6">Σ</span>

      <div class="relative">
        <span class="inline-flex px-4 py-2 mb-6 text-xs font-extrabold tracking-widest uppercase border rounded-full border-white/15 bg-white/10 text-white/65">MathMon</span>
        <h1 class="text-4xl font-extrabold leading-tight">Суралцах орчиндоо тавтай морил</h1>
        <p class="mt-4 text-sm leading-7 text-white/60">Тест өгч оноогоо хар, сул талаа ол, хэрэгтэй хичээлээ үргэлжлүүлэн үз.</p>

        <div class="grid gap-3 mt-8">
          @foreach(['Оноо болон ахицаа хянах','Сэдэв бүрийн дасгал, шалгалт','Premium эрхээр бүх агуулга нээх'] as $feature)
            <div class="flex items-center gap-3 text-sm text-white/70">
              <span class="w-2 h-2 rounded-full bg-gold"></span>{{ $feature }}
            </div>
          @endforeach
        </div>

        {{-- progress-ring badge, ties to "score tracking" feature --}}
        <div class="flex items-center gap-3 p-3 mt-10 border rounded-2xl border-white/10 bg-white/5 w-fit">
          <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
            <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="3"/>
            <circle cx="18" cy="18" r="15.5" fill="none" stroke="var(--gold, #d4af37)" stroke-width="3"
                    stroke-dasharray="97.4" stroke-dashoffset="18" stroke-linecap="round"/>
          </svg>
          <div class="leading-tight">
            <p class="text-sm font-extrabold text-white">81%</p>
            <p class="text-[11px] text-white/50">дундаж ахиц</p>
          </div>
        </div>
      </div>
    </aside>

    {{-- RIGHT: form --}}
    <div class="p-8 lg:p-10">
      <div class="mb-8 text-center">
        <div class="flex items-center justify-center mx-auto mb-4 text-lg font-extrabold shadow-lg w-12 h-12 rounded-2xl bg-gold text-navy">M</div>
        <p class="text-xs font-extrabold tracking-widest uppercase text-brand">Нэвтрэх</p>
        <h2 class="mt-1 text-2xl font-extrabold text-navy">Дансаа ашиглан орно</h2>
      </div>

      @if($errors->any())
        <div class="px-4 py-3 mb-5 text-sm text-red-700 border bg-red-50 border-red-100 rounded-2xl">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">И-мэйл</label>
          <input name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="example@mail.com" class="auth-input">
        </div>
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">Нууц үг</label>
          <input name="password" type="password" required placeholder="••••••••" class="auth-input">
        </div>
        <label class="inline-flex items-center gap-2 text-sm font-semibold cursor-pointer text-slate-500">
          <input type="checkbox" name="remember" class="w-4 h-4 accent-brand"> Намайг сана
        </label>
        <button type="submit" class="w-full py-4 font-extrabold transition-all home-btn-primary btn-bounce rounded-2xl bg-gold text-navy">
          Нэвтрэх →
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-slate-500">
        Шинэ хэрэглэгч үү?
        <a href="{{ route('register') }}" class="font-extrabold text-brand hover:underline">Бүртгүүлэх</a>
      </p>
    </div>
  </div>
</section>
@endsection
