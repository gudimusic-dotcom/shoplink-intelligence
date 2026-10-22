<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_job_id')->nullable()->constrained('import_jobs')->nullOnDelete();
            $table->foreignId('sales_channel_id')->nullable()->constrained('sales_channels')->nullOnDelete();
            $table->timestamp('ordered_at')->nullable();
            $table->string('order_code')->unique();
            $table->string('ext')->nullable();
            $table->string('ext_id')->nullable();
            $table->string('portal')->nullable();
            $table->string('portal_country', 10)->nullable();
            $table->text('product_models')->nullable();
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->string('currency', 10);
            $table->decimal('gross_amount_eur', 14, 2)->nullable();
            $table->string('channel_type', 30)->default('own_web');
            $table->string('status', 50)->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_amount', 14, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('payment_amount', 14, 2)->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamps();

            $table->index(['ordered_at', 'currency']);
            $table->index(['portal', 'currency']);
            $table->index(['channel_type', 'currency']);
            $table->index(['status', 'ordered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
