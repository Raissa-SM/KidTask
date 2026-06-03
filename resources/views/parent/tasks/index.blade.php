<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefas — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navegação --}}
<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="text-xl font-bold text-indigo-600">KidTask</span>
    <nav class="flex items-center gap-6 text-sm">
        <a href="{{ route('parent.dashboard') }}" class="text-gray-500 hover:text-indigo-600">Painel</a>
        <a href="{{ route('parent.tasks.index') }}" class="text-indigo-600 font-medium">Tarefas</a>
        {{-- Fase 6: Recompensas --}}
    </nav>
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Sair</button>
        </form>
    </div>
</header>

<main class="max-w-5xl mx-auto px-6 py-8">

    {{-- Cabeçalho + botão criar --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tarefas</h1>
        <a href="{{ route('parent.tasks.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Nova tarefa
        </a>
    </div>

    {{-- Mensagens flash --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('parent.tasks.index') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-end">

        {{-- Busca por título --}}
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nome da tarefa..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- Filtro por filho --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Filho</label>
            <select name="child_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach($children as $child)
                    <option value="{{ $child->id }}" {{ request('child_id') == $child->id ? 'selected' : '' }}>
                        {{ $child->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filtro por recorrência --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Recorrência</label>
            <select name="recurrence"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                <option value="none"    {{ request('recurrence') === 'none'    ? 'selected' : '' }}>Evento único</option>
                <option value="daily"   {{ request('recurrence') === 'daily'   ? 'selected' : '' }}>Diária</option>
                <option value="weekly"  {{ request('recurrence') === 'weekly'  ? 'selected' : '' }}>Semanal</option>
                <option value="monthly" {{ request('recurrence') === 'monthly' ? 'selected' : '' }}>Mensal</option>
            </select>
        </div>

        {{-- Filtro por status --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="is_active"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Ativas</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativas</option>
            </select>
        </div>

        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            Filtrar
        </button>

        @if(request()->hasAny(['search', 'child_id', 'recurrence', 'is_active']))
            <a href="{{ route('parent.tasks.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 py-2">
                Limpar
            </a>
        @endif
    </form>

    {{-- Lista de tarefas --}}
    @if($tasks->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-3">📋</p>
            <p class="font-medium">Nenhuma tarefa encontrada.</p>
            <p class="text-sm mt-1">
                <a href="{{ route('parent.tasks.create') }}" class="text-indigo-500 hover:underline">
                    Criar a primeira tarefa
                </a>
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tasks as $task)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between gap-4">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800 truncate">{{ $task->title }}</span>

                            {{-- Badge recorrência --}}
                            @php
                                $recurrenceLabels = [
                                    'none'    => ['label' => 'Evento único', 'class' => 'bg-gray-100 text-gray-600'],
                                    'daily'   => ['label' => 'Diária',       'class' => 'bg-blue-100 text-blue-700'],
                                    'weekly'  => ['label' => 'Semanal',      'class' => 'bg-purple-100 text-purple-700'],
                                    'monthly' => ['label' => 'Mensal',       'class' => 'bg-orange-100 text-orange-700'],
                                ];
                                $rec = $recurrenceLabels[$task->recurrence] ?? $recurrenceLabels['none'];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $rec['class'] }}">
                                {{ $rec['label'] }}
                            </span>

                            @if(! $task->is_active)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">
                                    Inativa
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                            <span>⭐ {{ $task->points }} pts</span>

                            @if($task->recurrence === 'none' && $task->due_date)
                                <span>📅 {{ $task->due_date->format('d/m/Y') }}</span>
                            @elseif($task->recurrence === 'weekly' && $task->recurrence_day !== null)
                                @php
                                    $weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                @endphp
                                <span>📅 {{ $weekDays[$task->recurrence_day] }}</span>
                            @elseif($task->recurrence === 'monthly' && $task->recurrence_day !== null)
                                <span>📅 dia {{ $task->recurrence_day }}</span>
                            @endif

                            @if($task->reminder_time)
                                <span>🔔 {{ substr($task->reminder_time, 0, 5) }}</span>
                            @endif

                            @if($task->assignedUsers->isNotEmpty())
                                <span>👤 {{ $task->assignedUsers->pluck('name')->implode(', ') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('parent.tasks.edit', $task) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 border border-indigo-200 rounded-lg">
                            Editar
                        </a>

                        <form method="POST" action="{{ route('parent.tasks.destroy', $task) }}"
                              onsubmit="return confirm('{{ $task->completions()->exists() ? 'Esta tarefa tem histórico e será desativada. Confirmar?' : 'Excluir esta tarefa permanentemente?' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium px-3 py-1.5 border border-red-200 rounded-lg">
                                {{ $task->completions()->exists() ? 'Desativar' : 'Excluir' }}
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        <p class="text-xs text-gray-400 mt-4 text-right">
            {{ $tasks->count() }} tarefa(s) encontrada(s)
        </p>
    @endif

</main>

</body>
</html>
