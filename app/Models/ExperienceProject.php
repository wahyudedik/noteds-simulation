<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ExperienceProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'description',
        'config',
        'status',
        'version',
        'slug',
        'thumbnail_path',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'version' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the route key name for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The user who owns this project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The template this project was created from (nullable).
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ExperienceTemplate::class, 'template_id');
    }

    /**
     * Get the simulation created from this project (nullable).
     */
    public function simulation(): HasOne
    {
        return $this->hasOne(Simulation::class);
    }

    /**
     * Check if this project has been published to the platform.
     */
    public function hasSimulation(): bool
    {
        return $this->simulation()->exists();
    }

    /**
     * Get the public URL for this project's simulation.
     */
    public function getSimulationUrl(): ?string
    {
        $simulation = $this->simulation;

        return $simulation ? route('simulations.show', $simulation->slug) : null;
    }

    /**
     * Check if project is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if project is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the project.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Archive the project.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Get components from config.
     */
    public function getComponents(): array
    {
        return $this->config['components'] ?? [];
    }

    /**
     * Get the status badge CSS class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            'draft' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'archived' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path
            ? asset('storage/'.$this->thumbnail_path)
            : null;
    }

    /**
     * Scope: filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: only published projects.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Boot the model — auto-generate slug from title.
     */
    protected static function booted(): void
    {
        static::creating(function (ExperienceProject $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title).'-'.Str::random(5);
            }
        });
    }
}
