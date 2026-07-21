<?php

namespace App\Observers;

use App\Models\Simulation;
use App\Models\SimulationAnalytic;
use App\Models\SimulationDailyMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimulationObserver
{
    /**
     * Handle the Simulation "created" event.
     */
    public function created(Simulation $simulation): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the Simulation "updated" event.
     *
     * Logs view_count and play_count changes to analytics tables.
     */
    public function updated(Simulation $simulation): void
    {
        $this->regenerateSitemap();
        $this->logAnalyticsChanges($simulation);
    }

    /**
     * Handle the Simulation "deleted" event.
     */
    public function deleted(Simulation $simulation): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Handle the Simulation "restored" event.
     */
    public function restored(Simulation $simulation): void
    {
        $this->regenerateSitemap();
    }

    /**
     * Log view and play count changes to analytics and daily metrics tables.
     */
    private function logAnalyticsChanges(Simulation $simulation): void
    {
        $viewDelta = $simulation->view_count - ($simulation->getOriginal('view_count') ?? 0);
        $playDelta = $simulation->play_count - ($simulation->getOriginal('play_count') ?? 0);

        if ($viewDelta <= 0 && $playDelta <= 0) {
            return;
        }

        $today = Carbon::today()->toDateString();

        // Update simulation_analytics (daily aggregate)
        SimulationAnalytic::updateOrCreate(
            [
                'simulation_id' => $simulation->id,
                'date' => $today,
            ],
            [
                'views' => DB::raw("views + {$viewDelta}"),
                'plays' => DB::raw("plays + {$playDelta}"),
            ]
        );

        // Create simulation_daily_metrics records
        if ($viewDelta > 0) {
            SimulationDailyMetric::create([
                'simulation_id' => $simulation->id,
                'date' => $today,
                'metric_type' => 'view',
                'count' => $viewDelta,
            ]);
        }

        if ($playDelta > 0) {
            SimulationDailyMetric::create([
                'simulation_id' => $simulation->id,
                'date' => $today,
                'metric_type' => 'play',
                'count' => $playDelta,
            ]);
        }
    }

    /**
     * Regenerate the sitemap.xml file.
     *
     * Wrapped in try/catch to prevent sitemap generation failures
     * from crashing the main request (e.g. file permission errors on production).
     */
    private function regenerateSitemap(): void
    {
        try {
            Artisan::call('sitemap:generate');
        } catch (\Throwable $e) {
            Log::warning('Sitemap regeneration failed: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
