@extends('layouts.public')

@section('title', $guru->nama)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tenaga-pendidik.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/guru-detail.css') }}">
@endpush

@section('content')
@php
    $jabatanList = $guru->jabatan_list;
    $mapelList   = $guru->mapel_list;
@endphp

<div class="tenaga-page gd-page">

    <section class="gd-hero">
        <div class="tenaga-container">
            <nav class="tenaga-crumb">
                <span class="dot"></span>
                <a href="{{ url('/') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('guru') }}">Tenaga Pendidik</a>
                <span>/</span>
                <strong>{{ $guru->nama }}</strong>
            </nav>
        </div>
    </section>

    <section class="tenaga-section gd-section">
        <div class="tenaga-container">

            <article class="gd-card">
                <div class="gd-foto">
                    @if ($guru->foto)
                        <img src="{{ asset('storage/' . ltrim($guru->foto, '/')) }}" alt="{{ $guru->nama }}">
                    @else
                        <x-icon name="users" />
                    @endif
                </div>

                <div class="gd-info">
                    <h1>{{ $guru->nama }}</h1>
                    <div class="gd-bar"></div>

                    @if (count($jabatanList))
                        <div class="gd-block">
                            <h2>Jabatan</h2>
                            <div class="gd-chips">
                                @foreach ($jabatanList as $j)
                                    <span class="gd-chip gd-chip-jabatan">{{ $j }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($mapelList))
                        <div class="gd-block">
                            <h2>Mata pelajaran</h2>
                            <div class="gd-chips">
                                @foreach ($mapelList as $m)
                                    <span class="gd-chip gd-chip-mapel">{{ $m }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($guru->pendidikan)
                        <div class="gd-block">
                            <h2>Pendidikan terakhir</h2>
                            <p>{{ $guru->pendidikan }}</p>
                        </div>
                    @endif

                    @if ($guru->bio)
                        <div class="gd-block">
                            <h2>Tentang</h2>
                            <p class="gd-bio">{!! nl2br(e($guru->bio)) !!}</p>
                        </div>
                    @endif

                    <a href="{{ route('guru') }}" class="gd-back">&larr; Kembali ke daftar guru</a>
                </div>
            </article>

        </div>
    </section>

</div>
@endsection