@extends('layouts.app')
@section('title', 'Nova Recompensa')

@section('content')

    <x-page-header title="Nova Recompensa" back-route="parent.rewards.index" />

    <div class="max-w-lg bg-white border border-gray-200 rounded-2xl p-6">
        <form id="form-reward" method="POST" action="{{ route('parent.rewards.store') }}">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Nome <span class="text-red-400">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror"
                       placeholder="Ex: Mesada do mês">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Descrição <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Detalhes da recompensa...">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="points_required" class="block text-sm font-medium text-gray-700 mb-1">
                    Pontos necessários <span class="text-red-400">*</span>
                </label>
                <input type="number" id="points_required" name="points_required"
                       value="{{ old('points_required', 50) }}" min="1" max="9999" required
                       class="w-36 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('points_required') border-red-400 @enderror">
                @error('points_required') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo <span class="text-red-400">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="prize" class="peer sr-only"
                               {{ old('type', 'prize') === 'prize' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <span class="text-2xl mb-1">🎁</span>
                            <span class="text-sm font-medium text-gray-700">Prêmio</span>
                            <span class="text-xs text-gray-400 text-center">Objeto ou passeio</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="allowance" class="peer sr-only"
                               {{ old('type') === 'allowance' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <span class="text-2xl mb-1">💰</span>
                            <span class="text-sm font-medium text-gray-700">Mesada</span>
                            <span class="text-xs text-gray-400 text-center">Valor em dinheiro</span>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Criar recompensa
                </button>
                <a href="{{ route('parent.rewards.index') }}"
                   class="px-4 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

@endsection
