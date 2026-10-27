<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RekapController extends Controller
{
    const PER_PAGE = 6;

    // Hari yang tidak dihitung sebagai hari efektif. Kalau sekolah masuk Sabtu, hapus SATURDAY.
    const HARI_LIBUR = [Carbon::SATURDAY, Carbon::SUNDAY];

    const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        [$rows, $f] = $this->olah($request);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $data = new LengthAwarePaginator(
            $rows->forPage($page, self::PER_PAGE)->values(),
            $rows->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.admin.rekap', [
            'rows'      => $data,
            'f'         => $f,
            'kelasList' => Kelas::orderBy('nama_kelas')->get(),
            'bulanList' => self::BULAN,
        ]);
    }

    // Ekspor Excel (CSV, bisa dibuka langsung di Excel)
    public function excel(Request $request)
    {
        [$rows, $f] = $this->olah($request);
        $nama = 'rekap-kehadiran-' . $f['jenis'] . '-' . $f['label'] . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya karakter terbaca benar di Excel
            fputcsv($out, ['No', 'Nama', 'H', 'S', 'I', 'A', 'Total', '% Hadir'], ';');
            foreach ($rows as $i => $r) {
                fputcsv($out, [$i + 1, $r['nama'], $r['h'], $r['s'], $r['i'], $r['a'], $r['total'], $r['persen'] . '%'], ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Ekspor PDF: halaman cetak (otomatis membuka dialog Print -> Simpan sebagai PDF)
    public function pdf(Request $request)
    {
        [$rows, $f] = $this->olah($request);

        return view('pages.admin.rekap-print', compact('rows', 'f'));
    }

    private function olah(Request $request): array
    {
        $jenis   = $request->input('jenis') === 'guru' ? 'guru' : 'siswa';
        $periode = $request->input('periode') === 'tahunan' ? 'tahunan' : 'bulanan';
        $bulan   = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun   = (int) $request->input('tahun', now()->year);
        $kelasId = $request->input('kelas_id');

        if ($periode === 'tahunan') {
            $start = Carbon::create($tahun, 1, 1)->startOfDay();
            $end   = Carbon::create($tahun, 12, 31)->startOfDay();
            $label = (string) $tahun;
        } else {
            $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
            $end   = $start->copy()->endOfMonth()->startOfDay();
            $label = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        }

        // Hari yang belum terjadi tidak dihitung sebagai alpa
        if ($end->gt(today())) {
            $end = today();
        }

        $total = $this->hariEfektif($start, $end);
        $range = [$start->toDateString(), $end->toDateString()];

        if ($jenis === 'guru') {
            $orang = Guru::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama']);
            $rekap = AbsensiGuru::whereBetween('tanggal', $range)
                ->selectRaw('guru_id as pid, status, count(*) as n')
                ->groupBy('guru_id', 'status')->get()->groupBy('pid');
        } else {
            $orang = Siswa::where('status', 'aktif')
                ->when($kelasId, fn ($q, $k) => $q->where('kelas_id', $k))
                ->orderBy('nama')->get(['id', 'nama']);
            $rekap = AbsensiSiswa::whereBetween('tanggal', $range)
                ->selectRaw('siswa_id as pid, status, count(*) as n')
                ->groupBy('siswa_id', 'status')->get()->groupBy('pid');
        }

        $rows = $orang->map(function ($o) use ($rekap, $total) {
            $c = $rekap->get($o->id, collect())->pluck('n', 'status');

            $h = ($c['hadir'] ?? 0) + ($c['dinas_luar'] ?? 0);
            $s = $c['sakit'] ?? 0;
            $i = $c['izin'] ?? 0;
            $a = max($total - $h - $s - $i, 0);

            return [
                'nama'   => $o->nama,
                'h'      => $h,
                's'      => $s,
                'i'      => $i,
                'a'      => $a,
                'total'  => $total,
                'persen' => $total > 0 ? min(100, (int) round($h / $total * 100)) : 0,
            ];
        })->values();

        $f = compact('jenis', 'periode', 'bulan', 'tahun', 'label') + ['kelas_id' => $kelasId];

        return [$rows, $f];
    }

    private function hariEfektif(Carbon $start, Carbon $end): int
    {
        if ($end->lt($start)) {
            return 0;
        }

        $n = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if (! in_array($d->dayOfWeek, self::HARI_LIBUR, true)) {
                $n++;
            }
        }

        return $n;
    }
}
