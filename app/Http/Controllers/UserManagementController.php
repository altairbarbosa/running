<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PermissionGroup;
use App\Notifications\TemporaryPasswordNotification;
use App\Rules\PhoneNumber;
use App\Services\AvatarService;
use App\Services\TemporaryPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->with('permissionGroup')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($user) => $user->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->orderBy('name')->paginate(15)->withQueryString();

        $modalUser = $request->integer('user') ? User::with('permissionGroup')->find($request->integer('user')) : null;

        $permissionGroups = PermissionGroup::orderByDesc('is_system')->orderBy('name')->get();

        return view('users.index', compact('users', 'modalUser', 'permissionGroups'));
    }

    public function create()
    {
        return redirect()->route('users.index', ['modal' => 'create']);
    }

    public function store(Request $request, AvatarService $avatars, TemporaryPasswordService $passwords)
    {
        $data = $this->validated($request);
        $temporaryPassword = $passwords->generate();
        $data['password'] = $temporaryPassword;
        $data['must_change_password'] = true;
        $data['permission_group_id'] ??= PermissionGroup::where('key', $data['role'])->value('id');
        $user = User::create(Arr::except($data, ['avatar', 'remove_avatar']));
        $avatars->update($user, $request->file('avatar'));
        $user->notify(new TemporaryPasswordNotification($temporaryPassword));

        return redirect()->route('users.index')->with('success', 'Usuário criado. A senha temporária foi enviada por e-mail.');
    }

    public function edit(User $user)
    {
        return redirect()->route('users.index', ['modal' => 'edit', 'user' => $user->id]);
    }

    public function update(Request $request, User $user, AvatarService $avatars)
    {
        $data = $this->validated($request, $user);
        if ($request->user()->is($user)) {
            $data['role'] = $user->role;
            $data['active'] = true;
        }

        $user->update(Arr::except($data, ['avatar', 'remove_avatar']));
        $avatars->update($user, $request->file('avatar'), $request->boolean('remove_avatar'));

        return redirect()->route('users.index')->with('success', 'Usuário atualizado.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($request->user()->is($user), 422, 'Você não pode desativar sua própria conta.');
        $user->update(['active' => false]);

        return back()->with('success', 'Usuário desativado.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'trainer', 'member'])],
            'phone' => ['nullable', 'string', 'max:30', new PhoneNumber],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
            'permission_group_id' => ['nullable', 'exists:permission_groups,id'],
            'avatar' => ['nullable', File::image()->max(2 * 1024)],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);
    }
}
