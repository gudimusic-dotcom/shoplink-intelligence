<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_job_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('ordered_at')->nullable();
            $table->string('order_code');
            $table->string('portal')->nullable();
            $table->string('portal_country', 10)->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->index();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('category')->nullable();
            $table->decimal('net_sales_amount', 14, 2)->default(0);
            $table->decimal('purchase_amount', 14, 2)->nullable();
            $table->decimal('margin_amount', 14, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('net_sales_amount_eur', 14, 2)->nullable();
            $table->decimal('purchase_amount_eur', 14, 2)->nullable();
            $table->decimal('margin_amount_eur', 14, 2)->nullable();
            $table->string('channel_type')->nullable();
            $table->string('ext')->nullable();
            $table->string('status')->nullable();
            $table->string('source_hash')->unique();
            $table->timestamps();

            $table->index(['ordered_at', 'currency']);
            $table->index(['channel_type', 'portal_country']);
            $table->index(['order_code', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_lines');
    }
};
