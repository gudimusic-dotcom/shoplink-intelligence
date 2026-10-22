<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(TaskCategorySeeder::class);
        $this->call(SalesChannelSeeder::class);
        $this->call(ExchangeRateSeeder::class);

        $adminRole = Role::query()->where('code', 'admin')->first();

        $user = User::query()->updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Shoplink Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
            'default_role_id' => $adminRole?->id,
        ]);

        if ($adminRole !== null) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
