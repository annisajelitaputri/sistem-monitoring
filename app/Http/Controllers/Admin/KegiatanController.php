<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $kegiatans = Kegiatan::query()
            ->when($request->filled('q'), fn ($q) => $q->where('judul', 'like', '%' . $request->q . '%'))
            ->when($request->filled('tahun'), fn ($q) => $q->whereYear('tanggal', $request->tahun))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $daftarTahun = Kegiatan::selectRaw('YEAR(tanggal) as tahun')
            ->whereNotNull('tanggal')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('pages.admin.kegiatan', compact('kegiatans', 'daftarTahun'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kegiatan', 'public');
        }

        // unguarded: tanggal pasti tersimpan walau belum ada di $fillable model
        Kegiatan::unguarded(fn () => Kegiatan::create($data));

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $data = $this->validated($request);

        if ($request->hasFile('foto')) {
            if ($kegiatan->foto) {
                Storage::disk('public')->delete($kegiatan->foto);
            }
            $data['foto'] = $request->file('foto')->store('kegiatan', 'public');
        }

        $kegiatan->forceFill($data)->save();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        if ($kegiatan->foto) {
            Storage::disk('public')->delete($kegiatan->foto);
        }
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tanggal'   => 'required|date',
            'status'    => 'required|in:terbit,draft',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        unset($data['foto']);

        return $data;
    }
}
