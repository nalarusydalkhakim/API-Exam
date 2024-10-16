<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CanAccessThisSchool
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $mySchoolId = Cache::remember('mySchoolId-' . $request->user()->id, (1 * 60 * 60 * 24), function () use ($request) {
            if ($request->user()->level === 'student') {
                return DB::table('students')->where('user_id', $request->user()->id)
                    ->pluck('school_id')
                    ->first();
            } else if ($request->user()->level === 'school') {
                return DB::table('employees')->where('user_id', $request->user()->id)
                    ->pluck('school_id')
                    ->first();
            }
        });
        if ($request->user()->level !== 'admin' && $mySchoolId !== $request->route('schoolId')) {
            return response()->json([
                'code'      => 403,
                'message'   => "Anda tidak diizinkan mengakses data sekolah ini.",
                'errors'    => []
            ], 403);
        }
        return $next($request);
    }
}
