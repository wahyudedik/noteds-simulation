<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('traffic_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->string('source', 50); // direct, search, social, embed, referral
            $table->string('metric_type', 20); // view, play
            $table->unsignedInteger('count')->default(0);
            $table->date('date');
            $table->timestamps();

            $table->unique(['simulation_id', 'source', 'metric_type', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_sources');
    }
};
