@extends('layouts.app')
@section('title', 'Recompensas')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Recompensas</h1>
        <div class="flex flex-col items-end">
            <span class="text-2xl font-bold text-indigo-600">{{ $balance }}</span>
            <span class="text-xs text-gray-400">pontos disponíveis</span>
        </div>
    </div>

    @if($rewards->isEmpty())
        <x-empty-state
            icon="🎁"
            title="Nenhuma recompensa disponível ainda."
            description="Peça para seus pais cadastrarem recompensas."
        />
    @else
        <div class="space-y-3">
            @foreach($rewards as $reward)
                @php $canRedeem = $balance >= $reward->points_required; @endphp
                <div class="bg-white border rounded-xl px-5 py-4 flex items-center justify-between gap-4
                            {{ $canRedeem ? 'border-gray-200' : 'border-gray-100 opacity-60' }}">

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800">
                                {{ $reward->type === 'allowance' ? '💰' : '🎁' }} {{ $reward->title }}
                            </span>
                            <x-badge color="{{ $reward->type === 'allowance' ? 'green' : 'purple' }}">
                                {{ $reward->type === 'allowance' ? 'Mesada' : 'Prêmio' }}
                            </x-badge>
                        </div>

                        @if($reward->description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $reward->description }}</p>
                        @endif

                        <p class="text-sm font-semibold mt-1 {{ $canRedeem ? 'text-indigo-600' : 'text-gray-400' }}">
                            ⭐ {{ $reward->points_required }} pontos
                            @if(! $canRedeem)
                                <span class="text-xs font-normal text-gray-400">
                                    (faltam {{ $reward->points_required - $balance }})
                                </span>
                            @endif
                        </p>
                    </div>

                    @if($canRedeem)
                        <form method="POST" action="{{ route('child.rewards.redeem', $reward) }}"
                              onsubmit="return confirm('Resgatar \'{{ $reward->title }}\' por {{ $reward->points_required }} pontos?')">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition shrink-0">
                                Resgatar
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-gray-300 shrink-0">🔒</span>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

@endsection
