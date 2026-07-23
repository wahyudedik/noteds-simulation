<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_method',
        'bank_name',
        'account_number',
        'account_holder',
        'paypal_email',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function hasBankDetails(): bool
    {
        return $this->bank_name !== null && $this->account_number !== null;
    }

    public function hasPaypal(): bool
    {
        return $this->paypal_email !== null;
    }

    public function isFullyConfigured(): bool
    {
        return match ($this->preferred_method) {
            'bank_transfer' => $this->hasBankDetails(),
            'paypal' => $this->hasPaypal(),
            'midtrans' => $this->hasBankDetails(),
            default => false,
        };
    }

    public function getMethodInfoAttribute(): string
    {
        return match ($this->preferred_method) {
            'bank_transfer' => "{$this->bank_name} - {$this->account_number} ({$this->account_holder})",
            'paypal' => $this->paypal_email,
            'midtrans' => "Midtrans - {$this->bank_name} {$this->account_number}",
            default => 'Belum dikonfigurasi',
        };
    }
}
