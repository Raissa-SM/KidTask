<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Str;

class FamilyService
{
    /**
     * Cria uma nova família e associa o usuário como responsável (pai/mãe).
     * Gera um código de convite único automaticamente.
     */
    public function createForUser(User $user, string $name): Family
    {
        $family = Family::create([
            'name'        => $name,
            'invite_code' => $this->generateUniqueCode(),
        ]);

        $user->update(['family_id' => $family->id]);

        return $family;
    }

    /**
     * Associa um usuário (filho) a uma família existente usando o código de convite.
     * Lança exceção se o código for inválido.
     *
     * @throws \InvalidArgumentException
     */
    public function joinByCode(User $user, string $code): Family
    {
        $family = Family::where('invite_code', strtoupper(trim($code)))->first();

        if (! $family) {
            throw new \InvalidArgumentException('Código de convite inválido. Verifique com seu responsável.');
        }

        $user->update(['family_id' => $family->id]);

        return $family;
    }

    /**
     * Gera um código de convite de 6 caracteres único (maiúsculas + números).
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Family::where('invite_code', $code)->exists());

        return $code;
    }
}
