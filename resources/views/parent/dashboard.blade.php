@extends('layouts.app')
@section('title', 'Painel')

@section('content')

<div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Painel do Responsável</h1>
            <p class="text-gray-500 text-sm mt-1">
                Família: <strong>{{ auth()->user()->family->name }}</strong>
                &nbsp;·&nbsp;
                Código de convite:
                <code class="bg-gray-100 px-2 py-0.5 rounded text-indigo-600 font-mono text-xs">
                    {{ auth()->user()->family->invite_code }}
                </code>
            </p>
        </div>
    </div>

    {{-- Resumo: cards de ação rápida --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        {{-- Pendentes de validação --}}
        <a href="{{ route('parent.validations.index') }}"
           class="bg-white border rounded-xl px-5 py-5 hover:border-indigo-300 transition
                  {{ $pending->isNotEmpty() ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200' }}">
            <p class="text-3xl font-bold {{ $pending->isNotEmpty() ? 'text-yellow-600' : 'text-gray-700' }}">
                {{ $pending->count() }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Aguardando validação</p>
        </a>

        {{-- Tarefas ativas --}}
        <a href="{{ route('parent.tasks.index') }}"
           class="bg-white border border-gray-200 rounded-xl px-5 py-5 hover:border-indigo-300 transition">
            <p class="text-3xl font-bold text-gray-700">
                {{ auth()->user()->family->tasks()->where('is_active', true)->count() }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Tarefas ativas</p>
        </a>

        {{-- Filhos --}}
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-5">
            <p class="text-3xl font-bold text-gray-700">{{ $children->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $children->count() === 1 ? 'Filho' : 'Filhos' }} na família</p>
        </div>

    </div>

    {{-- Saldo de pontos por filho --}}
    @if($children->isNotEmpty())
        <h2 class="text-base font-semibold text-gray-700 mb-3">Pontos por filho</h2>
        <div class="space-y-2 mb-8">
            @foreach($children as $child)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ $child->name }}</span>
                    <span class="text-sm font-bold text-indigo-600">
                        {{ $childrenBalances[$child->id] }} pts
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Pendentes de validação (preview) --}}
    @if($pending->isNotEmpty())
        <h2 class="text-base font-semibold text-gray-700 mb-3">Conclusões pendentes</h2>
        <div class="space-y-2">
            @foreach($pending->take(3) as $completion)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $completion->task->title }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $completion->user->name }}
                            &nbsp;·&nbsp;
                            {{ $completion->completed_at->diffForHumans() }}
                        </p>
                    </div>
                    <a href="{{ route('parent.validations.index') }}"
                       class="text-xs text-indigo-600 hover:underline font-medium">
                        Validar →
                    </a>
                </div>
            @endforeach

            @if($pending->count() > 3)
                <a href="{{ route('parent.validations.index') }}"
                   class="block text-center text-sm text-indigo-600 hover:underline py-2">
                    Ver todas as {{ $pending->count() }} pendências →
                </a>
            @endif
        </div>
    @endif

@endsection
