@extends('admin.layout')

@section('title', 'Тест нэмэх')

@section('admin_content')
<div class="max-w-3xl px-4 py-6">
    <h1 class="text-2xl font-bold text-white">Тест нэмэх</h1>

    <form action="{{ route('admin.tests.store') }}" method="POST" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="text-sm text-white/60">Гарчиг</label>
            <input name="title" class="w-full p-3 mt-1 rounded-xl bg-slate-900/50" required />
        </div>
        <div>
            <label class="text-sm text-white/60">Slug</label>
            <input name="slug" class="w-full p-3 mt-1 rounded-xl bg-slate-900/50" required />
        </div>
        <div>
            <label class="text-sm text-white/60">Анги</label>
            <input name="grade_level" type="number" min="1" max="12" class="w-32 p-3 mt-1 rounded-xl bg-slate-900/50" />
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-sm text-white/60">Хугацаа (минут)</label>
                <input name="duration_minutes" type="number" class="w-40 p-3 mt-1 rounded-xl bg-slate-900/50" value="30" />
            </div>
            <div>
                <label class="text-sm text-white/60">Асуултын тоо</label>
                <input name="total_questions" type="number" class="w-40 p-3 mt-1 rounded-xl bg-slate-900/50" value="10" />
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2 text-sm text-white/60">
                <input type="checkbox" name="is_published" value="1" /> <span>Published</span>
            </label>
        </div>

        <div>
            <button class="px-4 py-2 font-semibold rounded-xl bg-gold text-slate-900">Хадгалах</button>
        </div>
    </form>
</div>
@endsection
