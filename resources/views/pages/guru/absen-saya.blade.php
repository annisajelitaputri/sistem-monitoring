@extends('layouts.guru')

@section('title', 'Absen saya')
@section('page-title', 'Absen saya')
@section('page-subtitle', 'Catat kehadiran Anda hari ini')

@php
    $badge = [
        'hadir'      => 'gp-badge-green',
        'sakit'      => 'gp-badge-amber',
        'izin'       => 'gp-badge-blue',
        'dinas_luar' => 'gp-badge-blue',
    ];
@endphp

@section('content')
<div class="gp">

    @if (! $guruId)
        <div class="gp-alert gp-alert-error">Akun Anda belum terhubung dengan data guru. Hubungi admin TU.</div>
    @else
        <div class="gp-two">

            {{-- Form absen hari ini --}}
            <section class="gp-card gp-card-pad">
                <h3 class="gp-card-title">{{ now()->locale('id')->isoFormat('dddd, DD-MM-YYYY') }}</h3>

                <form method="POST" action="{{ route('guru.absen-saya.store') }}">
                    @csrf

                    @php $pilih = old('status', $hariIni->status ?? 'hadir'); @endphp

                    <div class="gp-opts">
                        @foreach ($statusList as $nilai => $nama)
                            <label class="gp-opt">
                                <input type="radio" name="status" value="{{ $nilai }}" @checked($pilih === $nilai)>
                                <span>{{ $nama }}</span>
                            </label>
                        @endforeach
                    </div>

                    <label for="keterangan" class="gp-label">Keterangan (opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" class="gp-input gp-input-line"
                           placeholder="Tulis keterangan" maxlength="255"
                           value="{{ old('keterangan', $hariIni->keterangan ?? '') }}">

                    <button type="submit" class="gp-btn gp-btn-save">Simpan absen</button>
                </form>
            </section>

            {{-- Riwayat --}}
            <section class="gp-card gp-card-pad">
                <h3 class="gp-card-title">Riwayat 5 hari terakhir</h3>

                <table class="gp-table gp-table-flat gp-table-riwayat">
                    <thead>
                    <tr>
                        <th style="width:35%">Tanggal</th>
                        <th style="width:30%">Jam masuk</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $r->jam_masuk ? \Carbon\Carbon::parse($r->jam_masuk)->format('H:i') : '-' }}</td>
                            <td>
                                <span class="gp-badge {{ $badge[$r->status] ?? 'gp-badge-gray' }}">
                                    {{ $statusList[$r->status] ?? ucfirst($r->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="gp-empty">Belum ada riwayat absen.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </section>

        </div>
    @endif

</div>
@endsection
