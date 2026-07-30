<?php

namespace App\Jobs;

use App\Models\Simulation;
use App\Services\SecurityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScanSimulationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 15;

    /**
     * The time in seconds the job can run before timing out.
     */
    public int $timeout = 120;

    public function __construct(
        public int $simulationId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SecurityService $security): void
    {
        $simulation = Simulation::find($this->simulationId);

        if (! $simulation || ! $simulation->zip_path) {
            Log::info('ScanSimulationJob: simulation or zip not found', ['simulation_id' => $this->simulationId]);

            return;
        }

        $zipFullPath = storage_path('app/public/'.$simulation->zip_path);

        if (! file_exists($zipFullPath)) {
            Log::warning('ScanSimulationJob: zip file not found', [
                'simulation_id' => $this->simulationId,
                'path' => $zipFullPath,
            ]);

            return;
        }

        $scanResult = $security->autoScan($simulation, $zipFullPath);

        if ($scanResult->result === 'reject' && $simulation->is_published) {
            $simulation->update(['status' => 'pending']);
            Log::info('ScanSimulationJob: auto-pended simulation', ['simulation_id' => $this->simulationId]);
        }

        Log::info('ScanSimulationJob: scan completed', [
            'simulation_id' => $this->simulationId,
            'result' => $scanResult->result,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ScanSimulationJob: permanently failed', [
            'simulation_id' => $this->simulationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
