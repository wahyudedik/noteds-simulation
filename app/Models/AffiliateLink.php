<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_id',
        'code',
        'clicks',
        'conversions',
    ];

    protected function casts(): array
    {
        return [
            'clicks' => 'integer',
            'conversions' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function affiliateConversions(): HasMany
    {
        return $this->hasMany(AffiliateConversion::class);
    }

    /**
     * Get the affiliate URL for this link.
     */
    public function getUrlAttribute(): string
    {
        return route('affiliate.track', $this->code);
    }

    /**
     * Get total commission earned from this link.
     */
    public function getTotalCommissionAttribute(): float
    {
        return (float) $this->affiliateConversions()->sum('commission');
    }
}
