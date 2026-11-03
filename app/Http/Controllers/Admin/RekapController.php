<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    // Hari yang diberi warna abu-abu dan tidak dihitung alpa. Kalau sekolah masuk Sabtu, hapus SATURDAY.
    const HARI_LIBUR = [Carbon::SATURDAY, Carbon::SUNDAY];

    const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        return view('pages.admin.rekap', $this->siapkan($request) + ['bulanList' => self::BULAN]);
    }

    // Ekspor Excel (CSV, bisa dibuka langsung di Excel)
    public function excel(Request $request)
    {
        $d = $this->siapkan($request);

        $nama = 'rekap-' . $d['jenis']
              . ($d['jenis'] === 'siswa' ? '-' . Str::slug(($d['kelas']->nama_kelas ?? 'kelas') . '-' . ($d['mapel']->nama ?? 'mapel')) : '')
              . '-' . $d['label'] . '.csv';

        return response()->streamDownload(function () use ($d) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya terbaca benar di Excel

            fputcsv($out, ['Rekap kehadiran ' . $d['jenis']], ';');
            if ($d['jenis'] === 'siswa') {
                fputcsv($out, ['Kelas', $d['kelas']->nama_kelas ?? '-'], ';');
                fputcsv($out, ['Mapel', $d['mapel']->nama ?? '-'], ';');
            }
            fputcsv($out, ['Periode', $d['namaBulan'] . ' ' . $d['tahun']], ';');
            fputcsv($out, [], ';');

            $head = ['No', $d['jenis'] === 'guru' ? 'NIP' : 'NIS', 'Nama'];
            foreach ($d['hari'] as $h) {
                $head[] = $h['tgl'];
            }
            fputcsv($out, array_merge($head, ['H', 'S', 'I', 'A', 'Total', '% Hadir']), ';');

            foreach ($d['rows'] as $i => $r) {
                $line = [$i + 1, $r['id'], $r['nama']];
                foreach ($d['hari'] as $h) {
                    $line[] = $r['cells'][$h['tgl']] ?? '';
                }
                fputcsv($out, array_merge($line, [$r['h'], $r['s'], $r['i'], $r['a'], $r['total'], $r['persen'] . '%']), ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Ekspor PDF: langsung terunduh sebagai file .pdf
    public function pdf(Request $request)
    {
        $d = $this->siapkan($request);

        $nama = 'rekap-' . $d['jenis']
              . ($d['jenis'] === 'siswa' ? '-' . Str::slug(($d['kelas']->nama_kelas ?? 'kelas') . '-' . ($d['mapel']->nama ?? 'mapel')) : '')
              . '-' . $d['label'] . '.pdf';

        return Pdf::loadView('pages.admin.rekap-print', $d)
            ->setPaper('a4', 'landscape')
            ->download($nama);
    }

    private function siapkan(Request $request): array
    {
        $jenis = $request->input('jenis') === 'guru' ? 'guru' : 'siswa';
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->startOfDay();

        // Kolom tanggal
        $hari = [];
        for ($d = 1; $d <= $start->daysInMonth; $d++) {
            $tgl = Carbon::create($tahun, $bulan, $d)->startOfDay();
            $hari[] = [
                'tgl'   => $d,
                'libur' => in_array($tgl->dayOfWeek, self::HARI_LIBUR, true),
                'lewat' => $tgl->lte(today()),   // hari yang belum terjadi tidak dihitung
            ];
        }

        // Daftar tahun: dari data absensi yang ada + tahun berjalan
        $tahunList = AbsensiSiswa::selectRaw('YEAR(tanggal) as y')->distinct()->pluck('y')
            ->merge(AbsensiGuru::selectRaw('YEAR(tanggal) as y')->distinct()->pluck('y'))
            ->push(now()->year)->push($tahun)
            ->map(fn ($y) => (int) $y)->unique()->sortDesc()->values();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::orderBy('nama')->get();
        $kelas = null;
        $mapel = null;
        $rows  = [];

        $range = [$start->toDateString(), $end->toDateString()];

        if ($jenis === 'siswa') {
            $kelas = $kelasList->firstWhere('id', (int) $request->input('kelas_id')) ?? $kelasList->first();
            $mapel = $mapelList->firstWhere('id', (int) $request->input('mapel_id')) ?? $mapelList->first();

            $siswas = $kelas
                ? Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->orderBy('nis')->get(['id', 'nis', 'nama'])
                : collect();

            $absensi = ($kelas && $mapel)
                ? AbsensiSiswa::where('kelas_id', $kelas->id)
                    ->where('mapel_id', $mapel->id)
                    ->whereBetween('tanggal', $range)
                    ->get()->groupBy('siswa_id')
                : collect();

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
                $rows[] = $this->baris($s->nis, $s->nama, $cells, $hit['hadir'], $hit['sakit'], $hit['izin'], $hit['alpa']);
            }
        } else {
            $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get(['id', 'nip', 'nama']);

            $absensi = AbsensiGuru::whereBetween('tanggal', $range)->get()->groupBy('guru_id');

            foreach ($gurus as $g) {
                $cells = [];
                $h = $s = $i = $a = 0;

                foreach ($absensi->get($g->id, collect()) as $row) {
                    $tgl = (int) $row->tanggal->day;

                    switch ($row->status) {
                        case 'hadir':      $cells[$tgl] = 'H'; $h++; break;
                        case 'dinas_luar': $cells[$tgl] = 'D'; $h++; break; // dinas luar dihitung hadir
                        case 'sakit':      $cells[$tgl] = 'S'; $s++; break;
                        case 'izin':       $cells[$tgl] = 'I'; $i++; break;
                        default:           $cells[$tgl] = 'A'; $a++;
                    }
                }

                // Hari kerja yang sudah lewat tanpa catatan dianggap alpa
                foreach ($hari as $x) {
                    if (! $x['libur'] && $x['lewat'] && ! isset($cells[$x['tgl']])) {
                        $cells[$x['tgl']] = 'A';
                        $a++;
                    }
                }

                $rows[] = $this->baris($g->nip, $g->nama, $cells, $h, $s, $i, $a);
            }
        }

        return [
            'jenis'     => $jenis,
            'kelasList' => $kelasList,
            'kelas'     => $kelas,
            'mapelList' => $mapelList,
            'mapel'     => $mapel,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'tahunList' => $tahunList,
            'label'     => $tahun . '-' . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT),
            'namaBulan' => self::BULAN[$bulan],
            'hari'      => $hari,
            'rows'      => $rows,
        ];
    }

    private function baris($id, string $nama, array $cells, int $h, int $s, int $i, int $a): array
    {
        $total = $h + $s + $i + $a;

        return [
            'id'     => $id,
            'nama'   => $nama,
            'cells'  => $cells,
            'h'      => $h,
            's'      => $s,
            'i'      => $i,
            'a'      => $a,
            'total'  => $total,
            'persen' => $total > 0 ? (int) round($h / $total * 100) : 0,
        ];
    }
}