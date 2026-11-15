<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stat_incoming_items', function (Blueprint $table): void {
            $table->string('target_warehouse', 2)->default('s1')->after('qty_incoming')->index();
        });
    }

    public function down(): void
    {
        Schema::table('product_stat_incoming_items', function (Blueprint $table): void {
            $table->dropIndex(['target_warehouse']);
            $table->dropColumn('target_warehouse');
        });
    }
};
