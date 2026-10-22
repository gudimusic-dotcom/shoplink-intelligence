<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryIds = DB::table('task_categories')
            ->where('code', 'like', 'order%')
            ->orWhereRaw('LOWER(name) LIKE ?', ['objedn%'])
            ->pluck('id');

        DB::table('tasks')
            ->whereIn('task_category_id', $categoryIds)
            ->whereNotIn('status', ['new', 'done'])
            ->update(['status' => 'new']);
    }

    public function down(): void
    {
        // The previous detailed status cannot be reconstructed safely.
    }
};
