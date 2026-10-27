<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $guruId = auth()->user()->guru_id;

        // Kelas yang diampu guru sebagai wali kelas
        $kelas = $guruId
            ? Kelas::where('wali_kelas_id', $guruId)->orderBy('nama_kelas')->first()
            : null;

        $stat = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
        $sudahIsi = false;
        $terakhir = collect();

        if ($kelas) {
            $hariIni = AbsensiSiswa::where('kelas_id', $kelas->id)
                ->whereDate('tanggal', today())
                ->get();

            $sudahIsi = $hariIni->isNotEmpty();

            foreach (array_keys($stat) as $status) {
                $stat[$status] = $hariIni->where('status', $status)->count();
            }

            $terakhir = AbsensiSiswa::where('kelas_id', $kelas->id)
                ->selectRaw("tanggal, SUM(status = 'hadir') as hadir, SUM(status <> 'hadir') as tidak_hadir")
                ->groupBy('tanggal')
                ->orderByDesc('tanggal')
                ->limit(3)
                ->get()
                ->map(fn ($r) => [
                    'tanggal'     => Carbon::parse($r->tanggal)->format('d-m-Y'),
                    'hadir'       => (int) $r->hadir,
                    'tidak_hadir' => (int) $r->tidak_hadir,
                ]);
        }

        return view('pages.guru.dashboard', compact('kelas', 'stat', 'sudahIsi', 'terakhir'));
    }
}
