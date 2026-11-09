<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DinasAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('dinas')->check()) {
            return redirect()->route('dinas.login')->with('error', 'Silakan pilih dinas dan masukkan PIN terlebih dahulu.');
        }

        $dinas = Auth::guard('dinas')->user();

        // Jika login pertama kali dan belum melengkapi form PIC
        if ($dinas->is_first_login && !$request->routeIs('dinas.first_login', 'dinas.first_login.save', 'dinas.logout')) {
            return redirect()->route('dinas.first_login');
        }

        return $next($request);
    }
}

