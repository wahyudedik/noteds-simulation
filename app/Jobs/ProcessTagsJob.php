<?php

namespace App\Jobs;

use App\Models\Simulation;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessTagsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 5;

    public function __construct(
        public int $simulationId,
        public string $tagString,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $simulation = Simulation::find($this->simulationId);

        if (! $simulation) {
            Log::info('ProcessTagsJob: simulation not found', ['simulation_id' => $this->simulationId]);

            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $this->tagString)));

        foreach ($tagNames as $tagName) {
            $tagName = Str::limit($tagName, 255, '');
            $tagSlug = Str::limit(Str::slug($tagName), 255, '');

            if (empty($tagSlug)) {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['slug' => $tagSlug],
                ['name' => $tagName]
            );

            $simulation->tagModels()->syncWithoutDetaching($tag->id);
        }

        Log::info('ProcessTagsJob: tags processed', [
            'simulation_id' => $this->simulationId,
            'count' => count($tagNames),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessTagsJob: permanently failed', [
            'simulation_id' => $this->simulationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
