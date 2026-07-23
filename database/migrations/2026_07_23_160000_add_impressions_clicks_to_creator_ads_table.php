<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_ads', function (Blueprint $table) {
            $table->unsignedBigInteger('impressions')->default(0)->after('is_active');
            $table->unsignedBigInteger('clicks')->default(0)->after('impressions');
            $table->decimal('revenue', 12, 2)->default(0)->after('clicks');
        });
    }

    public function down(): void
    {
        Schema::table('creator_ads', function (Blueprint $table) {
            $table->dropColumn(['impressions', 'clicks', 'revenue']);
        });
    }
};
