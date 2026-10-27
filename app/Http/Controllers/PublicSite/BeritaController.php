<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $kategori = in_array($request->kategori, ['berita', 'pengumuman'], true)
            ? $request->kategori
            : null;

        $query = Berita::where('status', 'terbit')
            ->when($kategori, fn ($q) => $q->where('kategori', $kategori));

        // Berita utama = yang terbaru
        $utama = (clone $query)->orderByDesc('tanggal')->orderByDesc('id')->first();

        // Sisanya (tanpa berita utama)
        $beritas = $query
            ->when($utama, fn ($q) => $q->where('id', '!=', $utama->id))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(6)
            ->withQueryString();

        return view('pages.public.berita', compact('beritas', 'utama', 'kategori'));
    }
}