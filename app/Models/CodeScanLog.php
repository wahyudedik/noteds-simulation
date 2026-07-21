<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodeScanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'version',
        'scan_type',
        'result',
        'findings',
        'scanned_by',
        'scan_duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'findings' => 'array',
            'scan_duration_ms' => 'integer',
        ];
    }

    /**
     * Get the simulation.
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    /**
     * Get the admin who performed manual review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Scope to auto scans.
     */
    public function scopeAutoScan($query)
    {
        return $query->where('scan_type', 'auto_scan');
    }

    /**
     * Scope to failed scans (flag or reject).
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('result', ['flag', 'reject']);
    }
}
