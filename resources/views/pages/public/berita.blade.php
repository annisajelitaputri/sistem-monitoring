@extends('layouts.situs')

@section('title', 'Berita')
@section('crumb', 'Berita')
@section('hero-title', 'Berita dan Pengumuman')
@section('hero-text', 'Kabar terbaru dan informasi resmi dari SMK Negeri 9 Luwu untuk siswa, guru, dan orang tua.')
@section('hero-icon', 'news')

@section('content')
    @php $palet = [6, 4, 2, 3]; @endphp

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
@endsection
