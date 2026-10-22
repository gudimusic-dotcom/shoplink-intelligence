<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stat_supplier_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->index();
            $table->string('supplier_code')->index();
            $table->string('stock_status', 30)->index();
            $table->string('source_name')->nullable();
            $table->dateTime('checked_at')->nullable();
            $table->timestamps();
            $table->unique(['supplier_name', 'supplier_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stat_supplier_stocks');
    }
};
