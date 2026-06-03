<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\CompletionService;
use Illuminate\Http\RedirectResponse;

class TaskCompletionController extends Controller
{
    public function __construct(private CompletionService $completionService)
    {
    }

    /**
     * Criança marca uma tarefa como concluída.
     * Verifica se a tarefa pertence à família e foi atribuída a esta criança.
     */
    public function store(Task $task): RedirectResponse
    {
        $child = auth()->user();

        // Garante que a tarefa pertence à família da criança
        if ($task->family_id !== $child->family_id) {
            abort(403);
        }

        // Garante que a tarefa está atribuída a esta criança
        $isAssigned = $task->assignedUsers()->where('users.id', $child->id)->exists();
        if (! $isAssigned) {
            abort(403);
        }

        try {
            $this->completionService->markDone($task, $child);

            return redirect()
                ->route('child.dashboard')
                ->with('success', "Boa! \"{$task->title}\" marcada como concluída. Aguarde a validação.");
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('child.dashboard')
                ->with('error', $e->getMessage());
        }
    }
}
