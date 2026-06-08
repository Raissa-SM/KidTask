{{--
    Componente de estado vazio.
    Uso: <x-empty-state icon="📋" title="Nenhuma tarefa" description="Crie a primeira tarefa." />
    Parâmetros opcionais: link (rota), linkText (texto do link)
--}}
@props(['icon' => '📭', 'title', 'description' => null, 'link' => null, 'linkText' => 'Criar'])

<div class="text-center py-16 text-gray-400">
    <p class="text-5xl mb-3">{{ $icon }}</p>
    <p class="font-medium text-gray-600">{{ $title }}</p>
    @if($description)
        <p class="text-sm mt-1">{{ $description }}</p>
    @endif
    @if($link)
        <a href="{{ $link }}" class="inline-block mt-4 text-sm text-indigo-600 hover:underline font-medium">
            {{ $linkText }}
        </a>
    @endif
</div>
