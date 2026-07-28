<?php

namespace App\Console\Commands;

use App\Services\PlayRevenueService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:calculate-play-revenue')]
#[Description('Calculate and update play revenue for all creators based on ad impressions')]
class CalculatePlayRevenue extends Command
{
    public function __construct(
        private PlayRevenueService $revenueService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Calculating play revenue...');

        $count = $this->revenueService->processDailyRevenue();

        $this->info("Successfully updated revenue for {$count} creators.");

        return self::SUCCESS;
    }
}
