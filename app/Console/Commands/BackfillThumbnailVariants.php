<?php

namespace App\Console\Commands;

use App\Jobs\ResizeThumbnailJob;
use App\Models\Simulation;
use Illuminate\Console\Command;

class BackfillThumbnailVariants extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backfill:thumbnail-variants
        {--dry-run : Show what would be processed without actually dispatching jobs}
        {--limit= : Maximum number of simulations to process}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatch ResizeThumbnailJob for all simulations that have a thumbnail but no variants yet';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit');

        $query = Simulation::whereNotNull('thumbnail')
            ->where(function ($q) {
                $q->whereNull('thumbnail_variants')
                    ->orWhere('thumbnail_variants', '[]')
                    ->orWhere('thumbnail_variants', '');
            });

        if ($limit) {
            $query->limit((int) $limit);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('All simulations already have thumbnail variants. Nothing to do.');

            return Command::SUCCESS;
        }

        $this->info("Found {$count} simulation(s) with thumbnail but no variants.");

        if ($dryRun) {
            $this->warn('DRY RUN — no jobs will be dispatched.');
            $this->newLine();

            $query->select('id', 'title', 'thumbnail')->each(function ($sim) {
                $this->line("  [{$sim->id}] {$sim->title} → {$sim->thumbnail}");
            });

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->select('id')->chunk(50, function ($simulations) use ($bar) {
            foreach ($simulations as $sim) {
                ResizeThumbnailJob::dispatch($sim->id);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Dispatched {$count} ResizeThumbnailJob(s). They will be processed by the queue worker.");

        return Command::SUCCESS;
    }
}
