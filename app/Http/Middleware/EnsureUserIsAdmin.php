<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // ganti angka 1 di bawah dengan role_id admin yang benar
        if ($request->user()?->role_id !== 1) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return $next($request);
    }
}