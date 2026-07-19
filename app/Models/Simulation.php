<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'category',
        'subcategory',
        'tags',
        'thumbnail',
        'version',
        'zip_path',
        'entry_point',
        'is_published',
        'is_featured',
        'play_count',
        'view_count',
        'like_count',
        'bookmark_count',
        'share_count',
        'average_rating',
        'rating_count',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'play_count' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'bookmark_count' => 'integer',
        'share_count' => 'integer',
        'average_rating' => 'float',
        'rating_count' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user (creator) that owns this simulation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Auto-generate slug from title if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Simulation $simulation) {
            if (empty($simulation->slug)) {
                $simulation->slug = Str::slug($simulation->title);
            }
        });
    }

    /**
     * Get the tags as an array.
     *
     * @return array<string>
     */
    public function getTagsArrayAttribute(): array
    {
        return $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
    }

    /**
     * Get the simulation URL.
     */
    public function getUrlAttribute(): string
    {
        return route('simulations.show', $this->slug);
    }

    /**
     * Get the formatted play count.
     */
    public function getFormattedPlayCountAttribute(): string
    {
        return $this->formatCount($this->play_count);
    }

    /**
     * Get the formatted view count.
     */
    public function getFormattedViewCountAttribute(): string
    {
        return $this->formatCount($this->view_count);
    }

    /**
     * Get the time ago string.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->published_at?->diffForHumans() ?? $this->created_at->diffForHumans();
    }

    /**
     * Scope: only published simulations.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: featured simulations.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: by category.
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: search by title or tags.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('tags', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%");
        });
    }

    /**
     * Format a number to human-readable string.
     */
    private function formatCount(int $count): string
    {
        if ($count >= 1_000_000) {
            return number_format($count / 1_000_000, 1) . 'jt';
        }
        if ($count >= 1_000) {
            return number_format($count / 1_000, 1) . 'rb';
        }
        return (string) $count;
    }
}
