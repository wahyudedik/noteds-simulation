<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Platform-wide stats for admin/superadmin
        $stats = [
            'total_users' => User::count(),
            'total_simulations' => Simulation::count(),
            'published' => Simulation::where('is_published', true)->count(),
            'draft' => Simulation::where('is_published', false)->count(),
            'total_views' => (int) Simulation::sum('view_count'),
            'total_plays' => (int) Simulation::sum('play_count'),
            'total_likes' => (int) Simulation::sum('like_count'),
            'total_bookmarks' => (int) Simulation::sum('bookmark_count'),
            'total_shares' => (int) Simulation::sum('share_count'),
            'total_comments' => Comment::count(),
        ];

        $recentSimulations = Simulation::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentSimulations'));
    }
}
