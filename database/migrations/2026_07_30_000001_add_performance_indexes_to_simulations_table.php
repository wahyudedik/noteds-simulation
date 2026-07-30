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
            if (! Schema::hasIndex('simulations', 'simulations_published_at_index')) {
                $table->index('published_at');
            }

            if (! Schema::hasIndex('simulations', 'simulations_is_published_published_at_index')) {
                $table->index(['is_published', 'published_at']);
            }

            if (! Schema::hasIndex('simulations', 'simulations_is_published_play_count_index')) {
                $table->index(['is_published', 'play_count']);
            }

            if (! Schema::hasIndex('simulations', 'simulations_is_published_average_rating_index')) {
                $table->index(['is_published', 'average_rating']);
            }

            if (! Schema::hasIndex('simulations', 'simulations_is_published_category_play_count_index')) {
                $table->index(['is_published', 'category', 'play_count']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            if (Schema::hasIndex('simulations', 'simulations_is_published_category_play_count_index')) {
                $table->dropIndex(['is_published', 'category', 'play_count']);
            }

            if (Schema::hasIndex('simulations', 'simulations_is_published_average_rating_index')) {
                $table->dropIndex(['is_published', 'average_rating']);
            }

            if (Schema::hasIndex('simulations', 'simulations_is_published_play_count_index')) {
                $table->dropIndex(['is_published', 'play_count']);
            }

            if (Schema::hasIndex('simulations', 'simulations_is_published_published_at_index')) {
                $table->dropIndex(['is_published', 'published_at']);
            }

            if (Schema::hasIndex('simulations', 'simulations_published_at_index')) {
                $table->dropIndex(['published_at']);
            }
        });
    }
};
