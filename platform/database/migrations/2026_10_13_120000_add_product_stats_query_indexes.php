<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stat_items', function (Blueprint $table) {
            $table->index(['import_id', 'supplier_name', 'sales_p30'], 'product_stat_items_import_supplier_sales_idx');
            $table->index(['import_id', 'last_order_at'], 'product_stat_items_import_last_order_idx');
        });

        Schema::table('product_stat_incoming_items', function (Blueprint $table) {
            $table->index(['supplier_name', 'ean'], 'product_stat_incoming_supplier_ean_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_stat_incoming_items', function (Blueprint $table) {
            $table->dropIndex('product_stat_incoming_supplier_ean_idx');
        });

        Schema::table('product_stat_items', function (Blueprint $table) {
            $table->dropIndex('product_stat_items_import_supplier_sales_idx');
            $table->dropIndex('product_stat_items_import_last_order_idx');
        });
    }
};
