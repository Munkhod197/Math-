@extends('admin.layout')

@section('title', 'Асуулт засах')

@section('admin_content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">Асуулт засах</h1>
        <p class="mt-2 text-white/50 text-sm">Асуултын мэдээллийг шинэчилнэ үү</p>
    </div>

    {{-- Form Card --}}
    <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data"
          class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 sm:p-8 space-y-6 shadow-xl shadow-black/20">
        @csrf
        @method('PUT')

        {{-- Сэдэв --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Сэдэв <span class="text-rose-400">*</span>
            </label>
            <div class="relative">
                <select name="topic_id" required
                        class="w-full appearance-none p-3.5 pr-10 rounded-xl bg-slate-950/70 border border-white/10
                               text-white placeholder-white/30
                               focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                               transition-all duration-200">
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ $topic->id == old('topic_id', $question->topic_id) ? 'selected' : '' }}>
                            {{ $topic->name }} ({{ $topic->grade_level }}-р анги)
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-white/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Асуулт --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Асуултын текст <span class="text-rose-400">*</span>
            </label>
            <textarea name="text" rows="3" required
                      placeholder="Асуултаа энд бичнэ үү..."
                      class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                             text-white placeholder-white/30 resize-y
                             focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                             transition-all duration-200">{{ old('text', $question->text) }}</textarea>
        </div>

        <div class="space-y-3 rounded-xl bg-slate-950/45 border border-white/5 p-4">
            <label class="block text-sm font-medium text-white/70">Зурагтай бодлого</label>
            @if($question->image_path)
                <img src="{{ asset('storage/' . $question->image_path) }}"
                     alt="Асуултын зураг"
                     class="max-h-56 rounded-xl border border-white/10 bg-white object-contain">
                <label class="inline-flex items-center gap-2 text-sm text-white/60">
                    <input type="checkbox" name="remove_image" value="1" class="rounded border-white/20 bg-slate-900">
                    Одоогийн зургийг устгах
                </label>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="block w-full text-sm text-white/60 file:mr-4 file:rounded-lg file:border-0 file:bg-gold file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-amber-400">
            <p class="text-xs text-white/35">Шинэ зураг сонговол өмнөх зургийг солино.</p>
        </div>

        {{-- Хариултууд --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-white/70">
                Сонголтууд <span class="text-rose-400">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $name => $label)
                    <div class="relative group">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center
                                     rounded-lg bg-gold/10 text-gold text-xs font-bold border border-gold/20
                                     group-focus-within:bg-gold/20 transition-colors">
                            {{ $label }}
                        </span>
                        <input name="{{ $name }}" required
                               value="{{ old($name, $question->{$name}) }}"
                               placeholder="Хариулт {{ $label }}"
                               class="w-full pl-12 pr-3.5 py-3.5 rounded-xl bg-slate-950/70 border border-white/10
                                      text-white placeholder-white/30
                                      focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                                      transition-all duration-200" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Зөв хариулт --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Зөв хариулт <span class="text-rose-400">*</span>
            </label>
            <div class="flex flex-wrap gap-3">
                @foreach(['A', 'B', 'C', 'D'] as $opt)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="answer" value="{{ $opt }}" required class="peer sr-only"
                               {{ old('answer', $question->answer) == $opt ? 'checked' : '' }}>
                        <span class="flex items-center justify-center w-12 h-12 rounded-xl
                                     bg-slate-950/70 border border-white/10 text-white/70 font-semibold
                                     peer-checked:bg-gold peer-checked:text-slate-900 peer-checked:border-gold
                                     peer-checked:shadow-lg peer-checked:shadow-gold/20
                                     hover:border-white/20 transition-all duration-200">
                            {{ $opt }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Тайлбар --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-white/70">
                Тайлбар <span class="text-white/40 font-normal">(заавал биш)</span>
            </label>
            <textarea name="explanation" rows="2"
                      placeholder="Зөв хариултын тайлбар..."
                      class="w-full p-3.5 rounded-xl bg-slate-950/70 border border-white/10
                             text-white placeholder-white/30 resize-y
                             focus:outline-none focus:ring-2 focus:ring-gold/50 focus:border-gold/40
                             transition-all duration-200">{{ old('explanation', $question->explanation) }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
            <a href="{{ route('admin.questions.index') }}"
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
@endsection
