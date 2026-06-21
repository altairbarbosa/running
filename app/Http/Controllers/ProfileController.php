<?php

namespace App\Http\Controllers;

use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request, AvatarService $avatars)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', File::image()->max(2 * 1024)],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->update(collect($data)->except(['avatar', 'remove_avatar'])->all());
        $avatars->update($user, $request->file('avatar'), $request->boolean('remove_avatar'));

        return back()->with('success', 'Perfil atualizado.');
    }
}
