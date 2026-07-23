<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorAd;
use App\Models\CreatorReputation;
use App\Models\User;
use App\Services\AdRevenueService;
use App\Services\CreatorReputationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorController extends Controller
{
    public function __construct(
        private CreatorReputationService $reputationService,
        private AdRevenueService $revenueService,
    ) {}

    /**
     * Display a listing of all creators.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'creator')
            ->withCount('simulations')
            ->with('reputation');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tier')) {
            $query->whereHas('reputation', function ($q) use ($request) {
                $q->where('revenue_tier', $request->input('tier'));
            });
        }

        if ($request->boolean('suspended')) {
            $query->whereHas('reputation', function ($q) {
                $q->where('score', '<', 20);
            });
        }

        $creators = $query->latest()->paginate(20)->withQueryString();

        return view('admin.creators.index', compact('creators'));
    }

    /**
     * Display the specified creator's detail.
     */
    public function show(User $creator): View
    {
        $creator->load('reputation', 'badges');

        $stats = [
            'simulations' => $creator->simulations()->count(),
            'published' => $creator->simulations()->where('is_published', true)->count(),
            'total_views' => $creator->simulations()->sum('view_count'),
            'total_plays' => $creator->simulations()->sum('play_count'),
            'avg_rating' => $creator->simulations()->avg('average_rating') ?? 0,
            'followers' => $creator->followers()->count(),
            'total_ad_revenue' => CreatorAd::where('user_id', $creator->id)
                ->where('review_status', 'approved')
                ->sum('revenue'),
            'total_ads' => CreatorAd::where('user_id', $creator->id)->count(),
            'approved_ads' => CreatorAd::where('user_id', $creator->id)
                ->where('review_status', 'approved')
                ->count(),
            'pending_ads' => CreatorAd::where('user_id', $creator->id)
                ->where('review_status', 'pending_review')
                ->count(),
        ];

        $recentSimulations = $creator->simulations()
            ->latest()
            ->take(5)
            ->get();

        $revenueTiers = $this->revenueService->getTiers();

        return view('admin.creators.show', compact('creator', 'stats', 'recentSimulations', 'revenueTiers'));
    }

    /**
     * Update creator reputation score.
     */
    public function updateReputation(Request $request, User $creator): RedirectResponse
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $reputation = CreatorReputation::firstOrCreate(
            ['user_id' => $creator->id],
            [
                'score' => 100,
                'total_uploads' => 0,
                'approved_count' => 0,
                'rejected_count' => 0,
                'flagged_count' => 0,
                'reports_received' => 0,
                'revenue_tier' => 'basic',
                'total_revenue' => 0,
            ]
        );

        $oldScore = $reputation->score;
        $reputation->update(['score' => $validated['score']]);

        return redirect()->route('admin.creators.show', $creator)
            ->with('success', "Reputasi {$creator->name} berhasil diubah dari {$oldScore} menjadi {$validated['score']}.");
    }

    /**
     * Toggle suspend/unsuspend a creator.
     */
    public function toggleSuspend(User $creator): RedirectResponse
    {
        $reputation = CreatorReputation::where('user_id', $creator->id)->first();

        if (! $reputation) {
            return redirect()->back()->with('error', 'Creator tidak memiliki data reputasi.');
        }

        if ($reputation->score >= 20) {
            // Suspend: set score to 15 (below 20 threshold)
            $reputation->update(['score' => 15]);

            return redirect()->route('admin.creators.show', $creator)
                ->with('success', "Creator {$creator->name} berhasil ditangguhkan.");
        }

        // Unsuspend: set score to 50 (above 20 threshold)
        $reputation->update(['score' => 50]);

        return redirect()->route('admin.creators.show', $creator)
            ->with('success', "Creator {$creator->name} berhasil diaktifkan kembali.");
    }
}
