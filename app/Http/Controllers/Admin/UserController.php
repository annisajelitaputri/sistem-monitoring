<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($w) use ($request) {
                    $w->where('name', 'like', '%' . $request->q . '%')
                      ->orWhere('username', 'like', '%' . $request->q . '%');
                });
            })
            ->when($request->filled('peran'), fn ($q) => $q->where('role', $request->peran))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $gurus = Guru::orderBy('nama')->get(['id', 'nama']);

        return view('pages.admin.users', compact('users', 'gurus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,guru',
            'status'   => 'required|in:aktif,nonaktif',
            'guru_id'  => ['nullable', 'required_if:role,guru', Rule::exists(Guru::class, 'id'), 'unique:users,guru_id'],
        ]);

        $data['username'] = trim($data['username']);
        $data['password'] = Hash::make($data['password']);
        $data['guru_id'] = $data['role'] === 'guru' ? ($data['guru_id'] ?? null) : null;

        (new User)->forceFill($data)->save();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        // ===== DEBUG SEMENTARA (hapus setelah masalah selesai) =====
        logger('EDIT AKUN', [
            'id'           => $user->id,
            'username'     => $request->username,
            'ada_password' => $request->filled('password'),
            'panjang'      => strlen((string) $request->password),
        ]);
        // ===== AKHIR DEBUG =====

        if ($request->input('role', $user->role) === 'guru'
            && !$request->filled('guru_id')
            && $user->guru_id) {
            $request->merge(['guru_id' => $user->guru_id]);
        }

        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:admin,guru',
            'status'   => 'required|in:aktif,nonaktif',
            'guru_id'  => ['nullable', 'required_if:role,guru', Rule::exists(Guru::class, 'id'), Rule::unique('users', 'guru_id')->ignore($user->id)],
        ]);

        $data['username'] = trim($data['username']);

        // Jangan sampai admin mengunci akunnya sendiri
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role;
            $data['status'] = 'aktif';
        }

        $data['guru_id'] = $data['role'] === 'guru' ? ($data['guru_id'] ?? null) : null;

        // Password baru di-hash; kalau kosong, password lama tetap dipakai
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->forceFill($data)->save();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')->withErrors(['Kamu tidak bisa menghapus akunmu sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }

    // Buat akun sekaligus untuk semua guru yang belum punya akun.
    // Username = NIP, kata sandi awal = NIP (admin bisa menggantinya lewat Edit).
    public function generate()
    {
        $dibuat = 0;
        $dilewati = 0;

        Guru::doesntHave('user')->get()->each(function ($guru) use (&$dibuat, &$dilewati) {
            $nip = trim($guru->nip);

            if (User::where('username', $nip)->exists()) {
                $dilewati++;
                return;
            }

            (new User)->forceFill([
                'name'     => $guru->nama,
                'username' => $nip,
                'password' => Hash::make($nip),
                'role'     => 'guru',
                'status'   => 'aktif',
                'guru_id'  => $guru->id,
            ])->save();

            $dibuat++;
        });

        $pesan = "$dibuat akun guru berhasil dibuat (username dan kata sandi awal = NIP).";
        if ($dilewati > 0) {
            $pesan .= " $dilewati dilewati karena username sudah dipakai.";
        }

        return redirect()->route('admin.users.index')->with('success', $pesan);
    }
}