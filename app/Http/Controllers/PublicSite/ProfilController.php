<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Guru;

class ProfilController extends Controller
{
    public function index()
    {
        return view('pages.public.profil');
    }

    public function tenagaPendidik()
    {
        $guru = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $data = [
            'kepala_sekolah' => [
                'nama' => 'Kepala Sekolah',
                'foto' => null,
                'nip' => null,
                'sambutan' => null,
            ],
        ];

        return view('pages.public.tenaga-pendidik', compact('guru', 'data'));
    }

    public function kontak()
    {
        return view('pages.public.kontak');
    }
}