@extends('layouts.app')
@section('title', 'Editar Tarefa')

@section('content')

<div class="flex items-center gap-3 mb-6">
        <a href="{{ route('parent.tasks.index') }}" class="text-gray-400 hover:text-gray-600 text-sm shrink-0">← Voltar</a>
        <h1 class="text-2xl font-bold text-gray-800">Editar Tarefa</h1>
    </div>

    {{-- Aviso tarefa inativa --}}
    @if(! $task->is_active)
        <div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-sm">
            ⚠️ Esta tarefa está <strong>inativa</strong> e não aparece para os filhos.
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6">
        <form method="POST" action="{{ route('parent.tasks.update', $task) }}">
            @csrf
            @method('PUT')

            {{-- Título --}}
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Título <span class="text-red-400">*</span>
                </label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $task->title) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
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
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $task->description) }}</textarea>
            </div>

            {{-- Pontos --}}
            <div class="mb-4">
                <label for="points" class="block text-sm font-medium text-gray-700 mb-1">
                    Pontos <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="number" id="points" name="points"
                           value="{{ old('points', $task->points) }}"
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
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @php $currentRecurrence = old('recurrence', $task->recurrence); @endphp
                    <option value="none"    {{ $currentRecurrence === 'none'    ? 'selected' : '' }}>Evento único</option>
                    <option value="daily"   {{ $currentRecurrence === 'daily'   ? 'selected' : '' }}>Diária</option>
                    <option value="weekly"  {{ $currentRecurrence === 'weekly'  ? 'selected' : '' }}>Semanal</option>
                    <option value="monthly" {{ $currentRecurrence === 'monthly' ? 'selected' : '' }}>Mensal</option>
                </select>
            </div>

            {{-- Data (evento único) --}}
            <div id="field-due-date" class="mb-4 {{ $currentRecurrence !== 'none' ? 'hidden' : '' }}">
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Data <span class="text-red-400">*</span>
                </label>
                <input type="date" id="due_date" name="due_date"
                       value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('due_date') border-red-400 @enderror">
                @error('due_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dia da semana (semanal) --}}
            <div id="field-recurrence-day-weekly" class="mb-4 {{ $currentRecurrence !== 'weekly' ? 'hidden' : '' }}">
                <label for="recurrence_day_weekly" class="block text-sm font-medium text-gray-700 mb-1">
                    Dia da semana <span class="text-red-400">*</span>
                </label>
                @php $currentDay = old('recurrence_day', $task->recurrence_day); @endphp
                <select id="recurrence_day_weekly" name="recurrence_day"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="0" {{ $currentDay == 0 ? 'selected' : '' }}>Domingo</option>
                    <option value="1" {{ $currentDay == 1 ? 'selected' : '' }}>Segunda-feira</option>
                    <option value="2" {{ $currentDay == 2 ? 'selected' : '' }}>Terça-feira</option>
                    <option value="3" {{ $currentDay == 3 ? 'selected' : '' }}>Quarta-feira</option>
                    <option value="4" {{ $currentDay == 4 ? 'selected' : '' }}>Quinta-feira</option>
                    <option value="5" {{ $currentDay == 5 ? 'selected' : '' }}>Sexta-feira</option>
                    <option value="6" {{ $currentDay == 6 ? 'selected' : '' }}>Sábado</option>
                </select>
            </div>

            {{-- Dia do mês (mensal) --}}
            <div id="field-recurrence-day-monthly" class="mb-4 {{ $currentRecurrence !== 'monthly' ? 'hidden' : '' }}">
                <label for="recurrence_day_monthly" class="block text-sm font-medium text-gray-700 mb-1">
                    Dia do mês <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="number" id="recurrence_day_monthly" name="recurrence_day"
                           value="{{ $currentRecurrence === 'monthly' ? $currentDay : '' }}"
                           min="1" max="28"
                           class="w-28 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('recurrence_day') border-red-400 @enderror">
                    <span class="text-xs text-gray-400">entre 1 e 28</span>
                </div>
                @error('recurrence_day')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lembrete --}}
            <div class="mb-4">
                <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-1">
                    Horário do lembrete <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input type="time" id="reminder_time" name="reminder_time"
                       value="{{ old('reminder_time', $task->reminder_time ? substr($task->reminder_time, 0, 5) : '') }}"
                       class="w-full sm:w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Status ativo/inativo --}}
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $task->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm text-gray-700">Tarefa ativa</span>
                </label>
                <p class="text-xs text-gray-400 mt-1 ml-6">Tarefas inativas não aparecem para os filhos.</p>
            </div>

            {{-- Atribuir a filhos --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Atribuir a <span class="text-red-400">*</span>
                </label>

                @if($children->isEmpty())
                    <p class="text-sm text-gray-400 italic">Nenhum filho cadastrado.</p>
                @else
                    <div class="space-y-2">
                        @foreach($children as $child)
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:border-indigo-300 transition">
                                <input type="checkbox"
                                       name="children[]"
                                       value="{{ $child->id }}"
                                       {{ in_array($child->id, old('children', $assignedChildrenIds)) ? 'checked' : '' }}
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
                    Salvar alterações
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
<script>function toggleRecurrenceFields(value) {
        document.getElementById('field-due-date').classList.toggle('hidden', value !== 'none');
        document.getElementById('field-recurrence-day-weekly').classList.toggle('hidden', value !== 'weekly');
        document.getElementById('field-recurrence-day-monthly').classList.toggle('hidden', value !== 'monthly');
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleRecurrenceFields(document.getElementById('recurrence').value);
    });

    /**
     * Desabilita os campos dos blocos ocultos antes do submit.
     * Cobre due_date, recurrence_day_weekly e recurrence_day_monthly —
     * somente o campo do tipo ativo é enviado ao servidor.
     */
    document.querySelector('form').addEventListener('submit', function () {
        const recurrence = document.getElementById('recurrence').value;

        document.getElementById('due_date').disabled              = (recurrence !== 'none');
        document.getElementById('recurrence_day_weekly').disabled  = (recurrence !== 'weekly');
        document.getElementById('recurrence_day_monthly').disabled = (recurrence !== 'monthly');
    });</script>
@endpush
