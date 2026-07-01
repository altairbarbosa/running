<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        PasswordBroker::sendResetLink($request->only('email'));

        return back()->with('status', 'Se o e-mail estiver cadastrado, enviaremos um link para redefinir a senha.');
    }

    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->string('email')]);
    }

    public function reset(Request $request)
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
            return back()->withErrors(['email' => __($status)])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Senha redefinida. Você já pode entrar.');
    }
}
