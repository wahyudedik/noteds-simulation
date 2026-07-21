<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'comment_count',
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
        'comment_count' => 'integer',
        'average_rating' => 'float',
        'rating_count' => 'integer',
        'published_at' => 'datetime',
    ];

    // ─── Boot ─────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Simulation $simulation) {
            if (empty($simulation->slug)) {
                $simulation->slug = Str::slug($simulation->title);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Get the user (creator) that owns this simulation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get comments for this simulation.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get ratings for this simulation.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get reactions for this simulation.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get bookmarks for this simulation.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get play history for this simulation.
     */
    public function playHistory(): HasMany
    {
        return $this->hasMany(PlayHistory::class);
    }

    /**
     * Get users who follow this simulation.
     */
    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    /**
     * Get users who favorited this simulation.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get shares for this simulation.
     */
    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    /**
     * Get tags for this simulation.
     */
    public function tagModels(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'simulation_tags');
    }

    /**
     * Get versions of this simulation.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(SimulationVersion::class);
    }

    /**
     * Get daily analytics for this simulation.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(SimulationAnalytic::class);
    }

    // ─── Accessors ────────────────────────────────────────────────

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

    // ─── Scopes ───────────────────────────────────────────────────

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

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Check if a user has bookmarked this simulation.
     */
    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user has favorited this simulation.
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user has rated this simulation.
     */
    public function getRatingBy(?User $user): ?Rating
    {
        if (! $user) {
            return null;
        }

        return $this->ratings()->where('user_id', $user->id)->first();
    }

    /**
     * Get the user's reactions for this simulation.
     *
     * @return Collection
     */
    public function getReactionsBy(?User $user)
    {
        if (! $user) {
            return collect();
        }

        return $this->reactions()->where('user_id', $user->id)->get();
    }

    /**
     * Get reaction counts grouped by type.
     *
     * @return array<string, int>
     */
    public function getReactionCountsAttribute(): array
    {
        return $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Format a number to human-readable string.
     */
    private function formatCount(int $count): string
    {
        if ($count >= 1_000_000) {
            return number_format($count / 1_000_000, 1).'jt';
        }
        if ($count >= 1_000) {
            return number_format($count / 1_000, 1).'rb';
        }

        return (string) $count;
    }
}
