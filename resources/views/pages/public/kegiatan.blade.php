@extends('layouts.situs')

@section('title', 'Kegiatan')

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
<div class="tenaga-page">

    {{-- HERO dengan foto latar --}}
    <section class="tenaga-hero" style="--hero-img: url('{{ asset('storage/publik/hero-2.jpg') }}');">
        <div class="tenaga-container">

            <div class="kg-text">
                <div class="tenaga-crumb">
                    <span class="dot"></span>
                    <a href="{{ url('/') }}">Beranda</a>
                    <span>/</span>
                    <b>Kegiatan</b>
                </div>
                <h1>Kegiatan Sekolah</h1>
                <div class="tenaga-bars"><span></span><span></span><span></span></div>
                <p>Dokumentasi kegiatan belajar, perlombaan, dan acara yang membuat hari-hari di SMKN 9 Luwu lebih berwarna.</p>
            </div>

            {{-- Foto miring di kanan --}}
            <div class="kg-photo" style="background-image: url('{{ asset('storage/publik/labkomm.jpeg') }}');"></div>
            <div class="kg-badge">
                <span class="kg-badge-ic">
                    @include('partials.situs-icon', ['n' => 'image', 's' => 20, 'w' => 2])
                </span>
                <div>
                    <b>SMKN 9 Luwu</b>
                    <small>Dokumentasi Kegiatan</small>
                </div>
            </div>

        </div>
    </section>

    {{-- ISI --}}
    <section class="tenaga-section">
        <div class="tenaga-container">
            @if ($kegiatans->isEmpty())
                <div class="st-empty">Belum ada kegiatan yang dipublikasikan.</div>
            @else
                <div class="st-grid">
                    @foreach ($kegiatans as $i => $k)
                        @php
                            $t = \App\Support\Tampil::tanggal($k->tanggal, $k->created_at ?? null);
                            $warna = ($i % 6) + 1;
                        @endphp
                        <article class="st-card">
                            <a href="{{ route('kegiatan.show', $k) }}" class="st-ph st-g{{ $warna }} {{ $k->foto ? 'has-img' : '' }}" aria-label="{{ $k->judul }}">
                                @if ($k->foto)
                                    <img src="{{ asset('storage/' . $k->foto) }}" alt="{{ $k->judul }}" loading="lazy">
                                @else
                                    <span class="st-ph-icon">@include('partials.situs-icon', ['n' => 'image', 's' => 44, 'w' => 1.6])</span>
                                @endif
                                <span class="st-date">
                                    <b>{{ \App\Support\Tampil::hari($t) }}</b>
                                    <small>{{ \App\Support\Tampil::bulan($t) }}</small>
                                </span>
                            </a>

                            <div class="st-card-body">
                                <h3>{{ $k->judul }}</h3>
                                <p class="st-meta">
                                    <x-icon n="calendar" :s="15" />
                                    {{ \App\Support\Tampil::panjang($t) }}
                                </p>
                                <p class="st-desc">{{ \App\Support\Tampil::ringkas($k->deskripsi, 90) ?: 'Belum ada deskripsi.' }}</p>
                                <a href="{{ route('kegiatan.show', $k) }}" class="st-link">
                                    Lihat detail @include('partials.situs-icon', ['n' => 'arrow', 's' => 16, 'w' => 2])
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{ $kegiatans->links('partials.situs-pagination') }}
            @endif
        </div>
    </section>

</div>
@endsection