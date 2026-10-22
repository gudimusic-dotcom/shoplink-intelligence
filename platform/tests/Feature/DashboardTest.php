<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_dashboard(): void
    {
        $role = Role::query()->create([
            'code' => 'admin',
            'name' => 'Administrátor',
        ]);

        $user = User::factory()->create([
            'default_role_id' => $role->id,
        ]);

        $user->roles()->attach($role);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('SHOPLINK Intelligence')
            ->assertSee('Administrátor');
    }
}
