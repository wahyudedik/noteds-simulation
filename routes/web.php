<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SimulationController as AdminSimulationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Landing page = simulation feed (like YouTube homepage)
Route::get('/', [SimulationController::class, 'index'])->name('home');

// Public simulation routes
Route::get('/explore/{category}', [SimulationController::class, 'category'])->name('simulations.category');
Route::get('/sim/{slug}', [SimulationController::class, 'show'])->name('simulations.show');

// Auth routes (already registered by Breeze)

// Dashboard for regular users
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes (superadmin + admin)
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':superadmin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('simulations', AdminSimulationController::class);
    Route::post('/simulations/{simulation}/toggle-publish', [AdminSimulationController::class, 'togglePublish'])->name('simulations.toggle-publish');
});

require __DIR__.'/auth.php';
