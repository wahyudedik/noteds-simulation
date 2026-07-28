<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Display the public marketplace browse page.
     */
    public function index(Request $request): View
    {
        $query = MarketplaceListing::query()
            ->where('is_active', true)
            ->whereHas('simulation', fn ($q) => $q->where('is_published', true))
            ->with(['simulation', 'simulation.user', 'simulation.ratings']);

        // Search by simulation title or description
        $search = $request->input('search');
        if ($search) {
            $query->whereHas('simulation', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        $activeCategory = $request->input('category');
        if ($activeCategory) {
            $query->whereHas('simulation', function ($q) use ($activeCategory) {
                $q->where('category', $activeCategory);
            });
        }

        // Filter by license type
        $activeLicense = $request->input('license');
        if ($activeLicense) {
            $query->where('license_type', $activeLicense);
        }

        // Filter by price range
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'popular' => $query->join('simulations', 'marketplace_listings.simulation_id', '=', 'simulations.id')
                ->orderByDesc('simulations.play_count')
                ->select('marketplace_listings.*'),
            'rating' => $query->orderByDesc('simulations.average_rating'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'sales' => $query->orderByDesc('total_sales'),
            default => $query->latest(),
        };

        $listings = $query->paginate(12)->withQueryString();

        // Get categories for filter sidebar
        $categories = MarketplaceListing::where('is_active', true)
            ->join('simulations', 'marketplace_listings.simulation_id', '=', 'simulations.id')
            ->selectRaw('simulations.category as category, count(*) as count')
            ->groupBy('simulations.category')
            ->orderByDesc('count')
            ->get();

        // Stats
        $totalListings = MarketplaceListing::where('is_active', true)->count();

        $licenseTypes = [
            'single' => 'Single Use',
            'institutional' => 'Institutional',
            'subscription' => 'Subscription',
        ];

        return view('marketplace.index', compact(
            'listings',
            'categories',
            'totalListings',
            'licenseTypes',
            'search',
            'activeCategory',
            'activeLicense',
            'sort'
        ));
    }

    /**
     * Display the marketplace detail page for a simulation.
     */
    public function show(string $slug): View
    {
        $simulation = Simulation::where('slug', $slug)
            ->with(['user', 'ratings.user', 'comments.user'])
            ->published()
            ->firstOrFail();

        $listing = MarketplaceListing::where('simulation_id', $simulation->id)
            ->where('is_active', true)
            ->with('purchases')
            ->firstOrFail();

        // Check if current user has purchased
        $hasPurchased = auth()->check() && $listing->isPurchasedBy(auth()->user());

        // Rating distribution
        $ratingDistribution = $simulation->ratings
            ->groupBy(fn ($r) => (int) $r->rating)
            ->map(fn ($group) => $group->count())
            ->toArray();
        $ratingCounts = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($star) => [$star => $ratingDistribution[$star] ?? 0]);

        // Related simulations (same category, active listing, exclude current)
        $relatedListings = MarketplaceListing::where('is_active', true)
            ->where('simulation_id', '!=', $simulation->id)
            ->whereHas('simulation', fn ($q) => $q->where('is_published', true)
                ->where('category', $simulation->category))
            ->with(['simulation', 'simulation.user', 'simulation.ratings'])
            ->take(4)
            ->get();

        return view('marketplace.show', compact(
            'simulation',
            'listing',
            'hasPurchased',
            'ratingCounts',
            'relatedListings'
        ));
    }
}
