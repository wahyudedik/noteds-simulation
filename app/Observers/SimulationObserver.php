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
        // After increment()/decrement(), Eloquent may store a Query\Expression
        // in the attribute instead of the actual integer value (PHP 8.4 cannot
        // convert Query\Expression to int). We capture the originals first, then
        // fetch fresh values from the database to compute accurate deltas.
        $originalViewCount = (int) ($simulation->getOriginal('view_count') ?? 0);
        $originalPlayCount = (int) ($simulation->getOriginal('play_count') ?? 0);

        $fresh = $simulation->fresh();
        $currentViewCount = $fresh ? (int) $fresh->view_count : $originalViewCount;
        $currentPlayCount = $fresh ? (int) $fresh->play_count : $originalPlayCount;

        $viewDelta = $currentViewCount - $originalViewCount;
        $playDelta = $currentPlayCount - $originalPlayCount;

        if ($viewDelta <= 0 && $playDelta <= 0) {
            return;
        }

        $today = Carbon::today()->toDateString();

        // Update simulation_analytics (daily aggregate)
        // Use query builder to avoid PHP 8.4 Query\Expression → int conversion error
        $existing = SimulationAnalytic::where('simulation_id', $simulation->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            $existing->update([
                'views' => DB::raw("views + {$viewDelta}"),
                'plays' => DB::raw("plays + {$playDelta}"),
            ]);
        } else {
            SimulationAnalytic::create([
                'simulation_id' => $simulation->id,
                'date' => $today,
                'views' => max(0, $viewDelta),
                'plays' => max(0, $playDelta),
            ]);
        }

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
