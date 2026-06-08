<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function __construct(private PointService $pointService)
    {
    }

    /**
     * Lista as recompensas disponíveis para a criança.
     */
    public function index(): View
    {
        $child   = auth()->user();
        $rewards = Reward::where('family_id', $child->family_id)
            ->orderBy('points_required')
            ->get();

        $balance = $this->pointService->getBalance($child);

        return view('child.rewards', compact('rewards', 'balance'));
    }

    /**
     * Criança resgata uma recompensa.
     */
    public function redeem(Reward $reward): RedirectResponse
    {
        $child = auth()->user();

        // Garante que a recompensa pertence à família da criança
        if ($reward->family_id !== $child->family_id) {
            abort(403);
        }

        try {
            $this->pointService->redeem($child, $reward);

            return redirect()
                ->route('child.rewards')
                ->with('success', "Recompensa \"{$reward->title}\" resgatada com sucesso! 🎉");
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('child.rewards')
                ->with('error', $e->getMessage());
        }
    }
}
