<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanApiController extends Controller
{
    private const STATUS_OPTIONS = ['Menunggu', 'Diproses', 'Selesai'];
    private const ADMIN_SYSTEM_EMAIL = 'system@roadfix.local';

    public function index(Request $request)
    {
        $query = Laporan::with(['user', 'kategori'])->latest();

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%");
            });
        }

        return response()->json($query->get()->map(fn (Laporan $l) => $this->transform($l)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'             => ['required', 'string', 'max:255'],
            'kategori_id'       => ['required', 'exists:kategori_kerusakan,id'],
            'tingkat_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'status'            => ['required', 'in:' . implode(',', self::STATUS_OPTIONS)],
            'deskripsi'         => ['required', 'string'],
            'alamat'            => ['required', 'string', 'max:255'],
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'foto'              => ['nullable', 'image', 'max:5120'],
        ]);

$data['user_id'] = auth()->id();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Laporan::create($data)->load(['user', 'kategori']);

        return response()->json($this->transform($laporan), 201);
    }

    public function update(Request $request, Laporan $laporan)
    {
        $data = $request->validate([
            'judul'             => ['required', 'string', 'max:255'],
            'kategori_id'       => ['required', 'exists:kategori_kerusakan,id'],
            'tingkat_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'status'            => ['required', 'in:' . implode(',', self::STATUS_OPTIONS)],
            'deskripsi'         => ['required', 'string'],
            'alamat'            => ['required', 'string', 'max:255'],
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
        ]);

        $laporan->update($data);

        return response()->json($this->transform($laporan->fresh(['user', 'kategori'])));
    }

    public function destroy(Laporan $laporan)
    {
        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }
        $laporan->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus']);
    }

    public function verifikasi(Laporan $laporan)
    {
        $next = match ($laporan->status) {
            'Menunggu' => 'Diproses',
            'Diproses' => 'Selesai',
            default    => null,
        };

        if ($next === null) {
            return response()->json(['message' => 'Laporan ini sudah tidak bisa diverifikasi lagi.'], 422);
        }

        $laporan->update(['status' => $next]);

        return response()->json($this->transform($laporan->fresh(['user', 'kategori'])));
    }

    private function transform(Laporan $laporan): array
{
    return [
        'id'                => $laporan->id,
        'judul'             => $laporan->judul,
        'pelapor'           => $laporan->user?->name ?? '-',
'dari_akun_warga' => $laporan->user && $laporan->user->email !== self::ADMIN_SYSTEM_EMAIL,        'kategori_id'       => $laporan->kategori_id,
        'kategori'          => $laporan->kategori?->nama_kategori, // <- diperbaiki
        'tingkat_kerusakan' => $laporan->tingkat_kerusakan,
        'alamat'            => $laporan->alamat,
        'deskripsi'         => $laporan->deskripsi,
        'status'            => $laporan->status,
        'foto_url'          => $laporan->foto ? Storage::disk('public')->url($laporan->foto) : null,
        'latitude'          => (float) $laporan->latitude,
        'longitude'         => (float) $laporan->longitude,
        'tanggal'           => $laporan->created_at->translatedFormat('d M Y'),
    ];
}
}