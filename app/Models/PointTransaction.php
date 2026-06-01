<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'task_completion_id',
        'points',
        'type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
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
}
