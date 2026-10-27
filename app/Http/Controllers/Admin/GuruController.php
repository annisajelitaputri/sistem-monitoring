<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            ->paginate(4)
            ->withQueryString();

        return view('pages.admin.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-guru', 'public');
        }

        Guru::create($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
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

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        try {
            $guru->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Guru masih terhubung dengan data lain (wali kelas, absensi, atau akun), jadi belum bisa dihapus.');
        }

        if ($guru->foto) {
            Storage::disk('public')->delete($guru->foto);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nip'     => ['required', 'string', 'max:30', Rule::unique(Guru::class, 'nip')->ignore($ignoreId)],
            'nama'    => ['required', 'string', 'max:150'],
            'jabatan' => ['required', 'string', 'max:100'],
            'mapel'   => ['nullable', 'string', 'max:100'],
            'status'  => ['required', Rule::in(['aktif', 'nonaktif'])],
            'foto'    => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['foto']); // file ditangani terpisah

        return $data;
    }
}
