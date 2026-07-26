<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/// Endpoint untuk Portal Warga: /api/laporan (GET & POST).
/// Beda dengan App\Http\Controllers\Api\Admin\LaporanApiController yang
/// bisa akses/ubah SEMUA laporan — controller ini SELALU dibatasi ke
/// laporan milik user yang sedang login (auth()->id()).
class LaporanApiController extends Controller
{
    private const STATUS_AWAL = 'Menunggu';

    /// Riwayat laporan milik user yang login saja.
    public function index(Request $request)
    {
        $rows = Laporan::with(['user', 'kategori', 'tindakLanjuts.user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($rows->map(fn (Laporan $l) => $this->transform($l)));
    }

    /// Detail satu laporan milik user yang login (termasuk riwayat tindak
    /// lanjut) — dipakai halaman Detail Laporan warga supaya bisa lihat
    /// perkembangan tanpa perlu muat ulang seluruh daftar.
    public function show(Request $request, Laporan $laporan)
    {
        if ($laporan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
        }

        $laporan->load(['user', 'kategori', 'tindakLanjuts.user']);

        return response()->json($this->transform($laporan));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'             => ['required', 'string', 'max:255'],
            'kategori_id'       => ['required', 'exists:kategori_kerusakan,id'],
            'tingkat_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'deskripsi'         => ['required', 'string'],
            'alamat'            => ['required', 'string', 'max:255'],
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'foto'              => ['nullable', 'image', 'max:5120'],
        ]);

        // Warga TIDAK boleh set status sendiri lewat request (beda dengan
        // admin) — semua laporan baru wajib mulai dari status awal.
        $data['status']  = self::STATUS_AWAL;
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Laporan::create($data)->load(['user', 'kategori', 'tindakLanjuts.user']);

        return response()->json($this->transform($laporan), 201);
    }

    private function transform(Laporan $laporan): array
    {
        return [
            'id'                => $laporan->id,
            'judul'             => $laporan->judul,
            'pelapor'           => $laporan->user?->name ?? '-',
            'kategori_id'       => $laporan->kategori_id,
            'kategori'          => $laporan->kategori?->nama_kategori,
            'tingkat_kerusakan' => $laporan->tingkat_kerusakan,
            'alamat'            => $laporan->alamat,
            'deskripsi'         => $laporan->deskripsi,
            'status'            => $laporan->status,
            'foto_url'          => $laporan->foto ? url('foto-laporan/' . $laporan->foto) : null,
            'latitude'          => (float) $laporan->latitude,
            'longitude'         => (float) $laporan->longitude,
            'tanggal'           => $laporan->created_at->translatedFormat('d M Y'),
            'tindak_lanjut'     => $laporan->tindakLanjuts->map(fn (TindakLanjut $t) => [
                'id'         => $t->id,
                'status'     => $t->status,
                'catatan'    => $t->catatan,
                'admin'      => $t->user?->name,
                'created_at' => $t->created_at->toIso8601String(),
            ])->values(),
        ];
    }
}