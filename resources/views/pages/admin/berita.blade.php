@extends('layouts.admin')

@section('title', 'Kelola berita')
@section('page-title', 'Kelola berita')
@section('page-subtitle', 'Kelola berita dan pengumuman sekolah')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-manage.css') }}">
@endpush

@section('content')
@include('partials.form-errors')

<form method="GET" action="{{ route('admin.berita.index') }}" class="mg-toolbar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul berita" class="mg-input mg-grow">
    <select name="kategori" class="mg-input mg-select" onchange="this.form.submit()">
        <option value="">Semua kategori</option>
        <option value="berita" @selected(request('kategori') === 'berita')>Berita</option>
        <option value="pengumuman" @selected(request('kategori') === 'pengumuman')>Pengumuman</option>
    </select>
    <button type="button" class="mg-btn-primary" data-berita-create>+ Tambah berita</button>
</form>

<div class="mg-card">
    <table class="mg-table">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th style="width:100px">Gambar</th>
                <th>Judul</th>
                <th style="width:150px">Kategori</th>
                <th style="width:130px">Status</th>
                <th style="width:150px">Tanggal</th>
                <th style="width:170px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beritas as $i => $b)
                <tr>
                    <td>{{ $beritas->firstItem() + $i }}</td>
                    <td>
                        @if ($b->gambar)
                            <img class="mg-thumb" src="{{ asset('storage/' . $b->gambar) }}" alt="">
                        @else
                            <div class="mg-thumb"></div>
                        @endif
                    </td>
                    <td>{{ $b->judul }}</td>
                    <td>{{ ucfirst($b->kategori) }}</td>
                    <td>
                        <span class="mg-badge {{ strtolower($b->status) === 'terbit' ? 'mg-green' : 'mg-yellow' }}">
                            {{ ucfirst(strtolower($b->status)) }}
                        </span>
                    </td>
                    <td>{{ $b->tanggal ? \Carbon\Carbon::parse($b->tanggal)->format('d-m-Y') : '-' }}</td>
                    <td>
                        <div class="mg-actions">
                            <button type="button" class="mg-btn"
                                data-berita-edit
                                data-id="{{ $b->id }}"
                                data-judul="{{ $b->judul }}"
                                data-kategori="{{ $b->kategori }}"
                                data-isi="{{ $b->isi }}"
                                data-tanggal="{{ $b->tanggal ? \Carbon\Carbon::parse($b->tanggal)->format('Y-m-d') : '' }}"
                                data-status="{{ strtolower($b->status) }}">Edit</button>
                            <form method="POST" action="{{ route('admin.berita.destroy', $b) }}"
                                  onsubmit="return confirm('Hapus berita ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mg-btn mg-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="mg-empty">Belum ada berita.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $beritas->links('partials.pagination') }}
</div>

{{-- Modal tambah / edit --}}
<div class="mg-modal" id="mgModal" hidden>
    <div class="mg-modal-box">
        <h3 id="mgTitle">Tambah berita</h3>
        <form method="POST" id="mgForm" enctype="multipart/form-data" action="{{ route('admin.berita.store') }}">
            @csrf
            <input type="hidden" name="_method" id="mgMethod" value="PUT" disabled>

            <label for="fJudul">Judul</label>
            <input type="text" name="judul" id="fJudul" class="mg-input" required maxlength="255">

            <div class="mg-row">
                <div>
                    <label for="fKategori">Kategori</label>
                    <select name="kategori" id="fKategori" class="mg-input">
                        <option value="berita">Berita</option>
                        <option value="pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label for="fTanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="fTanggal" class="mg-input" required>
                </div>
            </div>

            <label for="fStatus">Status</label>
            <select name="status" id="fStatus" class="mg-input">
                <option value="terbit">Terbit</option>
                <option value="draft">Draft</option>
            </select>

            <label for="fIsi">Isi</label>
            <textarea name="isi" id="fIsi" rows="5" class="mg-input" required></textarea>

            <label for="fGambar">Gambar <small id="fHint"></small></label>
            <input type="file" name="gambar" id="fGambar" accept="image/*" class="mg-input">

            <div class="mg-modal-foot">
                <button type="button" class="mg-btn mg-btn-lg" data-berita-close>Batal</button>
                <button type="submit" class="mg-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var modal = document.getElementById('mgModal');
    var form = document.getElementById('mgForm');
    var methodInput = document.getElementById('mgMethod');
    var storeUrl = @json(route('admin.berita.store'));
    var updateUrl = @json(route('admin.berita.update', '__ID__'));

    var editingId = null; // null = mode tambah, angka = mode edit

    function openCreate() {
        form.reset();
        editingId = null;
        document.getElementById('mgTitle').textContent = 'Tambah berita';
        document.getElementById('fTanggal').value = new Date().toLocaleDateString('en-CA');
        document.getElementById('fHint').textContent = '';
        modal.hidden = false;
    }

    function openEdit(btn) {
        form.reset();
        editingId = btn.dataset.id;
        document.getElementById('mgTitle').textContent = 'Edit berita';
        document.getElementById('fJudul').value = btn.dataset.judul;
        document.getElementById('fKategori').value = btn.dataset.kategori;
        document.getElementById('fTanggal').value = btn.dataset.tanggal;
        document.getElementById('fStatus').value = btn.dataset.status;
        document.getElementById('fIsi').value = btn.dataset.isi;
        document.getElementById('fHint').textContent = '(kosongkan jika tidak diganti)';
        modal.hidden = false;
    }

    function closeModal() { modal.hidden = true; }

    // Tombol-tombol (tanpa onclick, jadi tidak bisa bentrok dengan main.js)
    document.addEventListener('click', function (e) {
        var btn;
        if ((btn = e.target.closest('[data-berita-create]'))) { openCreate(); return; }
        if ((btn = e.target.closest('[data-berita-edit]'))) { openEdit(btn); return; }
        if (e.target.closest('[data-berita-close]')) { closeModal(); return; }
        if (e.target === modal) { closeModal(); }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    // Alamat form ditentukan tepat saat Simpan ditekan
    form.addEventListener('submit', function () {
        if (editingId) {
            form.action = updateUrl.replace('__ID__', editingId);
            methodInput.disabled = false;
        } else {
            form.action = storeUrl;
            methodInput.disabled = true;
        }
    }, true);
})();
</script>
@endpush