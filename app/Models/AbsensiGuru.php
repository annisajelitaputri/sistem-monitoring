<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiGuru extends Model
{
    protected $table = 'absensi_guru';
    public $timestamps = false;
    protected $fillable = ['guru_id', 'tanggal', 'jam_masuk', 'status', 'keterangan', 'dicatat_oleh'];
    protected $casts = ['tanggal' => 'date'];

    public function guru(): BelongsTo { return $this->belongsTo(Guru::class); }
    public function pencatat(): BelongsTo { return $this->belongsTo(User::class, 'dicatat_oleh'); }
}
