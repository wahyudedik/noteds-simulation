<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdImpression extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_type',
        'ad_id',
        'simulation_id',
        'user_id',
        'position',
        'clicked',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'clicked' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
