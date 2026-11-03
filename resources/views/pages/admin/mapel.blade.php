@extends('layouts.admin')

@section('title', 'Data mapel')
@section('page-title', 'Data mapel')
@section('page-subtitle', 'Kelola mata pelajaran')

@section('content')
    <form method="GET" action="{{ route('admin.mapel.index') }}" class="toolbar" id="filterForm">
        <input type="text" name="q" value="{{ request('q') }}" class="input grow" placeholder="Cari nama mapel" id="searchInput" autocomplete="off">

        <button type="button" class="btn-primary" data-open-modal="modalMapel" data-mode="create">+ Tambah mapel</button>
    </form>

    <section class="card">
        <table class="table">
            <thead>
            <tr>
                <th style="width:60px">No</th>
                <th>Nama mapel</th>
                <th class="center" style="width:180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($mapel as $i => $m)
                <tr>
                    <td>{{ $mapel->firstItem() + $i }}</td>
                    <td><strong>{{ $m->nama }}</strong></td>
                    <td>
                        <div class="actions center">
                            <button type="button" class="btn-edit"
                                    data-open-modal="modalMapel" data-mode="edit"
                                    data-action="{{ route('admin.mapel.update', $m->id) }}"
                                    data-nama="{{ $m->nama }}">Edit</button>

                            <form method="POST" action="{{ route('admin.mapel.destroy', $m->id) }}" data-confirm="Hapus mapel {{ $m->nama }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty">
                        {{ request('q') ? 'Mapel tidak ditemukan.' : 'Belum ada data mapel. Klik "+ Tambah mapel" untuk mulai.' }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $mapel->links('partials.pagination') }}
    </section>

    <div class="modal" id="modalMapel" @if ($errors->any()) data-open-on-load @endif>
        <div class="modal-box">
            <h2 id="modalTitle">Tambah mapel</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.mapel.store') }}" id="mapelForm" data-store-url="{{ route('admin.mapel.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="methodField">

                <label class="field">
                    <span>Nama mapel</span>
                    <input type="text" name="nama" class="input" placeholder="Contoh: Matematika" value="{{ old('nama') }}" maxlength="100" required>
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn-edit" data-close-modal>Batal</button>
                    <button type="submit" class="btn-primary small">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection