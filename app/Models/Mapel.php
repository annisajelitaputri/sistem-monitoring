<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    protected $table = 'mapels';
    protected $fillable = ['nama'];

    public function pengampu(): HasMany { return $this->hasMany(Pengampu::class); }
}