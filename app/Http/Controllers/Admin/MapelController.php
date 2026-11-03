<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $mapel = Mapel::query()
            ->when($keyword !== '', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('pages.admin.mapel', compact('mapel', 'keyword'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('mapels', 'nama')],
        ], [
            'nama.required' => 'Nama mapel wajib diisi.',
            'nama.unique'   => 'Nama mapel sudah ada.',
        ]);

        Mapel::create($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil ditambahkan.');
    }

    public function update(Request $request, Mapel $mapel)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('mapels', 'nama')->ignore($mapel->id)],
        ], [
            'nama.required' => 'Nama mapel wajib diisi.',
            'nama.unique'   => 'Nama mapel sudah ada.',
        ]);

        $mapel->update($data);

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        if ($mapel->pengampu()->exists()) {
            return redirect()->route('admin.mapel.index')
                ->with('error', 'Mapel tidak bisa dihapus karena masih dipakai data pengampu.');
        }

        $mapel->delete();

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mapel berhasil dihapus.');
    }
}