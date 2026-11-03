@extends('layouts.admin')

@section('title', 'Rekap kehadiran')
@section('page-title', 'Rekap kehadiran')
@section('page-subtitle', 'Rekap bulanan dan ekspor laporan')

@push('styles')
<style>
    #filterForm {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }
    #filterForm .select-wrap {
        flex: 1 1 160px;
        min-width: 140px;
        max-width: 220px;
    }
    #filterForm .select-wrap select {
        width: 100%;
    }
    #filterForm .btn-primary {
        flex: 0 0 auto;
        white-space: nowrap;
    }
    #filterForm .export-actions {
        margin-left: auto;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')
    <form method="GET" action="{{ route('admin.rekap.index') }}" class="toolbar" id="filterForm">

        {{-- Jenis: tetap auto-submit karena mengubah isi form (Siswa/Guru) --}}
        <div class="select-wrap">
            <select name="jenis" class="input select" autocomplete="off" onchange="this.form.submit()">
                <option value="siswa" @selected($jenis === 'siswa')>Siswa</option>
                <option value="guru" @selected($jenis === 'guru')>Guru</option>
            </select>
        </div>

        @if ($jenis === 'siswa')
            <div class="select-wrap">
                <select name="kelas_id" class="input select" autocomplete="off">
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" @selected($kelas && (int) $kelas->id === (int) $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrap">
                <select name="mapel_id" class="input select" autocomplete="off">
                    @forelse ($mapelList as $m)
                        <option value="{{ $m->id }}" @selected($mapel && (int) $mapel->id === (int) $m->id)>{{ $m->nama }}</option>
                    @empty
                        <option value="">Belum ada mapel</option>
                    @endforelse
                </select>
            </div>
        @endif

        <div class="select-wrap">
            <select name="bulan" class="input select" autocomplete="off">
                @foreach ($bulanList as $no => $nama)
                    <option value="{{ $no }}" @selected($bulan === $no)>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="select-wrap">
            <select name="tahun" class="input select" autocomplete="off">
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" @selected($tahun === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary">Tampilkan</button>

        @php
            $q = ['jenis' => $jenis, 'kelas_id' => $kelas->id ?? null, 'mapel_id' => $mapel->id ?? null, 'bulan' => $bulan, 'tahun' => $tahun];
            $linkStyle = 'display:inline-flex;align-items:center;text-decoration:none';
        @endphp
        <div class="export-actions">
            <a href="{{ route('admin.rekap.pdf', $q) }}" class="btn-edit" style="{{ $linkStyle }}">Ekspor PDF</a>
            <a href="{{ route('admin.rekap.excel', $q) }}" class="btn-edit" style="{{ $linkStyle }}">Ekspor Excel</a>
        </div>
    </form>

    <p style="margin:6px 0 12px;font-size:14px;color:#475569">
        @if ($jenis === 'siswa')
            Rekap absensi <strong>{{ $mapel->nama ?? '-' }}</strong> kelas <strong>{{ $kelas->nama_kelas ?? '-' }}</strong>, {{ $namaBulan }} {{ $tahun }}
        @else
            Rekap kehadiran <strong>guru</strong>, {{ $namaBulan }} {{ $tahun }}
        @endif
    </p>

    <section class="card" style="overflow-x:auto">
        <table class="table" style="min-width:1400px">
            <thead>
            <tr>
                <th style="width:44px">No</th>
                <th style="min-width:200px">Nama</th>
                @foreach ($hari as $h)
                    <th class="center" style="width:30px;padding:8px 2px;{{ $h['libur'] ? 'background:#e5e7eb;color:#6b7280' : '' }}">{{ $h['tgl'] }}</th>
                @endforeach
                <th class="center" style="width:40px">H</th>
                <th class="center" style="width:40px">S</th>
                <th class="center" style="width:40px">I</th>
                <th class="center" style="width:40px">A</th>
                <th class="center" style="width:60px">Total</th>
                <th class="center" style="width:90px">% Hadir</th>
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
                            $warna = ['H' => '#15803d', 'D' => '#0f766e', 'S' => '#b45309', 'I' => '#1d4ed8', 'A' => '#b91c1c'][$c] ?? 'inherit';
                        @endphp
                        <td class="center" style="padding:8px 2px;font-weight:700;color:{{ $warna }};{{ $h['libur'] ? 'background:#f3f4f6' : '' }}">{{ $c }}</td>
                    @endforeach
                    <td class="center">{{ $r['h'] }}</td>
                    <td class="center">{{ $r['s'] }}</td>
                    <td class="center">{{ $r['i'] }}</td>
                    <td class="center">{{ $r['a'] }}</td>
                    <td class="center">{{ $r['total'] }}</td>
                    <td class="center"><span class="badge badge-green">{{ $r['persen'] }}%</span></td>
                </tr>
            @empty
                <tr><td colspan="{{ count($hari) + 8 }}" class="empty">
                    {{ $jenis === 'siswa' ? 'Belum ada siswa aktif di kelas ini.' : 'Belum ada data guru aktif.' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <p style="font-size:13px;color:#6b7280;margin-top:8px">
        @if ($jenis === 'siswa')
            Sel kosong = belum diabsen. Kolom abu-abu = Sabtu/Minggu. % hadir dihitung dari hari yang sudah diabsen.
        @else
            H = hadir, D = dinas luar (dihitung hadir), S = sakit, I = izin, A = alpa. Hari kerja yang sudah lewat tanpa catatan dihitung alpa. Kolom abu-abu = Sabtu/Minggu.
        @endif
    </p>
@endsection