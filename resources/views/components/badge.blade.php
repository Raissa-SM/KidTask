{{--
    Componente de badge/etiqueta de status.
    Uso: <x-badge color="green">Aprovada</x-badge>
    Cores disponíveis: green, red, yellow, blue, purple, orange, gray, indigo
--}}
@props(['color' => 'gray'])

@php
$colors = [
    'green'  => 'bg-green-100 text-green-700',
    'red'    => 'bg-red-100 text-red-600',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'blue'   => 'bg-blue-100 text-blue-700',
    'purple' => 'bg-purple-100 text-purple-700',
    'orange' => 'bg-orange-100 text-orange-700',
    'gray'   => 'bg-gray-100 text-gray-600',
    'indigo' => 'bg-indigo-100 text-indigo-700',
];
$classes = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "text-xs font-medium px-2 py-0.5 rounded-full $classes"]) }}>
    {{ $slot }}
</span>
