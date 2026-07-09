<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Laporan extends Model
{
    protected $fillable = [
        'user_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'foto',
        'alamat',
        'latitude',
        'longitude',
        'tingkat_kerusakan',
        'status',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Kategori Kerusakan
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKerusakan::class, 'kategori_id');
    }

    // Relasi ke Tindak Lanjut
    public function tindakLanjut(): HasOne
    {
        return $this->hasOne(TindakLanjut::class);
    }
}