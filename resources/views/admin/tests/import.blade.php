@extends('admin.layout')

@section('title', 'PDF асуулт импорт')

@section('admin_content')
<div class="max-w-5xl mx-auto">
    <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-6 items-start">
        <form action="{{ route('admin.tests.import.upload') }}" method="POST" enctype="multipart/form-data"
              class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl shadow-black/20">
            @csrf

            <div class="mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold/10 text-gold border border-gold/20 text-xs font-semibold">
                    Admin import
                </div>
                <h1 class="mt-4 text-3xl font-bold text-white tracking-tight">PDF-ээс асуулт оруулах</h1>
                <p class="mt-2 text-sm text-white/50 leading-6">
                    PDF файлаа оруулаад A, B, C, D сонголтуудыг preview дээр засаж баталгаажуулна.
                    Хадгалах товч дарах хүртэл мэдээлэл database-д нэмэгдэхгүй.
                </p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-rose-500/25 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <label for="pdf" class="group block rounded-2xl border-2 border-dashed border-white/10 bg-slate-950/45 p-8 text-center hover:border-gold/40 hover:bg-gold/5 transition-all cursor-pointer">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 13h6m-6 4h6M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 2v6h6"/>
                    </svg>
                </div>
                <p class="mt-4 text-base font-semibold text-white">PDF файл сонгох</p>
                <p id="file-name" class="mt-1 text-sm text-white/40">10MB хүртэл хэмжээтэй .pdf файл</p>
                <input id="pdf" name="pdf" type="file" accept="application/pdf,.pdf" required class="sr-only">
            </label>

            <div class="mt-6 space-y-2">
                <label class="block text-sm font-medium text-white/70">Default сэдэв</label>
                <select name="topic_id"
                        class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10 text-white focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40">
                    <option value="">Preview дээр тус бүр сонгоно</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" @selected(old('topic_id') == $topic->id)>
                            {{ $topic->name }} ({{ $topic->grade_level }}-р анги)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-8 flex items-center justify-between gap-3 pt-5 border-t border-white/5">
                <a href="{{ route('admin.tests.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 transition-all">
                    Буцах
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gold text-slate-900 font-semibold text-sm hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/25 active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Preview үүсгэх
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="bg-slate-900/50 border border-white/5 rounded-2xl p-5">
                <h2 class="text-sm font-semibold text-white">PDF format</h2>
                <div class="mt-4 rounded-xl bg-slate-950/60 border border-white/5 p-4 font-mono text-xs text-white/55 leading-6">
                    1. 2x + 3 = 9 бол x хэд вэ?<br>
                    A. 2<br>
                    B. 3<br>
                    C. 4<br>
                    D. 6<br>
                    Тайлбар: 2x = 6 тул x = 3
                </div>
            </div>
            <div class="bg-slate-900/50 border border-white/5 rounded-2xl p-5">
                <h2 class="text-sm font-semibold text-white">Import workflow</h2>
                <div class="mt-4 grid gap-3 text-sm text-white/55">
                    <div class="flex gap-3"><span class="text-gold font-bold">1</span><span>PDF-ээ upload хийнэ.</span></div>
                    <div class="flex gap-3"><span class="text-gold font-bold">2</span><span>Preview дээр сэдэв, текст, сонголт, зөв хариуг засна.</span></div>
                    <div class="flex gap-3"><span class="text-gold font-bold">3</span><span>Бүгд ready болсон үед database-д batch import хийнэ.</span></div>
                </div>
            </div>
        </aside>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('pdf')?.addEventListener('change', function () {
    const name = this.files && this.files[0] ? this.files[0].name : '10MB хүртэл хэмжээтэй .pdf файл';
    document.getElementById('file-name').textContent = name;
});
</script>
@endpush
@endsection
