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
        Schema::table('simulations', function (Blueprint $table) {
            $table->foreignId('experience_project_id')
                ->nullable()
                ->after('user_id')
                ->constrained('experience_projects')
                ->nullOnDelete();
            $table->index('experience_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropForeign(['experience_project_id']);
            $table->dropIndex(['experience_project_id']);
            $table->dropColumn('experience_project_id');
        });
    }
};
