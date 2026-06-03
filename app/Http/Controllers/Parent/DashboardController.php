<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\CompletionService;
use App\Services\PointService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private CompletionService $completionService,
        private PointService      $pointService,
    ) {
    }

    /**
     * Painel do pai: resumo com pendências de validação e pontos dos filhos.
     */
    public function index(): View
    {
        $parent   = auth()->user();
        $pending  = $this->completionService->getPendingForFamily($parent->family_id);
        $children = $parent->family->users()
            ->where('role', 'child')
            ->orderBy('name')
            ->get();

        // Saldo de cada filho para exibir no painel
        $childrenBalances = $children->mapWithKeys(fn ($child) => [
            $child->id => $this->pointService->getBalance($child),
        ]);

        return view('parent.dashboard', compact('pending', 'children', 'childrenBalances'));
    }
}
