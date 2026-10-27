<?php

namespace App\Support;

use Carbon\Carbon;

class Tampil
{
    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    private const BULAN_SINGKAT = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUN',
        7 => 'JUL', 8 => 'AGU', 9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

    /** Ambil tanggal dari kolom utama; kalau kosong pakai cadangan (mis. created_at). */
    public static function tanggal($nilai, $cadangan = null): ?Carbon
    {
        $v = $nilai ?: $cadangan;

        return $v ? Carbon::parse($v) : null;
    }

    /** Angka hari, contoh: 27 */
    public static function hari($t): string
    {
        return $t ? Carbon::parse($t)->format('d') : '-';
    }

    /** Bulan singkat, contoh: OKT */
    public static function bulan($t): string
    {
        return $t ? self::BULAN_SINGKAT[Carbon::parse($t)->month] : '';
    }

    /** Tanggal lengkap, contoh: 27 Oktober 2026 */
    public static function lengkap($t): string
    {
        if (! $t) {
            return '-';
        }

        $t = Carbon::parse($t);

        return $t->day . ' ' . self::BULAN[$t->month] . ' ' . $t->year;
    }

    /** Sama dengan lengkap(), dipakai di halaman kegiatan */
    public static function panjang($t): string
    {
        return self::lengkap($t);
    }

    /** Potong teks jadi ringkasan, contoh: ringkas($teks, 90) */
    public static function ringkas($teks, int $batas = 100): string
    {
        $teks = trim(strip_tags((string) $teks));

        return mb_strlen($teks) > $batas
            ? rtrim(mb_substr($teks, 0, $batas)) . '...'
            : $teks;
    }
}