@extends('layouts.app')

@section('title', 'Алгебр тоглоом')

@section('content')
@include('toys._shell', [
    'title' => 'Алгебр',
    'badge' => 'ALGEBRA',
    'badgeColor' => '#2563EB',
    'subtitle' => 'Тэгшитгэлийг зөв бодож, x-ийн утгыг ол.',
    'mode' => 'algebra',
])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const random = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
  const shuffle = (items) => [...items].sort(() => Math.random() - .5);
  const options = (answer) => {
    const values = new Set([answer]);
    while (values.size < 4) values.add(Math.max(1, answer + random(-5, 5)));
    return shuffle([...values]);
  };
  const signed = (value) => value >= 0 ? `+ ${value}` : `- ${Math.abs(value)}`;

  const makeEquation = ({ difficulty }) => {
    const x = random(2, difficulty === 'hard' ? 20 : 12);
    const a = random(2, difficulty === 'easy' ? 5 : 8);
    const b = random(1, 12);

    if (difficulty === 'hard' && Math.random() > .5) {
      let k = random(2, 8);
      while (k === a) k = random(2, 8);
      const m = (a - k) * x + b;
      return { q: `${a}x + ${b} = ${k}x ${signed(m)}`, answer: x, options: options(x), hint: 'x-тэй гишүүдийг нэг талд цуглуул.' };
    }
    if (difficulty !== 'easy' && Math.random() > .5) {
      const total = a * (x + b);
      return { q: `${a}(x + ${b}) = ${total}`, answer: x, options: options(x), hint: 'Эхлээд коэффициентоор хуваа.' };
    }
    const total = a * x + b;
    return { q: `${a}x + ${b} = ${total}`, answer: x, options: options(x), hint: 'Тогтмол тоог нөгөө тал руу шилжүүл.' };
  };

  window.MathMonToy?.boot({ mode: 'algebra', generator: makeEquation, rounds: 10 });
});
</script>
@endpush
