<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'is_public',
        'view_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'view_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->title);
            }
        });
    }

    /**
     * Get the user who created this collection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the simulations in this collection.
     */
    public function simulations(): BelongsToMany
    {
        return $this->belongsToMany(Simulation::class, 'collection_simulations')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Get users who saved this collection.
     */
    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedCollection::class);
    }

    /**
     * Check if a user has saved this collection.
     */
    public function isSavedByUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->savedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Get formatted view count.
     */
    public function getFormattedViewCountAttribute(): string
    {
        return $this->formatCount($this->view_count);
    }

    /**
     * Get time ago string.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Format large numbers.
     */
    private function formatCount(int $count): string
    {
        if ($count >= 1_000_000) {
            return round($count / 1_000_000, 1).'M';
        }

        if ($count >= 1_000) {
            return round($count / 1_000, 1).'K';
        }

        return (string) $count;
    }
}
