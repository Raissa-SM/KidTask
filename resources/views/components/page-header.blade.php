{{--
    Cabeçalho de página com título e botão de ação opcional.
    Uso:
        <x-page-header title="Tarefas" />
        <x-page-header title="Tarefas" action-label="+ Nova tarefa" action-route="parent.tasks.create" />
--}}
@props(['title', 'actionLabel' => null, 'actionRoute' => null, 'backRoute' => null, 'backLabel' => '← Voltar'])

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @if($backRoute)
            <a href="{{ route($backRoute) }}" class="text-gray-400 hover:text-gray-600 text-sm">
                {{ $backLabel }}
            </a>
        @endif
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
    </div>

    @if($actionLabel && $actionRoute)
        <a href="{{ route($actionRoute) }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            {{ $actionLabel }}
        </a>
    @endif
</div>
