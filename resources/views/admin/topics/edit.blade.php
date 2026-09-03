@extends('admin.layout')

@section('title', 'Сэдэв засах')

@section('admin_content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">Сэдэв засах</h1>
        <p class="mt-2 text-sm text-white/50">Сэдвийн мэдээлэл болон видеог шинэчилнэ үү</p>
    </div>

    {{-- Form Card --}}
    <form action="{{ route('admin.topics.update', $topic) }}" method="POST" enctype="multipart/form-data"
          class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl shadow-black/20">
        @csrf
        @method('PUT')

        {{-- Нэр --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Нэр <span class="text-rose-400">*</span>
            </label>
            <input name="name" required
                   value="{{ old('name', $topic->name) }}"
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
                       value="{{ old('slug', $topic->slug) }}"
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
                       value="{{ old('grade_level', $topic->grade_level) }}"
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
                   value="{{ old('video_url', $topic->video_url) }}"
                   placeholder="https://www.youtube.com/watch?v=..."
                   class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                          text-white placeholder-white/30
                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                          transition-all duration-200" />
        </div>

        {{-- Одоогийн видео --}}
        @if($topic->video_file || $topic->video_url)
            <div class="space-y-2">
                <label class="block text-sm font-medium text-white/70">Одоогийн видео</label>
                <div class="rounded-xl overflow-hidden border border-white/10 bg-black">
                    @if($topic->video_file)
                        <video controls class="w-full">
                            <source src="{{ asset('storage/' . $topic->video_file) }}" />
                            Your browser does not support the video tag.
                        </video>
                    @elseif($topic->video_url)
                        @php
                            $youtubeId = null;
                            if (preg_match('/(?:youtube\.com.*(?:v=|\/embed\/)|youtu\.be\/)([A-Za-z0-9_-]+)/', $topic->video_url, $m)) {
                                $youtubeId = $m[1];
                            }
                        @endphp
                        @if($youtubeId)
                            <iframe class="w-full aspect-video"
                                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                    frameborder="0" allowfullscreen></iframe>
                        @else
                            <div class="p-4">
                                <a href="{{ $topic->video_url }}" target="_blank"
                                   class="text-sm text-gold hover:underline">
                                    Видео холбоос →
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        {{-- Шинэ видео файл --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-white/70">
                Шинэ видео файл <span class="text-white/40 font-normal">(mp4, mov, mkv)</span>
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
                        Шинэ файл сонгох эсвэл чирж оруулна уу
                    </p>
                    <p class="mt-1 text-xs text-white/30">MP4, MOV, MKV • Хуучин видеог солино</p>
                </div>
                <input type="file" name="video_file" accept="video/*" id="video_file_input" class="hidden" />
            </label>

            {{-- New file preview --}}
            <div id="video_preview_wrapper" class="hidden">
                <video id="video_preview" controls
                       class="w-full rounded-xl bg-black border border-white/10 shadow-lg">
                    <source id="video_preview_src" src="" />
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
                   value="{{ old('video_title', $topic->video_title) }}"
                   placeholder="Жишээ: 6-р ангийн тэгшитгэл"
                   class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                          text-white placeholder-white/30
                          focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                          transition-all duration-200" />
        </div>

        {{-- Premium --}}
        <div>
            <label class="inline-flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" name="is_premium" value="1" class="peer sr-only"
                           {{ old('is_premium', $topic->is_premium) ? 'checked' : '' }} />
                    <div class="w-10 h-6 rounded-full bg-slate-700 peer-checked:bg-gold
                                transition-colors duration-200"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white
                                shadow peer-checked:translate-x-4 transition-transform duration-200"></div>
                </div>
                <span class="text-sm text-white/70 group-hover:text-white transition-colors">
                    Premium сэдэв
                </span>
            </label>
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
</script>
@endpush
@endsection
