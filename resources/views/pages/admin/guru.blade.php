@extends('layouts.admin')

@section('title', 'Data guru')
@section('page-title', 'Data guru')
@section('page-subtitle', 'Kelola data tenaga pendidik')

@section('content')
    <form method="GET" action="{{ route('admin.guru.index') }}" class="toolbar" id="filterForm">
        <input type="text" name="q" value="{{ request('q') }}" class="input grow" placeholder="Cari nama atau NIP" id="searchInput" autocomplete="off">

        <div class="select-wrap">
            <select name="status" class="input select" id="filterSelect">
                <option value="">Semua status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
        </div>

        <button type="button" class="btn-primary" data-open-modal="modalGuru" data-mode="create">+ Tambah guru</button>
    </form>

    <section class="card">
        <table class="table">
            <thead>
            <tr>
                <th style="width:56px">No</th>
                <th style="width:76px">Foto</th>
                <th style="width:200px">NIP</th>
                <th>Nama</th>
                <th class="center" style="width:190px">Jabatan</th>
                <th style="width:150px">Mapel</th>
                <th style="width:120px">Status</th>
                <th style="width:180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($gurus as $i => $g)
                <tr>
                    <td>{{ $gurus->firstItem() + $i }}</td>
                    <td>
                        <span class="avatar-sm">
                            @if ($g->foto)
                                <img src="{{ asset('storage/' . $g->foto) }}" alt="{{ $g->nama }}">
                            @else
                                {{ strtoupper(substr($g->nama, 0, 2)) }}
                            @endif
                        </span>
                    </td>
                    <td class="nip">{{ $g->nip }}</td>
                    <td><strong>{{ $g->nama }}</strong></td>
                    <td class="center">{{ $g->jabatan }}</td>
                    <td>{{ $g->mapel ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $g->status === 'aktif' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($g->status) }}</span>
                    </td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn-edit"
                                    data-open-modal="modalGuru" data-mode="edit"
                                    data-action="{{ route('admin.guru.update', $g) }}"
                                    data-nip="{{ $g->nip }}" data-nama="{{ $g->nama }}"
                                    data-jabatan="{{ $g->jabatan }}" data-mapel="{{ $g->mapel }}"
                                    data-status="{{ $g->status }}">Edit</button>

                            <form method="POST" action="{{ route('admin.guru.destroy', $g) }}" data-confirm="Hapus guru {{ $g->nama }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">Belum ada data guru. Klik "+ Tambah guru" untuk mulai.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $gurus->links('partials.pagination') }}
    </section>

    <div class="modal" id="modalGuru" @if ($errors->any()) data-open-on-load @endif>
        <div class="modal-box">
            <h2 id="modalTitle">Tambah guru</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.guru.store') }}" enctype="multipart/form-data" data-store-url="{{ route('admin.guru.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="methodField">

                <label class="field">
                    <span>NIP</span>
                    <input type="text" name="nip" class="input" value="{{ old('nip') }}" required>
                </label>
                <label class="field">
                    <span>Nama lengkap dan gelar</span>
                    <input type="text" name="nama" class="input" value="{{ old('nama') }}" required>
                </label>
                <label class="field">
                    <span>Jabatan</span>
                    <input type="text" name="jabatan" class="input" placeholder="Contoh: Wali Kelas" value="{{ old('jabatan') }}" required>
                </label>
                <label class="field">
                    <span>Mapel</span>
                    <input type="text" name="mapel" class="input" placeholder="Contoh: Matematika" value="{{ old('mapel') }}">
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
                <label class="field">
                    <span>Foto (opsional)</span>
                    <input type="file" name="foto" class="input" accept="image/*">
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn-edit" data-close-modal>Batal</button>
                    <button type="submit" class="btn-primary small">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
