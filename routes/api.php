<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriKerusakanApiController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\Admin\LaporanApiController as AdminLaporanApiController;
 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
 
// Data referensi, boleh publik
Route::get('/kategori', [KategoriKerusakanApiController::class, 'index']);
 
// Portal Warga: hanya lihat & buat laporan MILIK SENDIRI.
// Login wajib (siapa pun role-nya), tapi query di controller sudah
// difilter where('user_id', auth id) jadi warga tidak bisa lihat/edit
// punya orang lain.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/laporan', [LaporanApiController::class, 'index']);
    Route::post('/laporan', [LaporanApiController::class, 'store']);
});
 
// Portal Admin/Dinas: lihat & kelola SEMUA laporan.
// Middleware 'admin' sekarang dipasang — sebelumnya cuma auth:sanctum,
// artinya warga biasa yang login pun bisa akses endpoint ini. Sekarang
// wajib role_id = 1 (lihat EnsureUserIsAdmin).
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/laporan', [AdminLaporanApiController::class, 'index']);
        Route::post('/laporan', [AdminLaporanApiController::class, 'store']);
        Route::put('/laporan/{laporan}', [AdminLaporanApiController::class, 'update']);
        Route::delete('/laporan/{laporan}', [AdminLaporanApiController::class, 'destroy']);
        Route::patch('/laporan/{laporan}/verifikasi', [AdminLaporanApiController::class, 'verifikasi']);

        // Kelola Kategori (khusus admin)
        Route::post('/kategori', [KategoriKerusakanApiController::class, 'store']);
        Route::put('/kategori/{kategori}', [KategoriKerusakanApiController::class, 'update']);
        Route::delete('/kategori/{kategori}', [KategoriKerusakanApiController::class, 'destroy']);
    });