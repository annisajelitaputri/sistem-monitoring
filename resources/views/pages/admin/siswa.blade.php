@extends('layouts.admin')

@section('title', 'Data siswa')
@section('page-title', 'Data siswa')
@section('page-subtitle', 'Kelola data seluruh siswa')

@section('content')
    <form method="GET" action="{{ route('admin.siswa.index') }}" class="toolbar" id="filterForm">
        <input type="text" name="q" value="{{ request('q') }}" class="input grow" placeholder="Cari nama atau NIS" id="searchInput" autocomplete="off">

        <div class="select-wrap">
            <select name="kelas" class="input select" id="filterSelect">
                <option value="">Semua kelas</option>
                @foreach ($kelasOpsi as $k)
                    <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" class="btn-primary" data-open-modal="modalSiswa" data-mode="create">+ Tambah siswa</button>
    </form>

    <section class="card">
        <table class="table">
            <thead>
            <tr>
                <th style="width:60px">No</th>
                <th style="width:150px">NIS</th>
                <th>Nama</th>
                <th class="center" style="width:200px">Kelas</th>
                <th style="width:140px">Status</th>
                <th style="width:180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($siswas as $i => $s)
                <tr>
                    <td>{{ $siswas->firstItem() + $i }}</td>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->nama }}</td>
                    <td class="center">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $s->status === 'aktif' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn-edit"
                                    data-open-modal="modalSiswa" data-mode="edit"
                                    data-action="{{ route('admin.siswa.update', $s) }}"
                                    data-nis="{{ $s->nis }}" data-nama="{{ $s->nama }}"
                                    data-jenis_kelamin="{{ $s->jenis_kelamin }}"
                                    data-kelas="{{ $s->kelas->nama_kelas ?? '' }}" data-status="{{ $s->status }}">Edit</button>

                            <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}" data-confirm="Hapus siswa {{ $s->nama }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada data siswa. Klik "+ Tambah siswa" untuk mulai.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $siswas->links('partials.pagination') }}
    </section>

    {{-- Modal tambah / edit --}}
    <div class="modal" id="modalSiswa" @if ($errors->any()) data-open-on-load @endif>
        <div class="modal-box">
            <h2 id="modalTitle">Tambah siswa</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.siswa.store') }}" id="siswaForm" data-store-url="{{ route('admin.siswa.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="methodField">

                <label class="field">
                    <span>NIS</span>
                    <input type="text" name="nis" class="input" value="{{ old('nis') }}" required>
                </label>
                <label class="field">
                    <span>Nama lengkap</span>
                    <input type="text" name="nama" class="input" value="{{ old('nama') }}" required>
                </label>
                <label class="field">
                    <span>Jenis kelamin</span>
                    <div class="select-wrap">
                        <select name="jenis_kelamin" class="input select" required>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                        </select>
                    </div>
                </label>
                <label class="field">
                    <span>Kelas</span>
                    <div class="select-wrap">
                        <select name="kelas" class="input select" required>
                            <option value="">Pilih kelas</option>
                            @foreach ($kelasOpsi as $k)
                                <option value="{{ $k }}" @selected(old('kelas') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <label class="field">
                    <span>Status</span>
                    <div class="select-wrap">
                        <select name="status" class="input select" required>
                            <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn-edit" data-close-modal>Batal</button>
                    <button type="submit" class="btn-primary small">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
