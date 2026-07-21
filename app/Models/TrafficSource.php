<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrafficSource extends Model
{
    protected $fillable = [
        'simulation_id',
        'source',
        'metric_type',
        'count',
        'date',
    ];

    protected $casts = [
        'count' => 'integer',
        'date' => 'date',
    ];

    /**
     * Get the simulation.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
