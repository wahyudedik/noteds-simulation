<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'user_id',
        'price',
        'currency',
        'license_type',
        'is_active',
        'demo_available',
        'demo_limit_minutes',
        'total_sales',
        'total_revenue',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'is_active' => 'boolean',
            'demo_available' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(MarketplacePurchase::class, 'listing_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function getFormattedPriceAttribute(): string
    {
        return match ($this->currency) {
            'USD' => '$'.number_format($this->price, 2),
            default => 'Rp '.number_format($this->price, 0, ',', '.'),
        };
    }

    public function isPurchasedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->purchases()
            ->where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->exists();
    }
}
