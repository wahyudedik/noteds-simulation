<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Badge extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'criteria',
        'points_reward',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'points_reward' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Badge $badge) {
            if (empty($badge->slug)) {
                $badge->slug = Str::slug($badge->name);
            }
        });
    }

    /**
     * Get users who have earned this badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
