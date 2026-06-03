<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class TaskService
{
    /**
     * Cria uma tarefa e atribui às crianças selecionadas.
     */
    public function store(array $data, User $creator): Task
    {
        $task = Task::create([
            'family_id'      => $creator->family_id,
            'created_by'     => $creator->id,
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'points'         => $data['points'],
            'recurrence'     => $data['recurrence'],
            'recurrence_day' => $data['recurrence_day'] ?? null,
            'due_date'       => $data['due_date'] ?? null,
            'reminder_time'  => $data['reminder_time'] ?? null,
            'is_active'      => true,
        ]);

        $this->assignToChildren($task, $data['children']);

        return $task;
    }

    /**
     * Atualiza uma tarefa e sincroniza as atribuições de filhos.
     *
     * Garante que os campos mutuamente exclusivos sejam sempre limpos:
     * - due_date      só faz sentido para recurrence = none
     * - recurrence_day só faz sentido para recurrence = weekly ou monthly
     */
    public function update(Task $task, array $data): Task
    {
        $recurrence = $data['recurrence'];

        // Se não é evento único, due_date deve ser null — independente do que veio
        $dueDate = ($recurrence === 'none') ? ($data['due_date'] ?? null) : null;

        // Se é diária ou evento único, recurrence_day deve ser null
        $recurrenceDay = in_array($recurrence, ['weekly', 'monthly'])
            ? ($data['recurrence_day'] ?? null)
            : null;

        $task->update([
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'points'         => $data['points'],
            'recurrence'     => $recurrence,
            'recurrence_day' => $recurrenceDay,
            'due_date'       => $dueDate,
            'reminder_time'  => $data['reminder_time'] ?? null,
            'is_active'      => $data['is_active'] ?? $task->is_active,
        ]);

        $this->assignToChildren($task, $data['children']);

        return $task;
    }

    /**
     * Sincroniza quais filhos estão atribuídos a esta tarefa.
     */
    public function assignToChildren(Task $task, array $childrenIds): void
    {
        $task->assignedUsers()->sync($childrenIds);
    }

    /**
     * Retorna as tarefas do dia para uma criança específica.
     */
    public function getTasksForToday(User $child): Collection
    {
        $today = Carbon::today();

        return Task::active()
            ->forFamily($child->family_id)
            ->forDate($today)
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $child->id))
            ->with(['completions' => fn ($q) => $q->where('user_id', $child->id)
                                                   ->whereDate('completed_at', $today)])
            ->get();
    }

    /**
     * Retorna as tarefas da família com filtros opcionais para o painel do pai.
     */
    public function getFilteredTasks(User $parent, array $filters): Collection
    {
        $query = Task::forFamily($parent->family_id)
            ->with(['assignedUsers', 'creator'])
            ->orderBy('created_at', 'desc');

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['child_id'])) {
            $query->whereHas('assignedUsers', fn ($q) =>
                $q->where('users.id', $filters['child_id'])
            );
        }

        // Usa isset em vez de empty para que 'none' (falsy para empty) funcione corretamente
        if (isset($filters['recurrence']) && $filters['recurrence'] !== '') {
            $query->where('recurrence', $filters['recurrence']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        } else {
            $query->active();
        }

        return $query->get();
    }
}
