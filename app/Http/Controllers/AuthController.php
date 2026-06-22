<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()->must_change_password) {
            return redirect()->route('password.change.edit');
        }

        $destinations = [
            'dashboard.view' => 'dashboard',
            'workouts.view' => 'workouts.index',
            'shop.view' => 'shop.index',
            'members.view' => 'members.index',
            'billing.view-own' => 'portal.billing',
            'users.manage' => 'users.index',
            'permissions.manage' => 'permissions.index',
        ];
        foreach ($destinations as $permission => $route) {
            if ($request->user()->hasPermission($permission)) {
                return redirect()->intended(route($route));
            }
        }

        Auth::logout();

        return back()->withErrors(['email' => 'Sua conta não possui nenhum acesso habilitado.']);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
