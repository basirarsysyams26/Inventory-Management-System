<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next, $role)
    // {
    //     if(auth()->check() && auth()->user()->role==$role){
    //         return $next($request);
    //     }
    //     abort(403, Unauthorized);
    // }
    public function handle(Request $request, Closure $next, $role)
    {
        // Jika route hanya untuk Teknisi, Manager tidak boleh akses
        if ($role === 'Teknisi' && auth()->check() && auth()->user()->role !== 'Teknisi') {
            abort(403, 'Akses hanya untuk Teknisi');
        }
        // Jika user punya role yang sesuai, lanjutkan
        if (auth()->check() && auth()->user()->role == $role) {
            return $next($request);
        }
        // Jika Manager, boleh akses route lain selain yang dibatasi
        if ($role === 'Manager' && auth()->check() && auth()->user()->role === 'Manager') {
            return $next($request);
        }
        abort(403, 'Akses hanya untuk Teknisi');
    }
}