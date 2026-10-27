
@extends('layouts.public')

@section('title', 'Profil Sekolah')
@section('body', 'home')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/profil.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/profil.js') }}" defer></script>
@endpush

@php
    $fasilitas = [
        [
            'nama' => 'Gedung Kelas',
            'keterangan' => 'Ruang belajar utama bagi siswa.',
            'foto' => 'fasilitas-kelas.jpg',
        ],
        [
            'nama' => 'Gedung Praktek TKR',
            'keterangan' => 'Bengkel praktik dengan mobil praktik, lift mobil, dan kompresor.',
            'foto' => 'fasilitas-tkr.jpg',
        ],
        [
            'nama' => 'Laboratorium TKJ',
            'keterangan' => 'Ruang praktik komputer dan jaringan.',
            'foto' => null,
        ],
        [
            'nama' => 'Perpustakaan',
            'keterangan' => 'Koleksi buku pelajaran dan ruang baca untuk siswa.',
            'foto' => 'fasilitas-perpus.jpg',
        ],
    ];
@endphp

@section('content')
<div class="profile-page">

    {{-- HERO PROFIL --}}
    <section
        class="p-hero"
        style="background-image: url('{{ asset('storage/publik/hero-1.jpg') }}')"
    >
        <div class="wrap p-hero-in">

            <div class="p-hero-text">
                <div class="crumb">
                    <i></i>
                    <a href="{{ url('/') }}">Beranda</a>
                    <span>/</span>
                    <b>Profil</b>
                </div>

                <h1>PROFIL SEKOLAH</h1>

                <div class="bars">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <p>
                    Mengenal sejarah, visi, misi, dan fasilitas yang
                    mendukung proses belajar di SMK Negeri 9 Luwu.
                </p>
            </div>

            <div class="p-visual">
                <img
                    class="p-photo"
                    src="{{ asset('storage/publik/hero-3.jpg') }}"
                    alt="Gedung SMKN 9 Luwu"
                >

                <span class="flag">
                    <x-icon name="flag" />
                </span>

                <div class="p-badge">
                    <span class="ico c-blue">
                        <x-icon name="shield" />
                    </span>

                    <div>
                        <b>SMKN 9 Luwu</b>
                        <small>Kabupaten Luwu</small>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- SEJARAH, VISI, MISI --}}
    <section class="sec">
        <div class="wrap p-grid">

            {{-- SEJARAH --}}
            <article class="card history">
                <div class="eyebrow">Sejarah singkat</div>

                <h2 class="h2">Perjalanan SMKN 9 Luwu</h2>

                <p class="lead">
                    SMK Negeri 9 Luwu merupakan sekolah menengah kejuruan
                    yang berlokasi di Desa Pongko, Kecamatan Walenrang Utara,
                    Kabupaten Luwu, Provinsi Sulawesi Selatan. Sekolah ini
                    mulai berdiri pada tahun 2012 dan terus berkembang dalam
                    memberikan pendidikan kejuruan bagi peserta didik sesuai
                    dengan kebutuhan dunia kerja.
                </p>

                <div class="tl">
                    <div class="tl-item">
                        <h4>2012 (Awal Berdirinya Sekolah)</h4>
                        <p>
                            SMK Negeri 9 Luwu mulai berdiri dengan nama
                            SMK Negeri 1 Walenrang Utara, dengan program
                            keahlian Teknik Komputer Jaringan (TKJ) dan
                            Teknik Otomotif (TKR).
                        </p>
                    </div>

                    <div class="tl-item">
                        <h4>2018 (Perubahan Nama Sekolah)</h4>
                        <p>
                            Sejalan dengan perubahan nomenklatur satuan
                            pendidikan, nama sekolah berubah menjadi
                            SMK Negeri 9 Luwu.
                        </p>
                    </div>

                    <div class="tl-item now">
                        <h4>Saat Ini (Terus Berkembang)</h4>
                        <p>
                            SMK Negeri 9 Luwu terus meningkatkan kualitas
                            pendidikan, keterampilan, dan kompetensi peserta
                            didik agar siap melanjutkan pendidikan maupun
                            memasuki dunia kerja.
                        </p>
                    </div>
                </div>
            </article>

            {{-- VISI DAN MISI --}}
            <div class="side">

                <article class="card visi">
                    <div class="head">
                        <span class="ico glass">
                            <x-icon name="eye" />
                        </span>
                        <h3>Visi</h3>
                    </div>

                    <p>
                        Siap, Terampil dan Berkarakter, menembus lapangan
                        kerja dalam menyongsong peradaban era globalisasi.
                    </p>
                </article>

                <article class="card misi">
                    <div class="head">
                        <span class="ico soft">
                            <x-icon name="flag" />
                        </span>
                        <h3>Misi</h3>
                    </div>

                    <ol>
                        <li>
                            Membangun potensi diri berbasis dunia usaha dan
                            industri dengan motif kreatif, inovatif,
                            produktif dan kompetitif.
                        </li>
                        <li>
                            Meningkatkan inovasi pembelajaran berbasis
                            lingkungan/kearifan lokal yang potensial dan
                            edukatif.
                        </li>
                        <li>
                            Meningkatkan model pengembangan kurikulum untuk
                            menemukan lulusan yang unggul.
                        </li>
                    </ol>
                </article>

            </div>
        </div>
    </section>

    {{-- FASILITAS --}}
    <section class="sec fas">
        <div class="wrap">

            <div class="eyebrow center">Fasilitas</div>

            <h2 class="h2 center">
                Fasilitas pendukung belajar
            </h2>

            <p class="sub">
                Ruang dan sarana yang membantu siswa belajar, berlatih,
                dan berkarya.
            </p>

            <div class="fas-grid">
                @foreach ($fasilitas as $item)
                    <article class="fas-card">

                        @if ($item['foto'])
                            <img
                                src="{{ asset('storage/publik/' . $item['foto']) }}"
                                alt="{{ $item['nama'] }}"
                                loading="lazy"
                            >
                        @else
                            <div class="ph {{ $loop->index === 2 ? 'g4' : 'g2' }}">
                                <x-icon name="image" />
                            </div>
                        @endif

                        <div class="fas-txt">
                            <h3>{{ $item['nama'] }}</h3>
                            <p>{{ $item['keterangan'] }}</p>
                        </div>

                    </article>
                @endforeach
            </div>

        </div>
    </section>

</div>
@endsection
