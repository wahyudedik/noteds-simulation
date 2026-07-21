<?php

namespace App\Observers;

use App\Models\Simulation;
use Illuminate\Support\Facades\Artisan;

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
     */
    public function updated(Simulation $simulation): void
    {
        $this->regenerateSitemap();
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
     * Regenerate the sitemap.xml file.
     */
    private function regenerateSitemap(): void
    {
        Artisan::call('sitemap:generate');
    }
}
