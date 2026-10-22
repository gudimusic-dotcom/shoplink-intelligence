<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->string('engine_type')->default('simple')->after('type');
            $table->string('last_test_status')->nullable()->after('last_triggered_at');
            $table->string('last_test_message', 500)->nullable()->after('last_test_status');
            $table->string('last_test_challenge')->nullable()->after('last_test_message');
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn([
                'engine_type',
                'last_test_status',
                'last_test_message',
                'last_test_challenge',
            ]);
        });
    }
};
