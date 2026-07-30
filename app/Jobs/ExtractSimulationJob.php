<?php

namespace App\Jobs;

use App\Models\Simulation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExtractSimulationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * The time in seconds the job can run before timing out.
     */
    public int $timeout = 300;

    public function __construct(
        public int $simulationId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $simulation = Simulation::find($this->simulationId);

        if (! $simulation || ! $simulation->zip_path) {
            Log::info('ExtractSimulationJob: simulation or zip not found', ['simulation_id' => $this->simulationId]);

            return;
        }

        $zipFullPath = Storage::disk('public')->path($simulation->zip_path);

        if (! file_exists($zipFullPath)) {
            Log::warning('ExtractSimulationJob: zip file not found', [
                'simulation_id' => $this->simulationId,
                'path' => $zipFullPath,
            ]);

            return;
        }

        // Derive extract path from zip_path
        $extractDir = dirname($simulation->zip_path);
        $extractPath = Storage::disk('public')->path($extractDir.'/'.$simulation->slug);

        if (is_dir($extractPath)) {
            Log::info('ExtractSimulationJob: already extracted', ['simulation_id' => $this->simulationId]);

            return;
        }

        $zip = new ZipArchive;
        if ($zip->open($zipFullPath) === true) {
            if (! is_dir($extractPath)) {
                mkdir($extractPath, 0775, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            Log::info('ExtractSimulationJob: extracted successfully', [
                'simulation_id' => $this->simulationId,
                'extract_path' => $extractPath,
            ]);
        } else {
            Log::error('ExtractSimulationJob: failed to open zip', [
                'simulation_id' => $this->simulationId,
                'zip_path' => $zipFullPath,
            ]);

            throw new \RuntimeException('Failed to open ZIP file: '.$zipFullPath);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ExtractSimulationJob: permanently failed', [
            'simulation_id' => $this->simulationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
