@extends('layouts.public')

@section('title', 'Beranda')
@section('body', 'home')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/beranda.css') }}">
@endpush
@push('scripts')
    <script>window.BERANDA = @json($data);</script>
    <script src="{{ asset('assets/js/beranda.js') }}" defer></script>
@endpush

@section('content')
{{-- HERO + SLIDER (foto dari storage/app/public/publik) --}}
<section class="hero">
    @foreach (['hero-1', 'hero-2', 'hero-3'] as $s)
        <div class="hero-slide" style="background-image:url('{{ asset('storage/publik/' . $s . '.jpg') }}')"></div>
    @endforeach
    <div class="wrap hero-in">
        <span class="pill-top"><i></i> Sekolah Menengah Kejuruan Negeri · Kabupaten Luwu</span>
        <h1>SELAMAT DATANG DI <span class="gold">SMKN 9 LUWU</span></h1>
        <p>Belajar, berkarya, dan bertumbuh bersama guru-guru terbaik di lingkungan sekolah kejuruan yang modern, tertata, dan terhubung secara digital.</p>
        <div class="hero-btns">
            <a class="btn gold-btn" href="{{ route('profil') }}">Lihat profil <x-icon name="arrow" /></a>
            <a class="btn glass-btn" href="{{ route('berita') }}">Berita terbaru <x-icon name="arrow" /></a>
        </div>
        <ul class="checks">
            <li><x-icon name="check" /> Pembelajaran praktik</li>
            <li><x-icon name="check" /> Absensi digital</li>
            <li><x-icon name="check" /> Informasi terbuka</li>
        </ul>
    </div>
    <div class="hero-dots">
        @foreach (range(1, 3) as $n)<button type="button" aria-label="Foto {{ $n }}"></button>@endforeach
    </div>
</section>

{{-- STATISTIK --}}
<section class="wrap stats">
    <div><span class="ico c-blue"><x-icon name="cap" /></span><div><b>{{ $jumlahSiswa }}</b><small>Siswa aktif</small></div></div>
    <div><span class="ico c-teal"><x-icon name="users" /></span><div><b>{{ $jumlahGuru }}</b><small>Guru dan tenaga pendidik</small></div></div>
    <div><span class="ico c-indigo"><x-icon name="laptop" /></span><div><b>{{ $program->count() }}</b><small>Program keahlian</small></div></div>
</section>

{{-- TENTANG --}}
<section class="sec">
    <div class="wrap about">
        <div class="about-vis">
            <span class="chip c1"><x-icon name="laptop" /> Praktik</span>
            <span class="chip c2"><x-icon name="users" /> Bimbingan</span>
            <span class="chip c3"><x-icon name="shield" /> Karakter</span>
            <div class="logo-ring"><img src="{{ asset('storage/publik/logo.png') }}" alt="Logo SMKN 9 Luwu"></div>
            <div class="vis-cap"><b>SMK NEGERI 9 LUWU</b><small>Kabupaten Luwu · Sulawesi Selatan</small></div>
        </div>
        <div>
            <div class="eyebrow">Tentang sekolah</div>
            <h2 class="h2">Tempat tumbuh generasi muda yang terampil dan berkarakter</h2>
            <p class="lead">SMK Negeri 9 Luwu menghadirkan pendidikan kejuruan yang menggabungkan praktik, teknologi, dan pembinaan karakter. Seluruh kegiatan belajar didukung sistem yang tertata agar guru, siswa, dan orang tua dapat memantau perkembangan.</p>
            <div class="feat"><span class="ico c-blue"><x-icon name="laptop" /></span><div><b>Pembelajaran berbasis praktik</b><small>Siswa belajar langsung lewat praktik yang relevan dengan dunia kerja.</small></div></div>
            <div class="feat"><span class="ico c-teal"><x-icon name="users" /></span><div><b>Guru yang mendampingi</b><small>Tenaga pendidik siap membimbing setiap siswa sesuai kebutuhannya.</small></div></div>
            <div class="feat"><span class="ico c-gold"><x-icon name="shield" /></span><div><b>Absensi yang transparan</b><small>Kehadiran guru dan siswa dicatat rapi lewat sistem monitoring.</small></div></div>
        </div>
    </div>
</section>

{{-- KOMPETENSI --}}
<section class="sec alt" id="kompetensi">
    <div class="wrap">
        <div class="eyebrow">Kompetensi</div>
        <h2 class="h2">Program keahlian</h2>
        <div class="prog-grid">
            @foreach ($program as $p)
                <article class="prog">
                    <span class="no">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="ico big {{ $loop->first ? 'c-blue' : 'c-teal' }}"><x-icon :name="$p['icon']" /></span>
                    <h3>{{ $p['judul'] }}</h3>
                    <p>{{ $p['ringkas'] }}</p>
                    <div class="prog-foot">
                        <span class="kode">{{ $p['kode'] }}</span>
                        <button type="button" class="more" data-open="program:{{ $p['id'] }}">Selengkapnya <x-icon name="arrow" /></button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- BERITA --}}
<section class="sec">
    <div class="wrap">
        <div class="sec-head">
            <div><div class="eyebrow">Info sekolah</div><h2 class="h2">Berita terbaru</h2></div>
            <a class="link-pill" href="{{ route('berita') }}">Lihat semua <x-icon name="arrow" /></a>
        </div>
        <div class="news">
            @forelse ($berita as $b)
                @if ($loop->first)
                    <article class="news-main {{ $b['gambar'] ? '' : 'g1' }}" @if ($b['gambar']) style="background-image:url('{{ $b['gambar'] }}')" @endif data-open="berita:{{ $b['id'] }}" tabindex="0" role="button">
                        @unless ($b['gambar'])<x-icon name="image" class="ph" />@endunless
                        <div class="over">
                            <span class="tag">{{ $b['kategori'] }}</span>
                            <small class="date"><x-icon name="calendar" /> {{ $b['tanggal'] }}</small>
                            <h3>{{ $b['judul'] }}</h3>
                            <p>{{ $b['ringkas'] }}</p>
                        </div>
                        <span class="go"><x-icon name="arrow" /></span>
                    </article>
                @else
                    <article class="news-side" data-open="berita:{{ $b['id'] }}" tabindex="0" role="button">
                        <div class="thumb {{ $b['gambar'] ? '' : 'g' . ($loop->index + 1) }}" @if ($b['gambar']) style="background-image:url('{{ $b['gambar'] }}')" @endif>
                            @unless ($b['gambar'])<x-icon name="image" />@endunless
                        </div>
                        <div class="txt">
                            <div class="meta"><span class="tag {{ $b['kategori'] === 'Pengumuman' ? 'warn' : '' }}">{{ $b['kategori'] }}</span><small class="date"><x-icon name="calendar" /> {{ $b['tanggal'] }}</small></div>
                            <h3>{{ $b['judul'] }}</h3>
                            <p>{{ $b['ringkas'] }}</p>
                            <span class="more">Baca selengkapnya <x-icon name="arrow" /></span>
                        </div>
                    </article>
                @endif
            @empty
                <p class="empty">Belum ada berita yang diterbitkan.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- KEGIATAN --}}
<section class="sec alt">
    <div class="wrap">
        <div class="sec-head">
            <div><div class="eyebrow">Galeri</div><h2 class="h2">Kegiatan sekolah</h2></div>
            <a class="link-pill" href="{{ route('kegiatan') }}">Semua kegiatan <x-icon name="arrow" /></a>
        </div>
        <div class="gal">
            @forelse ($kegiatan as $k)
                <article class="gal-item {{ $k['gambar'] ? '' : 'g' . (($loop->index % 4) + 1) }}" @if ($k['gambar']) style="background-image:url('{{ $k['gambar'] }}')" @endif data-open="kegiatan:{{ $k['id'] }}" tabindex="0" role="button">
                    @unless ($k['gambar'])<x-icon name="image" class="ph" />@endunless
                    <div class="cap">
                        @if (in_array($loop->index, [0, 3]))<small>{{ $k['tanggal'] }}</small>@endif
                        <strong>{{ $k['judul'] }}</strong>
                    </div>
                </article>
            @empty
                <p class="empty">Belum ada kegiatan yang diterbitkan.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- MODAL DETAIL --}}
<div class="modal" id="modal" hidden role="dialog" aria-modal="true" aria-labelledby="m-title">
    <div class="m-box">
        <button type="button" class="m-x" aria-label="Tutup"><x-icon name="x" /></button>
        <div class="m-img" hidden></div>
        <div class="m-body">
            <div class="meta"><span class="tag m-tag"></span><small class="date m-date"><x-icon name="calendar" /> <span></span></small></div>
            <h3 id="m-title"></h3>
            <p class="m-text"></p>
        </div>
    </div>
</div>
@endsection