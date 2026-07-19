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
        $columnsToAdd = [];

        if (! Schema::hasColumn('users', 'role')) {
            $columnsToAdd[] = 'role';
        }
        if (! Schema::hasColumn('users', 'avatar')) {
            $columnsToAdd[] = 'avatar';
        }
        if (! Schema::hasColumn('users', 'bio')) {
            $columnsToAdd[] = 'bio';
        }

        if (empty($columnsToAdd)) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
            if (in_array('role', $columnsToAdd)) {
                $table->string('role')->default('user')->after('email');
            }
            if (in_array('avatar', $columnsToAdd)) {
                $table->string('avatar')->nullable()->after('role');
            }
            if (in_array('bio', $columnsToAdd)) {
                $table->text('bio')->nullable()->after('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'bio']);
        });
    }
};
