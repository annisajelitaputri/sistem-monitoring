<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    const TAHUN_AJARAN = '2026/2027';

    // Daftar kelas tetap (nama kelas => jurusan)
    const KELAS_OPSI = [
        'X TKJ'   => 'Teknik Komputer dan Jaringan',
        'X TKR'   => 'Teknik Kendaraan Ringan',
        'XI TKJ'  => 'Teknik Komputer dan Jaringan',
        'XI TKR'  => 'Teknik Kendaraan Ringan',
        'XII TKJ' => 'Teknik Komputer dan Jaringan',
        'XII TKR' => 'Teknik Kendaraan Ringan',
    ];

    public function index(Request $request)
    {
        $siswas = Siswa::with('kelas')
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('nama', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%");
            }))
            ->when($request->kelas, fn ($q, $k) => $q->whereHas('kelas', fn ($w) => $w->where('nama_kelas', $k)))
            ->orderBy('id')
            ->paginate(5)
            ->withQueryString();

        $kelasOpsi = array_keys(self::KELAS_OPSI);

        return view('pages.admin.siswa', compact('siswas', 'kelasOpsi'));
    }

    public function store(Request $request)
    {
        Siswa::create($this->dataSiswa($request));

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $siswa->update($this->dataSiswa($request, $siswa->id));

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    private function dataSiswa(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'nis'           => ['required', 'string', 'max:20', Rule::unique(Siswa::class, 'nis')->ignore($ignoreId)],
            'nama'          => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'kelas'         => ['required', Rule::in(array_keys(self::KELAS_OPSI))],
            'status'        => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        // Buat kelas di database kalau belum ada, lalu hubungkan ke siswa
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => $data['kelas'], 'tahun_ajaran' => self::TAHUN_AJARAN],
            ['jurusan' => self::KELAS_OPSI[$data['kelas']]]
        );

        unset($data['kelas']);
        $data['kelas_id'] = $kelas->id;

        return $data;
    }
}
