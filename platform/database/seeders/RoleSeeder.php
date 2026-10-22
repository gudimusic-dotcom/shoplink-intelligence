<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['code' => 'ceo', 'name' => 'CEO', 'description' => 'Celkový prehľad o firme a podpora rozhodovania.'],
            ['code' => 'customer_support', 'name' => 'Zákaznícka podpora', 'description' => 'Prevádzka RMA a zákazníckych prípadov.'],
            ['code' => 'warehouse', 'name' => 'Sklad', 'description' => 'Správa zásob a fyzického pohybu tovaru.'],
            ['code' => 'procurement', 'name' => 'Nákup', 'description' => 'Nákupné rozhodnutia a riadenie skladových rizík.'],
            ['code' => 'marketing', 'name' => 'Marketing', 'description' => 'Sledovanie marketingového výkonu a dopadu kampaní.'],
            ['code' => 'finance', 'name' => 'Financie', 'description' => 'Zisk, dobropisy a finančná kontrola.'],
            ['code' => 'admin', 'name' => 'Administrátor', 'description' => 'Správa systému a oprávnení.'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['code' => $role['code']],
                $role,
            );
        }
    }
}
