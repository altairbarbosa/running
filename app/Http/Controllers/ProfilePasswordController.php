<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilePasswordController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }
}
