<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->enum('reason', ['malware', 'spam_ads', 'inappropriate', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('action_taken')->nullable();
            $table->timestamps();

            $table->index(['simulation_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
