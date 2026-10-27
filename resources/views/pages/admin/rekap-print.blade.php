<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap kehadiran {{ $f['jenis'] }} - {{ $f['label'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p { margin: 0 0 14px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #e9eefc; }
        td.c, th.c { text-align: center; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <h1>Rekap Kehadiran {{ $f['jenis'] === 'guru' ? 'Guru' : 'Siswa' }} - SMKN 9 Luwu</h1>
    <p>Periode: {{ $f['label'] }} | Dicetak: {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
        <tr>
            <th class="c">No</th><th>Nama</th>
            <th class="c">H</th><th class="c">S</th><th class="c">I</th><th class="c">A</th>
            <th class="c">Total</th><th class="c">% Hadir</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rows as $i => $r)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $r['nama'] }}</td>
                <td class="c">{{ $r['h'] }}</td>
                <td class="c">{{ $r['s'] }}</td>
                <td class="c">{{ $r['i'] }}</td>
                <td class="c">{{ $r['a'] }}</td>
                <td class="c">{{ $r['total'] }}</td>
                <td class="c">{{ $r['persen'] }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
