<?php

namespace App\Console\Commands;

use App\Services\CreatorRankingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-creator-rankings')]
#[Description('Recalculate and update ranking scores for all creators')]
class UpdateCreatorRankings extends Command
{
    public function __construct(
        private CreatorRankingService $rankingService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Updating creator rankings...');

        $count = $this->rankingService->updateAllRankings();

        $this->info("Successfully updated rankings for {$count} creators.");

        return self::SUCCESS;
    }
}
