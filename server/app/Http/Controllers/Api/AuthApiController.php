<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PhoneNumber;
use App\Services\AvatarService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'active' => true,
        ], (bool) ($credentials['remember'] ?? false))) {
            return response()->json(['message' => 'E-mail ou senha inválidos.', 'errors' => ['email' => ['E-mail ou senha inválidos.']]], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => $this->userPayload($request->user()),
            'redirect_to' => $this->destination($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sessão encerrada.']);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        PasswordBroker::sendResetLink($request->only('email'));

        return response()->json(['message' => 'Se o e-mail estiver cadastrado, enviaremos um link para redefinir a senha.']);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        $status = PasswordBroker::reset($data, function (User $user, string $password) {
            $user->forceFill([
                'password' => $password,
                'must_change_password' => false,
                'password_changed_at' => now(),
                'remember_token' => Str::random(60),
            ])->save();
            event(new PasswordReset($user));
        });

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            return response()->json(['message' => __($status), 'errors' => ['email' => [__($status)]]], 422);
        }

        return response()->json(['message' => 'Senha redefinida. Você já pode entrar.']);
    }

    public function changeTemporaryPassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        $request->user()->update([
            'password' => $data['password'],
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);
        $request->session()->regenerate();

        return response()->json(['message' => 'Senha definitiva criada com sucesso.', 'user' => $this->userPayload($request->user()->fresh())]);
    }

    public function profile(Request $request)
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    public function updateProfile(Request $request, AvatarService $avatars)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', File::image()->max(2 * 1024)],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->update(collect($data)->except(['avatar', 'remove_avatar'])->all());
        $avatars->update($user, $request->file('avatar'), $request->boolean('remove_avatar'));

        return response()->json(['message' => 'Perfil atualizado.', 'user' => $this->userPayload($user->fresh())]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        $request->user()->update([
            'password' => $data['password'],
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }

    private function destination(User $user): string
    {
        if ($user->must_change_password) {
            return '/primeiro-acesso';
        }

        $destinations = [
            'dashboard.view' => '/dashboard',
            'workouts.view' => '/treinos',
            'shop.view' => '/loja',
            'members.view' => '/alunos',
            'billing.view-own' => '/minhas-mensalidades',
            'users.manage' => '/usuarios',
            'permissions.manage' => '/permissoes',
        ];

        foreach ($destinations as $permission => $path) {
            if ($user->hasPermission($permission)) {
                return $path;
            }
        }

        return '/dashboard';
    }

    private function userPayload(User $user): array
    {
        $user->loadMissing('permissionGroup.permissions');
        $permissions = $user->isAdmin()
            ? ['*']
            : ($user->permissionGroup?->permissions->pluck('permission')->values()->all() ?? config('permissions.defaults.'.$user->role, []));

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'birth_date' => $user->birth_date?->toDateString(),
            'address' => $user->address,
            'active' => $user->active,
            'must_change_password' => $user->must_change_password,
            'permission_group_id' => $user->permission_group_id,
            'permissions' => $permissions,
            'avatar_url' => $user->avatar_url,
            'initials' => $user->initials,
        ];
    }
}
