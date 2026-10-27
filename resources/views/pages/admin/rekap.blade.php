@extends('layouts.admin')

@section('title', 'Rekap kehadiran')
@section('page-title', 'Rekap kehadiran')
@section('page-subtitle', 'Rekap bulanan dan ekspor laporan')

@section('content')
    <form method="GET" action="{{ route('admin.rekap.index') }}" id="rekapForm">
        <input type="hidden" name="tahun" value="{{ $f['tahun'] }}">

        <div class="rekap-filters">
            <div class="select-wrap w200">
                <select name="jenis" class="input select" id="jenisSelect">
                    <option value="siswa" @selected($f['jenis'] === 'siswa')>Siswa</option>
                    <option value="guru" @selected($f['jenis'] === 'guru')>Guru</option>
                </select>
            </div>

            <div class="select-wrap w200">
                <select name="periode" class="input select" id="periodeSelect">
                    <option value="bulanan" @selected($f['periode'] === 'bulanan')>Bulanan</option>
                    <option value="tahunan" @selected($f['periode'] === 'tahunan')>Tahunan</option>
                </select>
            </div>

            <div class="select-wrap w200">
                <select name="kelas_id" class="input select" id="kelasSelect">
                    <option value="">Pilih kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" @selected((string) $f['kelas_id'] === (string) $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrap w200">
                <select name="bulan" class="input select" id="bulanSelect">
                    @foreach ($bulanList as $no => $nama)
                        <option value="{{ $no }}" @selected($f['bulan'] === $no)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="rekap-actions">
            <button type="submit" class="btn-primary w130">Tampilkan</button>

            <div class="export-group">
                <a href="{{ route('admin.rekap.pdf', request()->query()) }}" target="_blank" class="btn-white">Ekspor PDF</a>
                <a href="{{ route('admin.rekap.excel', request()->query()) }}" class="btn-white">Ekspor Excel</a>
            </div>
        </div>
    </form>

    <section class="card">
        <table class="table rekap-table">
            <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Nama</th>
                <th style="width:90px">H</th>
                <th style="width:90px">S</th>
                <th style="width:90px">I</th>
                <th style="width:90px">A</th>
                <th style="width:100px">Total</th>
                <th style="width:130px">% Hadir</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($rows as $i => $r)
                <tr>
                    <td>{{ $rows->firstItem() + $i }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td>{{ $r['h'] }}</td>
                    <td>{{ $r['s'] }}</td>
                    <td>{{ $r['i'] }}</td>
                    <td>{{ $r['a'] }}</td>
                    <td>{{ $r['total'] }}</td>
                    <td><span class="pill">{{ $r['persen'] }}%</span></td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">Tidak ada data untuk filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $rows->links('partials.pagination') }}
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const jenis = document.getElementById('jenisSelect');
        const periode = document.getElementById('periodeSelect');
        const kelas = document.getElementById('kelasSelect');
        const bulan = document.getElementById('bulanSelect');

        function sinkron() {
            kelas.disabled = jenis.value === 'guru';      // kelas hanya untuk siswa
            bulan.disabled = periode.value === 'tahunan'; // tahunan tidak perlu bulan
        }
        jenis.addEventListener('change', sinkron);
        periode.addEventListener('change', sinkron);
        sinkron();
    })();
</script>
@endpush
