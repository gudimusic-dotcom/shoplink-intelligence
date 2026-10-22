<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rmas', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('order_reference')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('customer_reference')->nullable();
            $table->string('status', 30)->default('received');
            $table->string('reason_code', 50)->nullable();
            $table->string('resolution_type', 50)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rmas');
    }
};
