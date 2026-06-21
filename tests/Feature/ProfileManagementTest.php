<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_and_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Novo Nome',
            'email' => 'novo@running.test',
            'phone' => '(53) 99999-1111',
            'avatar' => $this->image(),
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('Novo Nome', $user->name);
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_admin_can_create_user_with_role_and_avatar(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Professor Running',
            'email' => 'professor@running.test',
            'role' => 'trainer',
            'active' => 1,
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => $this->image(),
        ])->assertRedirect(route('users.index'));

        $trainer = User::where('email', 'professor@running.test')->firstOrFail();
        $this->assertSame('trainer', $trainer->role);
        Storage::disk('public')->assertExists($trainer->avatar_path);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $trainer = User::factory()->create(['role' => 'trainer']);

        $this->actingAs($trainer)->get(route('users.index'))->assertForbidden();
    }

    private function image(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );
    }
}
