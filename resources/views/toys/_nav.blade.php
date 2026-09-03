@php
  $toyNav = $toyNav ?? [
    ['toys.index', 'Тоглоомууд'],
    ['toys.speedrun', 'Хурдан бодолт'],
    ['toys.patterns', 'Хэв маяг'],
    ['toys.algebra', 'Алгебр'],
    ['toys.geometry', 'Геометр'],
  ];
@endphp

<nav aria-label="Тоглоомын горим" class="flex gap-2 pb-2 mt-6 overflow-x-auto toy-nav-scroll">
  @foreach($toyNav as [$route, $label])
    <a href="{{ route($route) }}" class="flex-none px-4 py-2.5 text-sm font-bold transition border rounded-xl {{ request()->routeIs($route) ? 'bg-navy text-white border-navy shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-brand/40 hover:text-brand' }}">
      {{ $label }}
    </a>
  @endforeach
</nav>
