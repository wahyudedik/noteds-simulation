<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ForumVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Get the user that gave this vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (thread or reply).
     */
    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
}
