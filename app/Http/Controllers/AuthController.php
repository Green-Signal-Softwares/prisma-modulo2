<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->role === 'atendente') {
                return redirect()->route('atendente.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            \App\Models\ActivityLog::writeLog('Autenticação', 'LOGIN', 'Usuário realizou login com sucesso no sistema.');

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            if (Auth::user()->role === 'atendente') {
                return redirect()->intended(route('atendente.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('As credenciais fornecidas estão incorretas.'),
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Models\ActivityLog::writeLog('Autenticação', 'LOGOUT', 'Usuário realizou logout do sistema.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
