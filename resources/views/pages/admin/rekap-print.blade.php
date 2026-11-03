<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap kehadiran {{ $jenis }} - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { margin: 10mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #111; }
        h2 { margin: 0 0 6px; font-size: 14px; }
        .meta { margin-bottom: 10px; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #444; padding: 2px 1px; text-align: center; }
        td.nama { text-align: left; white-space: nowrap; padding-left: 4px; }
        .libur { background: #ddd; }
    </style>
</head>
<body>
    <h2>Rekap Kehadiran {{ $jenis === 'guru' ? 'Guru' : 'Siswa' }}</h2>
    <div class="meta">
        @if ($jenis === 'siswa')
            Kelas: {{ $kelas->nama_kelas ?? '-' }} &nbsp;|&nbsp; Mapel: {{ $mapel->nama ?? '-' }}<br>
        @endif
        Periode: {{ $namaBulan }} {{ $tahun }}
    </div>

    <table>
        <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            @foreach ($hari as $h)
                <th class="{{ $h['libur'] ? 'libur' : '' }}">{{ $h['tgl'] }}</th>
            @endforeach
            <th>H</th><th>S</th><th>I</th><th>A</th><th>Total</th><th>%</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rows as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="nama">{{ $r['nama'] }}</td>
                @foreach ($hari as $h)
                    <td class="{{ $h['libur'] ? 'libur' : '' }}">{{ $r['cells'][$h['tgl']] ?? '' }}</td>
                @endforeach
                <td>{{ $r['h'] }}</td>
                <td>{{ $r['s'] }}</td>
                <td>{{ $r['i'] }}</td>
                <td>{{ $r['a'] }}</td>
                <td>{{ $r['total'] }}</td>
                <td>{{ $r['persen'] }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>