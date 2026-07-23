<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmbedTrack;
use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmbedAdminController extends Controller
{
    /**
     * Display a listing of embed tracks with statistics.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', '30');
        $startDate = now()->subDays((int) $period)->toDateString();

        // Total embeds
        $totalEmbeds = EmbedTrack::count();
        $periodEmbeds = EmbedTrack::where('created_at', '>=', $startDate)->count();

        // Top embedded simulations
        $topSimulations = Simulation::withCount(['embedTracks as embed_count' => function ($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        }])
            ->having('embed_count', '>', 0)
            ->orderByDesc('embed_count')
            ->take(15)
            ->get();

        // Top referrer domains
        $topReferrers = EmbedTrack::where('created_at', '>=', $startDate)
            ->whereNotNull('referrer_domain')
            ->selectRaw('referrer_domain, COUNT(*) as count')
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Daily embed chart
        $dailyEmbeds = EmbedTrack::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $dailyEmbeds->pluck('date');
        $chartData = $dailyEmbeds->pluck('count');

        // Recent embed tracks
        $recentTracks = EmbedTrack::with('simulation')
            ->latest()
            ->paginate(15);

        return view('admin.embeds.index', compact(
            'totalEmbeds',
            'periodEmbeds',
            'topSimulations',
            'topReferrers',
            'chartLabels',
            'chartData',
            'recentTracks',
            'period'
        ));
    }
}
