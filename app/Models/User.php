<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'family_id',
        'role',
        'avatar',
        'birthdate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birthdate'         => 'date',
        ];
    }

    // ─── Helpers de perfil ─────────────────────────────────────────────────────

    /**
     * Verifica se o usuário é pai/mãe.
     */
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    /**
     * Verifica se o usuário é filho/filha.
     */
    public function isChild(): bool
    {
        return $this->role === 'child';
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /**
     * Família à qual o usuário pertence.
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Tarefas criadas por este usuário (pai/mãe).
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Atribuições de tarefas para este usuário (filho).
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * Registros de conclusão de tarefas deste usuário (filho).
     */
    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    /**
     * Histórico de pontos (ganhos e resgatados) deste usuário.
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }
}
