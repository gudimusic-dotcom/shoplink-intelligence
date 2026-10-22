<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_task_center(): void
    {
        $user = $this->createAdminUser();

        $response = $this
            ->actingAs($user)
            ->get(route('tasks.index'));

        $response
            ->assertOk()
            ->assertSee('Task Center')
            ->assertSee('Vytvoriť task');
    }

    public function test_user_can_create_task_category(): void
    {
        $user = $this->createAdminUser();

        $response = $this
            ->actingAs($user)
            ->post(route('task-categories.store'), [
                'name' => 'Stratený dokument',
                'description' => 'Úlohy súvisiace so stratou dokumentov.',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('task_categories', [
            'name' => 'Stratený dokument',
            'code' => 'strateny_dokument',
        ]);
    }

    public function test_user_can_create_task_with_category(): void
    {
        $user = $this->createAdminUser();
        $category = TaskCategory::query()->create([
            'code' => 'general',
            'name' => 'Všeobecné',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Kontaktovať zákazníka',
                'description' => 'Overiť stav objednávky.',
                'task_category_id' => $category->id,
                'priority' => 'high',
                'assigned_user_id' => $user->id,
                'due_at' => '2026-07-30 09:30:00',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Kontaktovať zákazníka',
            'task_category_id' => $category->id,
            'priority' => 'high',
            'status' => 'new',
            'assigned_user_id' => $user->id,
            'source_type' => User::class,
            'source_id' => $user->id,
        ]);
    }

    public function test_user_can_update_task_status(): void
    {
        $user = $this->createAdminUser();
        $category = TaskCategory::query()->create([
            'code' => 'order',
            'name' => 'Objednávka',
        ]);

        $task = Task::query()->create([
            'title' => 'Skontrolovať objednávku',
            'task_category_id' => $category->id,
            'priority' => 'normal',
            'status' => 'new',
            'source_type' => User::class,
            'source_id' => $user->id,
            'generated_by' => 'manual',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('tasks.status', $task), [
                'status' => 'done',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    public function test_order_task_rejects_non_order_status(): void
    {
        $user = $this->createAdminUser();
        $category = TaskCategory::query()->create([
            'code' => 'order',
            'name' => 'Objednávky',
        ]);
        $task = Task::query()->create([
            'title' => 'Objednať tovar',
            'task_category_id' => $category->id,
            'priority' => 'normal',
            'status' => 'new',
        ]);

        $this->actingAs($user)
            ->patch(route('tasks.status', $task), ['status' => 'in_progress'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'new']);
    }

    public function test_user_can_update_task_center_preferences(): void
    {
        $user = $this->createAdminUser();

        $response = $this
            ->actingAs($user)
            ->patch(route('tasks.preferences'), [
                'show_notes' => '1',
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'task_center_show_notes' => true,
        ]);
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
