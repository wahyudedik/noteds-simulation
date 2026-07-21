<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'action_taken',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who made the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reported simulation.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    /**
     * Get the admin who reviewed the report.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to pending reports.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to active reports (pending or reviewed).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'reviewed']);
    }
}
