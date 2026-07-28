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
        Schema::table('creator_reputations', function (Blueprint $table) {
            $table->decimal('ranking_score', 12, 2)->default(0)->after('total_revenue');
            $table->index('ranking_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creator_reputations', function (Blueprint $table) {
            $table->dropIndex(['ranking_score']);
            $table->dropColumn('ranking_score');
        });
    }
};
