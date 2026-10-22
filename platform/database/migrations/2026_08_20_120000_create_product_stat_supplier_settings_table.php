<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stat_supplier_settings', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->unique();
            $table->string('portal_url_template')->nullable();
            $table->unsignedSmallInteger('target_coverage_days')->default(45);
            $table->timestamps();
        });

        $now = now();

        DB::table('product_stat_supplier_settings')->insert([
            ['supplier_name' => 'Fdd', 'portal_url_template' => 'https://fd-distribution.pl/en/?f=&a=sklep&k=0&q={code}', 'target_coverage_days' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Hurtel', 'portal_url_template' => 'https://eu.hurtel.com/en/search?text={code}', 'target_coverage_days' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Innpro', 'portal_url_template' => 'https://b2b.innpro.sk/search.php?text={code}', 'target_coverage_days' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Isotrade', 'portal_url_template' => 'https://maxy.eu/hurt/sk_sk/search/{code}', 'target_coverage_days' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Ikonka', 'portal_url_template' => 'https://www.ikonka.eu/products/search/language:eng?q={code}', 'target_coverage_days' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Kathay', 'portal_url_template' => 'https://hurtowniakathay.pl/?f=&a=sklep&k=0&q={code}&x=0&y=0', 'target_coverage_days' => 90, 'created_at' => $now, 'updated_at' => $now],
            ['supplier_name' => 'Ecarla', 'portal_url_template' => 'https://hurt.ecarla.pl/gb/jxsearch?search_query={code}&search_categories=2&jx_submit_search=', 'target_coverage_days' => 60, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stat_supplier_settings');
    }
};
