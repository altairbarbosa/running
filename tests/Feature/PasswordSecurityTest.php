<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TemporaryPasswordNotification;
use App\Notifications\ResetPasswordNotification;
use App\Services\TemporaryPasswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_temporary_password_is_strong_and_only_its_hash_is_stored(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Novo Professor',
            'email' => 'novo-professor@running.test',
            'role' => 'trainer',
            'active' => '1',
        ])->assertRedirect(route('users.index'));

        $user = User::where('email', 'novo-professor@running.test')->firstOrFail();
        $temporaryPassword = null;
        Notification::assertSentTo($user, TemporaryPasswordNotification::class, function ($notification) use (&$temporaryPassword) {
            $temporaryPassword = $notification->temporaryPassword;

            return true;
        });

        $this->assertMatchesRegularExpression('/[a-z]/', $temporaryPassword);
        $this->assertMatchesRegularExpression('/[A-Z]/', $temporaryPassword);
        $this->assertMatchesRegularExpression('/\d/', $temporaryPassword);
        $this->assertMatchesRegularExpression('/[^A-Za-z0-9]/', $temporaryPassword);
        $this->assertGreaterThanOrEqual(20, strlen($temporaryPassword));
        $this->assertNotSame($temporaryPassword, $user->password);
        $this->assertTrue(Hash::check($temporaryPassword, $user->password));
        $this->assertTrue($user->must_change_password);
    }

    public function test_temporary_password_requires_a_secure_replacement_before_access(): void
    {
        $user = User::factory()->create([
            'password' => 'Temporaria#2026',
            'must_change_password' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Temporaria#2026',
        ])->assertRedirect(route('password.change.edit'));

        $this->get(route('dashboard'))->assertRedirect(route('password.change.edit'));
        $this->put(route('password.change.update'), [
            'current_password' => 'Temporaria#2026',
            'password' => 'Definitiva#Segura2026',
            'password_confirmation' => 'Definitiva#Segura2026',
        ])->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertNotNull($user->password_changed_at);
        $this->assertTrue(Hash::check('Definitiva#Segura2026', $user->password));
    }

    public function test_staff_created_member_also_receives_a_temporary_password(): void
    {
        Notification::fake();
        $trainer = User::factory()->create(['role' => 'trainer']);

        $this->actingAs($trainer)->post(route('members.store'), [
            'name' => 'Novo Aluno',
            'email' => 'novo-aluno@running.test',
            'active' => '1',
        ])->assertRedirect(route('members.index'));

        $member = User::where('email', 'novo-aluno@running.test')->firstOrFail();
        $this->assertSame('member', $member->role);
        $this->assertTrue($member->must_change_password);
        Notification::assertSentTo($member, TemporaryPasswordNotification::class);
    }

    public function test_administrator_cannot_change_another_users_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'member', 'password' => 'SenhaOriginal#2026']);
        $originalHash = $user->password;

        $this->actingAs($admin)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'active' => '1',
            'password' => 'AlteradaPeloAdmin#2026',
            'password_confirmation' => 'AlteradaPeloAdmin#2026',
        ])->assertRedirect(route('users.index'));

        $this->assertSame($originalHash, $user->fresh()->password);
    }

    public function test_user_can_recover_password_with_a_single_use_email_token(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'SenhaAntiga#2026']);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'SenhaRecuperada#2026',
            'password_confirmation' => 'SenhaRecuperada#2026',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('SenhaRecuperada#2026', $user->fresh()->password));

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'OutraSenha#2026',
            'password_confirmation' => 'OutraSenha#2026',
        ])->assertSessionHasErrors('email');
    }

    public function test_administrative_forms_do_not_expose_password_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('users.index', ['modal' => 'create']))
            ->assertOk()
            ->assertDontSee('name="password"', false);
        $this->get(route('members.index', ['modal' => 'create']))
            ->assertOk()
            ->assertDontSee('name="password"', false);
    }

    public function test_temporary_password_generator_always_meets_policy(): void
    {
        $service = app(TemporaryPasswordService::class);

        foreach (range(1, 25) as $_) {
            $password = $service->generate();
            $this->assertMatchesRegularExpression('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{20,}$/', $password);
        }
    }
}
