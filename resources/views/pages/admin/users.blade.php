@extends('layouts.admin')

@section('title', 'Akun pengguna')
@section('page-title', 'Akun pengguna')
@section('page-subtitle', 'Kelola akun dan hak akses')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-manage.css') }}">
@endpush

@section('content')
@include('partials.form-errors')

<form method="GET" action="{{ route('admin.users.index') }}" class="mg-toolbar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau username" class="mg-input mg-grow">
    <select name="peran" class="mg-input mg-select" onchange="this.form.submit()">
        <option value="">Semua peran</option>
        <option value="admin" @selected(request('peran') === 'admin')>Admin</option>
        <option value="guru" @selected(request('peran') === 'guru')>Guru</option>
    </select>
    <button type="button" class="mg-btn-primary" onclick="openCreate()">+ Tambah akun</button>
</form>

<div class="mg-card">
    <table class="mg-table">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Nama</th>
                <th style="width:200px">Username</th>
                <th style="width:140px">Peran</th>
                <th style="width:130px">Status</th>
                <th style="width:170px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $i => $u)
                <tr>
                    <td>{{ $users->firstItem() + $i }}</td>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->username }}</td>
                    <td>
                        <span class="mg-badge {{ $u->role === 'admin' ? 'mg-blue' : 'mg-gray' }}">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="mg-badge {{ $u->status === 'nonaktif' ? 'mg-gray' : 'mg-green' }}">
                            {{ $u->status === 'nonaktif' ? 'Nonaktif' : 'Aktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="mg-actions">
                            <button type="button" class="mg-btn"
                                data-id="{{ $u->id }}"
                                data-name="{{ $u->name }}"
                                data-username="{{ $u->username }}"
                                data-role="{{ $u->role }}"
                                data-status="{{ $u->status ?? 'aktif' }}"
                                data-guru="{{ $u->guru_id }}"
                                onclick="openEdit(this)">Edit</button>

                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                      onsubmit="return confirm('Hapus akun ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mg-btn mg-danger">Hapus</button>
                                </form>
                            @else
                                {{-- Akun yang sedang dipakai tidak boleh dihapus --}}
                                <button type="button" class="mg-btn mg-danger" disabled
                                        title="Tidak bisa menghapus akun yang sedang dipakai">Hapus</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="mg-empty">Belum ada akun.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links('partials.pagination') }}
</div>

{{-- Modal tambah / edit --}}
<div class="mg-modal" id="mgModal" hidden>
    <div class="mg-modal-box">
        <h3 id="mgTitle">Tambah akun</h3>
        <form method="POST" id="mgForm" action="{{ route('admin.users.store') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="_method" id="mgMethod" value="PUT" disabled>

            <label for="fName">Nama</label>
            <input type="text" name="name" id="fName" class="mg-input" required maxlength="150">

            <label for="fUsername">Username</label>
            <input type="text" name="username" id="fUsername" class="mg-input" required maxlength="50">

            <label for="fPassword">Password <small id="fHint"></small></label>
            <input type="password" name="password" id="fPassword" class="mg-input" minlength="6" autocomplete="new-password">

            <div class="mg-row">
                <div>
                    <label for="fRole">Peran</label>
                    <select name="role" id="fRole" class="mg-input" onchange="syncRole()">
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                    </select>
                </div>
                <div>
                    <label for="fStatus">Status</label>
                    <select name="status" id="fStatus" class="mg-input">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Hanya untuk peran guru: hubungkan akun ke data guru --}}
            <div id="fGuruWrap" hidden>
                <label for="fGuru">Data guru</label>
                <select name="guru_id" id="fGuru" class="mg-input">
                    <option value="">Pilih guru</option>
                    @foreach ($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mg-modal-foot">
                <button type="button" class="mg-btn mg-btn-lg" onclick="closeModal()">Batal</button>
                <button type="submit" class="mg-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('mgModal');
    const form = document.getElementById('mgForm');
    const methodInput = document.getElementById('mgMethod');
    const pwd = document.getElementById('fPassword');
    const guruWrap = document.getElementById('fGuruWrap');
    const guruSelect = document.getElementById('fGuru');
    const storeUrl = @json(route('admin.users.store'));
    const updateUrl = @json(route('admin.users.update', '__ID__'));

    // Pilihan "Data guru" hanya muncul dan wajib untuk peran guru
    function syncRole() {
        const isGuru = document.getElementById('fRole').value === 'guru';
        guruWrap.hidden = !isGuru;
        guruSelect.required = isGuru;
        if (!isGuru) guruSelect.value = '';
    }

    function openCreate() {
        form.reset();
        form.action = storeUrl;
        methodInput.disabled = true;
        pwd.required = true;
        document.getElementById('mgTitle').textContent = 'Tambah akun';
        document.getElementById('fHint').textContent = '';
        syncRole();
        modal.hidden = false;
    }

    function openEdit(btn) {
        form.reset();
        form.action = updateUrl.replace('__ID__', btn.dataset.id);
        methodInput.disabled = false;
        pwd.required = false;
        document.getElementById('mgTitle').textContent = 'Edit akun';
        document.getElementById('fName').value = btn.dataset.name;
        document.getElementById('fUsername').value = btn.dataset.username;
        document.getElementById('fRole').value = btn.dataset.role;
        document.getElementById('fStatus').value = btn.dataset.status;
        document.getElementById('fHint').textContent = '(kosongkan jika tidak diganti)';
        syncRole();
        guruSelect.value = btn.dataset.guru || '';
        modal.hidden = false;
    }

    function closeModal() { modal.hidden = true; }
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
