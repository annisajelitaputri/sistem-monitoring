@extends('layouts.public')

@section('title', 'Tenaga Pendidik')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tenaga-pendidik.css') }}">
@endpush

@section('content')
<main class="tenaga-page">

    {{-- Hero: ganti NAMA-FOTO-HERO.jpg dengan foto yang dipakai di halaman Profil --}}
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

            {{-- Kepala sekolah --}}
            <div class="tenaga-heading">
                <h2>Kepala Sekolah</h2>
                <p>Pimpinan yang mengarahkan perkembangan dan mutu pendidikan sekolah.</p>
            </div>

            <article class="kepsek-card">
                <div class="tenaga-foto kepsek-foto">
                    @if (!empty($data['kepala_sekolah']['foto']))
                        <img
                            src="{{ $data['kepala_sekolah']['foto'] }}"
                            alt="{{ $data['kepala_sekolah']['nama'] }}"
                        >
                    @else
                        <x-icon name="users" />
                    @endif
                </div>

                <div class="kepsek-info">
                    <span class="tenaga-jabatan">KEPALA SEKOLAH</span>
                    <h3>{{ $data['kepala_sekolah']['nama'] }}</h3>

                    @if (!empty($data['kepala_sekolah']['nip']))
                        <p>NIP: {{ $data['kepala_sekolah']['nip'] }}</p>
                    @endif

                    @if (!empty($data['kepala_sekolah']['sambutan']))
                        <p class="kepsek-sambutan">
                            {{ $data['kepala_sekolah']['sambutan'] }}
                        </p>
                    @endif
                </div>
            </article>

            {{-- Daftar guru --}}
            <div class="tenaga-heading guru-heading">
                <h2>Daftar Guru</h2>
                <p>Tenaga pendidik yang berkontribusi dalam proses pembelajaran.</p>
            </div>

            <div class="tenaga-grid">
                @forelse ($guru as $item)
                    <article class="guru-card">
                        <div class="tenaga-foto guru-foto">
                            @if (!empty($item['foto']))
                                <img
                                    src="{{ $item['foto'] }}"
                                    alt="{{ $item['nama'] }}"
                                    loading="lazy"
                                >
                            @else
                                <x-icon name="users" />
                            @endif
                        </div>

                        <div class="guru-info">
                            <h3>{{ $item['nama'] }}</h3>

                            @if (!empty($item['jabatan']))
                                <p class="guru-jabatan">{{ $item['jabatan'] }}</p>
                            @endif

                            @if (!empty($item['mapel']))
                                <p class="guru-mapel">
                                    Mata Pelajaran: {{ $item['mapel'] }}
                                </p>
                            @endif
                        </div>
                    </article>
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