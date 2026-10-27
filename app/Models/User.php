<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'password', 'role', 'guru_id'];
    protected $hidden = ['password', 'remember_token'];

    public function guru(): BelongsTo { return $this->belongsTo(Guru::class); }
    public function berita(): HasMany { return $this->hasMany(Berita::class, 'penulis_id'); }
    public function absensiGuruDicatat(): HasMany { return $this->hasMany(AbsensiGuru::class, 'dicatat_oleh'); }
    public function absensiSiswaDicatat(): HasMany { return $this->hasMany(AbsensiSiswa::class, 'dicatat_oleh'); }
}
