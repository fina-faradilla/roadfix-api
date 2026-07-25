<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKerusakan extends Model
{
    protected $table = 'kategori_kerusakan';
    protected $fillable = ['nama_kategori', 'deskripsi'];
}