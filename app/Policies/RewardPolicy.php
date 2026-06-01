<?php

namespace App\Policies;

use App\Models\Reward;
use App\Models\User;

class RewardPolicy
{
    /**
     * Qualquer membro da família pode ver as recompensas disponíveis.
     */
    public function viewAny(User $user): bool
    {
        return $user->family_id !== null;
    }

    /**
     * Usuário pode ver uma recompensa se ela pertencer à sua família.
     */
    public function view(User $user, Reward $reward): bool
    {
        return $user->family_id === $reward->family_id;
    }

    /**
     * Apenas pais podem criar recompensas.
     */
    public function create(User $user): bool
    {
        return $user->isParent();
    }

    /**
     * Apenas pais podem editar recompensas da própria família.
     */
    public function update(User $user, Reward $reward): bool
    {
        return $user->isParent()
            && $user->family_id === $reward->family_id;
    }

    /**
     * Mesma regra do update.
     */
    public function delete(User $user, Reward $reward): bool
    {
        return $this->update($user, $reward);
    }
}
