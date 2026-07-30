<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_network_settings', function (Blueprint $table) {
            $table->id();
            $table->string('network', 50)->unique()
                ->comment('Network identifier: adsense, monetag, propellerads, media_net, adsterra, ezoic');
            $table->string('display_name', 100)
                ->comment('Human-readable network name');
            $table->boolean('is_enabled')->default(false)
                ->comment('Master toggle for this network');
            $table->boolean('is_active')->default(true)
                ->comment('Network status (approved by network or not)');

            // Common fields
            $table->string('publisher_id', 200)->nullable()
                ->comment('Publisher/Partner ID');
            $table->string('site_id', 200)->nullable()
                ->comment('Site/Property ID');
            $table->text('script_tag')->nullable()
                ->comment('Global script tag to include in <head>');
            $table->text('ads_txt_entry')->nullable()
                ->comment('Entry for ads.txt file');

            // Safety settings
            $table->boolean('allow_banner')->default(true);
            $table->boolean('allow_native')->default(true);
            $table->boolean('allow_interstitial')->default(false)
                ->comment('Disabled by default for educational platform safety');
            $table->boolean('allow_popunder')->default(false)
                ->comment('Disabled by default — harmful for UX');
            $table->boolean('allow_video')->default(false);

            // Ad unit slots (JSON: map position to slot/zone IDs)
            $table->json('ad_unit_slots')->nullable()
                ->comment('JSON mapping of positions to zone/slot IDs');

            // Performance tracking
            $table->decimal('estimated_rpm', 10, 2)->nullable()
                ->comment('Estimated Revenue Per Mille');
            $table->unsignedInteger('total_impressions')->default(0);
            $table->unsignedInteger('total_clicks')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_network_settings');
    }
};
