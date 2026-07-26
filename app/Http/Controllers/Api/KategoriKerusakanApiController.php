<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori_kerusakan,nama_kategori'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategori = KategoriKerusakan::create($validated);

        return response()->json($kategori, 201);
    }

    public function update(Request $request, KategoriKerusakan $kategoriKerusakan)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                // Unik, tapi abaikan baris ini sendiri supaya "ubah lalu
                // simpan tanpa ganti nama" tidak dianggap duplikat.
                Rule::unique('kategori_kerusakan', 'nama_kategori')->ignore($kategoriKerusakan->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategoriKerusakan->update($validated);

        return response()->json($kategoriKerusakan);
    }

    public function destroy(KategoriKerusakan $kategoriKerusakan)
    {
        try {
            $kategoriKerusakan->delete();
        } catch (QueryException $e) {
            // Kategori masih dipakai satu atau lebih laporan (foreign key
            // constraint) — tolak dengan pesan yang jelas, bukan error 500
            // mentah seperti yang sempat muncul waktu truncate manual.
            return response()->json([
                'message' => 'Kategori ini masih dipakai oleh laporan yang ada, tidak bisa dihapus.',
            ], 409);
        }

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
