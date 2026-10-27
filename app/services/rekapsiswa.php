<?php

namespace App\Services;

use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Perhitungan rekap kehadiran siswa untuk panel guru.
 */
class RekapSiswa
{
    // Hari yang tidak dihitung sebagai hari efektif. Kalau sekolah masuk Sabtu, hapus SATURDAY.
    public const HARI_LIBUR = [Carbon::SATURDAY, Carbon::SUNDAY];

    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Kelas yang boleh dilihat/diisi guru: kelas tempat ia menjadi wali kelas.
     * Kalau guru bukan wali kelas, semua kelas diperbolehkan.
     */
    public static function kelasUntukGuru(?int $guruId): Collection
    {
        $milik = $guruId
            ? Kelas::where('wali_kelas_id', $guruId)->orderBy('nama_kelas')->get()
            : collect();

        return $milik->isNotEmpty() ? $milik : Kelas::orderBy('nama_kelas')->get();
    }

    /**
     * Hitung H, S, I, A, total, dan persen hadir per siswa.
     *
     * @param  Collection  $siswas  koleksi model Siswa (butuh id & nama)
     */
    public static function hitung(Collection $siswas, Carbon $start, Carbon $end): Collection
    {
        // Hari yang belum terjadi tidak dihitung sebagai alpa
        if ($end->gt(today())) {
            $end = today();
        }

        $total = self::hariEfektif($start, $end);

        $rekap = $siswas->isEmpty()
            ? collect()
            : AbsensiSiswa::whereIn('siswa_id', $siswas->pluck('id'))
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                ->selectRaw('siswa_id, status, count(*) as n')
                ->groupBy('siswa_id', 'status')
                ->get()
                ->groupBy('siswa_id');

        return $siswas->map(function ($s) use ($rekap, $total) {
            $c = $rekap->get($s->id, collect())->pluck('n', 'status');

            $h = (int) ($c['hadir'] ?? 0);
            $sakit = (int) ($c['sakit'] ?? 0);
            $izin = (int) ($c['izin'] ?? 0);
            $alpa = max($total - $h - $sakit - $izin, 0);

            return [
                'nama'   => $s->nama,
                'h'      => $h,
                's'      => $sakit,
                'i'      => $izin,
                'a'      => $alpa,
                'total'  => $total,
                'persen' => $total > 0 ? min(100, (int) round($h / $total * 100)) : 0,
            ];
        })->values();
    }

    public static function hariEfektif(Carbon $start, Carbon $end): int
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
