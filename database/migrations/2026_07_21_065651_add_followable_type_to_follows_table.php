<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER TABLE DROP/ADD columns or FK constraints
            // Use table recreation approach
            Schema::table('follows', function ($table) {
                $table->string('followable_type')->nullable()->default('App\\Models\\User')->after('followable_id');
            });
        } else {
            // MySQL: Drop FK on follower_id first
            DB::statement('ALTER TABLE `follows` DROP FOREIGN KEY `follows_follower_id_foreign`');

            // Drop the old unique index
            DB::statement('ALTER TABLE `follows` DROP INDEX `follows_follower_id_followable_id_unique`');

            // Add followable_type column if not exists
            $hasColumn = DB::select("SHOW COLUMNS FROM `follows` LIKE 'followable_type'");
            if (empty($hasColumn)) {
                DB::statement("ALTER TABLE `follows` ADD COLUMN `followable_type` VARCHAR(255) NOT NULL DEFAULT 'App\\\\Models\\\\User' AFTER `followable_id`");
            }

            // Update existing rows
            DB::statement("UPDATE `follows` SET `followable_type` = 'App\\\\Models\\\\User' WHERE `followable_type` IS NULL OR `followable_type` = ''");

            // Add new composite unique index
            DB::statement('ALTER TABLE `follows` ADD UNIQUE INDEX `follows_follower_followable_type_unique` (`follower_id`, `followable_id`, `followable_type`)');

            // Re-add FK on follower_id
            DB::statement('ALTER TABLE `follows` ADD CONSTRAINT `follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('follows', function ($table) {
                $table->dropColumn('followable_type');
            });
        } else {
            DB::statement('ALTER TABLE `follows` DROP FOREIGN KEY `follows_follower_id_foreign`');
            DB::statement('ALTER TABLE `follows` DROP INDEX `follows_follower_followable_type_unique`');
            DB::statement('ALTER TABLE `follows` DROP COLUMN `followable_type`');
            DB::statement('ALTER TABLE `follows` ADD UNIQUE INDEX `follows_follower_id_followable_id_unique` (`follower_id`, `followable_id`)');
            DB::statement('ALTER TABLE `follows` ADD CONSTRAINT `follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
        }
    }
};
