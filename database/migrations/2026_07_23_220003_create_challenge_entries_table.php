<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('scores')->nullable();
            $table->decimal('total_score', 5, 2)->default(0);
            $table->integer('rank')->nullable();
            $table->enum('status', ['submitted', 'judging', 'scored', 'winner', 'runner_up'])->default('submitted');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['challenge_id', 'simulation_id']);
            $table->index(['challenge_id', 'total_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_entries');
    }
};
