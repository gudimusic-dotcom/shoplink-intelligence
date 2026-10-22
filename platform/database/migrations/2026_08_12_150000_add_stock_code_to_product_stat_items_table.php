<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stat_items', function (Blueprint $table) {
            $table->string('stock_code')->nullable()->after('ean')->index();
        });
    }

    public function down(): void
    {
        Schema::table('product_stat_items', function (Blueprint $table) {
            $table->dropColumn('stock_code');
        });
    }
};
