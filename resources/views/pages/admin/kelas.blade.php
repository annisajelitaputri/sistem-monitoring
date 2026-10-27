@extends('layouts.admin')

@section('title', 'Data kelas')
@section('page-title', 'Data kelas')
@section('page-subtitle', 'Kelola kelas dan wali kelas')

@section('content')
    <form method="GET" action="{{ route('admin.kelas.index') }}" class="toolbar" id="filterForm">
        <input type="text" name="q" value="{{ request('q') }}" class="input grow" placeholder="Cari nama kelas" id="searchInput" autocomplete="off">

        <div class="select-wrap">
            <select name="tahun_ajaran" class="input select" id="filterSelect">
                <option value="">Tahun ajaran</option>
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" @selected(request('tahun_ajaran') === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" class="btn-primary" data-open-modal="modalKelas" data-mode="create">+ Tambah kelas</button>
    </form>

    <section class="card">
        <table class="table">
            <thead>
            <tr>
                <th style="width:60px">No</th>
                <th style="width:150px">Nama kelas</th>
                <th class="center">Jurusan</th>
                <th style="width:160px">Tahun ajaran</th>
                <th style="width:260px">Wali kelas</th>
                <th class="center" style="width:150px">Jumlah siswa</th>
                <th class="center" style="width:180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($kelas as $i => $k)
                <tr>
                    <td>{{ $kelas->firstItem() + $i }}</td>
                    <td><strong>{{ $k->nama_kelas }}</strong></td>
                    <td class="center">{{ $k->jurusan }}</td>
                    <td>{{ $k->tahun_ajaran }}</td>
                    <td>{{ $k->waliKelas->nama ?? '-' }}</td>
                    <td class="center">{{ $k->siswa_count }}</td>
                    <td>
                        <div class="actions center">
                            <button type="button" class="btn-edit"
                                    data-open-modal="modalKelas" data-mode="edit"
                                    data-action="{{ route('admin.kelas.update', $k) }}"
                                    data-nama_kelas="{{ $k->nama_kelas }}" data-jurusan="{{ $k->jurusan }}"
                                    data-tahun_ajaran="{{ $k->tahun_ajaran }}" data-wali_kelas_id="{{ $k->wali_kelas_id }}">Edit</button>

                            <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" data-confirm="Hapus kelas {{ $k->nama_kelas }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada data kelas. Klik "+ Tambah kelas" untuk mulai.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $kelas->links('partials.pagination') }}
    </section>

    <div class="modal" id="modalKelas" @if ($errors->any()) data-open-on-load @endif>
        <div class="modal-box">
            <h2 id="modalTitle">Tambah kelas</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.kelas.store') }}" id="kelasForm" data-store-url="{{ route('admin.kelas.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="methodField">

                <label class="field">
                    <span>Nama kelas</span>
                    <input type="text" name="nama_kelas" class="input" placeholder="Contoh: X TKJ" value="{{ old('nama_kelas') }}" required>
                </label>
                <label class="field">
                    <span>Jurusan</span>
                    <input type="text" name="jurusan" class="input" placeholder="Contoh: Teknik Komputer dan Jaringan" value="{{ old('jurusan') }}" required>
                </label>
                <label class="field">
                    <span>Tahun ajaran</span>
                    <input type="text" name="tahun_ajaran" class="input" placeholder="2026/2027" value="{{ old('tahun_ajaran') }}" required>
                </label>
                <label class="field">
                    <span>Wali kelas</span>
                    <div class="select-wrap">
                        <select name="wali_kelas_id" class="input select">
                            <option value="">Pilih wali kelas</option>
                            @foreach ($guruList as $g)
                                <option value="{{ $g->id }}" @selected(old('wali_kelas_id') == $g->id)>{{ $g->nama }}</option>
                            @endforeach
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
