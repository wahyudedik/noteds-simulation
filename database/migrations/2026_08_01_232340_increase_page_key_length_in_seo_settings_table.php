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
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropUnique('seo_settings_page_key_unique');
            $table->string('page_key', 255)->change();
            $table->unique('page_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropUnique('seo_settings_page_key_unique');
            $table->string('page_key', 100)->change();
            $table->unique('page_key');
        });
    }
};
