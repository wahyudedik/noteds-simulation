<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_reputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(100);
            $table->integer('total_uploads')->default(0);
            $table->integer('approved_count')->default(0);
            $table->integer('rejected_count')->default(0);
            $table->integer('flagged_count')->default(0);
            $table->integer('reports_received')->default(0);
            $table->enum('revenue_tier', ['basic', 'verified', 'expert', 'platinum'])->default('basic');
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_reputations');
    }
};
