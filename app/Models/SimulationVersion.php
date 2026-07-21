<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'version',
        'zip_path',
        'changelog',
    ];

    /**
     * Get the simulation this version belongs to.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }
}
