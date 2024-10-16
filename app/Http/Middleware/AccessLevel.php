<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$levels): Response
    {
        if (in_array($request->user()->level, $levels)) {
            return $next($request);
        }
        return response()->json([
            'code'      => 403,
            'message'   => "Anda tidak diizinkan mengakses situs ini",
            'errors'    => []
        ], 403);
    }
}
