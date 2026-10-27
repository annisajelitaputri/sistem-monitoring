@extends('layouts.admin')

@section('title', 'Absensi guru')
@section('page-title', 'Absensi guru')
@section('page-subtitle', 'Pantau dan koreksi absensi guru')

@php
    $check = '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12.5l3 3 5-6"/>';
    $kartu = [
        ['Hadir', $stat['hadir'], 'ic-blue'],
        ['Sakit', $stat['sakit'], 'ic-amber'],
        ['Izin', $stat['izin'], 'ic-teal'],
        ['Belum absen', $stat['belum'], 'ic-red'],
    ];
    $label = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'dinas_luar' => 'Dinas luar'];
    $warna = ['hadir' => 'badge-green', 'sakit' => 'badge-orange', 'izin' => 'badge-blue', 'dinas_luar' => 'badge-blue'];
@endphp

@section('content')
    <form method="GET" action="{{ route('admin.absensi-guru.index') }}" class="date-bar">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="input date-input" value="{{ $tanggal }}">
        <button type="submit" class="btn-primary w130">Tampilkan</button>
    </form>

    <div class="stat-grid">
        @foreach ($kartu as [$nama, $jumlah, $kelasIcon])
            <div class="stat-card">
                <span class="stat-icon {{ $kelasIcon }}">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $check !!}</svg>
                </span>
                <div class="stat-text">
                    <span>{{ $nama }}</span>
                    <strong>{{ $jumlah }}</strong>
                </div>
            </div>
        @endforeach
    </div>

    <section class="card">
        <table class="table">
            <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Nama guru</th>
                <th class="center" style="width:150px">Jam masuk</th>
                <th style="width:170px">Status</th>
                <th style="width:230px">Keterangan</th>
                <th class="center" style="width:160px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($gurus as $i => $g)
                @php
                    $a = $absen[$g->id] ?? null;
                    $jam = $a && $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : null;
                @endphp
                <tr>
                    <td>{{ $gurus->firstItem() + $i }}</td>
                    <td>{{ $g->nama }}</td>
                    <td class="center">{{ $jam ?? '-' }}</td>
                    <td>
                        @if ($a)
                            <span class="badge {{ $warna[$a->status] ?? 'badge-gray' }}">{{ $label[$a->status] ?? ucfirst($a->status) }}</span>
                        @else
                            <span class="badge badge-red">Belum absen</span>
                        @endif
                    </td>
                    <td>{{ $a && $a->keterangan ? $a->keterangan : '-' }}</td>
                    <td class="center">
                        <button type="button" class="btn-edit"
                                data-open-modal="modalKoreksi" data-mode="edit"
                                data-title="Koreksi absensi"
                                data-action="{{ route('admin.absensi-guru.koreksi') }}"
                                data-guru_id="{{ $g->id }}" data-nama="{{ $g->nama }}"
                                data-tanggal="{{ $tanggal }}"
                                data-status="{{ $a->status ?? '' }}"
                                data-jam_masuk="{{ $jam }}"
                                data-keterangan="{{ $a->keterangan ?? '' }}">Koreksi</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada data guru aktif.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $gurus->links('partials.pagination') }}
    </section>

    <div class="modal" id="modalKoreksi" @if ($errors->any()) data-open-on-load @endif>
        <div class="modal-box">
            <h2 id="modalTitle">Koreksi absensi</h2>
            <p class="modal-sub" data-fill="nama"></p>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.absensi-guru.koreksi') }}" data-store-url="{{ route('admin.absensi-guru.koreksi') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="methodField">
                <input type="hidden" name="guru_id" value="{{ old('guru_id') }}">
                <input type="hidden" name="tanggal" value="{{ old('tanggal', $tanggal) }}">

                <label class="field">
                    <span>Status</span>
                    <div class="select-wrap">
                        <select name="status" class="input select">
                            <option value="">Belum absen (hapus data)</option>
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="dinas_luar">Dinas luar</option>
                        </select>
                    </div>
                </label>
                <label class="field">
                    <span>Jam masuk</span>
                    <input type="time" name="jam_masuk" class="input">
                </label>
                <label class="field">
                    <span>Keterangan</span>
                    <input type="text" name="keterangan" class="input" placeholder="Opsional">
                </label>

                <div class="modal-actions">
                    <button type="button" class="btn-edit" data-close-modal>Batal</button>
                    <button type="submit" class="btn-primary small">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
