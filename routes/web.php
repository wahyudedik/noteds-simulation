<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SimulationController as AdminSimulationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SimulationController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Landing page = simulation feed (like YouTube homepage)
Route::get('/', [SimulationController::class, 'index'])->name('home');

// Public simulation routes
Route::get('/explore/{category}', [SimulationController::class, 'category'])->name('simulations.category');
Route::get('/sim/{slug}', [SimulationController::class, 'show'])->name('simulations.show');
Route::get('/sim/{slug}/play', [SimulationController::class, 'play'])->name('simulations.play');
Route::get('/sim/serve/{slug}/{path?}', [SimulationController::class, 'serve'])->name('simulations.serve')->where('path', '.*');

// Public creator profile (uses ID for reliability)
Route::get('/creator/{id}', [FollowController::class, 'profile'])->name('creators.show');

// Auth routes (already registered by Breeze)

// Dashboard for regular users
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Comments (AJAX-friendly)
    Route::post('/sim/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Bookmarks (AJAX-friendly)
    Route::post('/bookmarks/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

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
});

// Admin routes (superadmin + admin)
Route::middleware(['auth', CheckRole::class.':superadmin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('simulations', AdminSimulationController::class);
    Route::post('/simulations/{simulation}/toggle-publish', [AdminSimulationController::class, 'togglePublish'])->name('simulations.toggle-publish');
});

require __DIR__.'/auth.php';
