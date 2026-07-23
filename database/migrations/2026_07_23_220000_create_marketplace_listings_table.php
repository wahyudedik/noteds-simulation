<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->enum('license_type', ['single', 'institutional', 'subscription'])->default('single');
            $table->boolean('is_active')->default(true);
            $table->boolean('demo_available')->default(true);
            $table->integer('demo_limit_minutes')->default(5);
            $table->bigInteger('total_sales')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->timestamps();

            $table->unique('simulation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_listings');
    }
};
