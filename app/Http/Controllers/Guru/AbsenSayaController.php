<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbsenSayaController extends Controller
{
    public const STATUS = [
        'hadir'      => 'Hadir',
        'sakit'      => 'Sakit',
        'izin'       => 'Izin',
        'dinas_luar' => 'Dinas luar',
    ];

    public function index()
    {
        $guruId = auth()->user()->guru_id;

        $hariIni = null;
        $riwayat = collect();

        if ($guruId) {
            $hariIni = AbsensiGuru::where('guru_id', $guruId)->whereDate('tanggal', today())->first();
            $riwayat = AbsensiGuru::where('guru_id', $guruId)->orderByDesc('tanggal')->limit(5)->get();
        }

        return view('pages.guru.absen-saya', [
            'guruId'     => $guruId,
            'hariIni'    => $hariIni,
            'riwayat'    => $riwayat,
            'statusList' => self::STATUS,
        ]);
    }

    public function store(Request $request)
    {
        $guruId = auth()->user()->guru_id;

        abort_unless($guruId, 403, 'Akun Anda belum terhubung dengan data guru.');

        $data = $request->validate([
            'status'     => ['required', Rule::in(array_keys(self::STATUS))],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        // Satu catatan per guru per hari: kalau sudah ada, diperbarui
        $absen = AbsensiGuru::where('guru_id', $guruId)->whereDate('tanggal', today())->first()
            ?? (new AbsensiGuru)->forceFill(['guru_id' => $guruId, 'tanggal' => today()->toDateString()]);

        $keterangan = trim($data['keterangan'] ?? '');

        $absen->forceFill([
            'status'     => $data['status'],
            'keterangan' => $keterangan !== '' ? $keterangan : null,
            // jam masuk dicatat saat pertama kali absen, tidak berubah saat diedit
            'jam_masuk'  => $absen->jam_masuk ?: now()->format('H:i:s'),
        ])->save();

        return redirect()->route('guru.absen-saya.index')->with('success', 'Absen Anda hari ini berhasil disimpan.');
    }
}
