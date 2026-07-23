<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_users',
        'new_registrations',
        'active_users',
        'total_simulations',
        'new_simulations',
        'total_views',
        'total_plays',
        'total_comments',
        'total_revenue',
        'top_categories',
    ];

    protected $casts = [
        'date' => 'date',
        'total_users' => 'integer',
        'new_registrations' => 'integer',
        'active_users' => 'integer',
        'total_simulations' => 'integer',
        'new_simulations' => 'integer',
        'total_views' => 'integer',
        'total_plays' => 'integer',
        'total_comments' => 'integer',
        'total_revenue' => 'decimal:2',
        'top_categories' => 'array',
    ];

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Get the formatted total revenue.
     */
    public function getFormattedRevenueAttribute(): string
    {
        return 'Rp '.number_format($this->total_revenue, 0, ',', '.');
    }

    /**
     * Collect and store platform analytics for a given date.
     */
    public static function collectForDate(?string $date = null): static
    {
        $date = $date ?? now()->toDateString();

        $previousDay = static::where('date', '<', $date)->latest('date')->first();

        $totalUsers = User::count();
        $totalSimulations = Simulation::count();
        $totalViews = Simulation::sum('view_count');
        $totalPlays = Simulation::sum('play_count');
        $totalRevenue = CreatorAd::where('review_status', 'approved')->sum('revenue')
            + PlatformAd::sum('revenue');

        $newRegistrations = User::whereDate('created_at', $date)->count();
        $newSimulations = Simulation::whereDate('created_at', $date)->count();
        $totalComments = Comment::whereDate('created_at', $date)->count();

        $weekAgo = now()->subDays(7);
        $activeUsers = PlayHistory::where('created_at', '>=', $weekAgo)
            ->distinct('user_id')
            ->count('user_id');

        $topCategories = Simulation::published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'category')
            ->toArray();

        return static::updateOrCreate(
            ['date' => $date],
            [
                'total_users' => $totalUsers,
                'new_registrations' => $newRegistrations,
                'active_users' => $activeUsers,
                'total_simulations' => $totalSimulations,
                'new_simulations' => $newSimulations,
                'total_views' => $totalViews,
                'total_plays' => $totalPlays,
                'total_comments' => $totalComments,
                'total_revenue' => $totalRevenue,
                'top_categories' => $topCategories,
            ]
        );
    }
}
