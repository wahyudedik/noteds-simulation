<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained('simulations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('provider', ['adsense', 'direct', 'affiliate', 'other']);
            $table->string('publisher_id', 100)->nullable();
            $table->json('ad_config');
            $table->text('code_snippet')->nullable();
            $table->enum('review_status', [
                'auto_approved', 'pending_review', 'approved', 'rejected', 'flagged',
            ])->default('pending_review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->json('scan_result')->nullable();
            $table->json('sandbox_result')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['simulation_id', 'provider', 'publisher_id']);
            $table->index('review_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_ads');
    }
};
