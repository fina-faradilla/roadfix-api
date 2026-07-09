<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    protected $fillable = [
        'laporan_id',
        'user_id',
        'status',
        'catatan',
    ];

    // Relasi ke Laporan
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    // Relasi ke User (Admin yang menindaklanjuti)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}