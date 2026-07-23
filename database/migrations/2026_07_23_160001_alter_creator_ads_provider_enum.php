<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing data first
        DB::table('creator_ads')
            ->where('provider', 'direct')
            ->update(['provider' => 'mediavine']);
        DB::table('creator_ads')
            ->where('provider', 'affiliate')
            ->update(['provider' => 'adthrive']);
        DB::table('creator_ads')
            ->where('provider', 'other')
            ->update(['provider' => 'custom']);

        // Change enum column (MySQL only — SQLite does not enforce ENUM via ALTER TABLE)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE creator_ads MODIFY provider ENUM('adsense', 'mediavine', 'adthrive', 'custom') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE creator_ads MODIFY provider ENUM('adsense', 'direct', 'affiliate', 'other') NOT NULL");
        }
    }
};
