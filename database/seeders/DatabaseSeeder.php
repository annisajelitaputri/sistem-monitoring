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
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Admin TU',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 'aktif',
            ]
        );

        // Contoh guru + akun guru (password: guru123)
        // jabatan dan mapel berupa array karena model Guru memakai cast array
        $guru = Guru::updateOrCreate(
            ['nip' => '198501012010011001'],
            [
                'nama'    => 'Armanto, S.Pd',
                'jabatan' => ['Wali Kelas'],
                'mapel'   => ['PKN'],
                'status'  => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => $guru->nip],
            [
                'name'     => $guru->nama,
                'password' => Hash::make('guru123'),
                'role'     => 'guru',
                'status'   => 'aktif',
                'guru_id'  => $guru->id,
            ]
        );

        // Contoh kelas + siswa
        $kelas = Kelas::updateOrCreate(
            ['nama_kelas' => 'X TKJ'],
            [
                'jurusan'       => 'Teknik Komputer dan Jaringan',
                'tahun_ajaran'  => '2026/2027',
                'wali_kelas_id' => $guru->id,
            ]
        );

        Siswa::updateOrCreate(
            ['nis' => '20250001'],
            [
                'nama'          => 'Contoh Siswa',
                'jenis_kelamin' => 'L',
                'kelas_id'      => $kelas->id,
                'status'        => 'aktif',
            ]
        );

        // ProfilSekolah::create([ ... ]) dan seterusnya: biarkan seperti semula
    
    }
}
