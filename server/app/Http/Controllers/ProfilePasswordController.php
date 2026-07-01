<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfilePasswordController extends Controller
{
    public function update(Request $request)
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

        return back()->with('success', 'Senha alterada com sucesso.');
    }
}
