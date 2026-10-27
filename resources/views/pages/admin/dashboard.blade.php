@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard monitoring')
@section('page-subtitle', 'Ringkasan kehadiran hari ini')

@section('content')

@php
    $nama = auth()->user()->name ?? 'Admin';
    $r = 30;                                  // jari-jari cincin progres
    $keliling = 2 * M_PI * $r;
    $isi = $keliling * ($persen / 100);
    $warnaBar = ['#1e3a8a', '#2f5bff', '#60a5fa', '#0ea5e9'];
@endphp

@if ($demo)
    <div class="adm-demo">Mode contoh: data di bawah hanya contoh sesuai desain. Hapus <code>?demo=1</code> dari alamat untuk melihat data asli.</div>
@endif

{{-- ===== BANNER SELAMAT DATANG ===== --}}
<section class="adm-welcome">
    <div class="adm-welcome-text">
        <h2>Selamat datang, {{ $nama }}</h2>
        <p>Pantau kehadiran guru dan siswa SMKN 9 Luwu hari ini.</p>
    </div>

    <span class="adm-welcome-pill">Sistem Monitoring Absensi</span>

    <div class="adm-ring">
        <svg viewBox="0 0 80 80" width="76" height="76">
            <circle cx="40" cy="40" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,.25)" stroke-width="8"/>
            <circle cx="40" cy="40" r="{{ $r }}" fill="none" stroke="#f5b82e" stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="{{ $isi }} {{ $keliling }}" transform="rotate(-90 40 40)"/>
        </svg>
        <span>{{ $persen }}%</span>
    </div>
</section>

{{-- ===== KARTU STATISTIK ===== --}}
<section class="adm-stats">
    <div class="adm-stat">
        <span class="ico blue"><i data-lucide="users"></i></span>
        <div><small>Siswa hadir</small><strong>{{ $siswaHadir }}</strong></div>
    </div>
    <div class="adm-stat">
        <span class="ico red"><i data-lucide="users"></i></span>
        <div><small>Siswa tidak hadir</small><strong>{{ $siswaTidakHadir }}</strong></div>
    </div>
    <div class="adm-stat">
        <span class="ico cyan"><i data-lucide="user"></i></span>
        <div><small>Guru hadir</small><strong>{{ $guruHadir }}</strong></div>
    </div>
    <div class="adm-stat">
        <span class="ico amber"><i data-lucide="square-check"></i></span>
        <div><small>Persentase hari ini</small><strong>{{ $persen }}%</strong></div>
    </div>
</section>

{{-- ===== GRAFIK ===== --}}
<section class="adm-charts">
    <div class="adm-card">
        <h3>Kehadiran 7 hari terakhir</h3>
        <div class="adm-line" id="lineChart"></div>
    </div>

    <div class="adm-card">
        <h3>Kehadiran per kelas</h3>
        <div class="adm-bars">
            @forelse ($perKelas as $k)
                <div class="bar-col">
                    <div class="bar-track">
                        <span class="bar" style="height: {{ max($k['nilai'], 4) }}%; background: {{ $warnaBar[$loop->index % 4] }};" title="{{ $k['nilai'] }}%"></span>
                    </div>
                    <small>{{ $k['nama'] }}</small>
                </div>
            @empty
                <p class="adm-empty">Belum ada data kelas.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== BELUM DIABSEN ===== --}}
<section class="adm-card adm-table-card">
    <h3>Belum diabsen hari ini</h3>

    <div class="adm-table">
        <div class="adm-thead">
            <span>KELAS</span>
            <span>GURU PENGAMPU</span>
            <span>STATUS</span>
        </div>

        @forelse ($belumDiabsen as $row)
            <div class="adm-trow">
                <span>{{ $row['kelas'] }}</span>
                <span>{{ $row['guru'] }}</span>
                <span><em class="badge-red">Belum diisi</em></span>
            </div>
        @empty
            <div class="adm-trow empty">Semua kelas sudah diabsen hari ini.</div>
        @endforelse
    </div>
</section>

@endsection

@push('scripts')
<script>
    window.ADMIN_DASH = {
        labels: @json($chartLabels),
        values: @json($chartValues)
    };
</script>
@endpush
