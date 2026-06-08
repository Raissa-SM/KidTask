@extends('layouts.app')
@section('title', 'Meus Pontos')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">Meus Pontos</h1>

    {{-- Card de saldo --}}
    <div class="bg-indigo-600 text-white rounded-2xl px-6 py-8 text-center mb-8">
        <p class="text-sm opacity-80 mb-1">Saldo atual</p>
        <p class="text-6xl font-bold">{{ $balance }}</p>
        <p class="text-sm opacity-80 mt-1">pontos disponíveis</p>
    </div>

    {{-- Histórico --}}
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Histórico</h2>

    @if($history->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <p class="text-4xl mb-3">📊</p>
            <p class="font-medium">Nenhuma movimentação ainda.</p>
            <p class="text-sm mt-1">Complete tarefas para ganhar pontos!</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($history as $transaction)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">
                            {{ $transaction->type === 'earned' ? '⭐' : '🎁' }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $transaction->description }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $transaction->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                    </div>
                    <span class="font-bold text-sm
                        {{ $transaction->type === 'earned' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $transaction->type === 'earned' ? '+' : '-' }}{{ $transaction->points }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

@endsection
