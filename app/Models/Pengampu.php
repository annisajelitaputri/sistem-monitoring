<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengampu extends Model
{
    protected $table = 'pengampu';
    protected $fillable = ['guru_id', 'mapel_id', 'kelas_id'];

    public function guru(): BelongsTo { return $this->belongsTo(Guru::class); }
    public function mapel(): BelongsTo { return $this->belongsTo(Mapel::class); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
}