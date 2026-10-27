<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::with('waliKelas')
            ->withCount('siswa')
            ->when($request->q, fn ($q, $s) => $q->where('nama_kelas', 'like', "%{$s}%"))
            ->when($request->tahun_ajaran, fn ($q, $t) => $q->where('tahun_ajaran', $t))
            ->orderBy('id')
            ->paginate(4)
            ->withQueryString();

        $tahunList = Kelas::select('tahun_ajaran')->distinct()->orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');
        $guruList  = Guru::orderBy('nama')->get();

        return view('pages.admin.kelas', compact('kelas', 'tahunList', 'guruList'));
    }

    public function store(Request $request)
    {
        Kelas::create($this->validated($request));

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $kelas->update($this->validated($request));

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        if ($kelas->siswa()->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa. Pindahkan atau hapus siswanya dulu.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_kelas'    => ['required', 'string', 'max:50'],
            'jurusan'       => ['required', 'string', 'max:100'],
            'tahun_ajaran'  => ['required', 'string', 'max:20'],
            'wali_kelas_id' => ['nullable', Rule::exists(Guru::class, 'id')],
        ]);
    }
}
