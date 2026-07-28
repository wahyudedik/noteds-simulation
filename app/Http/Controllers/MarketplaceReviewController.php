<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\MarketplaceReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceReviewController extends Controller
{
    /**
     * Store a new review for a marketplace listing.
     * Only users who have completed a purchase can review.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:marketplace_listings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $listing = MarketplaceListing::findOrFail($validated['listing_id']);

        // Check if user has a completed purchase
        $hasPurchased = MarketplacePurchase::where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->where('payment_status', 'completed')
            ->exists();

        if (! $hasPurchased) {
            return response()->json([
                'message' => 'Anda harus membeli simulasi ini terlebih dahulu untuk memberikan review.',
            ], 403);
        }

        // Check if already reviewed
        $existingReview = MarketplaceReview::where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->exists();

        if ($existingReview) {
            return response()->json([
                'message' => 'Anda sudah memberikan review untuk simulasi ini.',
            ], 422);
        }

        $review = MarketplaceReview::create([
            'user_id' => $request->user()->id,
            'listing_id' => $listing->id,
            'simulation_id' => $listing->simulation_id,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review berhasil dikirim.',
            'review' => $review->load('user'),
        ]);
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, MarketplaceReview $review): JsonResponse
    {
        // Only the review author can update
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review berhasil diperbarui.',
            'review' => $review->load('user'),
        ]);
    }

    /**
     * Delete a review.
     */
    public function destroy(Request $request, MarketplaceReview $review): JsonResponse
    {
        // Only the review author or listing owner can delete
        if ($review->user_id !== $request->user()->id && $review->listing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review berhasil dihapus.']);
    }
}
