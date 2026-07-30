<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformAd;
use App\Services\AdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdController extends Controller
{
    public function __construct(
        private AdService $adService,
    ) {}

    /**
     * Display a listing of platform ads.
     */
    public function index(Request $request): View
    {
        $query = PlatformAd::with('creator');

        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $ads = $query->latest()->paginate(15)->withQueryString();
        $stats = $this->adService->getStats();

        return view('admin.ads.index', compact('ads', 'stats'));
    }

    /**
     * Show the form for creating a new ad.
     */
    public function create(): View
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created ad.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner,interstitial,video,native,adsense',
            'ad_network' => 'nullable|in:adsense,monetag,propellerads,media_net,adsterra,ezoic',
            'position' => 'required|in:header,sidebar,pre_roll,mid_roll,post_simulation,feed_sponsored,search_sponsored',
            'content' => 'nullable|string|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:512',
            'target_url' => 'nullable|url|max:500',
            'adsense_publisher_id' => 'nullable|string|max:100',
            'adsense_ad_slot' => 'nullable|string|max:100',
            'weight' => 'nullable|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'ad_network' => $validated['ad_network'] ?? null,
            'position' => $validated['position'],
            'content' => $validated['content'] ?? null,
            'target_url' => $validated['target_url'] ?? null,
            'adsense_publisher_id' => $validated['adsense_publisher_id'] ?? null,
            'adsense_ad_slot' => $validated['adsense_ad_slot'] ?? null,
            'weight' => $validated['weight'] ?? 1,
            'is_active' => $request->boolean('is_active', true),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'created_by' => Auth::id(),
        ];

        // Store ad_network_config (zone_id, slot_id, etc.)
        $networkConfig = $this->extractNetworkConfig($request);
        if ($networkConfig) {
            $data['ad_network_config'] = $networkConfig;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        PlatformAd::create($data);

        return redirect()->route('admin.ads.index')
            ->with('success', 'Iklan berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(PlatformAd $ad): View
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified ad.
     */
    public function update(Request $request, PlatformAd $ad): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:banner,interstitial,video,native,adsense',
            'ad_network' => 'nullable|in:adsense,monetag,propellerads,media_net,adsterra,ezoic',
            'position' => 'required|in:header,sidebar,pre_roll,mid_roll,post_simulation,feed_sponsored,search_sponsored',
            'content' => 'nullable|string|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:512',
            'target_url' => 'nullable|url|max:500',
            'adsense_publisher_id' => 'nullable|string|max:100',
            'adsense_ad_slot' => 'nullable|string|max:100',
            'weight' => 'nullable|integer|min:1|max:100',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'ad_network' => $validated['ad_network'] ?? null,
            'position' => $validated['position'],
            'content' => $validated['content'] ?? null,
            'target_url' => $validated['target_url'] ?? null,
            'adsense_publisher_id' => $validated['adsense_publisher_id'] ?? null,
            'adsense_ad_slot' => $validated['adsense_ad_slot'] ?? null,
            'weight' => $validated['weight'] ?? 1,
            'is_active' => $request->boolean('is_active', true),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ];

        $networkConfig = $this->extractNetworkConfig($request);
        if ($networkConfig !== null) {
            $data['ad_network_config'] = $networkConfig;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')
            ->with('success', 'Iklan berhasil diupdate.');
    }

    /**
     * Remove the specified ad.
     */
    public function destroy(PlatformAd $ad): RedirectResponse
    {
        $ad->delete();

        return redirect()->route('admin.ads.index')
            ->with('success', 'Iklan berhasil dihapus.');
    }

    /**
     * Toggle active status of an ad.
     */
    public function toggle(PlatformAd $ad): RedirectResponse
    {
        $ad->update(['is_active' => ! $ad->is_active]);

        $status = $ad->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.ads.index')
            ->with('success', "Iklan berhasil {$status}.");
    }

    /**
     * Extract ad network configuration from request (zone_id, slot_id, etc.).
     */
    private function extractNetworkConfig(Request $request): ?array
    {
        $network = $request->input('ad_network');

        if (! $network) {
            return null;
        }

        $config = [];
        $zoneId = $request->input('zone_id');
        $slotId = $request->input('slot_id');

        if ($zoneId) {
            $config['zone_id'] = $zoneId;
        }

        if ($slotId) {
            $config['slot_id'] = $slotId;
        }

        return $config ?: null;
    }
}
