<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('marketplace_display')->nullable()->after('ext');
            $table->index('marketplace_display');
        });

        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->string('marketplace_display')->nullable()->after('ext');
            $table->index('marketplace_display');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex(['marketplace_display']);
            $table->dropColumn('marketplace_display');
        });

        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->dropIndex(['marketplace_display']);
            $table->dropColumn('marketplace_display');
        });
    }
};
