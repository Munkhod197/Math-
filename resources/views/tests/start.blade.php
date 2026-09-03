@extends('layouts.app')

@section('title','Тест эхлүүлэх')

@section('content')
<section class="relative overflow-hidden">
  <div class="absolute top-0 rounded-full pointer-events-none -left-24 h-72 w-72 bg-brand/15 blur-3xl opacity-90"></div>
  <div class="absolute right-0 rounded-full pointer-events-none top-20 h-80 w-80 bg-gold/10 blur-3xl opacity-80"></div>

  <div class="relative max-w-5xl px-4 py-12 mx-auto fade">
    <div class="grid gap-8 lg:grid-cols-[.9fr_1.1fr] lg:items-start">
      <aside class="p-8 text-white bg-navy rounded-[2rem] shadow-2xl shadow-navy/20">
        <span class="inline-flex px-4 py-2 mb-5 text-xs font-extrabold tracking-widest uppercase border rounded-full border-white/15 bg-white/10 text-white/70">
          {{ $hasPremium ? 'Premium тест' : 'Free preview' }}
        </span>
        <h1 class="text-4xl font-extrabold leading-tight">Тест эхлүүлэх</h1>
        <p class="mt-4 text-sm leading-7 text-white/65">
          Сэдвүүдээ сонгоод бодлого бод. Дүн дээр чинь зөв, буруу хариулт, сул талтай сэдэв, дараагийн алхам гарна.
        </p>
        <div class="grid gap-3 mt-8">
          <div class="p-4 border rounded-2xl bg-white/10 border-white/10">
            <p class="text-sm font-bold">Тестийн хязгаар</p>
            <p class="mt-1 text-xs text-white/55">{{ $hasPremium ? '10-30 бодлого, бүх сэдэв нээлттэй.' : '5 бодлого, эхний preview сэдвүүд нээлттэй.' }}</p>
          </div>
          @unless($hasPremium)
            <a href="{{ route('billing.index') }}" class="inline-flex justify-center px-5 py-3 mt-2 text-sm font-extrabold rounded-2xl bg-gold text-navy">
              Premium эрх нээх
            </a>
          @endunless
        </div>
      </aside>

      <div class="bg-white border border-slate-100 rounded-[2rem] shadow-xl p-6 sm:p-8">
        <form method="POST" action="{{ route('test.take') }}">
          @csrf

          <div class="mb-6">
            <label class="block mb-2 text-sm font-extrabold text-navy">Таны нэр</label>
            <input type="text" name="student_name" required placeholder="Жишээ: Болд"
                   value="{{ old('student_name', auth()->user()->name ?? '') }}"
                   class="w-full px-4 py-4 font-medium transition-colors border-2 outline-none border-slate-200 focus:border-brand rounded-2xl placeholder:text-slate-400 bg-slate-50">
          </div>

          <div class="mb-7">
            <label class="block mb-3 text-sm font-extrabold text-navy">Бодлогын тоо</label>
            <div class="grid gap-3 sm:grid-cols-3">
              @php
                $questionOptions = $hasPremium
                  ? [[10, '10 бодлого', 'Хурдан'], [20, '20 бодлого', 'Стандарт'], [30, '30 бодлого', 'Бүрэн']]
                  : [[5, '5 бодлого', 'Preview']];
              @endphp
              @foreach($questionOptions as $option)
                @php [$val, $lbl, $sub] = $option; @endphp
                <label class="cursor-pointer">
                  <input type="radio" name="question_count" value="{{ $val }}" class="hidden peer" {{ old('question_count', $questionOptions[0][0]) == $val ? 'checked' : '' }}>
                  <div class="p-4 text-center transition-all border-2 border-slate-200 peer-checked:border-brand peer-checked:bg-brand/5 rounded-3xl hover:border-slate-300">
                    <div class="font-extrabold text-navy">{{ $lbl }}</div>
                    <div class="mt-1 text-xs text-slate-400">{{ $sub }}</div>
                  </div>
                </label>
              @endforeach
            </div>
          </div>

          <div class="mb-8">
            <div class="mb-4">
              <label class="text-sm font-extrabold text-navy">Анги</label>
              <p class="mt-2 text-sm text-slate-500">Сонгосон ангийн хичээлүүдээс тест авна.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
              @foreach($topics->groupBy('grade_level')->keys() as $grade)
                <label class="cursor-pointer">
                  <input type="radio" name="grade_level" value="{{ $grade }}" class="hidden peer" {{ $loop->first ? 'checked' : '' }}>
                  <div class="p-4 text-center transition-all border-2 border-slate-200 peer-checked:border-brand peer-checked:bg-brand/5 rounded-3xl hover:border-slate-300">
                    <div class="font-extrabold text-navy">{{ $grade }}-р анги</div>
                    <div class="mt-1 text-xs text-slate-400">{{ $topics->where('grade_level', $grade)->count() }} хичээл</div>
                  </div>
                </label>
              @endforeach
            </div>
          </div>

          <button type="submit" class="w-full py-4 text-lg font-extrabold text-white transition-all shadow-2xl bg-gradient-to-r from-brand to-cyan-500 rounded-3xl hover:scale-[1.01] shadow-brand/20">
            Тест эхлүүлэх →
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection


