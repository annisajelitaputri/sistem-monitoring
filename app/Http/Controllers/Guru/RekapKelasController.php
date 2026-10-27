<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
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
        $nama = 'rekap-kelas-' . Str::slug($d['kelas']->nama_kelas ?? 'kelas') . '-' . $d['label'] . '.csv';

        return response()->streamDownload(function () use ($d) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM supaya karakter terbaca benar di Excel
            fputcsv($out, ['No', 'Nama', 'H', 'S', 'I', 'A', 'Total', '% Hadir'], ';');
            foreach ($d['rows'] as $i => $r) {
                fputcsv($out, [$i + 1, $r['nama'], $r['h'], $r['s'], $r['i'], $r['a'], $r['total'], $r['persen'] . '%'], ';');
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
        $guruId = auth()->user()->guru_id;
        $kelasList = RekapSiswa::kelasUntukGuru($guruId);

        $kelasId = (int) ($request->input('kelas_id') ?: optional($kelasList->first())->id);
        abort_if($kelasId && ! $kelasList->contains('id', $kelasId), 403);

        $kelas = $kelasList->firstWhere('id', $kelasId);

        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = (int) $request->input('tahun', now()->year);

        $start = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->startOfDay();

        $siswas = $kelas
            ? Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->orderBy('nama')->get(['id', 'nama'])
            : collect();

        return [
            'guruId'    => $guruId,
            'kelasList' => $kelasList,
            'kelas'     => $kelas,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'label'     => $tahun . '-' . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT),
            'namaBulan' => RekapSiswa::BULAN[$bulan],
            'rows'      => RekapSiswa::hitung($siswas, $start, $end),
        ];
    }
}
