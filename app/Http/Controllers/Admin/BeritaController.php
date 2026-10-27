<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $beritas = Berita::query()
            ->when($request->q, fn ($q, $cari) => $q->where('judul', 'like', "%{$cari}%"))
            ->when($request->kategori, fn ($q, $kat) => $q->where('kategori', $kat))
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.berita', compact('beritas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'required|in:berita,pengumuman',
            'tanggal'  => 'nullable|date',
            'isi'      => 'required|string',
            'gambar'   => 'nullable|image|max:2048',
            'status'   => 'required|in:draft,terbit',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $data['penulis_id'] = auth()->id();
        $data['tanggal']    = $data['tanggal'] ?? now();

        Berita::create($data);

        return redirect()->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'required|in:berita,pengumuman',
            'tanggal'  => 'required|date',
            'isi'      => 'required|string',
            'gambar'   => 'nullable|image|max:2048',
            'status'   => 'required|in:draft,terbit',
        ]);

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        } else {
            unset($data['gambar']); // gambar lama dipertahankan
        }

        $berita->update($data);

        // PESAN DIAGNOSIS (sementara)
        return redirect()->route('admin.berita.index')
            ->with('success', 'Diperbarui: ID ' . $berita->id . ' | judul sekarang: ' . $berita->fresh()->judul);
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $id = $berita->id;
        $jumlah = $berita->delete();

        // PESAN DIAGNOSIS (sementara)
        return redirect()->route('admin.berita.index')
            ->with('success', 'Hapus ID ' . $id . ' | hasil: ' . var_export($jumlah, true) . ' | sisa di database: ' . Berita::count());
    }
}