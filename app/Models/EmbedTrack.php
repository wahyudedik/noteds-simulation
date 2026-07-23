<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbedTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'embed_url',
        'referrer_domain',
        'ip_address',
        'user_agent',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
