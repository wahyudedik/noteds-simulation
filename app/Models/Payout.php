<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'bank_name',
        'account_number',
        'account_holder',
        'paypal_email',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'proof_path',
        'paid_at',
        'currency',
        'amount_usd',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency === 'USD'
            ? '$'.number_format($this->amount, 2, '.', ',')
            : 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'approved' => 'bg-green-100 text-green-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBePaid(): bool
    {
        return $this->status === 'approved';
    }
}
