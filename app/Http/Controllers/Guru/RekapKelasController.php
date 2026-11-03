<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengampu;
use App\Models\Siswa;
use App\Services\RekapSiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RekapKelasController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.guru.rekap-kelas', $this->siapkan($request) + ['bulanList' => RekapSiswa::BULAN]);
    }

    // Ekspor Excel (CSV, bisa dibuka langsung di Excel)
    public function excel(Request $request)
    {
        $d = $this->siapkan($request);
        $nama = 'rekap-' . Str::slug(($d['kelas']->nama_kelas ?? 'kelas') . '-' . ($d['mapel']->nama ?? 'mapel'))
              . '-' . $d['label'] . '.csv';

        return response()->streamDownload(function () use ($d) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya terbaca benar di Excel

            fputcsv($out, ['Rekap absensi siswa'], ';');
            fputcsv($out, ['Guru', auth()->user()->name], ';');
            fputcsv($out, ['Kelas', $d['kelas']->nama_kelas ?? '-'], ';');
            fputcsv($out, ['Mapel', $d['mapel']->nama ?? '-'], ';');
            fputcsv($out, ['Periode', $d['namaBulan'] . ' ' . $d['tahun']], ';');
            fputcsv($out, [], ';');

            $head = ['No', 'NIS', 'Nama'];
            foreach ($d['hari'] as $h) {
                $head[] = $h['tgl'];
            }
            fputcsv($out, array_merge($head, ['H', 'S', 'I', 'A', 'Total', '% Hadir']), ';');

            foreach ($d['rows'] as $i => $r) {
                $line = [$i + 1, $r['nis'], $r['nama']];
                foreach ($d['hari'] as $h) {
                    $line[] = $r['cells'][$h['tgl']] ?? '';
                }
                fputcsv($out, array_merge($line, [$r['h'], $r['s'], $r['i'], $r['a'], $r['total'], $r['persen'] . '%']), ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Ekspor PDF: halaman cetak (otomatis membuka dialog Print -> Simpan sebagai PDF)
    public function pdf(Request $request)
    {
        return view('pages.guru.rekap-kelas-print', $this->siapkan($request));
    }

    private function siapkan(Request $request): array
    {
        $kelasList = $this->kelasDiizinkan();

        $kelasId = (int) ($request->input('kelas_id') ?: optional($kelasList->first())->id);
        abort_if($kelasId && ! $kelasList->contains('id', $kelasId), 403);
        $kelas = $kelasList->firstWhere('id', $kelasId);

        // Mapel mengikuti kelas; kalau tidak valid, pakai mapel pertama
        $mapelList = $this->mapelDiizinkan($kelasId);
        $mapelId = (int) $request->input('mapel_id');
        if (! $mapelList->contains('id', $mapelId)) {
            $mapelId = (int) optional($mapelList->first())->id;
        }
        $mapel = $mapelList->firstWhere('id', $mapelId);

        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->startOfDay();

        // Daftar tahun: dari data absensi yang ada + tahun berjalan (otomatis bertambah)
        $tahunList = AbsensiSiswa::selectRaw('YEAR(tanggal) as y')->distinct()->pluck('y')
            ->push(now()->year)->push($tahun)
            ->map(fn ($y) => (int) $y)->unique()->sortDesc()->values();

        // Kolom tanggal
        $hari = [];
        for ($d = 1; $d <= $start->daysInMonth; $d++) {
            $hari[] = ['tgl' => $d, 'libur' => Carbon::create($tahun, $bulan, $d)->isWeekend()];
        }

        $siswas = $kelas
            ? Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->orderBy('nis')->get(['id', 'nis', 'nama'])
            : collect();

        $absensi = ($kelas && $mapel)
            ? AbsensiSiswa::where('kelas_id', $kelas->id)
                ->where('mapel_id', $mapel->id)
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                ->get()->groupBy('siswa_id')
            : collect();

        $rows = [];
        foreach ($siswas as $s) {
            $cells = [];
            $hit = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];

            foreach ($absensi->get($s->id, collect()) as $a) {
                $cells[(int) $a->tanggal->day] = strtoupper(substr($a->status, 0, 1));
                if (isset($hit[$a->status])) {
                    $hit[$a->status]++;
                }
            }

            // Total & persen dihitung dari hari yang SUDAH diabsen saja
            $total = array_sum($hit);

            $rows[] = [
                'nis'    => $s->nis,
                'nama'   => $s->nama,
                'cells'  => $cells,
                'h'      => $hit['hadir'],
                's'      => $hit['sakit'],
                'i'      => $hit['izin'],
                'a'      => $hit['alpa'],
                'total'  => $total,
                'persen' => $total > 0 ? round($hit['hadir'] / $total * 100) : 0,
            ];
        }

        return [
            'kelasList' => $kelasList,
            'kelas'     => $kelas,
            'mapelList' => $mapelList,
            'mapel'     => $mapel,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'tahunList' => $tahunList,
            'label'     => $tahun . '-' . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT),
            'namaBulan' => RekapSiswa::BULAN[$bulan],
            'hari'      => $hari,
            'rows'      => $rows,
        ];
    }

    // null = guru belum punya penugasan, tidak dibatasi (mode testing)
    private function penugasan()
    {
        $guruId = auth()->user()->guru_id;

        if (! $guruId) {
            return null;
        }

        $rows = Pengampu::where('guru_id', $guruId)->get();

        return $rows->isEmpty() ? null : $rows;
    }

    private function kelasDiizinkan()
    {
        $p = $this->penugasan();

        return Kelas::when($p, fn ($q) => $q->whereIn('id', $p->pluck('kelas_id')->unique()))
            ->orderBy('nama_kelas')
            ->get();
    }

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