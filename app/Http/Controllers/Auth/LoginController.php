<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.public.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login.required'    => 'NIP atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Login memakai kolom "username" (admin: "admin", guru: NIP)
        $credentials = [
            'username' => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Arahkan sesuai role (kolom "role" pada tabel users: admin / guru)
            $role = Auth::user()->role ?? null;

            return match ($role) {
                'admin' => redirect()->intended('/admin/dashboard'),
                'guru'  => redirect()->intended('/guru/dashboard'),
                default => redirect()->route('beranda'),
            };
        }

        return back()
            ->withErrors(['login' => 'NIP/username atau kata sandi salah.'])
            ->onlyInput('login', 'remember');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}