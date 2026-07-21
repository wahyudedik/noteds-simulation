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
        Schema::table('tags', function (Blueprint $table) {
            // Drop unique indexes first to avoid duplicate index errors on SQLite
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('name', 255)->unique()->change();
            $table->string('slug', 255)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('name', 100)->unique()->change();
            $table->string('slug', 100)->unique()->change();
        });
    }
};
