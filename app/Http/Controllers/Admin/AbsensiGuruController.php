<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\Guru;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbsensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)->toDateString()
            : today()->toDateString();

        $guruAktif = Guru::where('status', 'aktif');

        $absen = AbsensiGuru::whereDate('tanggal', $tanggal)
            ->whereIn('guru_id', (clone $guruAktif)->pluck('id'))
            ->get()
            ->keyBy('guru_id');

        $stat = [
            // Dinas luar dihitung sebagai hadir (sedang bertugas)
            'hadir' => $absen->whereIn('status', ['hadir', 'dinas_luar'])->count(),
            'sakit' => $absen->where('status', 'sakit')->count(),
            'izin'  => $absen->where('status', 'izin')->count(),
            'belum' => (clone $guruAktif)->count() - $absen->count(),
        ];

        $gurus = (clone $guruAktif)->orderBy('nama')->paginate(4)->withQueryString();

        return view('pages.admin.absensi', compact('tanggal', 'absen', 'stat', 'gurus'));
    }

    public function koreksi(Request $request)
    {
        $data = $request->validate([
            'guru_id'    => ['required', Rule::exists(Guru::class, 'id')],
            'tanggal'    => ['required', 'date'],
            'status'     => ['nullable', Rule::in(['hadir', 'sakit', 'izin', 'dinas_luar'])],
            'jam_masuk'  => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $kunci = ['guru_id' => $data['guru_id'], 'tanggal' => $data['tanggal']];

        if (empty($data['status'])) {
            // "Belum absen" = hapus catatan hari itu
            AbsensiGuru::where($kunci)->delete();
        } else {
            AbsensiGuru::updateOrCreate($kunci, [
                'status'     => $data['status'],
                'jam_masuk'  => $data['jam_masuk'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.absensi-guru.index', ['tanggal' => $data['tanggal']])
            ->with('success', 'Absensi berhasil dikoreksi.');
    }
}
