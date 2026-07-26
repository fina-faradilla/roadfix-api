<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/foto-laporan/{path}', function (string $path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response(
        Storage::disk('public')->get($path),
        200,
        [
            'Content-Type' => Storage::disk('public')->mimeType($path),
            'Access-Control-Allow-Origin' => '*',
        ]
    );
})->where('path', '.*');