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
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['copy_link', 'whatsapp', 'telegram', 'twitter', 'facebook']);
            $table->timestamp('created_at')->useCurrent();

            $table->index('simulation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
