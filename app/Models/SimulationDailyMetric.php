<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationDailyMetric extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'simulation_daily_metrics';

    protected $fillable = [
        'simulation_id',
        'date',
        'metric_type',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'count' => 'integer',
        ];
    }

    /**
     * Get the simulation.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
