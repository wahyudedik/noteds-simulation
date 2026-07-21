<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationAnalytic extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'simulation_analytics';

    protected $fillable = [
        'simulation_id',
        'date',
        'views',
        'plays',
        'likes',
        'bookmarks',
        'shares',
        'comments',
        'avg_duration_seconds',
        'completions',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'views' => 'integer',
            'plays' => 'integer',
            'likes' => 'integer',
            'bookmarks' => 'integer',
            'shares' => 'integer',
            'comments' => 'integer',
            'avg_duration_seconds' => 'integer',
            'completions' => 'integer',
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
