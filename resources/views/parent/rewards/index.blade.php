@extends('layouts.app')
@section('title', 'Recompensas')

@section('content')

    <x-page-header title="Recompensas" action-label="+ Nova recompensa" action-route="parent.rewards.create" />

    @if($rewards->isEmpty())
        <x-empty-state
            icon="🎁"
            title="Nenhuma recompensa cadastrada."
            description="Crie recompensas para motivar seus filhos!"
            link="{{ route('parent.rewards.create') }}"
            link-text="Criar primeira recompensa"
        />
    @else
        <div class="space-y-3 mb-10">
            @foreach($rewards as $reward)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-center justify-between gap-4">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800">{{ $reward->title }}</span>
                            <x-badge color="{{ $reward->type === 'allowance' ? 'green' : 'purple' }}">
                                {{ $reward->type === 'allowance' ? 'Mesada' : 'Prêmio' }}
                            </x-badge>
                        </div>

                        @if($reward->description)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $reward->description }}</p>
                        @endif

                        <p class="text-sm font-semibold text-indigo-600 mt-1">
                            ⭐ {{ $reward->points_required }} pontos
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('parent.rewards.edit', $reward) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 border border-indigo-200 rounded-lg">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('parent.rewards.destroy', $reward) }}"
                              onsubmit="return confirm('Excluir a recompensa \'{{ $reward->title }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium px-3 py-1.5 border border-red-200 rounded-lg">
                                Excluir
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        <p class="text-xs text-gray-400 -mt-8 mb-10 text-right">{{ $rewards->count() }} recompensa(s)</p>
    @endif

    {{-- ── Histórico de resgates ──────────────────────────────────────────── --}}
    <h2 class="text-base font-semibold text-gray-700 mb-3">Histórico de resgates</h2>

    @if($redemptions->isEmpty())
        <div class="text-center py-8 text-gray-400 text-sm bg-white border border-gray-200 rounded-xl">
            Nenhum resgate realizado ainda.
        </div>
    @else
        <div class="space-y-2">
            @foreach($redemptions as $redemption)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-3 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🎁</span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $redemption->description }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $redemption->user->name }}
                                &nbsp;·&nbsp;
                                {{ $redemption->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-red-500 shrink-0">
                        −{{ $redemption->points }} pts
                    </span>
                </div>
            @endforeach
        </div>
    @endif

@endsection
