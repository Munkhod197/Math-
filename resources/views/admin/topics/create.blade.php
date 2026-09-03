@extends('admin.layout')

@section('title', 'Сэдэв нэмэх')

@section('admin_content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">Сэдэв нэмэх</h1>
        <p class="mt-2 text-sm text-white/50">Шинэ сэдэв үүсгэж, видео холбоно уу</p>
    </div>

    {{-- Form Card --}}
    <form action="{{ route('admin.topics.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl shadow-black/20">
        @csrf

        {{-- Нэр --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Нэр <span class="text-rose-400">*</span>
            </label>
            <input name="name" required
                   placeholder="Жишээ: Тоо тоолох, унших, бичих"
                   class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                          text-white placeholder-white/30
                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                          transition-all duration-200" />
        </div>

        {{-- Slug + Анги --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2 space-y-2">
                <label class="block text-sm font-medium text-white/70">
                    Slug <span class="text-rose-400">*</span>
                </label>
                <input name="slug" required
                       placeholder="too-toolol-1"
                       class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                              text-white placeholder-white/30 font-mono text-sm
                              focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                              transition-all duration-200" />
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white/70">
                    Анги <span class="text-rose-400">*</span>
                </label>
                <input name="grade_level" type="number" min="1" max="12" required
                       placeholder="1–12"
                       class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                              text-white placeholder-white/30 text-center
                              focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                              transition-all duration-200" />
            </div>
        </div>

        {{-- Видео URL --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Видео URL <span class="text-white/40 font-normal">(заавал биш)</span>
            </label>
            <input name="video_url"
                   placeholder="https://www.youtube.com/watch?v=..."
                   class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                          text-white placeholder-white/30
                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                          transition-all duration-200" />
        </div>

        {{-- Видео файл --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-white/70">
                Видео файл <span class="text-white/40 font-normal">(mp4, mov, mkv)</span>
            </label>

            <label for="video_file_input"
                   class="flex flex-col items-center justify-center gap-3 w-full py-8 px-4
                          rounded-xl border-2 border-dashed border-white/10 bg-slate-950/40
                          hover:border-gold/30 hover:bg-slate-950/60 cursor-pointer
                          transition-all duration-200 group">
                <div class="w-12 h-12 rounded-full bg-slate-800/80 flex items-center justify-center
                            group-hover:bg-gold/10 transition-colors">
                    <svg class="w-5 h-5 text-white/40 group-hover:text-gold transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-sm text-white/60 group-hover:text-white/80">
                        Файл сонгох эсвэл энд чирж оруулна уу
                    </p>
                    <p class="mt-1 text-xs text-white/30">MP4, MOV, MKV</p>
                </div>
                <input type="file" name="video_file" accept="video/*" id="video_file_input" class="hidden" />
            </label>

            {{-- Preview --}}
            <div id="video_preview_wrapper" class="hidden">
                <video id="video_preview" controls
                       class="w-full rounded-xl bg-black border border-white/10 shadow-lg">
                    <source id="video_preview_src" src="" />
                    Your browser does not support the video tag.
                </video>
                <p id="video_file_name" class="mt-2 text-xs text-white/40 truncate"></p>
            </div>
        </div>

        {{-- Видео гарчиг --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Видео гарчиг <span class="text-white/40 font-normal">(заавал биш)</span>
            </label>
            <input name="video_title"
                   placeholder="Жишээ: 6-р ангийн тэгшитгэл"
                   class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                          text-white placeholder-white/30
                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                          transition-all duration-200" />
        </div>

        {{-- Premium toggle --}}
        <div class="flex items-center gap-3">
            <button type="button"
                    id="premium_toggle"
                    role="switch"
                    aria-checked="false"
                    style="position:relative; width:44px; height:24px; border-radius:9999px; background:#334155; border:none; cursor:pointer; transition:background 0.2s; flex-shrink:0;">
                <span id="premium_thumb"
                      style="position:absolute; top:2px; left:2px; width:20px; height:20px; border-radius:9999px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.3); transition:transform 0.2s ease; transform:translateX(0);"></span>
            </button>
            <input type="hidden" name="is_premium" id="is_premium_input" value="0">
            <span class="text-sm text-white/70 select-none">Premium сэдэв</span>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.topics.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-medium text-white/60
                      hover:text-white hover:bg-white/5 transition-all duration-200">
                Болих
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                           bg-gold text-slate-900 font-semibold text-sm
                           hover:bg-amber-400 hover:shadow-lg hover:shadow-gold/25
                           active:scale-[0.98] transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
                Хадгалах
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Video preview
document.getElementById('video_file_input').addEventListener('change', function () {
    const file = this.files[0];
    const wrapper = document.getElementById('video_preview_wrapper');
    const preview = document.getElementById('video_preview');
    const src = document.getElementById('video_preview_src');
    const nameEl = document.getElementById('video_file_name');

    if (!file) {
        wrapper.classList.add('hidden');
        src.src = '';
        preview.load();
        nameEl.textContent = '';
        return;
    }

    const url = URL.createObjectURL(file);
    src.src = url;
    preview.load();
    wrapper.classList.remove('hidden');
    nameEl.textContent = file.name + ' • ' + (file.size / 1024 / 1024).toFixed(1) + ' MB';
});

// Premium toggle — inline style ашигласан (Tailwind-ээс хамаарахгүй)
(function () {
    const btn = document.getElementById('premium_toggle');
    const input = document.getElementById('is_premium_input');
    const thumb = document.getElementById('premium_thumb');

    if (!btn || !input || !thumb) return;

    btn.addEventListener('click', function () {
        const isOn = btn.getAttribute('aria-checked') === 'true';
        const next = !isOn;

        btn.setAttribute('aria-checked', next ? 'true' : 'false');
        input.value = next ? '1' : '0';

        if (next) {
            btn.style.background = '#f59e0b'; // gold
            thumb.style.transform = 'translateX(20px)';
        } else {
            btn.style.background = '#334155'; // slate-700
            thumb.style.transform = 'translateX(0)';
        }
    });
})();
</script>
@endpush
@endsection
