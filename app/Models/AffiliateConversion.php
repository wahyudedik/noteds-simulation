<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_link_id',
        'buyer_user_id',
        'amount',
        'commission',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
        ];
    }

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }
}
