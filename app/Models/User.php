<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'avatar', 'bio', 'google_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if user is a creator.
     */
    public function isCreator(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'creator']);
    }

    // ─── Relationships ────────────────────────────────────────────

    /**
     * Get the simulations created by this user.
     */
    public function simulations(): HasMany
    {
        return $this->hasMany(Simulation::class);
    }

    /**
     * Get comments written by this user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get bookmarks by this user.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get ratings by this user.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get reactions by this user.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get play history for this user.
     */
    public function playHistory(): HasMany
    {
        return $this->hasMany(PlayHistory::class);
    }

    /**
     * Get notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get learning collections created by this user.
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get follows that this user has made.
     */
    public function following(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    /**
     * Get user follow records where this user is being followed.
     */
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'followable_id')
            ->where('followable_type', User::class);
    }

    /**
     * Get simulation follow records for this user's simulations.
     */
    public function simulationFollowers(): HasMany
    {
        return $this->hasManyThrough(Follow::class, Simulation::class, 'user_id', 'followable_id')
            ->where('followable_type', Simulation::class);
    }

    /**
     * Get creator reputation record.
     */
    public function reputation(): HasOne
    {
        return $this->hasOne(CreatorReputation::class);
    }

    /**
     * Get payout records.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * Get payment settings.
     */
    public function paymentSettings(): HasOne
    {
        return $this->hasOne(CreatorPaymentSetting::class);
    }

    /**
     * Check if this user follows another user.
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()
            ->where('followable_id', $user->id)
            ->where('followable_type', User::class)
            ->exists();
    }

    /**
     * Check if this user follows a simulation.
     */
    public function isFollowingSimulation(Simulation $simulation): bool
    {
        return $this->following()
            ->where('followable_id', $simulation->id)
            ->where('followable_type', Simulation::class)
            ->exists();
    }

    /**
     * Get bookmarked simulations.
     */
    public function bookmarkedSimulations(): HasManyThrough
    {
        return $this->hasManyThrough(Simulation::class, Bookmark::class);
    }

    /**
     * Get favorited simulations.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get forum threads created by this user.
     */
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    /**
     * Get forum replies written by this user.
     */
    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    /**
     * Get forum votes given by this user.
     */
    public function forumVotes(): HasMany
    {
        return $this->hasMany(ForumVote::class);
    }

    /**
     * Get saved collections.
     */
    public function savedCollections(): HasMany
    {
        return $this->hasMany(SavedCollection::class);
    }

    /**
     * Get badges earned by this user.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    /**
     * Get points log for this user.
     */
    public function pointsLog(): HasMany
    {
        return $this->hasMany(UserPointsLog::class);
    }

    /**
     * Get total points earned by this user.
     */
    public function getTotalPointsAttribute(): int
    {
        return (int) $this->pointsLog()->sum('points');
    }

    /**
     * Get current level based on points.
     */
    public function getCurrentLevelAttribute(): int
    {
        $points = $this->total_points;

        return match (true) {
            $points >= 10000 => 10,
            $points >= 5000 => 9,
            $points >= 3000 => 8,
            $points >= 2000 => 7,
            $points >= 1500 => 6,
            $points >= 1000 => 5,
            $points >= 500 => 4,
            $points >= 200 => 3,
            $points >= 50 => 2,
            default => 1,
        };
    }

    /**
     * Get the user's share count for a simulation.
     */
    public function getShareCountAttribute(): int
    {
        return $this->hasMany(Share::class)->count();
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }
}
