<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['weekly', 'monthly', 'annual'])->default('monthly');
            $table->string('theme');
            $table->json('criteria')->nullable();
            $table->text('prize_description')->nullable();
            $table->foreignId('prize_badge_id')->nullable()->constrained('badges')->nullOnDelete();
            $table->foreignId('winner_simulation_id')->nullable()->constrained('simulations')->nullOnDelete();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->enum('status', ['upcoming', 'active', 'judging', 'completed'])->default('upcoming');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
