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
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start justify-between gap-3">

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

    {{-- ── Resgates pendentes de entrega ─────────────────────────────────────── --}}
    <h2 class="text-base font-semibold text-gray-700 mb-3">
        Aguardando entrega
        @if($pendingRedemptions->isNotEmpty())
            <span class="text-gray-400 font-normal">({{ $pendingRedemptions->count() }})</span>
        @endif
    </h2>

    @if($pendingRedemptions->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-8 text-center text-gray-400 mb-8">
            <p class="text-3xl mb-2">✅</p>
            <p class="font-medium">Nenhum resgate aguardando entrega.</p>
        </div>
    @else
        <div class="space-y-3 mb-8">
            @foreach($pendingRedemptions as $redemption)
                <div class="bg-white border border-orange-200 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div class="flex items-start gap-3">
                        <span class="text-2xl shrink-0">🎁</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800">{{ $redemption->description }}</p>
                            <div class="flex flex-wrap gap-x-2 gap-y-0.5 text-sm text-gray-500 mt-0.5">
                                <span>👤 {{ $redemption->user->name }}</span>
                                <span>🕐 {{ $redemption->created_at->format('d/m/Y H:i') }}</span>
                                <span class="font-medium text-red-500">−{{ $redemption->points }} pts</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('parent.rewards.redemptions.deliver', $redemption) }}"
                          onsubmit="return confirm('Confirmar entrega de \'{{ addslashes($redemption->description) }}\' para {{ $redemption->user->name }}?')">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                            Entreguei ✓
                        </button>
                    </form>

                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Histórico de entregas ─────────────────────────────────────────────── --}}
    <h2 class="text-base font-semibold text-gray-700 mb-3">Histórico de entregas</h2>

    @if($deliveredRedemptions->isEmpty())
        <div class="text-center py-8 text-gray-400 text-sm bg-white border border-gray-200 rounded-xl">
            Nenhum resgate entregue ainda.
        </div>
    @else
        <div class="space-y-2">
            @foreach($deliveredRedemptions as $redemption)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-3 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <span class="text-xl shrink-0">✅</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $redemption->description }}</p>
                            <div class="flex flex-wrap gap-x-2 gap-y-0.5 text-xs text-gray-400 mt-0.5">
                                <span>{{ $redemption->user->name }}</span>
                                <span>Entregue em {{ $redemption->delivered_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-red-400 shrink-0">
                        −{{ $redemption->points }} pts
                    </span>
                </div>
            @endforeach
        </div>
    @endif

@endsection
