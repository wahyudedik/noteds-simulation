<?php

namespace App\Observers;

use App\Models\Simulation;
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
        // Use raw query builder to avoid PHP 8.4 Query\Expression → int conversion error
        // (Eloquent model methods cast DB::raw() values to int, which fails in PHP 8.4)
        DB::table('simulation_analytics')->updateOrInsert(
            [
                'simulation_id' => $simulation->id,
                'date' => $today,
            ],
            [
                'views' => DB::raw("COALESCE(views, 0) + {$viewDelta}"),
                'plays' => DB::raw("COALESCE(plays, 0) + {$playDelta}"),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Upsert simulation_daily_metrics records (unique on simulation_id + date + metric_type)
        if ($viewDelta > 0) {
            DB::table('simulation_daily_metrics')->updateOrInsert(
                [
                    'simulation_id' => $simulation->id,
                    'date' => $today,
                    'metric_type' => 'view',
                ],
                [
                    'count' => DB::raw("COALESCE(`count`, 0) + {$viewDelta}"),
                    'updated_at' => now(),
                ]
            );
        }

        if ($playDelta > 0) {
            DB::table('simulation_daily_metrics')->updateOrInsert(
                [
                    'simulation_id' => $simulation->id,
                    'date' => $today,
                    'metric_type' => 'play',
                ],
                [
                    'count' => DB::raw("COALESCE(`count`, 0) + {$playDelta}"),
                    'updated_at' => now(),
                ]
            );
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
