@extends('layouts.app')

@section('title', $topic->name.' - Шалгалт')

@section('content')
<section class="max-w-2xl px-4 mx-auto py-14 fade">
    <a href="{{ route('topics.show', $topic->slug) }}" class="inline-flex items-center gap-2 mb-8 text-sm font-extrabold transition text-brand hover:underline">
        ← {{ $topic->name }} хичээл рүү буцах
    </a>

    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-5 text-4xl shadow-lg bg-brand/10 rounded-3xl">{{ $topic->icon }}</div>
        <h1 class="text-4xl font-extrabold leading-tight text-navy">Сэдвийн шалгалт</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $topic->name }} · {{ $topic->grade_level }}-р анги</p>
    </div>

    <div class="p-8 bg-white border shadow-xl border-slate-100 rounded-3xl">
        <form method="POST" action="{{ route('test.topic.take', $topic->slug) }}">
            @csrf
            <div class="mb-6">
                <label class="block mb-2 text-sm font-extrabold text-navy">Таны нэр</label>
                <input type="text" name="student_name" required value="{{ old('student_name', auth()->user()->name ?? '') }}" placeholder="Жишээ: Болд"
                       class="w-full px-4 py-3 font-medium transition border-2 outline-none border-slate-200 focus:border-brand focus:ring-2 focus:ring-brand/20 rounded-2xl placeholder:text-slate-400">
            </div>

            <div class="relative p-5 overflow-hidden border mb-7 bg-gradient-to-br from-slate-50 to-white border-slate-200 rounded-2xl">
                <div class="relative">
                    <div class="flex items-center gap-2 mb-3 font-extrabold text-navy">Шалгалтын мэдээлэл</div>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li>• Зөвхөн <strong class="text-navy">{{ $topic->name }}</strong> сэдвийн <strong>10 бодлого</strong></li>
                        <li>• Тооцоолол шаардсан хэцүү бодлого (дасгал биш)</li>
                        <li>• Дүн гарсны дараа тайлбар, сул тал харагдана</li>
                    </ul>
                </div>
            </div>

            <button type="submit" class="w-full py-4 text-lg font-extrabold text-white transition-all shadow-xl bg-gradient-to-r from-navy to-blue-800 hover:from-blue-900 hover:to-blue-950 rounded-2xl hover:scale-[1.02] active:scale-100">
                Шалгалт эхлүүлэх →
            </button>
        </form>
    </div>
</section>
@endsection
