<?php

namespace App\Jobs;

use App\Models\Simulation;
use App\Services\ThumbnailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResizeThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * The maximum number of unhandled exceptions before the job fails.
     */
    public int $maxExceptions = 3;

    public function __construct(
        public int $simulationId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ThumbnailService $thumbnailService): void
    {
        $simulation = Simulation::find($this->simulationId);

        if (! $simulation || ! $simulation->thumbnail) {
            Log::info('ResizeThumbnailJob: simulation or thumbnail not found', ['simulation_id' => $this->simulationId]);

            return;
        }

        $variants = $thumbnailService->generateVariants($simulation->thumbnail);

        if (empty($variants)) {
            Log::warning('ResizeThumbnailJob: no variants generated', ['simulation_id' => $this->simulationId]);

            return;
        }

        $simulation->update(['thumbnail_variants' => $variants]);

        Log::info('ResizeThumbnailJob: variants generated', [
            'simulation_id' => $this->simulationId,
            'variants' => array_keys($variants),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ResizeThumbnailJob: permanently failed', [
            'simulation_id' => $this->simulationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
