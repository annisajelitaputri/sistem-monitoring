<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom baru
        Schema::table('gurus', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('tampil_di_web')->default(true);
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('pendidikan')->nullable();
            $table->text('bio')->nullable();
        });

        // 2. Ubah jabatan & mapel agar bisa menyimpan JSON
        DB::statement('ALTER TABLE gurus MODIFY jabatan TEXT NULL');
        DB::statement('ALTER TABLE gurus MODIFY mapel TEXT NULL');

        // 3. Data lama diubah jadi daftar berisi satu item
        DB::table('gurus')->get()->each(function ($guru) {
            DB::table('gurus')->where('id', $guru->id)->update([
                'jabatan' => json_encode($guru->jabatan ? [$guru->jabatan] : [], JSON_UNESCAPED_UNICODE),
                'mapel'   => json_encode($guru->mapel ? [$guru->mapel] : [], JSON_UNESCAPED_UNICODE),
            ]);
        });
    }

    public function down(): void
    {
        // Kembalikan ke teks biasa (ambil item pertama)
        DB::table('gurus')->get()->each(function ($guru) {
            $jabatan = json_decode($guru->jabatan, true)[0] ?? null;
            $mapel   = json_decode($guru->mapel, true)[0] ?? null;

            DB::table('gurus')->where('id', $guru->id)->update([
                'jabatan' => $jabatan,
                'mapel'   => $mapel,
            ]);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['urutan', 'tampil_di_web', 'jenis_kelamin', 'pendidikan', 'bio']);
        });
    }
};