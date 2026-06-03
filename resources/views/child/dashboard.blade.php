<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Dia — KidTask</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="text-xl font-bold text-indigo-600">KidTask</span>
    <nav class="flex items-center gap-6 text-sm">
        <a href="{{ route('child.dashboard') }}" class="text-indigo-600 font-medium">Meu Dia</a>
        <a href="{{ route('child.points') }}" class="text-gray-500 hover:text-indigo-600">Meus Pontos</a>
    </nav>
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Sair</button>
        </form>
    </div>
</header>

<main class="max-w-2xl mx-auto px-6 py-8">

    {{-- Saudação + saldo --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Olá, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
        <a href="{{ route('child.points') }}"
           class="flex flex-col items-center bg-indigo-600 text-white rounded-2xl px-5 py-3 hover:bg-indigo-700 transition">
            <span class="text-2xl font-bold">{{ $balance }}</span>
            <span class="text-xs mt-0.5 opacity-90">pontos</span>
        </a>
    </div>

    {{-- Mensagens flash --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Lista de tarefas do dia --}}
    @if($tasks->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-4">🎉</p>
            <p class="font-medium text-gray-600">Nenhuma tarefa para hoje!</p>
            <p class="text-sm mt-1">Aproveite o dia livre.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tasks as $task)
                @php
                    // A tarefa já carregou as conclusões de hoje via eager load
                    $todayCompletion = $task->completions->first();
                    $isDone     = $todayCompletion !== null;
                    $isPending  = $todayCompletion?->isPending() ?? false;
                    $isApproved = $todayCompletion?->isApproved() ?? false;
                    $isRejected = $todayCompletion?->isRejected() ?? false;
                @endphp

                <div class="bg-white border rounded-xl px-5 py-4 flex items-center gap-4
                            {{ $isApproved ? 'border-green-200 bg-green-50' : ($isRejected ? 'border-red-200 bg-red-50' : ($isPending ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200')) }}">

                    {{-- Ícone de status --}}
                    <div class="text-2xl shrink-0">
                        @if($isApproved) ✅
                        @elseif($isPending) ⏳
                        @elseif($isRejected) ❌
                        @else ⬜
                        @endif
                    </div>

                    {{-- Informações da tarefa --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 {{ $isApproved ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </p>

                        @if($task->description)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $task->description }}</p>
                        @endif

                        <div class="flex items-center gap-3 mt-1 text-xs flex-wrap">
                            <span class="text-indigo-600 font-medium">⭐ {{ $task->points }} pts</span>

                            @if($task->reminder_time)
                                <span class="text-gray-400">🔔 {{ substr($task->reminder_time, 0, 5) }}</span>
                            @endif

                            {{-- Status badge --}}
                            @if($isPending)
                                <span class="text-yellow-600 font-medium">Aguardando validação</span>
                            @elseif($isApproved)
                                <span class="text-green-600 font-medium">Aprovada!</span>
                            @elseif($isRejected)
                                <span class="text-red-600 font-medium">Rejeitada</span>
                                @if($todayCompletion->notes)
                                    <span class="text-gray-400">— {{ $todayCompletion->notes }}</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Botão de ação --}}
                    <div class="shrink-0">
                        @if(! $isDone)
                            <form method="POST" action="{{ route('child.tasks.complete', $task) }}">
                                @csrf
                                <button type="submit"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                                    Fiz! ✓
                                </button>
                            </form>
                        @elseif($isRejected)
                            {{-- Permite tentar novamente após rejeição --}}
                            <form method="POST" action="{{ route('child.tasks.complete', $task) }}">
                                @csrf
                                <button type="submit"
                                        class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                                    Tentar de novo
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Resumo do dia --}}
        @php
            $totalTasks    = $tasks->count();
            $approvedCount = $tasks->filter(fn($t) => $t->completions->first()?->isApproved())->count();
            $pendingCount  = $tasks->filter(fn($t) => $t->completions->first()?->isPending())->count();
            $doneCount     = $approvedCount + $pendingCount;
        @endphp
        <div class="mt-6 bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between text-sm">
            <span class="text-gray-500">Progresso de hoje</span>
            <span class="font-medium text-gray-700">{{ $doneCount }}/{{ $totalTasks }} tarefas</span>
        </div>
    @endif

</main>

</body>
</html>
