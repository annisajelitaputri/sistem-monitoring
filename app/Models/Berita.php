<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Berita extends Model
{
    protected $table = 'berita';
    public $timestamps = false;

    protected $fillable = [
        'judul', 'kategori', 'isi', 'gambar', 'status', 'penulis_id', 'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }
}