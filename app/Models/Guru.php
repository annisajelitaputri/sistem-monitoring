<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    protected $table = 'gurus';
    public $timestamps = false;
    protected $fillable = ['nip', 'nama', 'jabatan', 'mapel', 'foto', 'status'];

    public function user(): HasOne { return $this->hasOne(User::class); }
    public function kelasWali(): HasMany { return $this->hasMany(Kelas::class, 'wali_kelas_id'); }
    public function absensi(): HasMany { return $this->hasMany(AbsensiGuru::class); }
}
