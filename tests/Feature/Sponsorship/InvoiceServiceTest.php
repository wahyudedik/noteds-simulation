<?php

use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use App\Services\InvoiceService;

test('can create an invoice for a sponsorship', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    $invoice = $service->create($sponsorship, 5000000, now()->addDays(14), 'Pembayaran bulan pertama');

    expect($invoice)->toBeInstanceOf(SponsorshipInvoice::class)
        ->and($invoice->sponsorship_id)->toBe($sponsorship->id)
        ->and($invoice->amount)->toBe('5000000.00')
        ->and($invoice->status)->toBe('draft')
        ->and($invoice->notes)->toBe('Pembayaran bulan pertama');

    $this->assertDatabaseHas('sponsorship_invoices', [
        'sponsorship_id' => $sponsorship->id,
        'status' => 'draft',
    ]);
});

test('invoice number is generated sequentially', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    $invoice1 = $service->create($sponsorship, 1000000, now()->addDays(14));
    $invoice2 = $service->create($sponsorship, 2000000, now()->addDays(28));

    $year = date('Y');
    expect($invoice1->invoice_number)->toBe("INV-{$year}-0001")
        ->and($invoice2->invoice_number)->toBe("INV-{$year}-0002");
});

test('can mark invoice as sent', function () {
    $service = app(InvoiceService::class);
    $invoice = SponsorshipInvoice::factory()->draft()->create();

    $sent = $service->markSent($invoice);

    expect($sent->status)->toBe('sent');
});

test('can mark invoice as paid', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->withBudget(10000000)->create(['spent' => 0]);
    $invoice = SponsorshipInvoice::factory()->sent()->withAmount(5000000)->create([
        'sponsorship_id' => $sponsorship->id,
    ]);

    $paid = $service->markPaid($invoice, 'bank_transfer');

    expect($paid->status)->toBe('paid')
        ->and($paid->payment_method)->toBe('bank_transfer')
        ->and($paid->paid_at)->not->toBeNull();

    // Verify budget was deducted
    $sponsorship->refresh();
    expect((float) $sponsorship->spent)->toBe(5000000.0);
});

test('can mark invoice as overdue', function () {
    $service = app(InvoiceService::class);
    $invoice = SponsorshipInvoice::factory()->sent()->create();

    $overdue = $service->markOverdue($invoice);

    expect($overdue->status)->toBe('overdue');
});

test('getForSponsorship returns all invoices for a sponsorship', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    $invoice1 = $service->create($sponsorship, 1000000, now()->addDays(14));
    $invoice2 = $service->create($sponsorship, 2000000, now()->addDays(28));

    $invoices = $service->getForSponsorship($sponsorship);

    expect($invoices)->toHaveCount(2);

    $ids = $invoices->pluck('id')->toArray();
    expect($ids)->toContain($invoice1->id, $invoice2->id);
});

test('getTotalPaid calculates correctly', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    SponsorshipInvoice::factory()->paid()->withAmount(3000000)->create(['sponsorship_id' => $sponsorship->id]);
    SponsorshipInvoice::factory()->paid()->withAmount(2000000)->create(['sponsorship_id' => $sponsorship->id]);
    SponsorshipInvoice::factory()->sent()->withAmount(1000000)->create(['sponsorship_id' => $sponsorship->id]);

    expect($service->getTotalPaid($sponsorship))->toBe(5000000.0);
});

test('getTotalPending calculates correctly', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    SponsorshipInvoice::factory()->draft()->withAmount(1000000)->create(['sponsorship_id' => $sponsorship->id]);
    SponsorshipInvoice::factory()->sent()->withAmount(2000000)->create(['sponsorship_id' => $sponsorship->id]);
    SponsorshipInvoice::factory()->paid()->withAmount(3000000)->create(['sponsorship_id' => $sponsorship->id]);

    expect($service->getTotalPending($sponsorship))->toBe(3000000.0);
});

test('markOverdueInvoices marks all overdue sent invoices', function () {
    $service = app(InvoiceService::class);
    $sponsorship = Sponsorship::factory()->active()->create();

    // Sent invoice with past due date (should be marked overdue)
    SponsorshipInvoice::factory()->sent()->dueInPast()->create(['sponsorship_id' => $sponsorship->id]);
    SponsorshipInvoice::factory()->sent()->dueInPast()->create(['sponsorship_id' => $sponsorship->id]);

    // Sent invoice with future due date (should NOT be marked overdue)
    SponsorshipInvoice::factory()->sent()->create(['sponsorship_id' => $sponsorship->id]);

    // Draft invoice with past due date (should NOT be marked overdue - only sent ones)
    SponsorshipInvoice::factory()->draft()->dueInPast()->create(['sponsorship_id' => $sponsorship->id]);

    $count = $service->markOverdueInvoices();

    expect($count)->toBe(2);
    $this->assertDatabaseHas('sponsorship_invoices', ['status' => 'overdue']);
    $this->assertDatabaseCount('sponsorship_invoices', 4);
});

test('invoice model isOverdue returns correct status', function () {
    $pastDue = SponsorshipInvoice::factory()->sent()->dueInPast()->create();
    $futureDue = SponsorshipInvoice::factory()->sent()->create();

    expect($pastDue->isOverdue())->toBeTrue()
        ->and($futureDue->isOverdue())->toBeFalse();
});

test('invoice model formatted_amount returns rupiah format', function () {
    $invoice = SponsorshipInvoice::factory()->create(['amount' => 5000000]);

    expect($invoice->formatted_amount)->toBe('Rp 5.000.000');
});

test('invoice model status_label returns correct label', function () {
    expect(SponsorshipInvoice::factory()->draft()->make()->status_label)->toBe('Draft')
        ->and(SponsorshipInvoice::factory()->sent()->make()->status_label)->toBe('Terkirim')
        ->and(SponsorshipInvoice::factory()->paid()->make()->status_label)->toBe('Lunas')
        ->and(SponsorshipInvoice::factory()->overdue()->make()->status_label)->toBe('Jatuh Tempo');
});
