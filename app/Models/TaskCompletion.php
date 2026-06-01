<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskCompletion extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'completed_at',
        'status',
        'validated_by',
        'validated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * A criança que registrou a conclusão.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * O pai/mãe que validou (aprovou ou rejeitou).
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Transação de pontos gerada pela aprovação desta conclusão.
     */
    public function pointTransaction(): HasOne
    {
        return $this->hasOne(PointTransaction::class);
    }

    // ─── Helpers de status ─────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending_validation';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
