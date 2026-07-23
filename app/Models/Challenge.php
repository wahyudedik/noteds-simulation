<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'theme',
        'criteria',
        'prize_description',
        'prize_badge_id',
        'winner_simulation_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Challenge $challenge) {
            if (empty($challenge->slug)) {
                $challenge->slug = Str::slug($challenge->title);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function prizeBadge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'prize_badge_id');
    }

    public function winnerSimulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class, 'winner_simulation_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ChallengeEntry::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getEntryCountAttribute(): int
    {
        return $this->entries()->count();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'annual' => 'Tahunan',
            default => $this->type,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-emerald-100 text-emerald-700',
            'upcoming' => 'bg-blue-100 text-blue-700',
            'judging' => 'bg-amber-100 text-amber-700',
            'completed' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
