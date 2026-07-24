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
            $table->index('user_id');
            $table->index('subcategory');
            $table->index('published_at');
            $table->index('view_count');
            $table->index('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['subcategory']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['view_count']);
            $table->dropIndex(['average_rating']);
        });
    }
};
