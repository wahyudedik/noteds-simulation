<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'structured_data',
        'updated_by',
    ];

    protected $casts = [
        'structured_data' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Find SEO settings by page key.
     */
    public static function findByKey(string $pageKey): ?static
    {
        return static::where('page_key', $pageKey)->first();
    }

    /**
     * Get the display label for the page key.
     */
    public function getPageLabelAttribute(): string
    {
        $labels = [
            'home' => 'Beranda',
            'explore' => 'Explore',
            'leaderboard' => 'Leaderboard',
            'login' => 'Login',
            'register' => 'Register',
        ];

        if (isset($labels[$this->page_key])) {
            return $labels[$this->page_key];
        }

        if (str_starts_with($this->page_key, 'simulation:')) {
            $slug = str_replace('simulation:', '', $this->page_key);

            return 'Simulasi: '.$slug;
        }

        if (str_starts_with($this->page_key, 'category:')) {
            $cat = str_replace('category:', '', $this->page_key);

            return 'Kategori: '.ucfirst($cat);
        }

        if (str_starts_with($this->page_key, 'creator:')) {
            return 'Creator: '.str_replace('creator:', '', $this->page_key);
        }

        return ucfirst(str_replace(':', ' > ', $this->page_key));
    }
}
