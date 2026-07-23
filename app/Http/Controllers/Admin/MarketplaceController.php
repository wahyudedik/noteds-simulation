<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Display marketplace listings with stats.
     */
    public function index(Request $request): View
    {
        $query = MarketplaceListing::with(['simulation', 'creator']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('simulation', fn ($q) => $q->where('title', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $listings = $query->latest()->paginate(15)->withQueryString();

        $totalListings = MarketplaceListing::count();
        $activeListings = MarketplaceListing::where('is_active', true)->count();
        $totalSales = MarketplacePurchase::where('payment_status', 'completed')->count();
        $totalRevenue = MarketplacePurchase::where('payment_status', 'completed')->sum('amount');

        return view('admin.marketplace.index', compact(
            'listings',
            'totalListings',
            'activeListings',
            'totalSales',
            'totalRevenue'
        ));
    }

    /**
     * Display a specific marketplace listing.
     */
    public function show(MarketplaceListing $listing): View
    {
        $listing->load(['simulation', 'creator']);
        $purchases = $listing->purchases()->with('user')->latest()->paginate(15);

        return view('admin.marketplace.show', compact('listing', 'purchases'));
    }

    /**
     * Toggle listing active status.
     */
    public function toggle(MarketplaceListing $listing): RedirectResponse
    {
        $listing->update(['is_active' => ! $listing->is_active]);

        return back()->with('success', 'Status listing marketplace diperbarui.');
    }
}
