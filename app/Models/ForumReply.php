<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'forum_thread_id',
        'parent_id',
        'body',
        'is_accepted',
        'votes_count',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'votes_count' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Get the user that wrote this reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent thread.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    /**
     * Get the parent reply (for nested replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get child replies.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get all votes on this reply.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(ForumVote::class, 'votable');
    }

    /**
     * Check if the given user is the author of this reply.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
