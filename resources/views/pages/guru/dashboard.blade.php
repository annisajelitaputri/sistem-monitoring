@extends('layouts.guru')

@section('title', 'Dashboard guru')
@section('page-title', 'Dashboard guru')
@section('page-subtitle', 'Ringkasan absensi kelas Anda')

@php
    $check = '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12.5l3 3 5-6"/>';
    $kartu = [
        ['Hadir', $stat['hadir'], 'gp-ic-blue'],
        ['Sakit', $stat['sakit'], 'gp-ic-amber'],
        ['Izin',  $stat['izin'],  'gp-ic-teal'],
        ['Alpa',  $stat['alpa'],  'gp-ic-red'],
    ];
    $linkAbsensi = $kelas
        ? route('guru.absensi-siswa.index', ['kelas_id' => $kelas->id, 'tanggal' => today()->toDateString()])
        : null;
@endphp

@section('content')
<div class="gp">

    <section class="gp-banner">
        <div class="gp-banner-text">
            @if (! $kelas)
                <h2>Anda belum menjadi wali kelas</h2>
                <p>Hubungi admin TU untuk menetapkan kelas Anda.</p>
            @elseif ($sudahIsi)
                <h2>Absensi kelas hari ini sudah diisi</h2>
                <p>Terima kasih, rekap kehadiran {{ $kelas->nama_kelas }} hari ini sudah tercatat.</p>
            @else
                <h2>Absensi kelas hari ini belum diisi</h2>
                <p>Isi absensi siswa {{ $kelas->nama_kelas }} agar rekap kehadiran tetap akurat.</p>
            @endif
        </div>

        @if ($linkAbsensi)
            <a href="{{ $linkAbsensi }}" class="gp-btn-white">{{ $sudahIsi ? 'Lihat absensi' : 'Input absensi' }}</a>
        @endif
    </section>

    <div class="gp-stats">
        @foreach ($kartu as [$nama, $jumlah, $warna])
            <div class="gp-stat">
                <span class="gp-stat-icon {{ $warna }}">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $check !!}</svg>
                </span>
                <div class="gp-stat-text">
                    <span>{{ $nama }}</span>
                    <strong>{{ $jumlah }}</strong>
                </div>
            </div>
        @endforeach
    </div>

    <section class="gp-card gp-card-pad">
        <h3 class="gp-card-title">Absensi terakhir kelas Anda</h3>

        <table class="gp-table gp-table-flat">
            <thead>
            <tr>
                <th style="width:35%">Tanggal</th>
                <th style="width:35%">Hadir</th>
                <th>Tidak hadir</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($terakhir as $r)
                <tr>
                    <td>{{ $r['tanggal'] }}</td>
                    <td><span class="gp-pill gp-pill-green">{{ $r['hadir'] }}</span></td>
                    <td><span class="gp-pill {{ $r['tidak_hadir'] > 0 ? 'gp-pill-red' : 'gp-pill-gray' }}">{{ $r['tidak_hadir'] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" class="gp-empty">Belum ada riwayat absensi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

</div>
@endsection
