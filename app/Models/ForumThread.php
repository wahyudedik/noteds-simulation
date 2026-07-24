<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forum_category_id',
        'simulation_id',
        'title',
        'slug',
        'body',
        'is_pinned',
        'is_locked',
        'is_solved',
        'views_count',
        'replies_count',
        'votes_count',
        'last_reply_at',
        'last_reply_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'votes_count' => 'integer',
        'last_reply_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ForumThread $thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Get the user that created this thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this thread belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    /**
     * Get the linked simulation (optional).
     */
    public function simulation(): BelongsTo
    {
        return $this->belongsTo(Simulation::class);
    }

    /**
     * Get replies for this thread.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get top-level replies only.
     */
    public function topLevelReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get all votes on this thread.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(ForumVote::class, 'votable');
    }

    /**
     * Get the last reply user.
     */
    public function lastReplyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /**
     * Scope: pinned threads first, then by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('last_reply_at')->orderByDesc('created_at');
    }

    /**
     * Scope: most voted.
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('votes_count')->orderByDesc('replies_count');
    }

    /**
     * Scope: unanswered threads.
     */
    public function scopeUnanswered($query)
    {
        return $query->where('replies_count', 0);
    }

    /**
     * Check if the given user is the author of this thread.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
