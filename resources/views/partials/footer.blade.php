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
            <p><x-icon name="phone" /> 087731242003</p>
            <p><x-icon name="mail" /> Smkn1walut@gmail.com</p>
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
