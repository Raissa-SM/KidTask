<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
    ];

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * A criança atribuída à tarefa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
