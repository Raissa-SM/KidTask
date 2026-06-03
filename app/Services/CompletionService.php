<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskCompletion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompletionService
{
    public function __construct(private PointService $pointService)
    {
    }

    /**
     * Registra que uma criança marcou uma tarefa como concluída.
     * Status inicial: pending_validation (aguardando aprovação do pai).
     *
     * @throws \InvalidArgumentException se a tarefa já foi concluída hoje por esta criança
     */
    public function markDone(Task $task, User $child): TaskCompletion
    {
        // Impede duplo registro no mesmo dia para tarefas recorrentes
        $alreadyDone = TaskCompletion::where('task_id', $task->id)
            ->where('user_id', $child->id)
            ->whereDate('completed_at', today())
            ->whereIn('status', ['pending_validation', 'approved'])
            ->exists();

        if ($alreadyDone) {
            throw new \InvalidArgumentException('Esta tarefa já foi marcada como concluída hoje.');
        }

        return TaskCompletion::create([
            'task_id'      => $task->id,
            'user_id'      => $child->id,
            'completed_at' => now(),
            'status'       => 'pending_validation',
        ]);
    }

    /**
     * Pai aprova uma conclusão pendente.
     * Usa transaction para garantir que a aprovação e o crédito de pontos
     * sejam atômicos — ou os dois acontecem, ou nenhum.
     */
    public function approve(TaskCompletion $completion, User $validator, ?string $notes = null): void
    {
        DB::transaction(function () use ($completion, $validator, $notes) {
            $completion->update([
                'status'       => 'approved',
                'validated_by' => $validator->id,
                'validated_at' => now(),
                'notes'        => $notes,
            ]);

            $this->pointService->credit($completion);
        });
    }

    /**
     * Pai rejeita uma conclusão pendente.
     * Nenhum ponto é creditado. A nota de justificativa é obrigatória.
     */
    public function reject(TaskCompletion $completion, User $validator, string $notes): void
    {
        $completion->update([
            'status'       => 'rejected',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'notes'        => $notes,
        ]);
    }

    /**
     * Retorna as conclusões pendentes de validação da família do pai.
     * Ordena pelas mais antigas primeiro (FIFO).
     */
    public function getPendingForFamily(int $familyId): \Illuminate\Database\Eloquent\Collection
    {
        return TaskCompletion::where('status', 'pending_validation')
            ->whereHas('task', fn ($q) => $q->where('family_id', $familyId))
            ->with(['task', 'user'])
            ->orderBy('completed_at', 'asc')
            ->get();
    }

    /**
     * Retorna o histórico de conclusões validadas (aprovadas e rejeitadas) da família.
     */
    public function getHistoryForFamily(int $familyId): \Illuminate\Database\Eloquent\Collection
    {
        return TaskCompletion::whereIn('status', ['approved', 'rejected'])
            ->whereHas('task', fn ($q) => $q->where('family_id', $familyId))
            ->with(['task', 'user', 'validator'])
            ->orderBy('validated_at', 'desc')
            ->limit(50)
            ->get();
    }
}
