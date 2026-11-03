@extends('layouts.guru')

@section('title', 'Rekap kelas')
@section('page-title', 'Rekap kelas')
@section('page-subtitle', 'Rekap absensi ' . ($mapel->nama ?? '-') . ' kelas ' . ($kelas->nama_kelas ?? '-') . ', ' . $namaBulan . ' ' . $tahun)

@section('content')
<div class="gp">

    <form method="GET" action="{{ route('guru.rekap-kelas.index') }}" class="gp-filter">
        <label for="kelas_id">Kelas</label>
        <div class="gp-select-wrap" style="width:160px">
            <select name="kelas_id" id="kelas_id" class="gp-input gp-select"
                    onchange="this.form.mapel_id.selectedIndex = -1; this.form.submit()">
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}" @selected($kelas && (int) $k->id === (int) $kelas->id)>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <label for="mapel_id">Mapel</label>
        <div class="gp-select-wrap" style="width:220px">
            <select name="mapel_id" id="mapel_id" class="gp-input gp-select">
                @forelse ($mapelList as $m)
                    <option value="{{ $m->id }}" @selected($mapel && (int) $m->id === (int) $mapel->id)>{{ $m->nama }}</option>
                @empty
                    <option value="">Belum ada mapel</option>
                @endforelse
            </select>
        </div>

        <label for="bulan">Bulan</label>
        <div class="gp-select-wrap" style="width:150px">
            <select name="bulan" id="bulan" class="gp-input gp-select">
                @foreach ($bulanList as $no => $nama)
                    <option value="{{ $no }}" @selected($bulan === $no)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <label for="tahun">Tahun</label>
        <div class="gp-select-wrap" style="width:110px">
            <select name="tahun" id="tahun" class="gp-input gp-select">
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" @selected($tahun === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="gp-btn">Tampilkan</button>

        @php
            $q = ['kelas_id' => $kelas->id ?? null, 'mapel_id' => $mapel->id ?? null, 'bulan' => $bulan, 'tahun' => $tahun];
        @endphp
        <div class="gp-export">
            <a href="{{ route('guru.rekap-kelas.pdf', $q) }}" target="_blank" class="gp-btn-white">Ekspor PDF</a>
            <a href="{{ route('guru.rekap-kelas.excel', $q) }}" class="gp-btn-white">Ekspor Excel</a>
        </div>
    </form>

    @if (! $mapel)
        <div class="gp-empty">Belum ada mapel untuk kelas ini.</div>
    @else
    <section class="gp-card" style="overflow-x:auto">
        <table class="gp-table" style="min-width:1400px">
            <thead>
            <tr>
                <th style="width:44px">No</th>
                <th style="min-width:200px">Nama</th>
                @foreach ($hari as $h)
                    <th class="gp-c" style="width:30px;padding:8px 2px;{{ $h['libur'] ? 'background:#e5e7eb;color:#6b7280' : '' }}">{{ $h['tgl'] }}</th>
                @endforeach
                <th class="gp-c" style="width:40px">H</th>
                <th class="gp-c" style="width:40px">S</th>
                <th class="gp-c" style="width:40px">I</th>
                <th class="gp-c" style="width:40px">A</th>
                <th class="gp-c" style="width:60px">Total</th>
                <th class="gp-c" style="width:90px">% Hadir</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $r['nama'] }}</strong></td>
                    @foreach ($hari as $h)
                        @php
                            $c = $r['cells'][$h['tgl']] ?? '';
                            $warna = ['H' => '#15803d', 'S' => '#b45309', 'I' => '#1d4ed8', 'A' => '#b91c1c'][$c] ?? 'inherit';
                        @endphp
                        <td class="gp-c" style="padding:8px 2px;font-weight:700;color:{{ $warna }};{{ $h['libur'] ? 'background:#f3f4f6' : '' }}">{{ $c }}</td>
                    @endforeach
                    <td class="gp-c">{{ $r['h'] }}</td>
                    <td class="gp-c">{{ $r['s'] }}</td>
                    <td class="gp-c">{{ $r['i'] }}</td>
                    <td class="gp-c">{{ $r['a'] }}</td>
                    <td class="gp-c">{{ $r['total'] }}</td>
                    <td class="gp-c"><span class="gp-pill gp-pill-soft">{{ $r['persen'] }}%</span></td>
                </tr>
            @empty
                <tr><td colspan="{{ count($hari) + 8 }}" class="gp-empty">
                    {{ $kelas ? 'Belum ada siswa aktif di kelas ini.' : 'Belum ada kelas yang bisa ditampilkan.' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
    <p style="font-size:13px;color:#6b7280;margin-top:8px">
        Sel kosong = belum diabsen. Kolom abu-abu = Sabtu/Minggu. % hadir dihitung dari hari yang sudah diabsen.
    </p>
    @endif

</div>
@endsection