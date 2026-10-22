<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stat_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('product_stat_imports')->cascadeOnDelete();
            $table->string('sku')->index();
            $table->string('ean')->nullable()->index();
            $table->string('supplier_raw')->nullable();
            $table->string('supplier_name')->index();
            $table->string('supplier_code')->nullable();
            $table->text('product_name');
            $table->integer('stock_s1')->default(0);
            $table->integer('stock_s2')->default(0);
            $table->integer('sales_period')->default(0);
            $table->integer('market_noa')->default(0);
            $table->integer('market_alza')->default(0);
            $table->integer('market_pr1')->default(0);
            $table->integer('market_pr2')->default(0);
            $table->integer('sales_p30')->default(0);
            $table->integer('sales_pop')->default(0);
            $table->integer('external_stock')->default(0);
            $table->string('clearance_flag')->nullable();
            $table->decimal('purchase_cost', 10, 2)->default(0);
            $table->dateTime('last_order_at')->nullable();
            $table->integer('product_age_days')->default(0);
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stat_items');
    }
};
