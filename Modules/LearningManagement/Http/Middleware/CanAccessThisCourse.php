<?php

namespace Modules\LearningManagement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LearningManagement\Services\Student\CourseService;

class CanAccessThisCourse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $service = new CourseService;
        $course = $service->findById(
            $request->route('course')
        );

        if (!$course) {
            return response()->json([
                'code'      => 403,
                'message'   => "Pertemuan tidak ditemukan",
                'errors'    => []
            ], 403);
        } else if (!$course->is_can_access) {
            return response()->json([
                'code'      => 403,
                'message'   => "Materi ini belum dapat dibuka, silahkan hubungi guru yang bersangkutan",
                'errors'    => []
            ], 403);
        }
        return $next($request);
    }
}
