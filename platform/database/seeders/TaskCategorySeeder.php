<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'general',
                'name' => 'Všeobecné',
                'description' => 'Bežné interné úlohy bez špecifického workflow.',
                'sort_order' => 10,
            ],
            [
                'code' => 'order',
                'name' => 'Objednávka',
                'description' => 'Úlohy súvisiace so spracovaním objednávok.',
                'sort_order' => 20,
            ],
            [
                'code' => 'complaint',
                'name' => 'Reklamácia',
                'description' => 'Úlohy súvisiace s reklamačnými prípadmi a RMA.',
                'sort_order' => 30,
            ],
            [
                'code' => 'payment',
                'name' => 'Platba',
                'description' => 'Úlohy súvisiace s platbami a prevodmi.',
                'sort_order' => 40,
            ],
            [
                'code' => 'invoicing',
                'name' => 'Fakturácia',
                'description' => 'Úlohy súvisiace s faktúrami, dobropismi a účtovaním.',
                'sort_order' => 50,
            ],
            [
                'code' => 'lost_package',
                'name' => 'Stratený balík',
                'description' => 'Úlohy súvisiace so stratenými alebo nedoručenými balíkmi.',
                'sort_order' => 60,
            ],
        ];

        foreach ($categories as $category) {
            TaskCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                $category + ['is_active' => true],
            );
        }
    }
}
