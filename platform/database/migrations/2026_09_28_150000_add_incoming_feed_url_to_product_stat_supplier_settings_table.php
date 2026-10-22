<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stat_supplier_settings', function (Blueprint $table) {
            $table->string('incoming_feed_url', 2000)->nullable()->after('stock_feed_url');
        });
    }

    public function down(): void
    {
        Schema::table('product_stat_supplier_settings', function (Blueprint $table) {
            $table->dropColumn('incoming_feed_url');
        });
    }
};
