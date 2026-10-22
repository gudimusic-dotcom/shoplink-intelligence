<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('assigned_user_id')->constrained('users')->nullOnDelete();
        });

        DB::table('tasks')
            ->whereNull('created_by')
            ->where('source_type', 'App\\Models\\User')
            ->where('generated_by', 'manual')
            ->update([
                'created_by' => DB::raw('source_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
