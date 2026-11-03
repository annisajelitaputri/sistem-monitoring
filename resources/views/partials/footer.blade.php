@php
    // 087731242003 -> 6287731242003 (format internasional untuk wa.me)
    $waNo = preg_replace('/\D/', '', config('sekolah.telepon', '087731242003'));
    $waNo = str_starts_with($waNo, '0') ? '62' . substr($waNo, 1) : $waNo;
    $waPesan = rawurlencode('Assalamu\'alaikum, saya ingin bertanya tentang SMKN 9 Luwu.');
    $emailSekolah = config('sekolah.email', 'Smkn1walut@gmail.com');
@endphp

<footer class="foot">
    <div class="wrap foot-grid">
        <div>
            <div class="brand"><img src="{{ asset('storage/publik/logo.png') }}" alt=""><span>SMKN 9 Luwu</span></div>
            <p>Sekolah menengah kejuruan negeri yang mencetak lulusan terampil, berkarakter, dan siap menghadapi dunia kerja.</p>
        </div>
        <div>
            <h4>Alamat</h4>
            <p><x-icon name="pin" /> Jl. Torede Km.34, Kec. Walenrang Utara<br>Kabupaten Luwu, Sulawesi Selatan</p>
        </div>
        <div>
            <h4>Kontak</h4>
            <p>
                <x-icon name="phone" />
                <a href="https://wa.me/{{ $waNo }}?text={{ $waPesan }}" target="_blank" rel="noopener noreferrer" style="display:inline;">
                    {{ config('sekolah.telepon', '087731242003') }}
                </a>
            </p>
            <p>
                <x-icon name="mail" />
                <a href="mailto:{{ $emailSekolah }}" style="display:inline;">{{ $emailSekolah }}</a>
            </p>
        </div>
        <div>
            <h4>Menu cepat</h4>
            <a href="{{ route('beranda') }}">Beranda</a><a href="{{ route('profil') }}">Profil sekolah</a>
            <a href="{{ route('guru') }}">Tenaga pendidik</a><a href="{{ route('kegiatan') }}">Kegiatan</a>
            <a href="{{ route('berita') }}">Berita</a><a href="{{ route('kontak') }}">Kontak</a>
        </div>
    </div>
    <div class="foot-copy">© {{ date('Y') }} SMKN 9 Luwu | Sistem Monitoring Absensi Guru dan Siswa</div>
</footer>