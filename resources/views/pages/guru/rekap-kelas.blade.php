@extends('layouts.guru')

@section('title', 'Rekap kelas')
@section('page-title', 'Rekap kelas')
@section('page-subtitle', 'Rekap kehadiran siswa kelas ' . ($kelas->nama_kelas ?? '-') . ' per bulan')

@section('content')
<div class="gp">

    <form method="GET" action="{{ route('guru.rekap-kelas.index') }}" class="gp-filter">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <label for="kelas_id">Kelas</label>
        <div class="gp-select-wrap" style="width:240px">
            <select name="kelas_id" id="kelas_id" class="gp-input gp-select">
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}" @selected($kelas && (int) $kelas->id === (int) $k->id)>
                        {{ $k->nama_kelas }}{{ (int) $k->wali_kelas_id === (int) $guruId ? ' (kelas Anda)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <label for="bulan">Bulan</label>
        <div class="gp-select-wrap" style="width:200px">
            <select name="bulan" id="bulan" class="gp-input gp-select">
                @foreach ($bulanList as $no => $nama)
                    <option value="{{ $no }}" @selected($bulan === $no)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="gp-btn">Tampilkan</button>

        <div class="gp-export">
            <a href="{{ route('guru.rekap-kelas.pdf', request()->query()) }}" target="_blank" class="gp-btn-white">Ekspor PDF</a>
            <a href="{{ route('guru.rekap-kelas.excel', request()->query()) }}" class="gp-btn-white">Ekspor Excel</a>
        </div>
    </form>

    <section class="gp-card">
        <table class="gp-table">
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
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $r['nama'] }}</strong></td>
                    <td>{{ $r['h'] }}</td>
                    <td>{{ $r['s'] }}</td>
                    <td>{{ $r['i'] }}</td>
                    <td>{{ $r['a'] }}</td>
                    <td>{{ $r['total'] }}</td>
                    <td><span class="gp-pill gp-pill-soft">{{ $r['persen'] }}%</span></td>
                </tr>
            @empty
                <tr><td colspan="8" class="gp-empty">
                    {{ $kelas ? 'Belum ada siswa aktif di kelas ini.' : 'Belum ada kelas yang bisa ditampilkan.' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

</div>
@endsection
