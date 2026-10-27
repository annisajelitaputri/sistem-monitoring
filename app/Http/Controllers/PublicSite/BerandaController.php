<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\{Berita, Guru, Kegiatan, Siswa};
use Carbon\Carbon;
use Illuminate\Support\Str;

class BerandaController extends Controller
{
    public function index()
    {
        // Aman untuk kolom bertipe Carbon maupun string, dan untuk nilai null
        $tgl = fn ($d) => $d ? Carbon::parse($d)->locale('id')->translatedFormat('d F Y') : '';
        $img = fn ($p) => $p ? asset('storage/' . $p) : null;
        $teks = fn ($s) => trim(html_entity_decode(strip_tags((string) $s), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        $berita = Berita::where('status', 'terbit')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->take(3)->get()
            ->map(fn ($b) => [
                'id'       => $b->id,
                'judul'    => (string) $b->judul,
                'kategori' => ucfirst(strtolower((string) $b->kategori)) ?: 'Berita',
                'tanggal'  => $tgl($b->tanggal),
                'ringkas'  => Str::limit($teks($b->isi), 90),
                'isi'      => $teks($b->isi),
                'gambar'   => $img($b->gambar),
            ]);

        $kegiatan = Kegiatan::where('status', 'terbit')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->take(4)->get()
            ->map(fn ($k) => [
                'id'       => $k->id,
                'judul'    => (string) $k->judul,
                'kategori' => 'Kegiatan',
                'tanggal'  => $tgl($k->tanggal),
                'isi'      => $teks($k->deskripsi),
                'gambar'   => $img($k->foto),
            ]);

        $program = collect([
            [
                'id' => 'tkj', 'kode' => 'TKJ', 'icon' => 'network', 'kategori' => 'Program Keahlian',
                'judul' => 'Teknik Komputer dan Jaringan',
                'ringkas' => 'Belajar merakit dan memperbaiki komputer, membangun jaringan, serta mengelola server dan keamanan data.',
                'isi' => "Teknik Komputer dan Jaringan membekali siswa dengan keterampilan merakit, memasang, dan memperbaiki perangkat komputer, membangun jaringan LAN/WAN dan nirkabel, serta mengelola server dan keamanan jaringan.\n\nYang dipelajari:\n• Perakitan dan perawatan komputer\n• Instalasi dan konfigurasi jaringan (LAN, WAN, Wi-Fi)\n• Administrasi sistem operasi dan server\n• Keamanan jaringan dan dasar pemrograman\n\nPeluang karier: teknisi jaringan, administrator sistem, teknisi komputer, IT support, atau wirausaha di bidang teknologi informasi.",
            ],
            [
                'id' => 'tkro', 'kode' => 'TKRO', 'icon' => 'car', 'kategori' => 'Program Keahlian',
                'judul' => 'Teknik Kendaraan Ringan Otomotif',
                'ringkas' => 'Belajar merawat, mendiagnosis, dan memperbaiki mesin serta sistem kelistrikan kendaraan ringan.',
                'isi' => "Teknik Kendaraan Ringan Otomotif melatih siswa merawat, mendiagnosis, dan memperbaiki kendaraan ringan, mulai dari mesin hingga sistem kelistrikan dan pemindah tenaga, dengan standar keselamatan kerja bengkel.\n\nYang dipelajari:\n• Perawatan berkala dan overhaul mesin\n• Sistem kelistrikan dan kelistrikan bodi\n• Sistem rem, suspensi, kemudi, dan chassis\n• Pemindah tenaga (kopling, transmisi, gardan)\n\nPeluang karier: mekanik bengkel, teknisi dealer, service advisor, teknisi kelistrikan kendaraan, atau wirausaha bengkel.",
            ],
        ]);

        return view('pages.public.beranda', [
            'berita'      => $berita,
            'kegiatan'    => $kegiatan,
            'program'     => $program,
            'jumlahSiswa' => Siswa::count(),
            'jumlahGuru'  => Guru::count(),
            // dikirim ke JS sebagai window.BERANDA
            'data' => [
                'berita'   => $berita->keyBy('id'),
                'kegiatan' => $kegiatan->keyBy('id'),
                'program'  => $program->keyBy('id'),
            ],
        ]);
    }
}