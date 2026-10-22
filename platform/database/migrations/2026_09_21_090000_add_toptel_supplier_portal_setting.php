<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $exists = DB::table('product_stat_supplier_settings')
            ->where('supplier_name', 'Toptel')
            ->exists();

        if ($exists) {
            DB::table('product_stat_supplier_settings')
                ->where('supplier_name', 'Toptel')
                ->update([
                    'portal_url_template' => 'https://toptel.com/pl/szukaj/?search_lang=pl&search=product&string={code}&search_category=&search_button=Szukaj',
                    'updated_at' => $now,
                ]);

            return;
        }

        DB::table('product_stat_supplier_settings')->insert([
            'supplier_name' => 'Toptel',
            'portal_url_template' => 'https://toptel.com/pl/szukaj/?search_lang=pl&search=product&string={code}&search_category=&search_button=Szukaj',
            'target_coverage_days' => 45,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('product_stat_supplier_settings')
            ->where('supplier_name', 'Toptel')
            ->delete();
    }
};
