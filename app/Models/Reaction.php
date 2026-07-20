<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory;

    public const TYPES = [
        'mudah_dipahami',
        'membuka_wawasan',
        'sangat_membantu',
        'interaktif',
        'favorit',
    ];

    protected $fillable = [
        'user_id',
        'simulation_id',
        'type',
    ];

    /**
     * Get the user who reacted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reacted simulation.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    /**
     * Get the human-readable label for this reaction type.
     */
    public function getLabelAttribute(): string
    {
        return match ($this->type) {
            'mudah_dipahami' => 'Mudah Dipahami',
            'membuka_wawasan' => 'Membuka Wawasan',
            'sangat_membantu' => 'Sangat Membantu',
            'interaktif' => 'Interaktif',
            'favorit' => 'Favorit',
            default => $this->type,
        };
    }
}
