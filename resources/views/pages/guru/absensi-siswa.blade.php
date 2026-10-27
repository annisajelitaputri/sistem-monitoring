@extends('layouts.guru')

@section('title', 'Input absensi siswa')
@section('page-title', 'Input absensi siswa')
@section('page-subtitle', 'H = Hadir, S = Sakit, I = Izin, A = Alpa')

@section('content')
<div class="gp">

    {{-- Filter kelas & tanggal --}}
    <form method="GET" action="{{ route('guru.absensi-siswa.index') }}" class="gp-filter">
        <label for="kelas_id">Kelas</label>
        <div class="gp-select-wrap" style="width:220px">
            <select name="kelas_id" id="kelas_id" class="gp-input gp-select">
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}" @selected($kelasId === $k->id)>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="gp-input" style="width:200px" value="{{ $tanggal }}">

        <button type="submit" class="gp-btn">Tampilkan</button>
    </form>

    {{-- Form simpan --}}
    <form method="POST" action="{{ route('guru.absensi-siswa.store') }}">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <section class="gp-card">
            <table class="gp-table">
                <thead>
                <tr>
                    <th style="width:60px">No</th>
                    <th style="width:140px">NIS</th>
                    <th>Nama siswa</th>
                    <th class="gp-c gp-h-h" style="width:90px">H</th>
                    <th class="gp-c gp-h-s" style="width:90px">S</th>
                    <th class="gp-c gp-h-i" style="width:90px">I</th>
                    <th class="gp-c gp-h-a" style="width:90px">A</th>
                    <th class="gp-c" style="width:240px">Keterangan</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($siswas as $i => $s)
                    @php
                        $a = $absen[$s->id] ?? null;
                        $pilih = old("status.{$s->id}", $a->status ?? 'hadir');
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->nis }}</td>
                        <td><strong>{{ $s->nama }}</strong></td>
                        @foreach (['hadir', 'sakit', 'izin', 'alpa'] as $st)
                            <td class="gp-c">
                                <input type="radio" class="gp-radio" name="status[{{ $s->id }}]" value="{{ $st }}"
                                       aria-label="{{ ucfirst($st) }} - {{ $s->nama }}" @checked($pilih === $st)>
                            </td>
                        @endforeach
                        <td class="gp-c">
                            <input type="text" class="gp-input gp-ket" name="keterangan[{{ $s->id }}]"
                                   value="{{ old("keterangan.{$s->id}", $a->keterangan ?? '') }}" maxlength="255">
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="gp-empty">Belum ada siswa aktif di kelas ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>

        @if ($siswas->isNotEmpty())
            <div class="gp-actions">
                <button type="button" class="gp-btn-white" id="tandaiHadir">Tandai semua hadir</button>
                <button type="submit" class="gp-btn">Simpan</button>
            </div>
        @endif
    </form>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const tombol = document.getElementById('tandaiHadir');
        if (!tombol) return;
        tombol.addEventListener('click', function () {
            document.querySelectorAll('input.gp-radio[value="hadir"]').forEach(function (r) { r.checked = true; });
        });
    })();
</script>
@endpush
