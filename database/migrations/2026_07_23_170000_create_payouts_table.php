<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['bank_transfer', 'paypal', 'midtrans'])->default('bank_transfer');
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('account_holder', 100)->nullable();
            $table->string('paypal_email', 255)->nullable();
            $table->enum('status', ['pending', 'processing', 'approved', 'paid', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('currency', ['IDR', 'USD'])->default('IDR');
            $table->decimal('amount_usd', 12, 2)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
