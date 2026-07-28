<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'thumbnail_path',
        'schema',
        'default_config',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'default_config' => 'array',
            'is_active' => 'boolean',
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
     * Projects created from this template.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(ExperienceProject::class, 'template_id');
    }

    /**
     * Get components defined in the template schema.
     */
    public function getComponents(): array
    {
        return $this->schema['components'] ?? [];
    }

    /**
     * Scope: only active templates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter by category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
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
}
