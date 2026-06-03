<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isParent();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points'      => ['required', 'integer', 'min:1', 'max:100'],
            'recurrence'  => ['required', 'in:none,daily,weekly,monthly'],

            // No edit não forçamos after_or_equal:today para permitir
            // salvar tarefas já vencidas sem forçar troca de data.
            'due_date' => [
                'exclude_unless:recurrence,none',
                'required',
                'date',
            ],

            'recurrence_day' => [
                'exclude_if:recurrence,none',
                'exclude_if:recurrence,daily',
                'required',
                'integer',
            ],

            'reminder_time' => ['nullable', 'date_format:H:i'],
            'is_active'     => ['boolean'],
            'children'      => ['required', 'array', 'min:1'],
            'children.*'    => ['integer', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('recurrence_day', 'min:0|max:6', function ($input) {
            return $input->recurrence === 'weekly';
        });

        $validator->sometimes('recurrence_day', 'min:1|max:28', function ($input) {
            return $input->recurrence === 'monthly';
        });
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'O título da tarefa é obrigatório.',
            'points.required'         => 'Informe quantos pontos a tarefa vale.',
            'points.min'              => 'A tarefa deve valer pelo menos 1 ponto.',
            'points.max'              => 'A tarefa pode valer no máximo 100 pontos.',
            'recurrence.required'     => 'Selecione a recorrência da tarefa.',
            'due_date.required'       => 'Informe a data para tarefas sem recorrência.',
            'recurrence_day.required' => 'Informe o dia para este tipo de recorrência.',
            'recurrence_day.min'      => 'Dia inválido para este tipo de recorrência.',
            'recurrence_day.max'      => 'Dia inválido para este tipo de recorrência.',
            'children.required'       => 'Selecione pelo menos um filho para a tarefa.',
            'children.*.exists'       => 'Um dos filhos selecionados é inválido.',
        ];
    }
}
