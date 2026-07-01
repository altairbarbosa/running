<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TemporaryPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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
            'password' => 'Nova-Senha#2026',
            'password_confirmation' => 'Nova-Senha#2026',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('Nova-Senha#2026', $user->fresh()->password));
    }

    public function test_admin_can_create_user_with_role_and_avatar(): void
    {
        Storage::fake('public');
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Professor Running',
            'email' => 'professor@running.test',
            'role' => 'trainer',
            'active' => 1,
            'avatar' => $this->image(),
        ])->assertRedirect(route('users.index'));

        $trainer = User::where('email', 'professor@running.test')->firstOrFail();
        $this->assertSame('trainer', $trainer->role);
        $this->assertTrue($trainer->must_change_password);
        $this->assertFalse(Hash::check('password', $trainer->password));
        Notification::assertSentTo($trainer, TemporaryPasswordNotification::class);
        Storage::disk('public')->assertExists($trainer->avatar_path);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $trainer = User::factory()->create(['role' => 'trainer']);

        $this->actingAs($trainer)->get(route('users.index'))->assertForbidden();
    }

    public function test_profile_and_logout_are_only_exposed_in_the_header_account_menu(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Abrir menu da conta')
            ->assertSee('Perfil')
            ->assertSee('Sair');

        preg_match('/<aside.*?<\/aside>/s', $response->getContent(), $sidebar);
        $this->assertNotEmpty($sidebar);
        $this->assertStringNotContainsString(route('profile.edit'), $sidebar[0]);
        $this->assertStringNotContainsString(route('logout'), $sidebar[0]);
    }

    public function test_phone_rejects_letters_in_user_member_and_profile_forms(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'phone' => '(53) 99999-0000']);
        $member = User::factory()->create(['role' => 'member', 'phone' => '(53) 98888-0000']);

        $this->actingAs($admin)->put(route('users.update', $member), [
            'name' => $member->name,
            'email' => $member->email,
            'role' => 'member',
            'active' => '1',
            'phone' => 'telefone com letras',
        ])->assertSessionHasErrors('phone');

        $this->put(route('members.update', $member), [
            'name' => $member->name,
            'email' => $member->email,
            'active' => '1',
            'phone' => 'abc99999999',
        ])->assertSessionHasErrors('phone');

        $this->put(route('profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => 'não é telefone',
        ])->assertSessionHasErrors('phone');

        $this->assertSame('(53) 98888-0000', $member->fresh()->phone);
        $this->assertSame('(53) 99999-0000', $admin->fresh()->phone);
    }

    public function test_personal_data_forms_expose_phone_and_length_constraints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([route('users.index', ['modal' => 'create']), route('members.index', ['modal' => 'create']), route('profile.edit')] as $url) {
            $this->actingAs($admin)->get($url)
                ->assertOk()
                ->assertSee('type="tel"', false)
                ->assertSee('inputmode="tel"', false)
                ->assertSee('maxlength="30"', false);
        }
    }

    private function image(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );
    }
}
