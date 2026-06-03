<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Services\CompletionService;
use App\Services\PointService;
use App\Services\TaskService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private TaskService       $taskService,
        private PointService      $pointService,
        private CompletionService $completionService,
    ) {
    }

    /**
     * Painel da criança: tarefas do dia + saldo de pontos.
     */
    public function index(): View
    {
        $child   = auth()->user();
        $tasks   = $this->taskService->getTasksForToday($child);
        $balance = $this->pointService->getBalance($child);

        return view('child.dashboard', compact('tasks', 'balance'));
    }
}
