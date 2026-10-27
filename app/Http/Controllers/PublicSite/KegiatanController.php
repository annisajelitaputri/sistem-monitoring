<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function index()
    {
        // Hanya kegiatan berstatus "terbit" yang tampil. Isinya dikelola admin.
        $kegiatans = Kegiatan::where('status', 'terbit')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(6);

        return view('pages.public.kegiatan', compact('kegiatans'));
    }

    public function show(Kegiatan $kegiatan)
    {
        abort_unless(strtolower($kegiatan->status) === 'terbit', 404);

        return view('pages.public.kegiatan-detail', compact('kegiatan'));
    }
}
