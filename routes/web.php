<?php

use App\Http\Controllers\Admin\AdAnalyticsController as AdminAdAnalyticsController;
use App\Http\Controllers\Admin\AdController as AdminAdController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CertificationController as AdminCertificationController;
use App\Http\Controllers\Admin\ChallengeController as AdminChallengeController;
use App\Http\Controllers\Admin\CreatorAdController as AdminCreatorAdController;
use App\Http\Controllers\Admin\CreatorController as AdminCreatorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmbedAdminController as AdminEmbedController;
use App\Http\Controllers\Admin\ErrorLogController as AdminErrorLogController;
use App\Http\Controllers\Admin\MarketplaceController as AdminMarketplaceController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ScanController as AdminScanController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SimulationController as AdminSimulationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdTrackingController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SavedCollectionController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserReportController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Health Check endpoint
Route::get('/health', HealthController::class)->name('health');

// Landing page = simulation feed (like YouTube homepage)
Route::get('/', [SimulationController::class, 'index'])->name('home');

// Leaderboard (public)
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

// AJAX Search API
Route::get('/api/search', [SimulationController::class, 'search'])->name('simulations.search');

// Explore / Discover page
Route::get('/explore', [SimulationController::class, 'explore'])->name('simulations.explore');

// Public simulation routes
Route::get('/explore/{category}', [SimulationController::class, 'category'])->name('simulations.category');
Route::get('/sim/{slug}', [SimulationController::class, 'show'])->name('simulations.show');
Route::get('/sim/{slug}/play', [SimulationController::class, 'play'])->name('simulations.play');
Route::get('/sim/serve/{slug}/{path?}', [SimulationController::class, 'serve'])->name('simulations.serve')->where('path', '.*');

// Simulation Embed (public)
Route::get('/embed/{slug}', [EmbedController::class, 'show'])->name('embed.show');
Route::get('/embed/{slug}/code', [EmbedController::class, 'code'])->name('embed.code');

// Public creator profile (uses ID for reliability)
Route::get('/creator/{id}', [FollowController::class, 'profile'])->name('creators.show');

// Auth routes (already registered by Breeze)

// Dashboard for regular users
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Become Creator
    Route::post('/become-creator', [DashboardController::class, 'becomeCreator'])->name('become-creator');

    // User Profile (My Profile with tabs)
    Route::get('/my-profile', [UserProfileController::class, 'index'])->name('user-profile.index');
    Route::get('/my-profile/{tab}', [UserProfileController::class, 'index'])->name('user-profile.tab');

    // Comments (AJAX-friendly)
    Route::post('/sim/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Bookmarks (AJAX-friendly)
    Route::post('/bookmarks/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

    // Favorites (AJAX-friendly)
    Route::post('/favorites/{simulationId}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Share tracking (AJAX-friendly)
    Route::post('/sim/{simulationId}/share', [ShareController::class, 'track'])->name('simulations.share');

    // Reactions (AJAX-friendly)
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Ratings (AJAX-friendly)
    Route::post('/ratings/store', [RatingController::class, 'store'])->name('ratings.store');

    // Follow/Unfollow creator
    Route::post('/follows/{id}/toggle', [FollowController::class, 'toggle'])->name('follows.toggle');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Collections
    Route::get('/my-collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('/my-collections/create', [CollectionController::class, 'create'])->name('collections.create');
    Route::post('/my-collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::get('/my-collections/{collection}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
    Route::patch('/my-collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
    Route::delete('/my-collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::post('/collections/add-simulation', [CollectionController::class, 'addSimulation'])->name('collections.add-simulation');
    Route::post('/collections/remove-simulation', [CollectionController::class, 'removeSimulation'])->name('collections.remove-simulation');
    Route::get('/collections/search-simulations', [CollectionController::class, 'searchSimulations'])->name('collections.search-simulations');

    // Saved Collections (save/unsave other users' collections)
    Route::post('/saved-collections/{collectionId}/toggle', [SavedCollectionController::class, 'toggle'])->name('saved-collections.toggle');
    Route::get('/my-saved-collections', [SavedCollectionController::class, 'index'])->name('saved-collections.index');

    // User Reports
    Route::post('/sim/{slug}/report', [UserReportController::class, 'store'])->name('reports.store');
});

// Public collection view

// ========== Simulation Studio (Creator Side) ==========
Route::middleware(['auth', 'verified'])->prefix('studio')->name('studio.')->group(function () {
    // Dashboard
    Route::get('/', [StudioController::class, 'dashboard'])->name('dashboard');

    // Simulation CRUD
    Route::get('/simulations', [StudioController::class, 'simulations'])->name('simulations');
    Route::get('/simulations/create', [StudioController::class, 'create'])->name('simulations.create');
    Route::post('/simulations', [StudioController::class, 'store'])->name('simulations.store');
    Route::get('/simulations/{slug}/edit', [StudioController::class, 'edit'])->name('simulations.edit');
    Route::put('/simulations/{slug}', [StudioController::class, 'update'])->name('simulations.update');
    Route::delete('/simulations/{slug}', [StudioController::class, 'destroy'])->name('simulations.destroy');

    // Versioning & Analytics
    Route::get('/simulations/{slug}/versions', [StudioController::class, 'versions'])->name('simulations.versions');
    Route::get('/simulations/{slug}/analytics', [StudioController::class, 'analytics'])->name('simulations.analytics');

    // Comments Moderation
    Route::get('/comments', [StudioController::class, 'comments'])->name('comments');
    Route::post('/comments/{commentId}/reply', [StudioController::class, 'replyComment'])->name('comments.reply');
    Route::post('/comments/{commentId}/pin', [StudioController::class, 'togglePinComment'])->name('comments.pin');
    Route::delete('/comments/{commentId}', [StudioController::class, 'destroyComment'])->name('comments.destroy');

    // Followers
    Route::get('/followers', [StudioController::class, 'followers'])->name('followers');

    // Settings
    Route::get('/settings', [StudioController::class, 'settings'])->name('settings');
    Route::put('/settings', [StudioController::class, 'updateSettings'])->name('settings.update');

    // Creator Ads (Monetisasi)
    Route::get('/simulations/{slug}/ads', [StudioController::class, 'showAdSettings'])->name('simulations.ads');
    Route::post('/simulations/{slug}/ads', [StudioController::class, 'storeAd'])->name('simulations.ads.store');
    Route::delete('/simulations/{slug}/ads/{creatorAd}', [StudioController::class, 'destroyAd'])->name('simulations.ads.destroy');
    Route::get('/ads/revenue', [StudioController::class, 'adRevenue'])->name('ads-revenue');

    // Payouts
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts');
    Route::post('/payouts/request', [PayoutController::class, 'requestPayout'])->name('payouts.request');
    Route::get('/payment-settings', [PayoutController::class, 'paymentSettings'])->name('payment-settings');
    Route::put('/payment-settings', [PayoutController::class, 'updatePaymentSettings'])->name('payment-settings.update');
});

// Public collection view
Route::get('/collection/{slug}', [CollectionController::class, 'show'])->name('collections.show');

// Admin routes (superadmin + admin)
Route::middleware(['auth', CheckRole::class.':superadmin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('simulations', AdminSimulationController::class);
    Route::post('/simulations/{simulation}/toggle-publish', [AdminSimulationController::class, 'togglePublish'])->name('simulations.toggle-publish');
    Route::post('/simulations/{simulation}/toggle-featured', [AdminSimulationController::class, 'toggleFeatured'])->name('simulations.toggle-featured');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/approve-creator', [AdminUserController::class, 'approveCreator'])->name('users.approve-creator');
    Route::patch('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/review', [AdminReportController::class, 'review'])->name('reports.review');
    Route::post('/reports/bulk-action', [AdminReportController::class, 'bulkAction'])->name('reports.bulk-action');

    // Security Scans
    Route::get('/scans', [AdminScanController::class, 'index'])->name('scans.index');
    Route::get('/scans/{log}', [AdminScanController::class, 'show'])->name('scans.show');
    Route::post('/scans/{simulation}/auto-scan', [AdminScanController::class, 'autoScan'])->name('scans.auto-scan');
    Route::post('/scans/{simulation}/manual-review', [AdminScanController::class, 'manualReview'])->name('scans.manual-review');

    // Error Logs
    Route::get('/logs', [AdminErrorLogController::class, 'index'])->name('logs.index');
    Route::post('/logs/clear', [AdminErrorLogController::class, 'clear'])->name('logs.clear');
    Route::get('/logs/download', [AdminErrorLogController::class, 'download'])->name('logs.download');
    Route::get('/logs/{id}', [AdminErrorLogController::class, 'show'])->name('logs.show')->where('id', '[0-9]+');

    // Platform Ads Management
    Route::get('/ads', [AdminAdController::class, 'index'])->name('ads.index');
    Route::get('/ads/create', [AdminAdController::class, 'create'])->name('ads.create');
    Route::post('/ads', [AdminAdController::class, 'store'])->name('ads.store');
    Route::get('/ads/{ad}/edit', [AdminAdController::class, 'edit'])->name('ads.edit');
    Route::put('/ads/{ad}', [AdminAdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{ad}', [AdminAdController::class, 'destroy'])->name('ads.destroy');
    Route::post('/ads/{ad}/toggle', [AdminAdController::class, 'toggle'])->name('ads.toggle');

    // Creator Ads Review
    Route::get('/creator-ads', [AdminCreatorAdController::class, 'index'])->name('creator-ads.index');
    Route::get('/creator-ads/{creatorAd}', [AdminCreatorAdController::class, 'show'])->name('creator-ads.show');
    Route::post('/creator-ads/{creatorAd}/approve', [AdminCreatorAdController::class, 'approve'])->name('creator-ads.approve');
    Route::post('/creator-ads/{creatorAd}/reject', [AdminCreatorAdController::class, 'reject'])->name('creator-ads.reject');
    Route::post('/creator-ads/{creatorAd}/flag', [AdminCreatorAdController::class, 'flag'])->name('creator-ads.flag');

    // Creator Management
    Route::get('/creators', [AdminCreatorController::class, 'index'])->name('creators.index');
    Route::get('/creators/{creator}', [AdminCreatorController::class, 'show'])->name('creators.show');
    Route::put('/creators/{creator}/reputation', [AdminCreatorController::class, 'updateReputation'])->name('creators.update-reputation');
    Route::post('/creators/{creator}/toggle-suspend', [AdminCreatorController::class, 'toggleSuspend'])->name('creators.toggle-suspend');

    // Payout Management
    Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/{payout}', [AdminPayoutController::class, 'show'])->name('payouts.show');
    Route::post('/payouts/{payout}/approve', [AdminPayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{payout}/mark-paid', [AdminPayoutController::class, 'markPaid'])->name('payouts.mark-paid');
    Route::post('/payouts/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('payouts.reject');

    // SEO Management
    Route::get('/seo', [AdminSeoController::class, 'index'])->name('seo.index');
    Route::get('/seo/create', [AdminSeoController::class, 'create'])->name('seo.create');
    Route::post('/seo', [AdminSeoController::class, 'store'])->name('seo.store');
    Route::get('/seo/{seo}/edit', [AdminSeoController::class, 'edit'])->name('seo.edit');
    Route::put('/seo/{seo}', [AdminSeoController::class, 'update'])->name('seo.update');
    Route::delete('/seo/{seo}', [AdminSeoController::class, 'destroy'])->name('seo.destroy');

    // Platform Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

    // Ad Analytics
    Route::get('/ad-analytics', [AdminAdAnalyticsController::class, 'index'])->name('ad-analytics.index');

    // Embed Management
    Route::get('/embeds', [AdminEmbedController::class, 'index'])->name('embeds.index');

    // Marketplace Management
    Route::get('/marketplace', [AdminMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/{listing}', [AdminMarketplaceController::class, 'show'])->name('marketplace.show');
    Route::post('/marketplace/{listing}/toggle', [AdminMarketplaceController::class, 'toggle'])->name('marketplace.toggle');

    // Challenge Management
    Route::get('/challenges', [AdminChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/create', [AdminChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [AdminChallengeController::class, 'store'])->name('challenges.store');
    Route::get('/challenges/{challenge}', [AdminChallengeController::class, 'show'])->name('challenges.show');
    Route::get('/challenges/{challenge}/edit', [AdminChallengeController::class, 'edit'])->name('challenges.edit');
    Route::put('/challenges/{challenge}', [AdminChallengeController::class, 'update'])->name('challenges.update');
    Route::delete('/challenges/{challenge}', [AdminChallengeController::class, 'destroy'])->name('challenges.destroy');
    Route::post('/challenges/{challenge}/entries/{entry}/score', [AdminChallengeController::class, 'scoreEntry'])->name('challenges.score-entry');

    // Certification Management
    Route::get('/certifications', [AdminCertificationController::class, 'index'])->name('certifications.index');
    Route::post('/certifications/award', [AdminCertificationController::class, 'award'])->name('certifications.award');
    Route::patch('/certifications/{certification}/revoke', [AdminCertificationController::class, 'revoke'])->name('certifications.revoke');
});

// ========== Ad Tracking API (AJAX) ==========
Route::post('/api/ads/impression', [AdTrackingController::class, 'recordImpression'])->middleware('auth')->name('api.ads.impression');
Route::post('/api/ads/click', [AdTrackingController::class, 'recordClick'])->middleware('auth')->name('api.ads.click');

require __DIR__.'/auth.php';
