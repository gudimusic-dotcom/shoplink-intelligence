<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_user_management(): void
    {
        $admin = $this->createAdminUser();

        $response = $this
            ->actingAs($admin)
            ->get(route('users.index'));

        $response
            ->assertOk()
            ->assertSee('Používatelia a prístupy')
            ->assertSee($admin->email);
    }

    public function test_user_can_update_another_user_profile_roles_status_and_password(): void
    {
        $admin = $this->createAdminUser();
        $supportRole = Role::query()->create([
            'code' => 'customer_support',
            'name' => 'Zákaznícka podpora',
        ]);
        $warehouseRole = Role::query()->create([
            'code' => 'warehouse',
            'name' => 'Sklad',
        ]);
        $target = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('users.update', $target), [
                'name' => 'Peter Admin',
                'email' => 'peter.admin@example.com',
                'default_role_id' => $supportRole->id,
                'roles' => [$warehouseRole->id],
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

        $response->assertRedirect(route('users.edit', $target));

        $target->refresh();

        $this->assertSame('Peter Admin', $target->name);
        $this->assertSame('peter.admin@example.com', $target->email);
        $this->assertFalse($target->is_active);
        $this->assertSame($supportRole->id, $target->default_role_id);
        $this->assertTrue($target->roles->contains($supportRole));
        $this->assertTrue($target->roles->contains($warehouseRole));
        $this->assertTrue(Hash::check('new-password-123', $target->password));
    }

    public function test_empty_password_does_not_change_existing_password(): void
    {
        $admin = $this->createAdminUser();
        $target = User::factory()->create([
            'password' => 'original-password-123',
            'is_active' => true,
        ]);
        $originalPasswordHash = $target->password;

        $response = $this
            ->actingAs($admin)
            ->patch(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'is_active' => '1',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertRedirect(route('users.edit', $target));

        $target->refresh();

        $this->assertSame($originalPasswordHash, $target->password);
    }

    private function createAdminUser(): User
    {
        $role = Role::query()->create([
            'code' => 'admin',
            'name' => 'Administrátor',
        ]);

        $user = User::factory()->create([
            'default_role_id' => $role->id,
            'is_active' => true,
        ]);

        $user->roles()->attach($role);

        return $user;
    }
}
