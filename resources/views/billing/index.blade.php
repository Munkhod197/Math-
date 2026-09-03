@extends('layouts.app')

@section('title', 'Төлбөр')

@section('content')
@php
    $plans = [
        'monthly' => [
            'name' => 'Сарын эрх',
            'price' => 24900,
            'period' => '1 сар',
            'saving' => null,
            'featured' => false,
            'note' => 'Шинээр туршиж эхлэхэд тохиромжтой.',
        ],
        'quarterly' => [
            'name' => 'Улирлын эрх',
            'price' => 69900,
            'period' => '3 сар',
            'saving' => '5,000₮ хэмнэнэ',
            'featured' => false,
            'note' => 'Тогтмол давтлагад хамгийн эвтэйхэн.',
        ],
        'yearly' => [
            'name' => 'Жилийн эрх',
            'price' => 239900,
            'period' => '12 сар',
            'saving' => '58,900₮ хэмнэнэ',
            'featured' => true,
            'note' => 'Бүтэн жилийн тасралтгүй бэлтгэл.',
        ],
    ];

    $currentPlan = $user->billing_plan;
    $isActive = $user->billing_status === 'active';
    $currentPlanName = $currentPlan && isset($plans[$currentPlan]) ? $plans[$currentPlan]['name'] : 'Үнэгүй';
    $endsAt = $user->billing_ends_at;
@endphp

<section class="billing-page pb-16">
    <div class="max-w-6xl px-4 mx-auto">

        {{-- Hero --}}
        <div class="relative overflow-hidden text-white billing-hero rounded-[2rem] shadow-2xl shadow-navy/25">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_16%_18%,rgba(245,158,11,.22),transparent_24%),radial-gradient(circle_at_88%_25%,rgba(37,99,235,.28),transparent_28%)]"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#05070D]/40 via-transparent to-[#0A1628]/60"></div>

            <div class="relative grid gap-8 p-6 sm:p-8 lg:grid-cols-[1.35fr_.65fr] lg:p-10">
                <div>
                    <span class="inline-flex items-center gap-2 px-4 py-2 text-xs font-extrabold tracking-widest uppercase border rounded-full border-white/15 bg-white/10 text-white/80 backdrop-blur-sm">
                        💳 MathMon Premium
                    </span>

                    <h1 class="max-w-2xl mt-5 text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl">
                        Сурах эрхээ удирдах төв
                    </h1>

                    <p class="max-w-xl mt-4 text-sm leading-7 sm:text-base text-white/65">
                        Видео хичээл, тайлбартай тест, ахицын дүн шинжилгээг нэг эрхээр нээгээд математикаа тогтвортой сайжруулаарай.
                    </p>

                    <div class="grid gap-3 mt-8 sm:grid-cols-3">
                        <div class="p-5 billing-card-soft rounded-2xl backdrop-blur-sm">
                            <p class="text-[10px] font-extrabold tracking-widest uppercase text-white/40">Одоогийн эрх</p>
                            <p class="mt-2 text-xl font-extrabold tracking-tight">{{ $currentPlanName }}</p>
                        </div>
                        <div class="p-5 billing-card-soft rounded-2xl backdrop-blur-sm">
                            <p class="text-[10px] font-extrabold tracking-widest uppercase text-white/40">Төлөв</p>
                            <p class="mt-2 text-xl font-extrabold tracking-tight {{ $isActive ? 'text-emerald-300' : 'text-white/70' }}">
                                {{ $isActive ? 'Идэвхтэй' : 'Идэвхгүй' }}
                            </p>
                        </div>
                        <div class="p-5 billing-card-soft rounded-2xl backdrop-blur-sm">
                            <p class="text-[10px] font-extrabold tracking-widest uppercase text-white/40">Данс</p>
                            <p class="mt-2 text-xl font-extrabold tracking-tight">{{ $invoices->count() }} ширхэг</p>
                        </div>
                    </div>
                </div>

                <aside class="p-6 billing-card-soft rounded-3xl backdrop-blur-sm border border-white/10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-extrabold tracking-widest uppercase text-white/40">Дуусах огноо</p>
                            <p class="mt-2 text-2xl font-extrabold tracking-tight">
                                {{ $endsAt ? $endsAt->format('Y.m.d') : 'Тохируулаагүй' }}
                            </p>
                        </div>
                        <span class="px-3 py-1.5 text-[10px] font-extrabold uppercase rounded-full tracking-wider {{ $isActive ? 'billing-status-badge' : 'bg-white/10 text-white/55 border border-white/10' }}">
                            {{ $user->billing_status ?? 'free' }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="p-4 billing-pill rounded-2xl border border-white/10">
                            <p class="font-bold text-sm">Premium-д багтах зүйлс</p>
                            <p class="mt-1.5 text-sm leading-6 text-white/60">
                                Бүх сэдвийн видео, тестийн тайлбар, давтлагын зөвлөмж, ахицын мэдээлэл.
                            </p>
                        </div>

                        @if($isActive)
                            <form method="POST" action="{{ route('billing.cancel') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-3.5 text-sm font-extrabold text-white transition border rounded-2xl border-white/15 bg-white/10 hover:bg-white/15 active:scale-[0.98]">
                                    Эрх цуцлах
                                </button>
                            </form>
                        @else
                            <a href="#plans"
                                class="inline-flex justify-center w-full px-4 py-3.5 text-sm font-extrabold rounded-2xl bg-gold text-navy hover:brightness-105 transition active:scale-[0.98]">
                                Эрх сонгох
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        {{-- Game Zone --}}
        <div class="grid gap-5 mt-8 lg:grid-cols-[1.15fr_.85fr]">
            <section class="relative overflow-hidden rounded-[1.75rem] p-7 text-white"
                style="background:linear-gradient(135deg,#05070D 0%,#0B1220 50%,#101A31 100%); border:1px solid rgba(45,224,255,.18);">
                <div class="absolute -top-24 -right-20 w-72 h-72 rounded-full blur-3xl pointer-events-none"
                    style="background:rgba(45,224,255,.16);"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full blur-3xl pointer-events-none"
                    style="background:rgba(245,158,11,.08);"></div>

                <div class="relative">
                    <span class="inline-flex px-4 py-2 mb-4 text-xs font-extrabold tracking-widest uppercase rounded-full"
                        style="background:rgba(45,224,255,.12); color:#2DE0FF; border:1px solid rgba(45,224,255,.35);">
                        Game Zone
                    </span>
                    <h2 class="text-2xl font-extrabold tracking-tight">Өдөр тутмын сорил</h2>
                    <p class="max-w-xl mt-2 text-sm leading-7 text-white/60">
                        Premium эрхтэй үед бүх хичээлээс гадна оноотой mini game-үүдээр өдөр бүр богино давтлага хийж болно.
                    </p>
                    <div class="flex flex-col gap-3 mt-6 sm:flex-row">
                        <a href="{{ route('toys.index') }}"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm font-extrabold rounded-2xl bg-gold text-navy hover:brightness-105 transition active:scale-[0.98]">
                            Тоглоом нээх
                        </a>
                        <a href="{{ route('toys.speedrun') }}"
                            class="inline-flex items-center justify-center px-5 py-3 text-sm font-extrabold text-white border rounded-2xl border-white/15 bg-white/10 hover:bg-white/15 transition active:scale-[0.98]">
                            Speedrun турших
                        </a>
                    </div>
                </div>
            </section>

            <aside class="p-7 bg-white border border-slate-100 shadow-lg rounded-[1.75rem] shadow-navy/5 flex flex-col justify-center">
                <p class="text-[10px] font-extrabold tracking-widest uppercase text-brand">Давтлага</p>
                <h3 class="mt-2 text-xl font-extrabold text-navy tracking-tight">Төлбөр + тоглоом</h3>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Тоглоомын оноо browser дээр хадгалагдаж, сурагч өдөр бүр өөрийн рекордыг эвдэх зорилготой болно.
                </p>
            </aside>
        </div>

        {{-- Plans --}}
        <div id="plans" class="mt-12">
            <div class="mb-6 text-center sm:text-left">
                <p class="text-[10px] font-extrabold tracking-widest uppercase text-brand">Багцууд</p>
                <h2 class="mt-1 text-2xl font-extrabold text-navy tracking-tight">Танд тохирох эрхийг сонгоорой</h2>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                @foreach($plans as $key => $plan)
                    @php $selected = $currentPlan === $key && $isActive; @endphp
                    <article
                        class="relative overflow-hidden billing-plan-card group transition-all duration-300 hover:-translate-y-1
                        {{ $plan['featured']
                            ? 'ring-2 ring-gold shadow-2xl shadow-gold/25 bg-gradient-to-b from-white to-amber-50/40'
                            : 'border border-slate-100 shadow-lg shadow-navy/5 bg-white hover:shadow-xl' }}">

                        @if($plan['featured'])
                            <div class="absolute top-0 right-0 px-4 py-2 text-[10px] font-extrabold tracking-widest uppercase bg-gold text-navy rounded-bl-2xl shadow-sm">
                                Хамгийн ашигтай
                            </div>
                        @endif

                        <div class="p-7 flex flex-col h-full">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-extrabold tracking-widest uppercase text-slate-400">{{ $plan['period'] }}</p>
                                    <h2 class="mt-1.5 text-xl font-extrabold text-navy tracking-tight">{{ $plan['name'] }}</h2>
                                </div>
                                @if($selected)
                                    <span class="px-3 py-1 text-[10px] font-extrabold uppercase rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 whitespace-nowrap">
                                        Ашиглаж байна
                                    </span>
                                @endif
                            </div>

                            <div class="mt-6">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-navy tracking-tight">{{ number_format($plan['price']) }}</span>
                                    <span class="text-lg font-bold text-navy/70">₮</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $plan['note'] }}</p>
                                @if($plan['saving'])
                                    <p class="inline-flex px-3 py-1 mt-3 text-xs font-extrabold rounded-full bg-gold/15 text-amber-700 border border-gold/20">
                                        {{ $plan['saving'] }}
                                    </p>
                                @endif
                            </div>

                            <ul class="mt-6 space-y-3 text-sm text-slate-600 flex-1">
                                <li class="flex items-center gap-3">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold">✓</span>
                                    Хязгааргүй видео хичээл
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold">✓</span>
                                    Тест бүрийн тайлбар
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold">✓</span>
                                    Ахицын дүн шинжилгээ
                                </li>
                            </ul>

                            <form method="POST" action="{{ route('billing.subscribe') }}" class="mt-7">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $key }}">
                                <button type="submit"
                                    class="home-btn-primary btn-bounce w-full rounded-2xl px-5 py-3.5 text-sm font-extrabold transition active:scale-[0.98]
                                    {{ $plan['featured']
                                        ? 'bg-gold text-navy hover:brightness-105 shadow-md shadow-gold/30'
                                        : 'bg-navy text-white hover:bg-navy/90' }}">
                                    {{ $selected ? 'Эрх сунгах' : 'Сонгох' }}
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- Help + Invoices --}}
        <div class="grid gap-8 mt-12 lg:grid-cols-[.85fr_1.15fr]">
            <aside class="p-7 bg-white border border-slate-100 shadow-lg rounded-[1.75rem] shadow-navy/5">
                <p class="text-[10px] font-extrabold tracking-widest uppercase text-brand">Тусламж</p>
                <h2 class="mt-2 text-2xl font-extrabold text-navy tracking-tight">Төлбөр яаж ажилладаг вэ?</h2>

                <div class="mt-7 space-y-5">
                    @foreach([
                        ['1', 'Эрхээ сонгоно', 'Сар, улирал, жилээр ашиглах эрхээс өөрт тохирохыг сонгоно.'],
                        ['2', 'Төлбөр бүртгэгдэнэ', 'Одоогоор demo төлбөр тул сонгох дармагц эрх идэвхжинэ.'],
                        ['3', 'Хичээлээ нээнэ', 'Видео, тест, тайлбартай давтлага бүрэн нээгдэнэ.'],
                    ] as [$num, $title, $desc])
                        <div class="flex gap-4">
                            <span class="flex items-center justify-center flex-none text-sm font-extrabold rounded-full w-9 h-9 bg-navy text-gold shadow-sm">
                                {{ $num }}
                            </span>
                            <div>
                                <p class="font-extrabold text-navy">{{ $title }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

            <section class="overflow-hidden bg-white border border-slate-100 shadow-lg rounded-[1.75rem] shadow-navy/5">
                <div class="flex flex-col gap-2 py-6 border-b px-7 border-slate-100 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[10px] font-extrabold tracking-widest uppercase text-brand">Нэхэмжлэл</p>
                        <h2 class="mt-1 text-2xl font-extrabold text-navy tracking-tight">Төлбөрийн түүх</h2>
                    </div>
                    <p class="text-sm text-slate-400">Сүүлийн {{ $invoices->count() }} бичилт</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                @foreach(['Огноо', 'Төлөв', 'Дүн', 'Тайлбар'] as $head)
                                    <th class="px-6 py-4 text-left text-[10px] font-extrabold tracking-widest uppercase text-slate-400">
                                        {{ $head }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoices as $invoice)
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                        {{ $invoice->created_at->format('Y.m.d') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 text-[10px] font-extrabold tracking-widest uppercase rounded-full
                                            {{ $invoice->status === 'paid'
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                                                : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-extrabold text-navy whitespace-nowrap">
                                        {{ number_format($invoice->amount) }} {{ $invoice->currency }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $invoice->description }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-14 text-center">
                                        <div class="max-w-sm mx-auto">
                                            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-lg font-extrabold text-navy">Одоогоор төлбөрийн түүх алга</p>
                                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                                Эрх сонгосны дараа төлбөрийн бичилтүүд энд харагдана.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</section>
@endsection
