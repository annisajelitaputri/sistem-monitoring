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
                <th style="width:170px">NIP</th>
                <th>Nama</th>
                <th style="width:220px">Jabatan</th>
                <th style="width:200px">Mapel</th>
                <th style="width:110px">Status</th>
                <th style="width:180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($gurus as $i => $g)
                @php
                    $jabatanList = $g->jabatan_list;
                    $mapelListGuru = $g->mapel_list;
                @endphp
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
                    <td>
                        <strong>{{ $g->nama }}</strong>
                        @if ($g->bio)
                            <div style="font-size:12px;color:#6b7280;font-style:italic">{{ \Illuminate\Support\Str::limit($g->bio, 70) }}</div>
                        @endif
                    </td>
                    <td>{{ count($jabatanList) ? implode(', ', $jabatanList) : '-' }}</td>
                    <td>{{ count($mapelListGuru) ? implode(', ', $mapelListGuru) : '-' }}</td>
                    <td>
                        <span class="badge {{ $g->status === 'aktif' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($g->status) }}</span>
                    </td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn-edit"
                                    data-open-modal="modalGuru" data-mode="edit"
                                    data-action="{{ route('admin.guru.update', $g) }}"
                                    data-nip="{{ $g->nip }}" data-nama="{{ $g->nama }}"
                                    data-status="{{ $g->status }}"
                                    data-pendidikan-val="{{ $g->pendidikan }}"
                                    data-bio-val="{{ $g->bio }}"
                                    data-jabatan-json="{{ json_encode($jabatanList) }}"
                                    data-mapel-json="{{ json_encode($mapelListGuru) }}">Edit</button>

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

                <div class="field">
                    <span>Jabatan (bisa lebih dari satu)</span>
                    <div id="jabatanWrap"></div>
                    <button type="button" class="btn-edit" id="tambahJabatan" style="margin-top:8px">+ Tambah jabatan</button>
                </div>

                <div class="field">
                    <span>Mapel yang dibawakan (bisa pilih lebih dari satu)</span>
                    <div id="mapelWrap" style="display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;max-height:170px;overflow-y:auto;padding:10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff">
                        @forelse ($mapelList as $m)
                            <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer">
                                <input type="checkbox" name="mapel[]" value="{{ $m->nama }}" @checked(in_array($m->nama, old('mapel', []), true))>
                                {{ $m->nama }}
                            </label>
                        @empty
                            <span style="font-size:13px;color:#6b7280">Belum ada data mapel. Isi dulu di menu Data mapel.</span>
                        @endforelse
                    </div>
                </div>

                <label class="field">
                    <span>Pendidikan terakhir (opsional)</span>
                    <input type="text" name="pendidikan" class="input" maxlength="255" placeholder="Contoh: S1 Pendidikan Matematika, UNM" value="{{ old('pendidikan') }}">
                </label>
                <label class="field">
                    <span>Bio / motto (opsional)</span>
                    <textarea name="bio" class="input" rows="4" maxlength="2000" placeholder="Contoh: Guru Matematika sejak 2010. Motto: Belajar sepanjang hayat.">{{ old('bio') }}</textarea>
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

    <script>
    (function () {
        const modal = document.getElementById('modalGuru');
        const form  = modal.querySelector('form');
        const wrap  = document.getElementById('jabatanWrap');

        function addRow(value) {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'jabatan[]';
            input.className = 'input';
            input.maxLength = 100;
            input.placeholder = 'Contoh: Wali Kelas X TKR';
            input.value = value || '';

            const hapus = document.createElement('button');
            hapus.type = 'button';
            hapus.className = 'btn-delete';
            hapus.textContent = 'Hapus';
            hapus.addEventListener('click', function () {
                if (wrap.children.length > 1) { row.remove(); } else { input.value = ''; }
            });

            row.appendChild(input);
            row.appendChild(hapus);
            wrap.appendChild(row);
        }

        function setJabatan(list) {
            wrap.innerHTML = '';
            (list && list.length ? list : ['']).forEach(addRow);
        }

        function setMapel(list) {
            form.querySelectorAll('input[name="mapel[]"]').forEach(function (c) {
                c.checked = list.indexOf(c.value) !== -1;
            });
        }

        document.getElementById('tambahJabatan').addEventListener('click', function () { addRow(''); });

        setJabatan(@json(array_values((array) old('jabatan', ['']))));

        document.querySelectorAll('[data-open-modal="modalGuru"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                setTimeout(function () {
                    if (btn.dataset.mode === 'edit') {
                        form.elements['nip'].value        = btn.dataset.nip || '';
                        form.elements['nama'].value       = btn.dataset.nama || '';
                        form.elements['status'].value     = btn.dataset.status || 'aktif';
                        form.elements['pendidikan'].value = btn.dataset.pendidikanVal || '';
                        form.elements['bio'].value        = btn.dataset.bioVal || '';
                        setJabatan(JSON.parse(btn.dataset.jabatanJson || '[]'));
                        setMapel(JSON.parse(btn.dataset.mapelJson || '[]'));
                    } else {
                        form.elements['pendidikan'].value = '';
                        form.elements['bio'].value        = '';
                        setJabatan(['']);
                        setMapel([]);
                    }
                }, 0);
            });
        });
    })();
    </script>
@endsection