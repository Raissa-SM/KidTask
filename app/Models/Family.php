<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'invite_code',
    ];

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /**
     * Todos os usuários (pais e filhos) desta família.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Todas as tarefas criadas nesta família.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Todas as recompensas disponíveis nesta família.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }
}
