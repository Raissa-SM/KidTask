<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Apenas pais podem ver a lista completa de tarefas da família.
     */
    public function viewAny(User $user): bool
    {
        return $user->isParent();
    }

    /**
     * Usuário pode ver uma tarefa se ela pertencer à sua família.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->family_id === $task->family_id;
    }

    /**
     * Apenas pais podem criar tarefas.
     */
    public function create(User $user): bool
    {
        return $user->isParent();
    }

    /**
     * Apenas o pai que criou a tarefa pode editá-la.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isParent()
            && $user->family_id === $task->family_id
            && $user->id === $task->created_by;
    }

    /**
     * Mesma regra do update — só o criador pode excluir.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
