<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;

class KategoriKerusakanApiController extends Controller
{
    public function index()
    {
        return response()->json(
            KategoriKerusakan::select('id', 'nama_kategori')->orderBy('nama_kategori')->get()
        );
    }
}