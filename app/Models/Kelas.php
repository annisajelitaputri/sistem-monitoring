<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    public $timestamps = false;
    protected $fillable = ['nama_kelas', 'jurusan', 'tahun_ajaran', 'wali_kelas_id'];

    public function waliKelas(): BelongsTo { return $this->belongsTo(Guru::class, 'wali_kelas_id'); }
    public function siswa(): HasMany { return $this->hasMany(Siswa::class); }
    public function absensiSiswa(): HasMany { return $this->hasMany(AbsensiSiswa::class); }
}
