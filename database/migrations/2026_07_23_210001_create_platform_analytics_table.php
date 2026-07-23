<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('new_registrations')->default(0);
            $table->unsignedInteger('active_users')->default(0);
            $table->unsignedInteger('total_simulations')->default(0);
            $table->unsignedInteger('new_simulations')->default(0);
            $table->unsignedBigInteger('total_views')->default(0);
            $table->unsignedBigInteger('total_plays')->default(0);
            $table->unsignedInteger('total_comments')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->json('top_categories')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_analytics');
    }
};
