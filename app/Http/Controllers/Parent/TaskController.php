<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Nota: No Laravel 12 o Controller base não inclui mais o trait AuthorizesRequests.
// Usamos Gate::authorize() diretamente em vez de $this->authorize().

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    /**
     * Lista as tarefas da família com filtros opcionais.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = $this->taskService->getFilteredTasks(
            auth()->user(),
            request()->only(['search', 'child_id', 'recurrence', 'is_active'])
        );

        $children = auth()->user()->family->users()
            ->where('role', 'child')
            ->orderBy('name')
            ->get();

        return view('parent.tasks.index', compact('tasks', 'children'));
    }

    /**
     * Formulário de criação de tarefa.
     */
    public function create(): View
    {
        Gate::authorize('create', Task::class);

        $children = auth()->user()->family->users()
            ->where('role', 'child')
            ->orderBy('name')
            ->get();

        return view('parent.tasks.create', compact('children'));
    }

    /**
     * Salva uma nova tarefa.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->taskService->store($request->validated(), auth()->user());

        return redirect()
            ->route('parent.tasks.index')
            ->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Formulário de edição de tarefa.
     */
    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        $children = auth()->user()->family->users()
            ->where('role', 'child')
            ->orderBy('name')
            ->get();

        // IDs dos filhos já atribuídos — para pré-marcar checkboxes
        $assignedChildrenIds = $task->assignedUsers->pluck('id')->toArray();

        return view('parent.tasks.edit', compact('task', 'children', 'assignedChildrenIds'));
    }

    /**
     * Atualiza uma tarefa existente.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $this->taskService->update($task, $request->validated());

        return redirect()
            ->route('parent.tasks.index')
            ->with('success', 'Tarefa atualizada com sucesso!');
    }

    /**
     * Remove uma tarefa.
     * Desativa se tiver histórico de conclusões; exclui definitivamente se não tiver.
     */
    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        if ($task->completions()->exists()) {
            // Tem histórico: desativa em vez de excluir para preservar o registro
            $task->update(['is_active' => false]);

            return redirect()
                ->route('parent.tasks.index')
                ->with('success', 'Tarefa desativada (possui histórico de conclusões).');
        }

        $task->assignedUsers()->detach();
        $task->delete();

        return redirect()
            ->route('parent.tasks.index')
            ->with('success', 'Tarefa excluída com sucesso!');
    }
}