<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';
    public $timestamps = false;
    protected $fillable = ['judul', 'deskripsi', 'tanggal', 'foto'];
    protected $casts = ['tanggal' => 'date'];
}
