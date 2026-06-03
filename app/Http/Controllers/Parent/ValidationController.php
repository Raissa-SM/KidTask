<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\TaskCompletion;
use App\Services\CompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ValidationController extends Controller
{
    public function __construct(private CompletionService $completionService)
    {
    }

    /**
     * Lista todas as conclusões pendentes de validação da família.
     */
    public function index(): View
    {
        $parent  = auth()->user();
        $pending = $this->completionService->getPendingForFamily($parent->family_id);
        $history = $this->completionService->getHistoryForFamily($parent->family_id);

        return view('parent.validations.index', compact('pending', 'history'));
    }

    /**
     * Pai aprova uma conclusão.
     * Pontos são creditados automaticamente via CompletionService + PointService.
     */
    public function approve(Request $request, TaskCompletion $completion): RedirectResponse
    {
        $this->ensureBelongsToFamily($completion);

        if (! $completion->isPending()) {
            return redirect()
                ->route('parent.validations.index')
                ->with('error', 'Esta conclusão já foi validada.');
        }

        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->completionService->approve($completion, auth()->user(), $request->notes);

        return redirect()
            ->route('parent.validations.index')
            ->with('success', "\"{$completion->task->title}\" aprovada! {$completion->task->points} pontos creditados para {$completion->user->name}.");
    }

    /**
     * Pai rejeita uma conclusão com justificativa obrigatória.
     */
    public function reject(Request $request, TaskCompletion $completion): RedirectResponse
    {
        $this->ensureBelongsToFamily($completion);

        if (! $completion->isPending()) {
            return redirect()
                ->route('parent.validations.index')
                ->with('error', 'Esta conclusão já foi validada.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ], [
            'notes.required' => 'Informe o motivo da rejeição.',
        ]);

        $this->completionService->reject($completion, auth()->user(), $request->notes);

        return redirect()
            ->route('parent.validations.index')
            ->with('success', "Conclusão de \"{$completion->task->title}\" rejeitada.");
    }

    /**
     * Garante que a conclusão pertence à família do pai logado.
     */
    private function ensureBelongsToFamily(TaskCompletion $completion): void
    {
        if ($completion->task->family_id !== auth()->user()->family_id) {
            abort(403);
        }
    }
}
