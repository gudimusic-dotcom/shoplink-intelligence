<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('email_notif_new_task')->default(true)->after('is_active');
            $table->boolean('email_notif_overdue_daily')->default(true)->after('email_notif_new_task');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'email_notif_new_task',
                'email_notif_overdue_daily',
            ]);
        });
    }
};
