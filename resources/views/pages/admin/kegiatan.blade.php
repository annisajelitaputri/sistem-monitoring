@extends('layouts.admin')

@section('title', 'Kelola kegiatan')
@section('page-title', 'Kelola kegiatan')
@section('page-subtitle', 'Kelola dokumentasi kegiatan sekolah')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-manage.css') }}">
@endpush

@section('content')
@include('partials.form-errors')

{{-- Fallback CSS modal: bobotnya 0 (:where) sehingga style.css tetap diutamakan --}}
<style>
    :where(.mg-modal) {
        position: fixed; inset: 0; z-index: 1000;
        display: flex; align-items: center; justify-content: center;
        padding: 20px; overflow-y: auto; background: rgba(8, 20, 60, .55);
    }
    .mg-modal[hidden] { display: none !important; }
    :where(.mg-modal-box) {
        width: min(560px, 100%); max-height: calc(100vh - 40px); overflow-y: auto;
        padding: 26px; border-radius: 16px; background: #fff;
    }
    :where(.mg-modal-box) label { display: block; margin: 14px 0 6px; font-weight: 700; font-size: 14px; }
    :where(.mg-modal-foot) { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
</style>

<form method="GET" action="{{ route('admin.kegiatan.index') }}" class="mg-toolbar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul kegiatan" class="mg-input mg-grow">
    <select name="tahun" class="mg-input mg-select" onchange="this.form.submit()">
        <option value="">Semua tahun</option>
        @foreach ($daftarTahun as $tahun)
            <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
        @endforeach
    </select>
    <button type="button" class="mg-btn-primary" onclick="openCreate()">+ Tambah kegiatan</button>
</form>

<div class="mg-card">
    <table class="mg-table">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th style="width:100px">Foto</th>
                <th>Judul</th>
                <th style="width:150px">Tanggal</th>
                <th style="width:130px">Status</th>
                <th style="width:170px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kegiatans as $i => $k)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $i }}</td>
                    <td>
                        @if ($k->foto)
                            <img class="mg-thumb" src="{{ asset('storage/' . $k->foto) }}" alt="">
                        @else
                            <div class="mg-thumb"></div>
                        @endif
                    </td>
                    <td>{{ $k->judul }}</td>
                    <td>{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td>
                        <span class="mg-badge {{ strtolower($k->status) === 'terbit' ? 'mg-green' : 'mg-yellow' }}">
                            {{ ucfirst(strtolower($k->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="mg-actions">
                            <button type="button" class="mg-btn"
                                data-id="{{ $k->id }}"
                                data-judul="{{ $k->judul }}"
                                data-deskripsi="{{ $k->deskripsi }}"
                                data-tanggal="{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->format('Y-m-d') : '' }}"
                                data-status="{{ strtolower($k->status) }}"
                                onclick="openEdit(this)">Edit</button>
                            <form method="POST" action="{{ route('admin.kegiatan.destroy', $k) }}"
                                  onsubmit="return confirm('Hapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mg-btn mg-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="mg-empty">Belum ada kegiatan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $kegiatans->links('partials.pagination') }}
</div>

{{-- Modal tambah / edit --}}
<div class="mg-modal" id="mgModal" hidden
     data-store-url="{{ route('admin.kegiatan.store') }}"
     data-update-url="{{ route('admin.kegiatan.update', '__ID__') }}"
     data-title-create="Tambah kegiatan"
     data-title-edit="Edit kegiatan">
    <div class="mg-modal-box">
        <h3 id="mgTitle">Tambah kegiatan</h3>
        <form method="POST" id="mgForm" enctype="multipart/form-data" action="{{ route('admin.kegiatan.store') }}">
            @csrf
            <input type="hidden" name="_method" id="mgMethod" value="PUT" disabled>

            <label for="fJudul">Judul</label>
            <input type="text" name="judul" id="fJudul" class="mg-input" required maxlength="200">

            <div class="mg-row">
                <div>
                    <label for="fTanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="fTanggal" class="mg-input" required>
                </div>
                <div>
                    <label for="fStatus">Status</label>
                    <select name="status" id="fStatus" class="mg-input">
                        <option value="terbit">Terbit</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <label for="fDeskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="fDeskripsi" rows="5" class="mg-input"></textarea>

            <label for="fFoto">Foto <small id="fHint"></small></label>
            <input type="file" name="foto" id="fFoto" accept="image/*" class="mg-input">

            <div class="mg-modal-foot">
                <button type="button" class="mg-btn mg-btn-lg" onclick="closeModal()">Batal</button>
                <button type="submit" class="mg-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection