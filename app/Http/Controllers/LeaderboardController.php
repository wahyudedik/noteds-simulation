<?php

namespace App\Http\Controllers;

use App\Services\CreatorRankingService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(
        private GamificationService $gamification,
        private CreatorRankingService $rankingService,
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

    /**
     * Show the creator leaderboard page with sort & trending support.
     */
    public function creators(Request $request): View
    {
        $period = $request->input('period', 'all');
        $sortBy = $request->input('sort', 'ranking');

        $topCreators = $this->rankingService->getTopCreators(50, $sortBy, $period);
        $trendingCreators = $this->rankingService->getTrendingCreators(5, $period === 'all' ? 'week' : $period);

        return view('leaderboard.creators', compact('topCreators', 'trendingCreators', 'period', 'sortBy'));
    }
}
