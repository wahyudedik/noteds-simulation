<?php

namespace App\Http\Controllers;

use App\Models\AdImpression;
use App\Models\CreatorAd;
use App\Models\PlatformAd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdTrackingController extends Controller
{
    /**
     * Record an ad impression (platform or creator ad).
     */
    public function recordImpression(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ad_type' => 'required|in:platform,creator',
            'ad_id' => 'required|integer',
            'position' => 'nullable|string|max:50',
            'simulation_id' => 'nullable|integer',
        ]);

        AdImpression::create([
            'ad_type' => $validated['ad_type'],
            'ad_id' => $validated['ad_id'],
            'simulation_id' => $validated['simulation_id'] ?? null,
            'user_id' => auth()->id(),
            'position' => $validated['position'] ?? null,
            'clicked' => false,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment impressions counter on the ad itself
        if ($validated['ad_type'] === 'platform') {
            PlatformAd::where('id', $validated['ad_id'])->increment('impressions');
        } elseif ($validated['ad_type'] === 'creator') {
            CreatorAd::where('id', $validated['ad_id'])->increment('impressions');
        }

        return response()->json(['success' => true]);
    }

    /**
     * Record an ad click (platform or creator ad).
     */
    public function recordClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ad_type' => 'required|in:platform,creator',
            'ad_id' => 'required|integer',
        ]);

        AdImpression::where('ad_type', $validated['ad_type'])
            ->where('ad_id', $validated['ad_id'])
            ->where('clicked', false)
            ->latest()
            ->first()
            ?->update(['clicked' => true]);

        // Increment clicks counter on the ad itself
        if ($validated['ad_type'] === 'platform') {
            PlatformAd::where('id', $validated['ad_id'])->increment('clicks');
        } elseif ($validated['ad_type'] === 'creator') {
            CreatorAd::where('id', $validated['ad_id'])->increment('clicks');
        }

        return response()->json(['success' => true]);
    }
}
