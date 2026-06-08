@extends('layouts.app')
@section('title', 'Validações')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Validações</h1>

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

    {{-- ── Pendentes ──────────────────────────────────────────────────────────── --}}
    <h2 class="text-base font-semibold text-gray-700 mb-3">
        Aguardando validação
        @if($pending->isNotEmpty())
            <span class="text-gray-400 font-normal">({{ $pending->count() }})</span>
        @endif
    </h2>

    @if($pending->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-8 text-center text-gray-400 mb-8">
            <p class="text-3xl mb-2">✅</p>
            <p class="font-medium">Tudo em dia! Nenhuma conclusão pendente.</p>
        </div>
    @else
        <div class="space-y-4 mb-8">
            @foreach($pending as $completion)
                <div class="bg-white border border-yellow-200 rounded-xl p-5">

                    {{-- Cabeçalho da conclusão --}}
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $completion->task->title }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">
                                👤 {{ $completion->user->name }}
                                &nbsp;·&nbsp;
                                🕐 {{ $completion->completed_at->format('d/m/Y \à\s H:i') }}
                                &nbsp;·&nbsp;
                                ⭐ {{ $completion->task->points }} pts
                            </p>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-700 font-medium px-2 py-1 rounded-full shrink-0">
                            Pendente
                        </span>
                    </div>

                    {{-- Formulários de aprovar e rejeitar lado a lado --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        {{-- Aprovar --}}
                        <form method="POST"
                              action="{{ route('parent.validations.approve', $completion) }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="notes"
                                       placeholder="Observação (opcional)"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 rounded-lg transition">
                                ✅ Aprovar
                            </button>
                        </form>

                        {{-- Rejeitar --}}
                        <form method="POST"
                              action="{{ route('parent.validations.reject', $completion) }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="notes"
                                       placeholder="Motivo da rejeição (obrigatório)"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400
                                              @error('notes') border-red-400 @enderror">
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-2 rounded-lg transition">
                                ❌ Rejeitar
                            </button>
                        </form>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Histórico ──────────────────────────────────────────────────────────── --}}
    <h2 class="text-base font-semibold text-gray-700 mb-3">Histórico recente</h2>

    @if($history->isEmpty())
        <div class="text-center py-8 text-gray-400 text-sm">
            Nenhuma validação realizada ainda.
        </div>
    @else
        <div class="space-y-2">
            @foreach($history as $completion)
                <div class="bg-white border rounded-xl px-5 py-3 flex items-center justify-between gap-4
                            {{ $completion->isApproved() ? 'border-green-200' : 'border-red-200' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ $completion->task->title }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $completion->user->name }}
                            &nbsp;·&nbsp;
                            {{ $completion->validated_at->format('d/m/Y H:i') }}
                            @if($completion->notes)
                                &nbsp;·&nbsp; "{{ $completion->notes }}"
                            @endif
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if($completion->isApproved())
                            <span class="text-xs bg-green-100 text-green-700 font-medium px-2 py-1 rounded-full">
                                +{{ $completion->task->points }} pts
                            </span>
                        @else
                            <span class="text-xs bg-red-100 text-red-600 font-medium px-2 py-1 rounded-full">
                                Rejeitada
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
