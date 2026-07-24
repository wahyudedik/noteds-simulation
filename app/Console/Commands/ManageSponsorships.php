<?php

namespace App\Console\Commands;

use App\Models\Sponsorship;
use App\Services\InvoiceService;
use App\Services\SponsorshipService;
use Illuminate\Console\Command;

class ManageSponsorships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorship:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-complete expired sponsorships and mark overdue invoices';

    public function __construct(
        protected SponsorshipService $sponsorshipService,
        protected InvoiceService $invoiceService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 1. Complete expired sponsorships
        $expiredCount = $this->sponsorshipService->pauseExpiredSponsorships();
        $this->info("Completed {$expiredCount} expired sponsorship(s).");

        // 2. Mark overdue invoices
        $overdueCount = $this->invoiceService->markOverdueInvoices();
        $this->info("Marked {$overdueCount} overdue invoice(s).");

        // 3. Pause sponsorships that exceeded budget
        $budgetPaused = 0;
        $activeSponsorships = Sponsorship::running()->get();
        foreach ($activeSponsorships as $sponsorship) {
            if ($this->sponsorshipService->isBudgetExceeded($sponsorship)) {
                $this->sponsorshipService->pause($sponsorship);
                $budgetPaused++;
            }
        }
        $this->info("Paused {$budgetPaused} sponsorship(s) due to exceeded budget.");

        return self::SUCCESS;
    }
}
