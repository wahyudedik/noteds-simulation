<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_ads', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->enum('type', ['banner', 'interstitial', 'video', 'native', 'adsense']);
            $table->enum('position', [
                'header', 'sidebar', 'pre_roll', 'mid_roll',
                'post_simulation', 'feed_sponsored', 'search_sponsored',
            ]);
            $table->text('content')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('target_url', 500)->nullable();
            $table->string('adsense_publisher_id', 100)->nullable();
            $table->string('adsense_ad_slot', 100)->nullable();
            $table->json('category_filter')->nullable();
            $table->unsignedInteger('weight')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('position');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_ads');
    }
};
