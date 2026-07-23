<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'simulation_id',
        'user_id',
        'scores',
        'total_score',
        'rank',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'total_score' => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'winner' => 'bg-amber-100 text-amber-700',
            'runner_up' => 'bg-blue-100 text-blue-700',
            'scored' => 'bg-emerald-100 text-emerald-700',
            'judging' => 'bg-violet-100 text-violet-700',
            'submitted' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
