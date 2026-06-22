<?php

namespace Tests\Feature;

use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_permission_group_and_assign_it_to_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $trainer = User::factory()->create(['role' => 'trainer']);

        $this->actingAs($admin)->post(route('permissions.store'), [
            'name' => 'Somente treinos e loja',
            'description' => 'Acesso operacional reduzido.',
            'permissions' => ['dashboard.view', 'workouts.view', 'shop.view'],
        ])->assertRedirect();

        $group = PermissionGroup::where('key', 'somente-treinos-e-loja')->firstOrFail();
        $this->assertEqualsCanonicalizing(['dashboard.view', 'workouts.view', 'shop.view'], $group->permissions()->pluck('permission')->all());

        $this->put(route('users.update', $trainer), [
            'name' => $trainer->name,
            'email' => $trainer->email,
            'role' => 'trainer',
            'active' => '1',
            'permission_group_id' => $group->id,
        ])->assertRedirect(route('users.index'));

        $trainer->refresh();
        $this->assertTrue($trainer->hasPermission('workouts.view'));
        $this->assertFalse($trainer->hasPermission('members.view'));

        $this->actingAs($trainer)->get(route('workouts.index'))->assertOk();
        $this->get(route('shop.index'))->assertOk();
        $this->get(route('members.index'))->assertForbidden();
        $this->get(route('finance.index'))->assertForbidden();
        $this->get(route('permissions.index'))->assertForbidden();
    }

    public function test_admin_always_has_full_access_regardless_of_group(): void
    {
        $emptyGroup = PermissionGroup::create(['name' => 'Sem permissões', 'key' => 'sem-permissoes']);
        $admin = User::factory()->create(['role' => 'admin', 'permission_group_id' => $emptyGroup->id]);

        $this->actingAs($admin)->get(route('permissions.index'))
            ->assertOk()
            ->assertSee('Grupos de permissões');
        $this->get(route('finance.index'))->assertOk();
        $this->assertTrue($admin->hasPermission('permissions.manage'));
    }

    public function test_user_form_contains_permission_tab_and_group_selector(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('users.index', ['modal' => 'edit', 'user' => $admin->id]))
            ->assertOk()
            ->assertSee('Permissões')
            ->assertSee('Grupo atribuído')
            ->assertSee('name="permission_group_id"', false);
    }

    public function test_unknown_permissions_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('permissions.store'), [
            'name' => 'Grupo inválido',
            'permissions' => ['system.take-over'],
        ])->assertSessionHasErrors('permissions.0');
    }
}
