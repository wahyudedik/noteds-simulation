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
        // play_history: composite indexes for "Discovered for You" and "Recently Played"
        Schema::table('play_history', function (Blueprint $table) {
            if (! Schema::hasIndex('play_history', 'play_history_user_simulation_index')) {
                $table->index(['user_id', 'simulation_id']);
            }
            if (! Schema::hasIndex('play_history', 'play_history_user_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        // comments: composite index for ordering by simulation + date
        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasIndex('comments', 'comments_simulation_created_at_index')) {
                $table->index(['simulation_id', 'created_at']);
            }
        });

        // notifications: composite index for unread count query
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasIndex('notifications', 'notifications_user_read_at_index')) {
                $table->index(['user_id', 'read_at']);
            }
        });

        // ratings: index for rating distribution queries
        Schema::table('ratings', function (Blueprint $table) {
            if (! Schema::hasIndex('ratings', 'ratings_simulation_id_index')) {
                $table->index('simulation_id');
            }
        });

        // bookmarks: index for simulation lookups
        Schema::table('bookmarks', function (Blueprint $table) {
            if (! Schema::hasIndex('bookmarks', 'bookmarks_simulation_id_index')) {
                $table->index('simulation_id');
            }
        });

        // reactions: index for simulation lookups
        Schema::table('reactions', function (Blueprint $table) {
            if (! Schema::hasIndex('reactions', 'reactions_simulation_id_index')) {
                $table->index('simulation_id');
            }
        });

        // shares: index for simulation lookups
        Schema::table('shares', function (Blueprint $table) {
            if (! Schema::hasIndex('shares', 'shares_simulation_id_index')) {
                $table->index('simulation_id');
            }
        });

        // simulation_daily_metrics: composite index for analytics queries
        Schema::table('simulation_daily_metrics', function (Blueprint $table) {
            if (! Schema::hasIndex('simulation_daily_metrics', 'simulation_daily_metrics_simulation_recorded_at_index')) {
                $table->index(['simulation_id', 'recorded_at']);
            }
        });

        // simulations: additional composite indexes for studio & latest queries
        Schema::table('simulations', function (Blueprint $table) {
            if (! Schema::hasIndex('simulations', 'simulations_user_id_is_published_index')) {
                $table->index(['user_id', 'is_published']);
            }
            if (! Schema::hasIndex('simulations', 'simulations_is_published_created_at_index')) {
                $table->index(['is_published', 'created_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('play_history', function (Blueprint $table) {
            if (Schema::hasIndex('play_history', 'play_history_user_simulation_index')) {
                $table->dropIndex('play_history_user_simulation_index');
            }
            if (Schema::hasIndex('play_history', 'play_history_user_created_at_index')) {
                $table->dropIndex('play_history_user_created_at_index');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasIndex('comments', 'comments_simulation_created_at_index')) {
                $table->dropIndex('comments_simulation_created_at_index');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasIndex('notifications', 'notifications_user_read_at_index')) {
                $table->dropIndex('notifications_user_read_at_index');
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasIndex('ratings', 'ratings_simulation_id_index')) {
                $table->dropIndex('ratings_simulation_id_index');
            }
        });

        Schema::table('bookmarks', function (Blueprint $table) {
            if (Schema::hasIndex('bookmarks', 'bookmarks_simulation_id_index')) {
                $table->dropIndex('bookmarks_simulation_id_index');
            }
        });

        Schema::table('reactions', function (Blueprint $table) {
            if (Schema::hasIndex('reactions', 'reactions_simulation_id_index')) {
                $table->dropIndex('reactions_simulation_id_index');
            }
        });

        Schema::table('shares', function (Blueprint $table) {
            if (Schema::hasIndex('shares', 'shares_simulation_id_index')) {
                $table->dropIndex('shares_simulation_id_index');
            }
        });

        Schema::table('simulation_daily_metrics', function (Blueprint $table) {
            if (Schema::hasIndex('simulation_daily_metrics', 'simulation_daily_metrics_simulation_recorded_at_index')) {
                $table->dropIndex('simulation_daily_metrics_simulation_recorded_at_index');
            }
        });

        Schema::table('simulations', function (Blueprint $table) {
            if (Schema::hasIndex('simulations', 'simulations_user_id_is_published_index')) {
                $table->dropIndex('simulations_user_id_is_published_index');
            }
            if (Schema::hasIndex('simulations', 'simulations_is_published_created_at_index')) {
                $table->dropIndex('simulations_is_published_created_at_index');
            }
        });
    }
};
