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
        Schema::create('simulation_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('metric_type', ['view', 'play', 'like', 'bookmark', 'share', 'reaction', 'comment']);
            $table->integer('count')->default(0);
            $table->timestamps();

            $table->unique(['simulation_id', 'date', 'metric_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_daily_metrics');
    }
};
