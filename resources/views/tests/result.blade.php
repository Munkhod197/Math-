@extends('layouts.app')

@section('title','Тестийн дүн')

@section('content')
<section class="max-w-4xl px-4 py-10 mx-auto fade">
  <div class="relative p-8 mb-8 overflow-hidden text-center text-white score-hero bg-navy rounded-3xl">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(circle at 80% 10%,rgba(245,158,11,.25),transparent 60%)"></div>
    <div class="relative">
      <div class="score-number text-[5.5rem] font-extrabold leading-none {{ $result->score_percent>=75?'text-gold':($result->score_percent>=60?'text-white':'text-red-400') }}">
        {{ $result->score_percent }}%
      </div>
      <p class="mt-1 text-white/65">{{ $result->student_name }}-ийн тестийн дүн</p>
      <div class="inline-flex px-5 py-2 mt-3 text-base font-bold border rounded-full bg-white/15 border-white/25">{{ $result->grade }}</div>
      <div class="flex justify-center gap-10 mt-6">
        <div><div class="text-3xl font-extrabold text-emerald-400">{{ $result->correct_answers }}</div><div class="text-xs text-white/50">Зөв</div></div>
        <div><div class="text-3xl font-extrabold text-red-400">{{ $result->total_questions-$result->correct_answers }}</div><div class="text-xs text-white/50">Алдаа</div></div>
        <div><div class="text-3xl font-extrabold">{{ $result->total_questions }}</div><div class="text-xs text-white/50">Нийт</div></div>
      </div>
    </div>
  </div>

  <div class="p-6 mb-8 bg-white border shadow-sm border-slate-200 rounded-3xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-xl font-extrabold text-navy">Сургалтын анализ</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Дүн дээр үндэслээд сул талтай сэдэв, давтах дарааллыг доор санал болголоо.</p>
      </div>
      <div class="inline-flex items-center gap-3 px-4 py-2 text-sm font-semibold rounded-full bg-slate-100 text-slate-700">
        <span class="text-emerald-600">{{ $result->score_percent }}%</span> мэдлэгийн үнэлгээ
      </div>
    </div>

    <div class="grid gap-4 mt-6 sm:grid-cols-3">
      <div class="p-4 border rounded-3xl bg-brand/5 border-brand/10">
        <div class="mb-2 font-bold text-slate-900">Зөв хариулт</div>
        <div class="text-sm text-slate-500">{{ $result->correct_answers }} бодлого</div>
      </div>
      <div class="p-4 border border-amber-100 rounded-3xl bg-amber-50">
        <div class="mb-2 font-bold text-slate-900">Сул тал</div>
        <div class="text-sm text-slate-500">{{ max(0, $result->total_questions - $result->correct_answers) }} алдаатай бодлого</div>
      </div>
      <div class="p-4 border rounded-3xl bg-slate-50 border-slate-200">
        <div class="mb-2 font-bold text-slate-900">Дараагийн алхам</div>
        <div class="text-sm text-slate-500">Алдсан сэдвүүдээ давтаад дахин тест өгнө.</div>
      </div>
    </div>
  </div>

  <h2 class="mb-2 text-xl font-extrabold text-navy">Сул талтай сэдвүүд</h2>
  <p class="mb-6 text-sm leading-6 text-slate-500">Алдаа гарсан сэдвүүд дээр дарж хичээл, дасгалаа давтаарай.</p>

  @if($weakTopics->isEmpty())
    <div class="p-6 mb-8 font-bold text-center border-2 bg-emerald-50 border-emerald-200 rounded-3xl text-emerald-800">
      Баяр хүргэе. Бүх бодлогыг зөв бодлоо.
    </div>
  @else
    <div class="grid gap-4 mb-8 sm:grid-cols-2">
      @foreach($weakTopics as $topic)
        <article class="flex flex-col overflow-hidden transition bg-white border shadow-sm group rounded-3xl border-slate-200 hover:-translate-y-1 hover:shadow-xl">
          <div class="p-5 text-white bg-gradient-to-r from-navy via-slate-900 to-brand">
            <div class="flex items-center justify-between gap-3">
              <span class="text-4xl">{{ $topic->icon }}</span>
              <span class="text-xs uppercase tracking-[0.22em] bg-white/10 px-3 py-1 rounded-full">{{ $topic->grade_level }}-р анги</span>
            </div>
            <h3 class="mt-5 text-xl font-extrabold">{{ $topic->name }}</h3>
          </div>
          <div class="flex flex-col flex-1 gap-4 p-5">
            <p class="text-sm leading-6 text-slate-600">{{ $topic->description }}</p>
            <a href="{{ route('topics.show', $topic->slug) }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 mt-auto font-bold text-white transition rounded-2xl bg-brand hover:bg-blue-600">
              Хичээл рүү очих
            </a>
          </div>
        </article>
      @endforeach
    </div>
  @endif

  <div class="p-6 mb-8 border bg-slate-50 border-slate-200 rounded-3xl">
    <h2 class="text-lg font-extrabold text-navy">Давтах төлөвлөгөө</h2>
    <div class="grid gap-4 mt-5 sm:grid-cols-3">
      @foreach([
        ['1. Хичээл үзэх', 'Сул талтай сэдвийн тайлбар, жишээг дахин хар.'],
        ['2. Дасгал бодох', 'Lesson page дээрх богино дасгалуудаар ойлголтоо шалга.'],
        ['3. Дахин тест өгөх', 'Алдаагаа зассан эсэхээ шинэ тестээр баталгаажуул.'],
      ] as [$title,$desc])
        <div class="p-4 bg-white border rounded-3xl border-slate-200">
          <div class="mb-2 font-bold text-slate-900">{{ $title }}</div>
          <p class="text-sm leading-6 text-slate-500">{{ $desc }}</p>
        </div>
      @endforeach
    </div>
  </div>

  <details class="mb-8 overflow-hidden bg-white border shadow-sm border-slate-100 rounded-3xl">
    <summary class="px-6 py-4 font-bold transition-colors cursor-pointer text-navy hover:bg-slate-50 select-none">
      Дэлгэрэнгүй хариулт харах ({{ count($answersDetail) }} асуулт)
    </summary>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-white bg-navy">
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Сэдэв</th>
            <th class="px-4 py-3">Асуулт</th>
            <th class="px-4 py-3 text-center">Таны</th>
            <th class="px-4 py-3 text-center">Зөв</th>
            <th class="px-4 py-3 text-center">Үр дүн</th>
          </tr>
        </thead>
        <tbody>
          @foreach($answersDetail as $i => $a)
            <tr class="{{ $i%2===0?'bg-white':'bg-slate-50' }} border-b border-slate-100">
              <td class="px-4 py-3 font-bold text-slate-400">{{ $i+1 }}</td>
              <td class="px-4 py-3 font-semibold whitespace-nowrap text-slate-600">{{ $a['topic'] }}</td>
              <td class="max-w-xs px-4 py-3 text-slate-700">
                {{ $a['question_text'] }}
                @if(!empty($a['image_path']))
                  <img src="{{ asset('storage/' . $a['image_path']) }}"
                       alt="Бодлогын зураг"
                       class="max-h-40 mt-2 rounded-xl border border-slate-200 bg-slate-50 object-contain">
                @endif
                @if(!$a['is_correct'] && $a['explanation'])
                  <div class="mt-1 text-xs italic text-brand">Тайлбар: {{ $a['explanation'] }}</div>
                @endif
              </td>
              <td class="px-4 py-3 text-center font-extrabold {{ $a['is_correct']?'text-emerald-600':'text-ruby' }}">
                @if($a['user_answer'])
                  {{ $a['user_answer'] }}) {{ $a['user_answer_text'] }}
                @else
                  -
                @endif
              </td>
              <td class="px-4 py-3 font-extrabold text-center text-emerald-600">
                {{ $a['correct_answer'] }}) {{ $a['correct_answer_text'] }}
              </td>
              <td class="px-4 py-3 text-center">
                @if($a['is_correct'])
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">Зөв</span>
                @else
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-ruby">Алдаа</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </details>

  <div class="flex flex-wrap justify-center gap-3">
    @if(isset($topic))
      <a href="{{ route('test.topic.start', $topic->slug) }}" class="px-6 py-3 font-bold transition-colors bg-gold hover:bg-amber-500 text-navy rounded-2xl">Дахин шалгалт өгөх</a>
      <a href="{{ route('topics.show', $topic->slug) }}" class="px-6 py-3 font-bold text-white transition-colors bg-brand hover:bg-blue-700 rounded-2xl">Хичээл рүү буцах</a>
    @else
      <a href="{{ route('test.start') }}" class="px-6 py-3 font-bold text-white transition-colors bg-navy hover:bg-blue-950 rounded-2xl">Дахин тест өгөх</a>
      <a href="{{ route('topics.index') }}" class="px-6 py-3 font-bold text-white transition-colors bg-brand hover:bg-blue-700 rounded-2xl">Бүх хичээл</a>
    @endif
    <a href="{{ route('home') }}" class="px-6 py-3 font-bold transition-colors border-2 border-slate-200 hover:border-slate-300 text-slate-600 rounded-2xl">Нүүр</a>
  </div>
</section>
@endsection
