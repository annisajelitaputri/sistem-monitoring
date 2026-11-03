<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->foreignId('mapel_id')->after('kelas_id')->constrained('mapels');
        });

        // Buat unique baru DULU, baru hapus yang lama
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->unique(['siswa_id', 'tanggal', 'mapel_id'], 'absensi_siswa_unik_mapel');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->dropUnique('absensi_siswa_siswa_id_tanggal_unique');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->unique(['siswa_id', 'tanggal']);
            $table->dropUnique('absensi_siswa_unik_mapel');
            $table->dropForeign(['mapel_id']);
            $table->dropColumn('mapel_id');
        });
    }
};