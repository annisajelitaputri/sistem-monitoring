<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $username = trim($request->login);

        // ===== DEBUG SEMENTARA (hapus setelah masalah selesai) =====
        $u = User::where('username', $username)->first();
        logger('LOGIN DEBUG', [
            'input_login'    => $username,
            'user_ditemukan' => (bool) $u,
            'status_akun'    => $u->status ?? null,
            'panjang_sandi'  => strlen((string) $request->password),
            'sandi_cocok'    => $u ? Hash::check($request->password, $u->password) : null,
        ]);
        // ===== AKHIR DEBUG =====

        $credentials = [
            'username' => $username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

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