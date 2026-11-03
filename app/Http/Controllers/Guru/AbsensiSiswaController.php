<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengampu;
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

        // Mapel mengikuti kelas yang dipilih
        $mapelList = $this->mapelDiizinkan($kelasId);

        // Kalau mapel dari request tidak ada di daftar (misal kelas diganti), pakai mapel pertama
        $mapelId = (int) $request->input('mapel_id');
        if (! $mapelList->contains('id', $mapelId)) {
            $mapelId = (int) optional($mapelList->first())->id;
        }

        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)->toDateString()
            : today()->toDateString();

        $siswas = $kelasId
            ? Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->orderBy('nis')->get()
            : collect();

        $absen = ($kelasId && $mapelId)
            ? AbsensiSiswa::where('kelas_id', $kelasId)
                ->where('mapel_id', $mapelId)
                ->whereDate('tanggal', $tanggal)
                ->get()->keyBy('siswa_id')
            : collect();

        return view('pages.guru.absensi-siswa', compact(
            'kelasList', 'mapelList', 'kelasId', 'mapelId', 'tanggal', 'siswas', 'absen'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelas_id'     => ['required', Rule::exists(Kelas::class, 'id')],
            'mapel_id'     => ['required', Rule::exists(Mapel::class, 'id')],
            'tanggal'      => ['required', 'date'],
            'status'       => ['required', 'array'],
            'status.*'     => [Rule::in(['hadir', 'sakit', 'izin', 'alpa'])],
            'keterangan'   => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless($this->kelasDiizinkan()->contains('id', (int) $data['kelas_id']), 403);
        abort_unless(
            $this->mapelDiizinkan((int) $data['kelas_id'])->contains('id', (int) $data['mapel_id']),
            403,
            'Anda tidak mengampu mapel ini di kelas tersebut.'
        );

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
                        [
                            'siswa_id' => $siswaId,
                            'mapel_id' => $data['mapel_id'],
                            'tanggal'  => $data['tanggal'],
                        ],
                        [
                            'kelas_id'     => $data['kelas_id'],
                            'status'       => $status,
                            'keterangan'   => $ket !== '' ? $ket : null,
                            'dicatat_oleh' => auth()->id(),
                        ]
                    );
                }
            });
        });

        return redirect()
            ->route('guru.absensi-siswa.index', [
                'kelas_id' => $data['kelas_id'],
                'mapel_id' => $data['mapel_id'],
                'tanggal'  => $data['tanggal'],
            ])
            ->with('success', 'Absensi siswa berhasil disimpan.');
    }

    /**
     * Penugasan guru (tabel pengampu). null = guru belum punya penugasan,
     * sehingga sementara tidak dibatasi (mode testing).
     */
    private function penugasan()
    {
        $guruId = auth()->user()->guru_id;

        if (! $guruId) {
            return null;
        }

        $rows = Pengampu::where('guru_id', $guruId)->get();

        return $rows->isEmpty() ? null : $rows;
    }

    // Kelas yang boleh diisi: kelas yang diampu guru. Kalau belum ada penugasan, semua kelas.
    private function kelasDiizinkan()
    {
        $p = $this->penugasan();

        return Kelas::when($p, fn ($q) => $q->whereIn('id', $p->pluck('kelas_id')->unique()))
            ->orderBy('nama_kelas')
            ->get();
    }

    // Mapel yang boleh diisi di kelas terpilih. Kalau belum ada penugasan, semua mapel.
    private function mapelDiizinkan(int $kelasId)
    {
        $p = $this->penugasan();

        return Mapel::when($p, fn ($q) => $q->whereIn(
                'id',
                $p->where('kelas_id', $kelasId)->pluck('mapel_id')->unique()
            ))
            ->orderBy('nama')
            ->get();
    }
}