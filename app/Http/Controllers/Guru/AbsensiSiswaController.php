<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AbsensiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = $this->kelasDiizinkan();

        $kelasId = (int) ($request->input('kelas_id') ?: optional($kelasList->first())->id);
        abort_if($kelasId && ! $kelasList->contains('id', $kelasId), 403);

        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)->toDateString()
            : today()->toDateString();

        $siswas = $kelasId
            ? Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get()
            : collect();

        $absen = $kelasId
            ? AbsensiSiswa::where('kelas_id', $kelasId)->whereDate('tanggal', $tanggal)->get()->keyBy('siswa_id')
            : collect();

        return view('pages.guru.absensi-siswa', compact('kelasList', 'kelasId', 'tanggal', 'siswas', 'absen'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelas_id'       => ['required', Rule::exists(Kelas::class, 'id')],
            'tanggal'        => ['required', 'date'],
            'status'         => ['required', 'array'],
            'status.*'       => [Rule::in(['hadir', 'sakit', 'izin', 'alpa'])],
            'keterangan'     => ['nullable', 'array'],
            'keterangan.*'   => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless($this->kelasDiizinkan()->contains('id', (int) $data['kelas_id']), 403);

        // Hanya siswa yang memang berada di kelas ini yang boleh disimpan
        $idSiswaKelas = Siswa::where('kelas_id', $data['kelas_id'])->pluck('id')->all();

        DB::transaction(function () use ($data, $idSiswaKelas) {
            AbsensiSiswa::unguarded(function () use ($data, $idSiswaKelas) {
                foreach ($data['status'] as $siswaId => $status) {
                    if (! in_array((int) $siswaId, $idSiswaKelas, true)) {
                        continue;
                    }

                    $ket = trim($data['keterangan'][$siswaId] ?? '');

                    AbsensiSiswa::updateOrCreate(
                        ['siswa_id' => $siswaId, 'tanggal' => $data['tanggal']],
                        ['kelas_id' => $data['kelas_id'], 'status' => $status, 'keterangan' => $ket !== '' ? $ket : null]
                    );
                }
            });
        });

        return redirect()
            ->route('guru.absensi-siswa.index', ['kelas_id' => $data['kelas_id'], 'tanggal' => $data['tanggal']])
            ->with('success', 'Absensi siswa berhasil disimpan.');
    }

    // Kelas yang boleh diisi: kelas wali guru ini. Kalau bukan wali kelas, semua kelas.
    private function kelasDiizinkan()
    {
        $milik = Kelas::where('wali_kelas_id', auth()->user()->guru_id)->orderBy('nama_kelas')->get();

        return $milik->isNotEmpty() ? $milik : Kelas::orderBy('nama_kelas')->get();
    }
}
