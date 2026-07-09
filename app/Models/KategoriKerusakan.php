<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKerusakan extends Model
{
    protected $table = 'kategori_kerusakan';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function laporans(): HasMany
    {
        return $this->hasMany(Laporan::class, 'kategori_id');
    }
}