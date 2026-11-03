<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class Guru extends Model
{
    protected $table = 'gurus';
    public $timestamps = false;

    protected $fillable = [
        'nip', 'nama', 'jabatan', 'mapel', 'foto', 'status',
        'urutan', 'tampil_di_web', 'jenis_kelamin', 'pendidikan', 'bio',
    ];

    protected $casts = [
        'jabatan'       => 'array',
        'mapel'         => 'array',
        'tampil_di_web' => 'boolean',
    ];

    // Selalu berupa array, data lama berupa teks biasa pun aman
    public function getJabatanListAttribute(): array
    {
        return array_values(array_filter(Arr::wrap($this->jabatan), fn ($v) => trim((string) $v) !== ''));
    }

    public function getMapelListAttribute(): array
    {
        return array_values(array_filter(Arr::wrap($this->mapel), fn ($v) => trim((string) $v) !== ''));
    }

    public function user(): HasOne { return $this->hasOne(User::class); }
    public function kelasWali(): HasMany { return $this->hasMany(Kelas::class, 'wali_kelas_id'); }
    public function absensi(): HasMany { return $this->hasMany(AbsensiGuru::class); }
    public function pengampu(): HasMany { return $this->hasMany(Pengampu::class); }
}