<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $exists = DB::table('product_stat_supplier_settings')
            ->where('supplier_name', 'Partnertele')
            ->exists();

        if ($exists) {
            DB::table('product_stat_supplier_settings')
                ->where('supplier_name', 'Partnertele')
                ->update([
                    'portal_url_template' => 'https://partnertele.com/products/search?query={code}',
                    'updated_at' => $now,
                ]);

            return;
        }

        DB::table('product_stat_supplier_settings')->insert([
            'supplier_name' => 'Partnertele',
            'portal_url_template' => 'https://partnertele.com/products/search?query={code}',
            'target_coverage_days' => 45,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('product_stat_supplier_settings')
            ->where('supplier_name', 'Partnertele')
            ->delete();
    }
};
