<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(
        private GamificationService $gamification,
    ) {}

    /**
     * Show the leaderboard page.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'all');

        $leaderboard = $this->gamification->getLeaderboard($period, 50);

        return view('leaderboard.index', compact('leaderboard', 'period'));
    }
}
