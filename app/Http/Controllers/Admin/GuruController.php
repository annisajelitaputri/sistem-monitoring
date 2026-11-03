<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $gurus = Guru::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('nama', 'like', "%{$s}%")->orWhere('nip', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        $mapelList = Mapel::orderBy('nama')->get();

        return view('pages.admin.guru', compact('gurus', 'mapelList'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-guru', 'public');
        }

        $akunDibuat = false;

        DB::transaction(function () use ($data, &$akunDibuat) {
            $guru = Guru::create($data);

            // Akun login otomatis: username = NIP, kata sandi awal = NIP
            if (!User::where('username', $guru->nip)->exists()) {
                (new User)->forceFill([
                    'name'     => $guru->nama,
                    'username' => $guru->nip,
                    'password' => Hash::make($guru->nip),
                    'role'     => 'guru',
                    'status'   => $guru->status,
                    'guru_id'  => $guru->id,
                ])->save();
                $akunDibuat = true;
            }
        });

        $pesan = $akunDibuat
            ? 'Data guru ditambahkan. Akun login dibuat otomatis (username dan kata sandi awal = NIP).'
            : 'Data guru ditambahkan, tetapi akun belum dibuat karena username sudah dipakai. Buat manual di menu Akun pengguna.';

        return redirect()->route('admin.guru.index')->with('success', $pesan);
    }

    public function update(Request $request, Guru $guru)
    {
        $data = $this->validated($request, $guru->id);

        if ($request->hasFile('foto')) {
            if ($guru->foto) {
                Storage::disk('public')->delete($guru->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-guru', 'public');
        }

        $nipLama = $guru->nip;

        DB::transaction(function () use ($guru, $data, $nipLama) {
            $guru->update($data);

            $akun = User::where('guru_id', $guru->id)->first();
            if ($akun) {
                $akun->name   = $guru->nama;
                $akun->status = $guru->status;

                // Jika username masih sama dengan NIP lama, ikut diperbarui
                if ($akun->username === $nipLama && $nipLama !== $guru->nip
                    && !User::where('username', $guru->nip)->where('id', '!=', $akun->id)->exists()) {
                    $akun->username = $guru->nip;
                }

                $akun->save();
            }
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        try {
            DB::transaction(function () use ($guru) {
                User::where('guru_id', $guru->id)->delete();
                $guru->delete();
            });
        } catch (QueryException $e) {
            return back()->with('error', 'Guru masih terhubung dengan data lain (wali kelas atau absensi), jadi belum bisa dihapus.');
        }

        if ($guru->foto) {
            Storage::disk('public')->delete($guru->foto);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan akunnya berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nip'        => ['required', 'string', 'max:30', Rule::unique(Guru::class, 'nip')->ignore($ignoreId)],
            'nama'       => ['required', 'string', 'max:150'],
            'jabatan'    => ['required', 'array'],
            'jabatan.*'  => ['nullable', 'string', 'max:100'],
            'mapel'      => ['nullable', 'array'],
            'mapel.*'    => ['string', Rule::exists(Mapel::class, 'nama')],
            'pendidikan' => ['nullable', 'string', 'max:255'],
            'bio'        => ['nullable', 'string', 'max:2000'],
            'status'     => ['required', Rule::in(['aktif', 'nonaktif'])],
            'foto'       => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['foto']);

        $jabatan = array_values(array_unique(array_filter(
            array_map('trim', $data['jabatan'] ?? []),
            fn ($v) => $v !== ''
        )));

        if (empty($jabatan)) {
            throw ValidationException::withMessages(['jabatan' => 'Isi minimal satu jabatan.']);
        }

        $data['jabatan'] = $jabatan;
        $data['mapel']   = array_values(array_unique($data['mapel'] ?? []));

        foreach (['pendidikan', 'bio'] as $k) {
            $data[$k] = isset($data[$k]) && trim($data[$k]) !== '' ? trim($data[$k]) : null;
        }

        return $data;
    }
}