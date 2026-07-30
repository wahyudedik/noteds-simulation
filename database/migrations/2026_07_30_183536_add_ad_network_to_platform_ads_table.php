<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_ads', function (Blueprint $table) {
            $table->string('ad_network', 50)->nullable()->after('type')
                ->comment('ad network provider: adsense, monetag, propellerads, media_net, adsterra, ezoic, null=custom');
            $table->text('ad_network_config')->nullable()->after('ad_network')
                ->comment('JSON config for ad network (zone_id, slot_id, etc.)');
        });
    }

    public function down(): void
    {
        Schema::table('platform_ads', function (Blueprint $table) {
            $table->dropColumn(['ad_network', 'ad_network_config']);
        });
    }
};
