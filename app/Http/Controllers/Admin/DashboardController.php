<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Hanya admin yang boleh masuk
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403, 'Akses khusus admin.');

        // Tambahkan ?demo=1 di URL untuk menampilkan data contoh persis seperti desain Figma
        $useDemo = request()->boolean('demo');
        $data = $useDemo ? $this->dataContoh() : $this->dataDatabase();

        $total = $data['siswaHadir'] + $data['siswaTidakHadir'];
        $data['persen'] = $total > 0 ? (int) round($data['siswaHadir'] / $total * 100) : 0;
        $data['demo'] = $useDemo;

        return view('pages.admin.dashboard', $data);
    }

    /**
     * Data dari database.
     * ASUMSI struktur tabel (sesuaikan jika nama kolom Anda berbeda):
     *  - absensi_siswa : siswa_id, tanggal, status ('hadir', 'izin', 'sakit', 'alpa')
     *  - absensi_guru  : guru_id, tanggal, status
     *  - siswa         : id, kelas_id, status
     *  - kelas         : id, nama_kelas, wali_kelas_id
     *  - gurus         : id, nama
     */
    private function dataDatabase(): array
    {
        $today = now()->toDateString();

        try {
            $siswaHadir = DB::table('absensi_siswa')->whereDate('tanggal', $today)->where('status', 'hadir')->count();
            $siswaTidakHadir = DB::table('absensi_siswa')->whereDate('tanggal', $today)->where('status', '!=', 'hadir')->count();
            $guruHadir = DB::table('absensi_guru')->whereDate('tanggal', $today)->where('status', 'hadir')->count();

            // Kehadiran 7 hari terakhir (persen siswa hadir per hari)
            $hari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $labels = [];
            $values = [];
            for ($i = 6; $i >= 0; $i--) {
                $tgl = now()->subDays($i);
                $hadir = DB::table('absensi_siswa')->whereDate('tanggal', $tgl)->where('status', 'hadir')->count();
                $semua = DB::table('absensi_siswa')->whereDate('tanggal', $tgl)->count();
                $labels[] = $hari[$tgl->dayOfWeek];
                $values[] = $semua > 0 ? round($hadir / $semua * 100) : 0;
            }

            // Kehadiran per kelas hari ini (persen)
            $perKelas = DB::table('kelas')
                ->selectRaw(
                    "kelas.nama_kelas,
                     (select count(*) from siswa where siswa.kelas_id = kelas.id) as total,
                     (select count(*) from absensi_siswa a join siswa s on s.id = a.siswa_id
                        where s.kelas_id = kelas.id and date(a.tanggal) = ? and a.status = 'hadir') as hadir",
                    [$today]
                )
                ->limit(8)
                ->get()
                ->map(fn ($k) => [
                    'nama'  => $k->nama_kelas,
                    'nilai' => $k->total > 0 ? (int) round($k->hadir / $k->total * 100) : 0,
                ])->all();

            // Kelas yang belum diabsen hari ini
            $belumDiabsen = DB::table('kelas')
                ->leftJoin('gurus', 'gurus.id', '=', 'kelas.wali_kelas_id')
                ->whereNotExists(function ($q) use ($today) {
                    $q->select(DB::raw(1))
                        ->from('absensi_siswa')
                        ->join('siswa', 'siswa.id', '=', 'absensi_siswa.siswa_id')
                        ->whereColumn('siswa.kelas_id', 'kelas.id')
                        ->whereDate('absensi_siswa.tanggal', $today);
                })
                ->select('kelas.nama_kelas as kelas', 'gurus.nama as guru')
                ->get()
                ->map(fn ($r) => ['kelas' => $r->kelas, 'guru' => $r->guru ?? '-'])
                ->all();

            return [
                'siswaHadir'      => $siswaHadir,
                'siswaTidakHadir' => $siswaTidakHadir,
                'guruHadir'       => $guruHadir,
                'chartLabels'     => $labels,
                'chartValues'     => $values,
                'perKelas'        => $perKelas,
                'belumDiabsen'    => $belumDiabsen,
            ];
        } catch (\Throwable $e) {
            // Jika tabel/kolom belum sesuai, tampilkan data kosong (bukan error)
            report($e);

            return [
                'siswaHadir'      => 0,
                'siswaTidakHadir' => 0,
                'guruHadir'       => 0,
                'chartLabels'     => ['Sab', 'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
                'chartValues'     => [0, 0, 0, 0, 0, 0, 0],
                'perKelas'        => [],
                'belumDiabsen'    => [],
            ];
        }
    }

    /** Data contoh sesuai desain Figma (buka /admin/dashboard?demo=1) */
    private function dataContoh(): array
    {
        return [
            'siswaHadir'      => 60,
            'siswaTidakHadir' => 6,
            'guruHadir'       => 15,
            'chartLabels'     => ['Sab', 'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
            'chartValues'     => [58, 72, 66, 82, 78, 94, 91],
            'perKelas'        => [
                ['nama' => 'X-1',  'nilai' => 78],
                ['nama' => 'X-2',  'nilai' => 50],
                ['nama' => 'XI-1', 'nilai' => 100],
                ['nama' => 'XI-2', 'nilai' => 66],
            ],
            'belumDiabsen'    => [
                ['kelas' => 'Kelas X',  'guru' => 'Armanto, S.Pd'],
                ['kelas' => 'Kelas XI', 'guru' => 'Wiwis Pratiwi, S.Pd'],
            ],
        ];
    }
}
