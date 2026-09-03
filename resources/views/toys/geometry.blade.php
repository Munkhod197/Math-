@extends('layouts.app')

@section('title', 'Геометр тоглоом')

@section('content')
@include('toys._shell', [
    'title' => 'Геометр',
    'badge' => 'GEOMETRY',
    'badgeColor' => '#E11D48',
    'subtitle' => 'Талбай, периметр, эзлэхүүн, Пифагорын бодлогуудыг тоглоом шиг бодоорой.',
    'mode' => 'geometry',
])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const random = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
  const shuffle = (items) => [...items].sort(() => Math.random() - .5);
  const options = (answer) => { const values = new Set([answer]); while (values.size < 4) values.add(Math.max(1, answer + random(-10, 10))); return shuffle([...values]); };
  const makeGeometry = ({ difficulty }) => {
    const kind = random(1, difficulty === 'easy' ? 3 : difficulty === 'medium' ? 5 : 7);
    if (kind === 1) { const w = random(3, 12), h = random(3, 12), answer = w*h; return { q: `Тэгш өнцөгтийн урт ${w}, өргөн ${h}. Талбай хэд вэ?`, answer, options: options(answer), hint: 'Талбай = урт × өргөн' }; }
    if (kind === 2) { const w = random(3, 12), h = random(3, 12), answer = 2*(w+h); return { q: `Тэгш өнцөгтийн урт ${w}, өргөн ${h}. Периметр хэд вэ?`, answer, options: options(answer), hint: 'Периметр = 2 × (урт + өргөн)' }; }
    if (kind === 3) { const a = random(3, 15), answer = a*a; return { q: `Квадратын тал ${a}. Талбай хэд вэ?`, answer, options: options(answer), hint: 'S = a²' }; }
    if (kind === 4) { const b = random(4, 16), h = random(2, 8) * 2, answer = b*h/2; return { q: `Гурвалжны суурь ${b}, өндөр ${h}. Талбай хэд вэ?`, answer, options: options(answer), hint: 'S = (суурь × өндөр) ÷ 2' }; }
    if (kind === 5) { const r = random(2, 8), answer = Math.round(3.14*r*r); return { q: `Тойргийн радиус ${r}. π = 3.14 гэвэл талбай хэд вэ?`, answer, options: options(answer), hint: 'S = πr²' }; }
    const triples = [[3,4,5],[5,12,13],[6,8,10],[8,15,17]]; const [a,b,c] = triples[random(0, triples.length-1)];
    return { q: `Катетууд ${a}, ${b}. Гипотенуз хэд вэ?`, answer: c, options: options(c), hint: 'a² + b² = c²' };
  };
  window.MathMonToy?.boot({ mode: 'geometry', generator: makeGeometry, rounds: 10 });
});
</script>
@endpush
