<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($user) => $user->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['managedUser' => new User]);
    }

    public function store(Request $request, AvatarService $avatars)
    {
        $data = $this->validated($request);
        $user = User::create(collect($data)->except(['avatar', 'remove_avatar'])->all());
        $avatars->update($user, $request->file('avatar'));

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['managedUser' => $user]);
    }

    public function update(Request $request, User $user, AvatarService $avatars)
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if ($request->user()->is($user)) {
            $data['role'] = 'admin';
            $data['active'] = true;
        }

        $user->update(collect($data)->except(['avatar', 'remove_avatar'])->all());
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
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['required', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', File::image()->max(2 * 1024)],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);
    }
}
