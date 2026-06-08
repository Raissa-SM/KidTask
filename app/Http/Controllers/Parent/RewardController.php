<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\PointTransaction;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RewardController extends Controller
{
    /**
     * Lista as recompensas da família + histórico de resgates.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Reward::class);

        $familyId = auth()->user()->family_id;

        $rewards = Reward::where('family_id', $familyId)
            ->orderBy('points_required')
            ->get();

        // Histórico de todos os resgates dos filhos desta família
        $redemptions = PointTransaction::where('type', 'redeemed')
            ->whereHas('user', fn ($q) => $q->where('family_id', $familyId))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('parent.rewards.index', compact('rewards', 'redemptions'));
    }

    public function create(): View
    {
        Gate::authorize('create', Reward::class);

        return view('parent.rewards.create');
    }

    public function store(StoreRewardRequest $request): RedirectResponse
    {
        Reward::create([
            'family_id'       => auth()->user()->family_id,
            'title'           => $request->title,
            'description'     => $request->description,
            'points_required' => $request->points_required,
            'type'            => $request->type,
        ]);

        return redirect()
            ->route('parent.rewards.index')
            ->with('success', 'Recompensa criada com sucesso!');
    }

    public function edit(Reward $reward): View
    {
        Gate::authorize('update', $reward);

        return view('parent.rewards.edit', compact('reward'));
    }

    public function update(UpdateRewardRequest $request, Reward $reward): RedirectResponse
    {
        Gate::authorize('update', $reward);

        $reward->update($request->validated());

        return redirect()
            ->route('parent.rewards.index')
            ->with('success', 'Recompensa atualizada com sucesso!');
    }

    public function destroy(Reward $reward): RedirectResponse
    {
        Gate::authorize('delete', $reward);

        $reward->delete();

        return redirect()
            ->route('parent.rewards.index')
            ->with('success', 'Recompensa removida.');
    }
}
