<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiSiswa extends Model
{
    protected $table = 'absensi_siswa';
    public $timestamps = false;
    protected $fillable = ['siswa_id', 'kelas_id', 'tanggal', 'status', 'keterangan', 'dicatat_oleh'];
    protected $casts = ['tanggal' => 'date'];

    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function pencatat(): BelongsTo { return $this->belongsTo(User::class, 'dicatat_oleh'); }
}
