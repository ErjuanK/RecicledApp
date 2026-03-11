<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allows users with rol = 'admin' to proceed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder al panel de administración.');
        }

        return $next($request);
    }
}
