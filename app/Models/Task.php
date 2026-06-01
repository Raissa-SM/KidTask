<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'created_by',
        'title',
        'description',
        'points',
        'recurrence',
        'recurrence_day',
        'due_date',
        'reminder_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'due_date'   => 'date',
            'is_active'  => 'boolean',
            'points'     => 'integer',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /**
     * Família à qual esta tarefa pertence.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Usuário (pai/mãe) que criou a tarefa.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Crianças atribuídas a esta tarefa (via tabela pivot task_assignments).
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments')
                    ->withTimestamps();
    }

    /**
     * Registros de conclusão desta tarefa.
     */
    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    // ─── Scopes (filtros reutilizáveis) ───────────────────────────────────────

    /**
     * Filtra tarefas de uma família específica.
     */
    public function scopeForFamily(Builder $query, int $familyId): Builder
    {
        return $query->where('family_id', $familyId);
    }

    /**
     * Retorna apenas tarefas ativas.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Retorna tarefas de uma data específica (eventos únicos ou recorrências).
     */
    public function scopeForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where(function (Builder $q) use ($date) {
            // Evento único na data exata
            $q->where('recurrence', 'none')
              ->whereDate('due_date', $date);
        })->orWhere(function (Builder $q) use ($date) {
            // Diária: sempre aparece
            $q->where('recurrence', 'daily');
        })->orWhere(function (Builder $q) use ($date) {
            // Semanal: aparece no dia da semana configurado (0=Dom)
            $q->where('recurrence', 'weekly')
              ->where('recurrence_day', $date->dayOfWeek);
        })->orWhere(function (Builder $q) use ($date) {
            // Mensal: aparece no dia do mês configurado
            $q->where('recurrence', 'monthly')
              ->where('recurrence_day', $date->day);
        });
    }
}
