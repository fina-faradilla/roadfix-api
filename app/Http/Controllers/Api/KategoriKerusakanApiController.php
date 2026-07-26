<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;
use Illuminate\Http\Request;

class KategoriKerusakanApiController extends Controller
{
    public function index()
    {
        return response()->json(
            KategoriKerusakan::select('id', 'nama_kategori', 'deskripsi')
                ->orderBy('nama_kategori')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255'],
            'deskripsi'     => ['required', 'string'],
        ]);

        $kategori = KategoriKerusakan::create($data);

        return response()->json($kategori, 201);
    }

    public function update(Request $request, KategoriKerusakan $kategori)
    {
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255'],
            'deskripsi'     => ['required', 'string'],
        ]);

        $kategori->update($data);

        return response()->json($kategori);
    }

    public function destroy(KategoriKerusakan $kategori)
    {
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}