<?php

namespace App\Services;

use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceService
{
    /**
     * Create a new invoice for a sponsorship.
     */
    public function create(Sponsorship $sponsorship, float $amount, Carbon $dueDate, ?string $notes = null): SponsorshipInvoice
    {
        return SponsorshipInvoice::create([
            'sponsorship_id' => $sponsorship->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $amount,
            'status' => 'draft',
            'due_date' => $dueDate,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark an invoice as sent.
     */
    public function markSent(SponsorshipInvoice $invoice): SponsorshipInvoice
    {
        $invoice->update(['status' => 'sent']);

        return $invoice->fresh();
    }

    /**
     * Mark an invoice as paid.
     */
    public function markPaid(SponsorshipInvoice $invoice, string $paymentMethod): SponsorshipInvoice
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
        ]);

        // Deduct from sponsorship budget tracking
        $sponsorship = $invoice->sponsorship;
        $service = app(SponsorshipService::class);
        $service->deductBudget($sponsorship, (float) $invoice->amount);

        return $invoice->fresh();
    }

    /**
     * Mark an invoice as overdue.
     */
    public function markOverdue(SponsorshipInvoice $invoice): SponsorshipInvoice
    {
        $invoice->update(['status' => 'overdue']);

        return $invoice->fresh();
    }

    /**
     * Generate a unique invoice number.
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.date('Y').'-';
        $lastInvoice = SponsorshipInvoice::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix.$nextNumber;
    }

    /**
     * Get overdue invoices.
     */
    public function getOverdueInvoices(): Collection
    {
        return SponsorshipInvoice::overdue()
            ->with('sponsorship.sponsor')
            ->get();
    }

    /**
     * Get all invoices for a sponsorship.
     */
    public function getForSponsorship(Sponsorship $sponsorship): Collection
    {
        return $sponsorship->invoices()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get total paid amount for a sponsorship.
     */
    public function getTotalPaid(Sponsorship $sponsorship): float
    {
        return (float) SponsorshipInvoice::where('sponsorship_id', $sponsorship->id)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get total pending amount for a sponsorship.
     */
    public function getTotalPending(Sponsorship $sponsorship): float
    {
        return (float) SponsorshipInvoice::where('sponsorship_id', $sponsorship->id)
            ->whereIn('status', ['draft', 'sent'])
            ->sum('amount');
    }

    /**
     * Mark all overdue invoices (past due date, not yet paid).
     */
    public function markOverdueInvoices(): int
    {
        $overdue = SponsorshipInvoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->get();

        $count = 0;
        foreach ($overdue as $invoice) {
            $this->markOverdue($invoice);
            $count++;
        }

        return $count;
    }
}
