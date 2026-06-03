<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Services\PointService;
use Illuminate\View\View;

class PointController extends Controller
{
    public function __construct(private PointService $pointService)
    {
    }

    /**
     * Exibe o histórico de pontos da criança.
     */
    public function index(): View
    {
        $child   = auth()->user();
        $balance = $this->pointService->getBalance($child);
        $history = $this->pointService->getHistory($child);

        return view('child.points', compact('balance', 'history'));
    }
}
