@extends('layouts.situs')

@section('title', 'Berita')

@push('styles')
<style>
    .tenaga-page .tenaga-hero .tenaga-container { position: relative; }
    .tenaga-page .kg-text { max-width: calc(100% - 420px); }

    .tenaga-page .kg-photo {
        position: absolute; right: 0; top: -20px; width: 340px; height: 250px;
        border: 8px solid #fff; border-radius: 26px; transform: rotate(3deg);
        background-size: cover; background-position: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
    }
    .tenaga-page .kg-badge {
        position: absolute; right: 250px; top: 215px; z-index: 2;
        display: flex; align-items: center; gap: 11px; padding: 12px 18px;
        background: #fff; color: #0b1b3f; border-radius: 16px;
        box-shadow: 0 12px 28px rgba(10, 30, 90, .25);
    }
    .tenaga-page .kg-badge b { display: block; font-size: 14px; }
    .tenaga-page .kg-badge small { color: #64718f; font-size: 12px; }
    .tenaga-page .kg-badge-ic {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: grid; place-items: center; color: #fff;
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    }

    @media (max-width: 1000px) {
        .tenaga-page .kg-text { max-width: 100%; }
        .tenaga-page .kg-photo,
        .tenaga-page .kg-badge { display: none; }
    }
</style>
@endpush

@section('content')
@php $palet = [6, 4, 2, 3]; @endphp
<div class="tenaga-page">

    {{-- HERO dengan foto latar --}}
    <section class="tenaga-hero" style="--hero-img: url('{{ asset('storage/publik/hero-1.jpg') }}');">
        <div class="tenaga-container">

            <div class="kg-text">
                <div class="tenaga-crumb">
                    <span class="dot"></span>
                    <a href="{{ url('/') }}">Beranda</a>
                    <span>/</span>
                    <b>Berita</b>
                </div>
                <h1>Berita dan Pengumuman</h1>
                <div class="tenaga-bars"><span></span><span></span><span></span></div>
                <p>Kabar terbaru dan informasi resmi dari SMK Negeri 9 Luwu untuk siswa, guru, dan orang tua.</p>
            </div>

            {{-- Foto miring di kanan --}}
            <div class="kg-photo" style="background-image: url('{{ asset('storage/publik/hero-3.jpg') }}');"></div>
            <div class="kg-badge">
                <span class="kg-badge-ic">
                    @include('partials.situs-icon', ['n' => 'news', 's' => 20, 'w' => 2])
                </span>
                <div>
                    <b>SMKN 9 Luwu</b>
                    <small>Kabar &amp; Informasi</small>
                </div>
            </div>

        </div>
    </section>

    {{-- ISI --}}
    <section class="tenaga-section">
        <div class="tenaga-container">

            <div class="st-chips">
                <a href="{{ route('berita') }}" class="{{ ! $kategori ? 'active' : '' }}">Semua</a>
                <a href="{{ route('berita', ['kategori' => 'berita']) }}" class="{{ $kategori === 'berita' ? 'active' : '' }}">Berita</a>
                <a href="{{ route('berita', ['kategori' => 'pengumuman']) }}" class="{{ $kategori === 'pengumuman' ? 'active' : '' }}">Pengumuman</a>
            </div>

            @if (! $utama && $beritas->isEmpty())
                <div class="st-empty">Belum ada berita yang dipublikasikan.</div>
            @endif

            {{-- Berita utama (terbaru) --}}
            @if ($utama)
                @php
                    $tu = \App\Support\Tampil::tanggal($utama->tanggal, $utama->created_at ?? null);
                    $kat = strtolower($utama->kategori);
                @endphp
                <section class="st-feature">
                    <div class="st-feature-copy">
                        <span class="st-feature-tag">{{ $kat === 'pengumuman' ? 'PENGUMUMAN UTAMA' : 'BERITA UTAMA' }}</span>
                        <h2>{{ $utama->judul }}</h2>
                        <p class="st-feature-meta">
                            @include('partials.situs-icon', ['n' => 'calendar', 's' => 15])
                            {{ ucfirst($kat) }} | {{ \App\Support\Tampil::panjang($tu) }}
                        </p>
                        <p class="st-feature-text">{{ \App\Support\Tampil::ringkas($utama->isi, 150) }}</p>
                        <a href="{{ route('berita.show', $utama) }}" class="st-btn-white">
                            Baca selengkapnya @include('partials.situs-icon', ['n' => 'arrow', 's' => 16, 'w' => 2])
                        </a>
                    </div>

                    <div class="st-feature-media">
                        @if ($utama->gambar)
                            <img src="{{ asset('storage/' . $utama->gambar) }}" alt="{{ $utama->judul }}">
                        @else
                            @include('partials.situs-icon', ['n' => 'image', 's' => 52, 'w' => 1.6])
                            <span>Gambar berita utama</span>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Daftar berita --}}
            <div class="st-rows">
                @foreach ($beritas as $i => $b)
                    @php
                        $tb = \App\Support\Tampil::tanggal($b->tanggal, $b->created_at ?? null);
                        $kb = strtolower($b->kategori);
                    @endphp
                    <article class="st-row">
                        <a href="{{ route('berita.show', $b) }}" class="st-ph st-g{{ $palet[$i % 4] }} {{ $b->gambar ? 'has-img' : '' }}" aria-label="{{ $b->judul }}">
                            @if ($b->gambar)
                                <img src="{{ asset('storage/' . $b->gambar) }}" alt="{{ $b->judul }}" loading="lazy">
                            @else
                                <span class="st-ph-icon">@include('partials.situs-icon', ['n' => 'image', 's' => 40, 'w' => 1.6])</span>
                            @endif
                        </a>

                        <div class="st-row-body">
                            <div class="st-row-top">
                                <span class="st-tag {{ $kb === 'pengumuman' ? 'st-tag-yellow' : 'st-tag-blue' }}">{{ ucfirst($kb) }}</span>
                                <span class="st-meta">
                                    @include('partials.situs-icon', ['n' => 'calendar', 's' => 15])
                                    {{ \App\Support\Tampil::panjang($tb) }}
                                </span>
                            </div>
                            <h3>{{ $b->judul }}</h3>
                            <p class="st-desc">{{ \App\Support\Tampil::ringkas($b->isi, 140) }}</p>
                            <a href="{{ route('berita.show', $b) }}" class="st-link">
                                Baca selengkapnya @include('partials.situs-icon', ['n' => 'arrow', 's' => 16, 'w' => 2])
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{ $beritas->links('partials.situs-pagination') }}

        </div>
    </section>

</div>
@endsection