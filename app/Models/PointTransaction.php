<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Reward;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'task_completion_id',
        'reward_id',
        'points',
        'type',
        'description',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'points'       => 'integer',
            'delivered_at' => 'datetime',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /**
     * A criança dona desta transação de pontos.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A conclusão de tarefa que originou estes pontos (se aplicável).
     */
    public function taskCompletion(): BelongsTo
    {
        return $this->belongsTo(TaskCompletion::class);
    }

    /**
     * A recompensa resgatada (se aplicável).
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }
}
