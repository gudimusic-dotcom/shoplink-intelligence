<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_stat_imports', function (Blueprint $table) {
            $table->date('period_from')->nullable()->after('sheet_name');
            $table->date('period_to')->nullable()->after('period_from');
        });
    }

    public function down(): void
    {
        Schema::table('product_stat_imports', function (Blueprint $table) {
            $table->dropColumn(['period_from', 'period_to']);
        });
    }
};
