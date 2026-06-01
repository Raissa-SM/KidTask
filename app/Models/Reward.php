<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'title',
        'description',
        'points_required',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'points_required' => 'integer',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
