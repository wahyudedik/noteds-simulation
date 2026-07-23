<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->enum('ad_type', ['platform', 'creator']);
            $table->unsignedBigInteger('ad_id');
            $table->foreignId('simulation_id')->nullable()->constrained('simulations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('position', 50);
            $table->boolean('clicked')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['ad_type', 'ad_id']);
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_impressions');
    }
};
