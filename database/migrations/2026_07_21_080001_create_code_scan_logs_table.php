<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->string('version', 20);
            $table->enum('scan_type', ['auto_scan', 'sandbox_test', 'manual_review']);
            $table->enum('result', ['pass', 'flag', 'reject']);
            $table->json('findings')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('scan_duration_ms')->nullable();
            $table->timestamps();

            $table->index(['simulation_id', 'scan_type']);
            $table->index(['result', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_scan_logs');
    }
};
