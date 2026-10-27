<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\ProfilSekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin/TU (password: admin123)
        User::create([
            'name' => 'Admin TU',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Contoh guru + akun guru
        $guru = Guru::create([
            'nip' => '198501012010011001',
            'nama' => 'Armanto, S.Pd',
            'jabatan' => 'Wali Kelas',
            'mapel' => 'PKN',
            'status' => 'aktif',
        ]);
        User::create([
            'name' => $guru->nama,
            'username' => $guru->nip,
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'guru_id' => $guru->id,
        ]);

        // Contoh kelas + siswa
        $kelas = Kelas::create([
            'nama_kelas' => 'X TKJ',
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'tahun_ajaran' => '2026/2027',
            'wali_kelas_id' => $guru->id,
        ]);
        Siswa::create([
            'nis' => '20250001',
            'nama' => 'Contoh Siswa',
            'jenis_kelamin' => 'L',
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
        ]);

        ProfilSekolah::create([
            'nama_sekolah' => 'SMKN 9 Luwu',
            'visi' => 'Siap, Terampil dan Berkarakter, menuju lapangan kerja dan peradaban era globalisasi.',
            'misi' => 'Membangun potensi siswa melalui pendidikan kejuruan berbasis praktik dan teknologi.',
            'alamat' => 'Jl. Poros Walenrang Utara, Kab. Luwu, Sulawesi Selatan',
            'telepon' => '-',
            'email' => '-',
        ]);
    }
}
