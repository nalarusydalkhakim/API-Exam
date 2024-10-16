<?php

namespace App\Http\Middleware;

use App\Services\CacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HasModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleId): Response
    {
        $modules = Cache::remember('schoolModules-'.$request->route('schoolId'), (1 * 60 * 60 * 24), function () use($request, $moduleId) {
            return DB::table('module_school')->where('school_id', $request->route('schoolId'))
                    ->pluck('module_id')
                    ->toArray();
        });
        if (in_array($moduleId, $modules)) {
            return $next($request);
        }
        return response()->json([
            'code'      => 403,
            'message'   => "Sekolah Anda tidak berlangganan modul ini.",
            'errors'    => []
        ], 403);
    }
}
