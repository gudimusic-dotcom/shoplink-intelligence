<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stat_incoming_items', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->index();
            $table->string('source_name')->nullable();
            $table->string('supplier_product_id')->nullable()->index();
            $table->string('ean')->nullable()->index();
            $table->integer('qty_incoming')->default(0);
            $table->json('raw_payload')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stat_incoming_items');
    }
};
