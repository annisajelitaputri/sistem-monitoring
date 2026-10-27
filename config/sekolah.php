<?php

/*
|--------------------------------------------------------------------------
| Data sekolah (dipakai di halaman Kontak dan footer)
|--------------------------------------------------------------------------
| Ubah di sini kalau alamat, kontak, atau lokasi peta berubah.
| Setelah mengubah file ini jalankan: php artisan config:clear
*/

return [
    'nama'      => 'SMKN 9 Luwu',
    'kabupaten' => 'Kabupaten Luwu',
    'deskripsi' => 'Sekolah menengah kejuruan negeri yang mencetak lulusan terampil, berkarakter, dan siap menghadapi dunia kerja.',

    'alamat'    => 'Jl. Torede Km.34, Kec. Walenrang Utara, Kab. Luwu, Sulawesi Selatan',
    'telepon'   => '087731242003',
    'email'     => 'Smkn1walut@gmail.com',

    'jam_hari'  => 'Senin - Jumat',
    'jam_pukul' => 'Pukul 09.00–11.30 WITA',

    // Link Google Maps asli SMKN 9 Luwu (tombol "Buka di aplikasi peta")
    'maps_url'  => 'https://www.google.com/maps/place/SMKN+9+Luwu/@-2.7401662,120.14084,17z/data=!3m1!4b1!4m6!3m5!1s0x2d93d5ef619aa59f:0xb8279e81e74b897f!8m2!3d-2.7401662!4d120.1434149!16s%2Fg%2F11f57w_jpn?entry=ttu&g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D',

    // Titik koordinat untuk peta yang ditanam di halaman Kontak
    'lat'       => -2.7401662,
    'lng'       => 120.1434149,

    // Foto gedung sekolah untuk bagian atas halaman (taruh di public/assets/images/)
    'foto_hero' => 'assets/images/hero-sekolah.jpg',
    'logo'      => 'assets/images/logo.png',
];
