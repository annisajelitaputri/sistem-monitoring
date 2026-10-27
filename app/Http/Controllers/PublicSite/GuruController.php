<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Guru;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('pages.public.tenaga-pendidik', compact('gurus'));
    }
}