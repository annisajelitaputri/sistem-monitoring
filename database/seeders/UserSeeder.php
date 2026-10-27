<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'name'       => 'Admin TU',
                'password'   => Hash::make('admin123'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
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

    }
}