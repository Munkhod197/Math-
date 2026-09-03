@extends('layouts.app')

@section('title','Бүртгүүлэх')

@section('content')
<section class="max-w-5xl px-4 py-12 mx-auto">
  <div class="grid overflow-hidden bg-white border shadow-2xl login-card border-slate-100 rounded-[2rem] lg:grid-cols-[.9fr_1fr]">

    {{-- LEFT: brand / signature panel --}}
    <aside class="relative p-8 overflow-hidden text-white login-aside lg:p-10">

      {{-- graph-paper texture --}}
      <div class="absolute inset-0 opacity-[0.07]" style="background-image:
          linear-gradient(rgba(255,255,255,.9) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,.9) 1px, transparent 1px);
          background-size: 28px 28px;"></div>

      {{-- ascending step / number-line motif: fits "starting a new journey" better than the login's protractor --}}
      <svg class="absolute -right-10 -bottom-10 w-72 h-72 opacity-[0.10]" viewBox="0 0 200 200" fill="none">
        <path d="M20 170 L20 130 L70 130 L70 90 L120 90 L120 50 L170 50 L170 20"
              stroke="white" stroke-width="2" fill="none"/>
        <circle cx="170" cy="20" r="5" fill="white"/>
        <circle cx="20" cy="170" r="5" fill="white"/>
        <path d="M20 170 L170 20" stroke="white" stroke-width="1" stroke-dasharray="4 4" opacity="0.6"/>
      </svg>

      {{-- floating math glyphs --}}
      <span class="absolute font-serif text-4xl select-none top-10 right-10 text-white/10 rotate-6">π</span>
      <span class="absolute text-3xl font-serif select-none top-32 right-24 text-white/10 -rotate-12">√x</span>
      <span class="absolute text-5xl font-serif select-none bottom-24 left-6 text-white/10 rotate-3">∫</span>
      <span class="absolute text-3xl font-serif select-none bottom-10 right-16 text-white/10 -rotate-6">Σ</span>

      <div class="relative">
        <span class="inline-flex px-4 py-2 mb-6 text-xs font-extrabold tracking-widest uppercase border rounded-full border-white/15 bg-white/10 text-white/65">MathMon</span>
        <h1 class="text-4xl font-extrabold leading-tight">Шинэ дансаа үүсгээд эхэл</h1>
        <p class="mt-4 text-sm leading-7 text-white/60">Бүртгүүлснээр тест өгч, дүнгээ хадгалж, сэдвүүдээ дарааллаар нь давтах боломжтой.</p>

        <div class="grid gap-3 mt-8">
          @foreach(['Хувийн сургалтын орчин','Тестийн дүн ба зөвлөмж','Видео хичээл, дасгал, шалгалт'] as $feature)
            <div class="flex items-center gap-3 text-sm text-white/70">
              <span class="w-2 h-2 rounded-full bg-gold"></span>{{ $feature }}
            </div>
          @endforeach
        </div>

        {{-- "start your journey" badge, ties to the ascending step motif above --}}
        <div class="flex items-center gap-3 p-3 mt-10 border rounded-2xl border-white/10 bg-white/5 w-fit">
          <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gold/15">
            <span class="text-lg font-extrabold text-gold">0%</span>
          </div>
          <div class="leading-tight">
            <p class="text-sm font-extrabold text-white">Эхлэл</p>
            <p class="text-[11px] text-white/50">эхний тестээ өгч дүнгээ хар</p>
          </div>
        </div>
      </div>
    </aside>

    {{-- RIGHT: form --}}
    <div class="p-8 lg:p-10">
      <div class="mb-8 text-center">
        <div class="flex items-center justify-center mx-auto mb-4 text-lg font-extrabold shadow-lg w-12 h-12 rounded-2xl bg-gold text-navy">M</div>
        <p class="text-xs font-extrabold tracking-widest uppercase text-brand">Шинэ данс</p>
        <h2 class="mt-1 text-2xl font-extrabold text-navy">Мэдээллээ оруулаарай</h2>
      </div>

      @if($errors->any())
        <div class="px-4 py-3 mb-5 text-sm text-red-700 border bg-red-50 border-red-100 rounded-2xl">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">Нэр</label>
          <input name="name" type="text" value="{{ old('name') }}" required autofocus placeholder="Овог нэр" class="auth-input">
        </div>
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">И-мэйл</label>
          <input name="email" type="email" value="{{ old('email') }}" required placeholder="example@mail.com" class="auth-input">
        </div>
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">Нууц үг</label>
          <input name="password" type="password" required placeholder="••••••••" class="auth-input">
        </div>
        <div>
          <label class="block mb-2 text-sm font-extrabold text-navy">Нууц үг давтах</label>
          <input name="password_confirmation" type="password" required placeholder="••••••••" class="auth-input">
        </div>
        <button type="submit" class="w-full py-4 font-extrabold transition-all home-btn-primary btn-bounce rounded-2xl bg-gold text-navy">
          Бүртгүүлэх →
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-slate-500">
        Данс байгаа юу?
        <a href="{{ route('login') }}" class="font-extrabold text-brand hover:underline">Нэвтрэх</a>
      </p>
    </div>
  </div>
</section>
@endsection
