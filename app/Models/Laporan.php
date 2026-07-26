<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Laporan extends Model
{
    protected $table = 'laporans';

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

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // User (Pelapor)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Kategori Kerusakan
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKerusakan::class, 'kategori_id');
    }

    // Tindak Lanjut (riwayat, bisa lebih dari satu per laporan)
    public function tindakLanjuts(): HasMany
    {
        return $this->hasMany(TindakLanjut::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    // Nama pelapor otomatis
    protected function namaPelapor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user?->name ?? '-',
        );
    }

    // URL Foto
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto
                ? asset('storage/' . $this->foto)
                : null,
        );
    }
}