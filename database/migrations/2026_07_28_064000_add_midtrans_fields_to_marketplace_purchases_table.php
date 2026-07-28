<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_purchases', function (Blueprint $table) {
            $table->string('snap_token', 255)->nullable()->after('transaction_id');
            $table->string('midtrans_order_id', 255)->nullable()->after('snap_token');
            $table->timestamp('paid_at')->nullable()->after('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_purchases', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'midtrans_order_id', 'paid_at']);
        });
    }
};
