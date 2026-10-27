<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilSekolah extends Model
{
    protected $table = 'profil_sekolah';
    public $timestamps = false;
    protected $fillable = ['nama_sekolah', 'visi', 'misi', 'alamat', 'telepon', 'email'];
}
