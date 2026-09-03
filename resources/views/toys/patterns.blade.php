@extends('layouts.app')

@section('title', 'Хэв маяг тоглоом')

@section('content')
@include('toys._shell', [
    'title' => 'Хэв маяг',
    'badge' => 'PATTERNS',
    'badgeColor' => '#7C3AED',
    'subtitle' => 'Дарааллын зүй тогтлыг олж, дараагийн тоог сонго.',
    'mode' => 'patterns',
])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const random = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
  const shuffle = (items) => [...items].sort(() => Math.random() - .5);
  const options = (answer) => {
    const values = new Set([answer]);
    while (values.size < 4) values.add(Math.max(1, answer + random(-12, 12)));
    return shuffle([...values]);
  };
  const makePattern = ({ difficulty }) => {
    const kind = random(1, difficulty === 'easy' ? 3 : difficulty === 'medium' ? 5 : 7);
    if (kind === 1) { const a = random(2, 12), d = random(2, 8); return { q: `${a}, ${a+d}, ${a+2*d}, ${a+3*d}, ?`, answer: a+4*d, options: options(a+4*d), hint: 'Ижил тоогоор нэмэгдэж байна.' }; }
    if (kind === 2) { const a = random(1, 4), m = random(2, 3); return { q: `${a}, ${a*m}, ${a*m*m}, ${a*m*m*m}, ?`, answer: a*m*m*m*m, options: options(a*m*m*m*m), hint: 'Тоо бүр ижил тоогоор үржигдэж байна.' }; }
    if (kind === 3) return { q: '1, 4, 9, 16, ?', answer: 25, options: options(25), hint: 'Квадрат тоонуудыг ажигла.' };
    if (kind === 4) return { q: '1, 1, 2, 3, 5, ?', answer: 8, options: options(8), hint: 'Өмнөх хоёр тооны нийлбэр.' };
    if (kind === 5) return { q: '2, 5, 4, 7, 6, ?', answer: 9, options: options(9), hint: '+3, -1 гэсэн хэв маяг давтагдана.' };
    if (kind === 6) return { q: '3, 6, 12, 24, ?', answer: 48, options: options(48), hint: 'Тоо бүр 2 дахин өсөж байна.' };
    return { q: '2, 6, 12, 20, ?', answer: 30, options: options(30), hint: 'Нэмэгдэх зөрүүг ажигла.' };
  };
  window.MathMonToy?.boot({ mode: 'patterns', generator: makePattern, rounds: 10 });
});
</script>
@endpush
