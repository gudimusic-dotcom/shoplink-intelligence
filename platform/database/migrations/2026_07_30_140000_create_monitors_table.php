<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('price_watch');
            $table->string('marketplace')->nullable();
            $table->text('target_url');
            $table->string('tracked_field')->default('price');
            $table->string('condition_operator')->default('drops_below_last');
            $table->unsignedSmallInteger('check_interval_hours')->default(6);
            $table->string('alert_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('should_create_task')->default(false);
            $table->decimal('last_value', 12, 2)->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
