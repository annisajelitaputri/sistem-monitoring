@extends('layouts.public')

@section('title', 'Tenaga Pendidik')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tenaga-pendidik.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/guru-detail.css') }}">
@endpush

@section('content')
@php
    $kepsek = $kepsek ?? \App\Models\Guru::where('status', 'aktif')
        ->where('jabatan', 'like', '%kepala sekolah%')
        ->first();

    $guru = collect($guru ?? $gurus ?? [])
        ->when($kepsek, fn ($c) => $c->reject(fn ($g) => $g->id === $kepsek->id));

    $fotoUrl = function ($path) {
        $path = is_array($path) ? (string) reset($path) : (string) $path;

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        return \Illuminate\Support\Str::startsWith($path, 'storage/')
            ? asset($path)
            : asset('storage/' . $path);
    };
@endphp

<main class="tenaga-page">

    <section
        class="tenaga-hero"
        style="--hero-img: url('{{ asset('assets/img/NAMA-FOTO-HERO.jpg') }}')"
    >
        <div class="tenaga-container">
            <nav class="tenaga-crumb">
                <span class="dot"></span>
                <a href="{{ url('/') }}">Beranda</a>
                <span>/</span>
                <strong>Tenaga Pendidik</strong>
            </nav>

            <h1>Tenaga Pendidik</h1>

            <div class="tenaga-bars"><span></span><span></span><span></span></div>

            <p>
                Mengenal tenaga pendidik dan tenaga kependidikan
                yang berperan dalam mendukung pendidikan di sekolah kami.
            </p>
        </div>
    </section>

    <section class="tenaga-section">
        <div class="tenaga-container">

            <div class="tenaga-heading">
                <h2>Kepala Sekolah</h2>
                <p>Pimpinan yang mengarahkan perkembangan dan mutu pendidikan sekolah.</p>
            </div>

            @if ($kepsek)
                <article class="kepsek-card">
                    <div class="tenaga-foto kepsek-foto">
                        @if (!empty($kepsek->foto))
                            <img
                                src="{{ $fotoUrl($kepsek->foto) }}"
                                alt="{{ $kepsek->nama }}"
                            >
                        @else
                            <x-icon name="users" />
                        @endif
                    </div>

                    <div class="kepsek-info">
                        <span class="tenaga-jabatan">KEPALA SEKOLAH</span>
                        <h3>{{ $kepsek->nama }}</h3>

                        @if (!empty($kepsek->nip))
                            <p>NIP: {{ $kepsek->nip }}</p>
                        @endif
                    </div>
                </article>
            @else
                <p class="tenaga-kosong">Data kepala sekolah belum tersedia.</p>
            @endif

            <div class="tenaga-heading guru-heading">
                <h2>Daftar Guru</h2>
                <p>Tenaga pendidik yang berkontribusi dalam proses pembelajaran. Klik kartu untuk melihat profil lengkap.</p>
            </div>

            <div class="tenaga-grid">
                @forelse ($guru as $item)
                    <a href="{{ route('guru.show', $item) }}" class="guru-card-link" aria-label="Lihat profil {{ $item->nama }}">
                        <article class="guru-card">
                            <div class="tenaga-foto guru-foto">
                                @if (!empty($item->foto))
                                    <img
                                        src="{{ $fotoUrl($item->foto) }}"
                                        alt="{{ $item->nama }}"
                                        loading="lazy"
                                    >
                                @else
                                    <x-icon name="users" />
                                @endif
                            </div>

                            <div class="guru-info">
                                <h3>{{ $item->nama }}</h3>

                                @if (count($item->jabatan_list))
                                    <p class="guru-jabatan">{{ implode(' • ', $item->jabatan_list) }}</p>
                                @endif

                                @if (count($item->mapel_list))
                                    <p class="guru-mapel">
                                        Mata Pelajaran: {{ implode(', ', $item->mapel_list) }}
                                    </p>
                                @endif

                                <span class="guru-lihat">Lihat profil &rarr;</span>
                            </div>
                        </article>
                    </a>
                @empty
                    <p class="tenaga-kosong">
                        Data tenaga pendidik belum tersedia.
                    </p>
                @endforelse
            </div>

        </div>
    </section>

</main>
@endsection