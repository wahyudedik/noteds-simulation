<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'total_simulations' => $user->simulations()->count(),
            'published' => $user->simulations()->where('is_published', true)->count(),
            'draft' => $user->simulations()->where('is_published', false)->count(),
            'total_views' => (int) $user->simulations()->sum('view_count'),
            'total_plays' => (int) $user->simulations()->sum('play_count'),
            'total_likes' => (int) $user->simulations()->sum('like_count'),
            'total_bookmarks' => (int) $user->simulations()->sum('bookmark_count'),
            'total_shares' => (int) $user->simulations()->sum('share_count'),
        ];

        $recentSimulations = $user->simulations()
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSimulations'));
    }
}
