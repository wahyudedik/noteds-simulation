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
        Schema::create('simulation_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('plays')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('bookmarks')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('avg_duration_seconds')->default(0);
            $table->integer('completions')->default(0);
            $table->timestamps();

            $table->unique(['simulation_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_analytics');
    }
};
