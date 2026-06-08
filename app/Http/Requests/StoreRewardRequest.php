<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isParent();
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'points_required' => ['required', 'integer', 'min:1', 'max:9999'],
            'type'            => ['required', 'in:allowance,prize'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'           => 'O nome da recompensa é obrigatório.',
            'points_required.required' => 'Informe quantos pontos são necessários.',
            'points_required.min'      => 'O mínimo é 1 ponto.',
            'points_required.max'      => 'O máximo é 9999 pontos.',
            'type.required'            => 'Selecione o tipo da recompensa.',
            'type.in'                  => 'Tipo inválido.',
        ];
    }
}
