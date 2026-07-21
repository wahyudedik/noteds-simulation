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
        Schema::create('simulation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->cascadeOnDelete();
            $table->string('version', 20);
            $table->string('zip_path', 500);
            $table->text('changelog')->nullable();
            $table->timestamps();

            $table->index(['simulation_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_versions');
    }
};
