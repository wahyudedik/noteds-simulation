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
        Schema::table('platform_ads', function (Blueprint $table) {
            $table->foreignId('sponsor_id')->nullable()->after('created_by')->constrained()->nullOnDelete();
            $table->foreignId('sponsorship_id')->nullable()->after('sponsor_id')->constrained()->nullOnDelete();
            $table->boolean('is_sponsored')->default(false)->after('sponsorship_id');
            $table->string('sponsored_label', 100)->nullable()->after('is_sponsored');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platform_ads', function (Blueprint $table) {
            $table->dropForeign(['sponsor_id']);
            $table->dropForeign(['sponsorship_id']);
            $table->dropColumn(['sponsor_id', 'sponsorship_id', 'is_sponsored', 'sponsored_label']);
        });
    }
};
