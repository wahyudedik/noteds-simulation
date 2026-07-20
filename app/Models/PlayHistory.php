<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_id',
        'duration_seconds',
        'completed',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'completed' => 'boolean',
    ];

    /**
     * Get the user who played.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the simulation that was played.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
