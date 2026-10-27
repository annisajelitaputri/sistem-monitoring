<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('kategori', ['berita', 'pengumuman'])->default('berita');
            $table->text('isi');
            $table->string('gambar')->nullable();
            $table->enum('status', ['draft', 'terbit'])->default('draft');
            $table->foreignId('penulis_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
