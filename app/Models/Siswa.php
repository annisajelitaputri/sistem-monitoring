<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';
    public $timestamps = false;
    protected $fillable = ['nis', 'nama', 'jenis_kelamin', 'kelas_id', 'status'];

    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function absensi(): HasMany { return $this->hasMany(AbsensiSiswa::class); }
}
