<?php

namespace App\Providers;

use App\Models\Simulation;
use App\Observers\SimulationObserver;
use App\Services\GamificationService;
use App\View\Composers\SeoComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GamificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', SeoComposer::class);

        Simulation::observe(SimulationObserver::class);
    }
}
