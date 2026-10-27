@extends('layouts.situs')

@section('title', 'Kegiatan')
@section('crumb', 'Kegiatan')
@section('hero-title', 'Kegiatan Sekolah')
@section('hero-text', 'Dokumentasi kegiatan belajar, perlombaan, dan acara yang membuat hari-hari di SMKN 9 Luwu lebih berwarna.')
@section('hero-icon', 'image')

@section('content')
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
@endsection
