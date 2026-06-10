<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\TaskCompletion;
use App\Models\User;

class PointService
{
    /**
     * Credita os pontos de uma conclusão aprovada.
     * Cria o registro em point_transactions vinculado à conclusão.
     */
    public function credit(TaskCompletion $completion): PointTransaction
    {
        return PointTransaction::create([
            'user_id'            => $completion->user_id,
            'task_completion_id' => $completion->id,
            'points'             => $completion->task->points,
            'type'               => 'earned',
            'description'        => 'Concluiu: ' . $completion->task->title,
        ]);
    }

    /**
     * Retorna o saldo atual de pontos de uma criança.
     * Saldo = soma de pontos ganhos − soma de pontos resgatados.
     */
    public function getBalance(User $child): int
    {
        $earned   = $child->pointTransactions()->where('type', 'earned')->sum('points');
        $redeemed = $child->pointTransactions()->where('type', 'redeemed')->sum('points');

        return (int) ($earned - $redeemed);
    }

    /**
     * Registra o resgate de uma recompensa.
     * Verifica se o saldo é suficiente antes de prosseguir.
     *
     * @throws \InvalidArgumentException se o saldo for insuficiente
     */
    public function redeem(User $child, Reward $reward): PointTransaction
    {
        $balance = $this->getBalance($child);

        if ($balance < $reward->points_required) {
            throw new \InvalidArgumentException(
                "Saldo insuficiente. Você tem {$balance} pontos e precisa de {$reward->points_required}."
            );
        }

        return PointTransaction::create([
            'user_id'            => $child->id,
            'task_completion_id' => null,
            'reward_id'          => $reward->id,
            'points'             => $reward->points_required,
            'type'               => 'redeemed',
            'description'        => 'Resgatou: ' . $reward->title,
        ]);
    }

    /**
     * Retorna o histórico de transações de uma criança, mais recente primeiro.
     */
    public function getHistory(User $child): \Illuminate\Database\Eloquent\Collection
    {
        return $child->pointTransactions()
            ->with('taskCompletion.task')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
