@extends('layouts.app')
@section('title', 'Nova Tarefa')

@section('content')

<div class="flex items-center gap-3 mb-6">
        <a href="{{ route('parent.tasks.index') }}" class="text-gray-400 hover:text-gray-600 text-sm shrink-0">← Voltar</a>
        <h1 class="text-2xl font-bold text-gray-800">Nova Tarefa</h1>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6">
        <form id="form-task" method="POST" action="{{ route('parent.tasks.store') }}">
            @csrf

            {{-- Título --}}
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Título <span class="text-red-400">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror"
                       placeholder="Ex: Arrumar a cama">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Descrição <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Instruções ou detalhes da tarefa...">{{ old('description') }}</textarea>
            </div>

            {{-- Pontos --}}
            <div class="mb-4">
                <label for="points" class="block text-sm font-medium text-gray-700 mb-1">
                    Pontos <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="number" id="points" name="points" value="{{ old('points', 5) }}"
                           min="1" max="100" required
                           class="w-28 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('points') border-red-400 @enderror">
                    <span class="text-xs text-gray-400">entre 1 e 100</span>
                </div>
                @error('points')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Recorrência --}}
            <div class="mb-4">
                <label for="recurrence" class="block text-sm font-medium text-gray-700 mb-1">
                    Recorrência <span class="text-red-400">*</span>
                </label>
                <select id="recurrence" name="recurrence" required
                        onchange="toggleRecurrenceFields(this.value)"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('recurrence') border-red-400 @enderror">
                    <option value="none"    {{ old('recurrence', 'none') === 'none'    ? 'selected' : '' }}>Evento único</option>
                    <option value="daily"   {{ old('recurrence') === 'daily'   ? 'selected' : '' }}>Diária</option>
                    <option value="weekly"  {{ old('recurrence') === 'weekly'  ? 'selected' : '' }}>Semanal</option>
                    <option value="monthly" {{ old('recurrence') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                </select>
                @error('recurrence')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data (evento único) --}}
            <div id="field-due-date" class="mb-4 {{ old('recurrence', 'none') !== 'none' ? 'hidden' : '' }}">
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data <span class="text-red-400">*</span>
                </label>
                <input type="date" id="due_date" name="due_date"
                       value="{{ old('due_date') }}"
                       min="{{ date('Y-m-d') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('due_date') border-red-400 @enderror">
                @error('due_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dia da semana (semanal) --}}
            <div id="field-recurrence-day-weekly" class="mb-4 {{ old('recurrence') !== 'weekly' ? 'hidden' : '' }}">
                <label for="recurrence_day_weekly" class="block text-sm font-medium text-gray-700 mb-1">
                    Dia da semana <span class="text-red-400">*</span>
                </label>
                <select id="recurrence_day_weekly" name="recurrence_day"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('recurrence_day') border-red-400 @enderror">
                    <option value="0" {{ old('recurrence_day') == '0' ? 'selected' : '' }}>Domingo</option>
                    <option value="1" {{ old('recurrence_day', '1') == '1' && old('recurrence') === 'weekly' ? 'selected' : '' }}>Segunda-feira</option>
                    <option value="2" {{ old('recurrence_day') == '2' ? 'selected' : '' }}>Terça-feira</option>
                    <option value="3" {{ old('recurrence_day') == '3' ? 'selected' : '' }}>Quarta-feira</option>
                    <option value="4" {{ old('recurrence_day') == '4' ? 'selected' : '' }}>Quinta-feira</option>
                    <option value="5" {{ old('recurrence_day') == '5' ? 'selected' : '' }}>Sexta-feira</option>
                    <option value="6" {{ old('recurrence_day') == '6' ? 'selected' : '' }}>Sábado</option>
                </select>
                @error('recurrence_day')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dia do mês (mensal) --}}
            <div id="field-recurrence-day-monthly" class="mb-4 {{ old('recurrence') !== 'monthly' ? 'hidden' : '' }}">
                <label for="recurrence_day_monthly" class="block text-sm font-medium text-gray-700 mb-1">
                    Dia do mês <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="number" id="recurrence_day_monthly" name="recurrence_day"
                           value="{{ old('recurrence') === 'monthly' ? old('recurrence_day') : '' }}"
                           min="1" max="28" placeholder="Ex: 5"
                           class="w-28 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('recurrence_day') border-red-400 @enderror">
                    <span class="text-xs text-gray-400">entre 1 e 28</span>
                </div>
            </div>

            {{-- Lembrete --}}
            <div class="mb-6">
                <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-1">
                    Horário do lembrete <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input type="time" id="reminder_time" name="reminder_time"
                       value="{{ old('reminder_time') }}"
                       class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('reminder_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Atribuir a filhos --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Atribuir a <span class="text-red-400">*</span>
                </label>

                @if($children->isEmpty())
                    <p class="text-sm text-gray-400 italic">
                        Nenhum filho cadastrado ainda.
                        Para atribuir tarefas, peça para os filhos se cadastrarem com o código
                        <code class="bg-gray-100 px-1 rounded text-indigo-600">{{ auth()->user()->family->invite_code }}</code>.
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach($children as $child)
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:border-indigo-300 transition">
                                <input type="checkbox"
                                       name="children[]"
                                       value="{{ $child->id }}"
                                       {{ in_array($child->id, old('children', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600">
                                <span class="text-sm text-gray-700">{{ $child->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('children')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            {{-- Botões --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Criar tarefa
                </button>
                <a href="{{ route('parent.tasks.index') }}"
                   class="px-4 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm transition">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
<script>/**
     * Mostra/oculta os campos conforme a recorrência selecionada.
     * NÃO limpa os valores — o submit handler cuida de desabilitar os campos
     * ocultos para que o PHP não receba valores de campos que não estão visíveis.
     */
    function toggleRecurrenceFields(value) {
        document.getElementById('field-due-date').classList.toggle('hidden', value !== 'none');
        document.getElementById('field-recurrence-day-weekly').classList.toggle('hidden', value !== 'weekly');
        document.getElementById('field-recurrence-day-monthly').classList.toggle('hidden', value !== 'monthly');
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleRecurrenceFields(document.getElementById('recurrence').value);
    });

    /**
     * Antes do submit: desabilita os campos dos blocos ocultos.
     * Campos disabled não são enviados ao servidor — evita conflito de mesmo name
     * e garante que o PHP receba apenas o valor do campo visível.
     */
    document.getElementById('form-task').addEventListener('submit', function () {
        const recurrence = document.getElementById('recurrence').value;

        // Desabilita os campos NÃO ativos
        document.getElementById('due_date').disabled              = (recurrence !== 'none');
        document.getElementById('recurrence_day_weekly').disabled  = (recurrence !== 'weekly');
        document.getElementById('recurrence_day_monthly').disabled = (recurrence !== 'monthly');
    });</script>
@endpush
